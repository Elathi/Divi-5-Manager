<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class D5TM_Import_Handler {

    public function __construct() {
        add_action( 'wp_ajax_d5tm_import_layout', array( $this, 'handle_import' ) );
    }

    public function handle_import() {
        check_ajax_referer( 'd5tm_import_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'divi5-tm' ) ) );
        }

        if ( ! isset( $_FILES['file'] ) || $_FILES['file']['error'] !== UPLOAD_ERR_OK ) {
            wp_send_json_error( array( 'message' => __( 'File upload failed.', 'divi5-tm' ) ) );
        }

        $file = $_FILES['file'];
        $ext  = pathinfo( $file['name'], PATHINFO_EXTENSION );

        if ( strtolower( $ext ) !== 'json' ) {
            wp_send_json_error( array( 'message' => __( 'Invalid file type. Please upload a JSON file.', 'divi5-tm' ) ) );
        }

        $json_data = file_get_contents( $file['tmp_name'] );
        $data      = json_decode( $json_data, true );

        if ( json_last_error() !== JSON_ERROR_NONE || empty( $data ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid JSON format.', 'divi5-tm' ) ) );
        }

        // The JSON data structure from Divi Export is usually:
        // {"context":"...", "data": {"<post_id>": {"post_title": "...", "post_content": "..."}}}
        // or {"data": {"et_pb_layout": {"<post_id>": {...}}}}
        $layouts_array = array();
        if ( isset( $data['data']['et_pb_layout'] ) && is_array( $data['data']['et_pb_layout'] ) ) {
            $layouts_array = $data['data']['et_pb_layout'];
        } elseif ( isset( $data['data'] ) && is_array( $data['data'] ) ) {
            $layouts_array = $data['data'];
        }

        $imported_count = 0;

        foreach ( $layouts_array as $layout_id => $layout_data ) {
            // Check if this is actually a layout array (must have post_title or title)
            if ( ! is_array( $layout_data ) ) continue;

            $title   = isset( $layout_data['post_title'] ) ? $layout_data['post_title'] : ( isset( $layout_data['title'] ) ? $layout_data['title'] : 'Imported Divi 5 Layout' );
            $content = isset( $layout_data['post_content'] ) ? $layout_data['post_content'] : ( isset( $layout_data['content'] ) ? $layout_data['content'] : '' );
            
            // Unslash and sanitize
            $title   = sanitize_text_field( $title );
            // Use wp_slash before importing content to prevent WordPress from stripping slashes improperly from blocks
            $content = wp_slash( $content ); 

            // Insert the layout
            $post_args = array(
                'post_title'   => $title,
                'post_content' => $content,
                'post_status'  => 'publish',
                'post_type'    => 'et_pb_layout',
            );

            $post_id = wp_insert_post( $post_args );

            if ( $post_id && ! is_wp_error( $post_id ) ) {
                // ── Import post_meta exactly as exported ──────────
                // Divi relies heavily on meta for type, post type built for, etc.
                if ( isset( $layout_data['post_meta'] ) && is_array( $layout_data['post_meta'] ) ) {
                    foreach ( $layout_data['post_meta'] as $meta_key => $meta_values ) {
                        // Divi exports meta values as arrays
                        if ( is_array( $meta_values ) ) {
                            foreach ( $meta_values as $val ) {
                                add_post_meta( $post_id, $meta_key, maybe_unserialize( $val ) );
                            }
                        } else {
                            add_post_meta( $post_id, $meta_key, maybe_unserialize( $meta_values ) );
                        }
                    }
                } else {
                    // Fallback
                    update_post_meta( $post_id, '_et_pb_built_for_post_type', 'page' );
                }
                
                // ── Import taxonomy terms (categories / tags / type) ──────────
                //
                // Divi 5 exports taxonomies in a flat "terms" object where keys are
                // original term IDs, and values contain "name", "slug", "taxonomy", etc.
                // Alternative exports might use "tax_input" keyed by taxonomy name.

                $supported_taxes = array( 'layout_category', 'layout_tag', 'layout_type', 'scope', 'module_width' );
                $terms_to_assign = array(); // Group by taxonomy: [ 'layout_category' => [ 'id1', 'id2' ] ]

                // 1. Check for the flat "terms" structure (standard Divi Export)
                if ( isset( $layout_data['terms'] ) && is_array( $layout_data['terms'] ) ) {
                    foreach ( $layout_data['terms'] as $original_term_id => $term_data ) {
                        if ( ! is_array( $term_data ) || empty( $term_data['name'] ) || empty( $term_data['taxonomy'] ) ) {
                            continue;
                        }

                        $tax = $term_data['taxonomy'];
                        if ( in_array( $tax, $supported_taxes ) && taxonomy_exists( $tax ) ) {
                            $term_name = sanitize_text_field( $term_data['name'] );
                            
                            $existing = term_exists( $term_name, $tax );
                            if ( ! $existing ) {
                                $existing = wp_insert_term( $term_name, $tax );
                            }
                            if ( ! is_wp_error( $existing ) && isset( $existing['term_id'] ) ) {
                                $terms_to_assign[ $tax ][] = (int) $existing['term_id'];
                            }
                        }
                    }
                }

                // 2. Check for "tax_input" (alternative plugin/older exports)
                if ( isset( $layout_data['tax_input'] ) && is_array( $layout_data['tax_input'] ) ) {
                    foreach ( $supported_taxes as $tax ) {
                        if ( isset( $layout_data['tax_input'][ $tax ] ) && taxonomy_exists( $tax ) ) {
                            $raw_terms = $layout_data['tax_input'][ $tax ];
                            if ( is_array( $raw_terms ) ) {
                                foreach ( $raw_terms as $term_item ) {
                                    if ( is_array( $term_item ) ) {
                                        $term_name = isset( $term_item['name'] ) ? sanitize_text_field( $term_item['name'] ) : '';
                                    } else {
                                        $term_name = sanitize_text_field( (string) $term_item );
                                    }

                                    if ( ! empty( $term_name ) ) {
                                        $existing = term_exists( $term_name, $tax );
                                        if ( ! $existing ) {
                                            $existing = wp_insert_term( $term_name, $tax );
                                        }
                                        if ( ! is_wp_error( $existing ) && isset( $existing['term_id'] ) ) {
                                            $terms_to_assign[ $tax ][] = (int) $existing['term_id'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // Assign all gathered terms
                foreach ( $terms_to_assign as $tax => $term_ids ) {
                    if ( ! empty( $term_ids ) ) {
                        wp_set_object_terms( $post_id, array_unique( $term_ids ), $tax );
                    }
                }

                // Handle Smart Image Upload Attachment
                if ( isset( $_FILES['thumbnail'] ) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK ) {
                    require_once( ABSPATH . 'wp-admin/includes/image.php' );
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                    require_once( ABSPATH . 'wp-admin/includes/media.php' );

                    $attachment_id = media_handle_upload( 'thumbnail', $post_id );
                    if ( ! is_wp_error( $attachment_id ) ) {
                        set_post_thumbnail( $post_id, $attachment_id );
                    }
                }
                
                $imported_count++;
            }
        }

        if ( $imported_count > 0 ) {
            wp_send_json_success( array( 'message' => sprintf( __( 'Successfully imported %d layout(s).', 'divi5-tm' ), $imported_count ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to import layouts. Check file structure.', 'divi5-tm' ) ) );
        }
    }
}

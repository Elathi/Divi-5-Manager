<?php
/**
 * Plugin Name:       Divi 5 Template Manager
 * Plugin URI:        https://divi.elathi.xyz
 * Description:       A professional asset library to browse, manage, preview, import, and export Divi 5 layouts and templates.
 * Version:           3.2.1
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Tested up to:      6.7
 * Stable tag:        3.2.1
 * Author:            S. Anand Kumar
 * Author URI:        https://elathi.xyz
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       divi5-tm
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'DIVI5_TM_VERSION', '3.2.1' );
define( 'DIVI5_TM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DIVI5_TM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include necessary files
require_once DIVI5_TM_PLUGIN_DIR . 'includes/class-layout-manager.php';
require_once DIVI5_TM_PLUGIN_DIR . 'includes/class-import-handler.php';
require_once DIVI5_TM_PLUGIN_DIR . 'includes/class-skeleton-generator.php';

class Divi5_Template_Manager {

    private static $instance = null;
    public $layout_manager;
    public $import_handler;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->layout_manager = new D5TM_Layout_Manager();
        $this->import_handler = new D5TM_Import_Handler();

        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        add_action( 'wp_ajax_d5tm_get_layout_data', array( $this, 'ajax_get_layout_data' ) );
        add_action( 'wp_ajax_d5tm_quick_edit_layout', array( $this, 'ajax_quick_edit_layout' ) );
        add_action( 'wp_ajax_d5tm_download_layout', array( $this, 'ajax_download_layout' ) );
        add_action( 'wp_ajax_d5tm_trash_layout', array( $this, 'ajax_trash_layout' ) );
        add_action( 'wp_ajax_d5tm_restore_layout', array( $this, 'ajax_restore_layout' ) );
        add_action( 'wp_ajax_d5tm_delete_layout', array( $this, 'ajax_delete_layout' ) );
        add_action( 'wp_ajax_d5tm_update_term_color', array( $this, 'ajax_update_term_color' ) );
        add_action( 'wp_ajax_d5tm_delete_empty_cats', array( $this, 'ajax_delete_empty_cats' ) );

        // v3.0 Batch Editor Hooks
        add_action( 'wp_ajax_d5tm_batch_update', array( $this, 'ajax_batch_update' ) );
        add_action( 'wp_ajax_d5tm_batch_trash', array( $this, 'ajax_batch_trash' ) );
    }

    /**
     * Bulk Update: Assign Term to multiple layouts
     */
    public function ajax_batch_update() {
        check_ajax_referer( 'd5tm_import_nonce', 'nonce' );
        if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Permission denied.' ) );

        $ids     = isset( $_POST['ids'] ) ? array_map( 'intval', $_POST['ids'] ) : array();
        $term_id = isset( $_POST['term_id'] ) ? intval( $_POST['term_id'] ) : 0;
        $tax     = isset( $_POST['tax'] ) ? sanitize_text_field( $_POST['tax'] ) : 'layout_category';

        if ( empty( $ids ) || ! $term_id ) {
            wp_send_json_error( array( 'message' => 'Invalid data.' ) );
        }

        foreach ( $ids as $id ) {
            wp_set_object_terms( $id, array( $term_id ), $tax, true ); // true = append
        }

        wp_send_json_success( array( 'message' => 'Batch update complete.' ) );
    }

    /**
     * Bulk Trash: Move multiple layouts to trash
     */
    public function ajax_batch_trash() {
        check_ajax_referer( 'd5tm_import_nonce', 'nonce' );
        if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Permission denied.' ) );

        $ids = isset( $_POST['ids'] ) ? array_map( 'intval', $_POST['ids'] ) : array();

        if ( empty( $ids ) ) {
            wp_send_json_error( array( 'message' => 'No items selected.' ) );
        }

        foreach ( $ids as $id ) {
            wp_trash_post( $id );
        }

        wp_send_json_success( array( 'message' => 'Items moved to trash.' ) );
    }

    public function ajax_get_layout_data() {
        check_ajax_referer( 'd5tm_import_nonce', 'nonce' );
        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied.' ) );
        }
        
        $layout_id = isset( $_POST['layout_id'] ) ? intval( $_POST['layout_id'] ) : 0;
        if ( ! $layout_id ) {
            wp_send_json_error( array( 'message' => 'Invalid layout ID.' ) );
        }

        $post = get_post( $layout_id );
        if ( ! $post || $post->post_type !== 'et_pb_layout' ) {
            wp_send_json_error( array( 'message' => 'Layout not found.' ) );
        }

        // Divi 5 relies on Gutenberg-like Block structures.
        // We just need the raw block markup text. 
        $raw_content = $post->post_content;

        // Package as Divi JSON structure - exact match for export format
        $json_export = array(
            'context' => 'et_builder_layouts',
            'data'    => array(
                $layout_id => array(
                    'post_title'   => $post->post_title,
                    'post_content' => $post->post_content,
                    'post_status'  => 'publish',
                    'post_type'    => 'et_pb_layout'
                )
            )
        );

        wp_send_json_success( array( 
            'layout_data' => wp_json_encode( $json_export ),
            'message'     => 'Success' 
        ) );
    }

    public function ajax_download_layout() {
        check_ajax_referer( 'd5tm_import_nonce', 'nonce' );
        if ( ! current_user_can( 'edit_posts' ) ) { wp_send_json_error( array( 'message' => 'Permission denied.' ) ); }

        $layout_id = isset( $_POST['layout_id'] ) ? intval( $_POST['layout_id'] ) : 0;
        $post = get_post( $layout_id );
        
        if ( ! $post || $post->post_type !== 'et_pb_layout' ) {
            wp_send_json_error( array( 'message' => 'Layout not found.' ) );
        }

        // Package as Divi JSON
        $json_export = array(
            'context' => 'et_builder_layouts',
            'data'    => array(
                $post->ID => array(
                    'post_title'   => $post->post_title,
                    'post_content' => $post->post_content,
                    'post_status'  => 'publish',
                    'post_type'    => 'et_pb_layout'
                )
            )
        );

        wp_send_json_success( array(
            'filename' => sanitize_title( $post->post_title ) . '.json',
            'json'     => wp_json_encode( $json_export )
        ) );
    }

    public function ajax_trash_layout() {
        check_ajax_referer( 'd5tm_import_nonce', 'nonce' );
        if ( ! current_user_can( 'delete_posts' ) ) { wp_send_json_error( array( 'message' => 'Permission denied.' ) ); }

        $layout_id = isset( $_POST['layout_id'] ) ? intval( $_POST['layout_id'] ) : 0;
        $trashed = wp_trash_post( $layout_id );
        if ( $trashed ) {
            wp_send_json_success( array( 'message' => 'Moved to trash' ) );
        }
        wp_send_json_error( array( 'message' => 'Failed to trash layout' ) );
    }

    public function ajax_restore_layout() {
        check_ajax_referer( 'd5tm_import_nonce', 'nonce' );
        if ( ! current_user_can( 'edit_posts' ) ) { wp_send_json_error( array( 'message' => 'Permission denied.' ) ); }

        $layout_id = isset( $_POST['layout_id'] ) ? intval( $_POST['layout_id'] ) : 0;
        $restored = wp_untrash_post( $layout_id );
        if ( $restored ) {
            wp_send_json_success( array( 'message' => 'Restored successfully' ) );
        }
        wp_send_json_error( array( 'message' => 'Failed to restore layout' ) );
    }

    public function ajax_delete_layout() {
        check_ajax_referer( 'd5tm_import_nonce', 'nonce' );
        if ( ! current_user_can( 'delete_posts' ) ) { wp_send_json_error( array( 'message' => 'Permission denied.' ) ); }

        $layout_id = isset( $_POST['layout_id'] ) ? intval( $_POST['layout_id'] ) : 0;
        $deleted = wp_delete_post( $layout_id, true ); // true = force delete
        if ( $deleted ) {
            wp_send_json_success( array( 'message' => 'Deleted permanently' ) );
        }
        wp_send_json_error( array( 'message' => 'Failed to delete layout' ) );
    }

    public function ajax_quick_edit_layout() {
        check_ajax_referer( 'd5tm_import_nonce', 'nonce' );
        
        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied.' ) );
        }

        $layout_id = isset( $_POST['layout_id'] ) ? intval( $_POST['layout_id'] ) : 0;
        if ( ! $layout_id ) {
            wp_send_json_error( array( 'message' => 'Invalid layout ID.' ) );
        }

        $post_title  = isset( $_POST['post_title'] ) ? sanitize_text_field( $_POST['post_title'] ) : '';
        $post_status = isset( $_POST['post_status'] ) ? sanitize_text_field( $_POST['post_status'] ) : 'publish';
        $thumbnail_id= isset( $_POST['thumbnail_id'] ) ? intval( $_POST['thumbnail_id'] ) : 0;
        
        // Update basic post data
        $post_data = array(
            'ID'          => $layout_id,
            'post_status' => $post_status,
        );
        if ( ! empty( $post_title ) ) {
            $post_data['post_title'] = $post_title;
        }

        wp_update_post( $post_data );

        // Update Thumbnail
        if ( $thumbnail_id > 0 ) {
            set_post_thumbnail( $layout_id, $thumbnail_id );
        } elseif ( $thumbnail_id === -1 ) {
            delete_post_thumbnail( $layout_id );
        }

        // Update Categories
        if ( isset( $_POST['layout_categories'] ) && is_array( $_POST['layout_categories'] ) ) {
            $cats = array_map( 'intval', $_POST['layout_categories'] );
            wp_set_object_terms( $layout_id, $cats, 'layout_category' );
        } else {
            wp_set_object_terms( $layout_id, array(), 'layout_category' );
        }

        // Update Tags
        if ( isset( $_POST['layout_tags'] ) ) {
            $tags = sanitize_text_field( $_POST['layout_tags'] );
            wp_set_object_terms( $layout_id, $tags, 'layout_tag' );
        }

        wp_send_json_success( array( 'message' => 'Layout updated successfully.' ) );
    }
    public function ajax_update_term_color() {
        check_ajax_referer( 'd5tm_import_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
        }

        $term_id = isset( $_POST['term_id'] ) ? intval( $_POST['term_id'] ) : 0;
        $color   = isset( $_POST['color'] ) ? sanitize_hex_color( $_POST['color'] ) : '';

        if ( ! $term_id || ! $color ) {
            wp_send_json_error( array( 'message' => 'Invalid data.' ) );
        }

        update_term_meta( $term_id, 'd5tm_color', $color );
        wp_send_json_success( array( 'message' => 'Color updated.' ) );
    }

    public function ajax_delete_empty_cats() {
        check_ajax_referer( 'd5tm_import_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
        }

        $deleted = 0;
        foreach ( array( 'layout_category', 'layout_tag' ) as $taxonomy ) {
            $empty_terms = get_terms( array(
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
                'fields'     => 'all',
            ) );
            if ( ! is_wp_error( $empty_terms ) ) {
                foreach ( $empty_terms as $term ) {
                    if ( $term->count === 0 ) {
                        wp_delete_term( $term->term_id, $taxonomy );
                        $deleted++;
                    }
                }
            }
        }

        wp_send_json_success( array(
            'message' => $deleted > 0
                ? sprintf( '%d empty %s deleted.', $deleted, _n( 'term', 'terms', $deleted, 'divi5-tm' ) )
                : 'No empty categories or tags found.',
            'count'   => $deleted,
        ) );
    }

    public function register_admin_menu() {
        add_menu_page(
            __( 'Divi 5 Manager', 'divi5-tm' ),
            __( 'Divi 5 Manager', 'divi5-tm' ),
            'manage_options',
            'divi5-template-manager',
            array( $this, 'render_admin_page' ),
            'dashicons-layout',
            60
        );
    }

    public function enqueue_admin_scripts( $hook ) {
        if ( 'toplevel_page_divi5-template-manager' !== $hook ) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style( 'd5tm-bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', array(), null );
        wp_enqueue_style( 'd5tm-admin-style', DIVI5_TM_PLUGIN_URL . 'admin/css/admin-style.css', array( 'd5tm-bootstrap-icons' ), time() );
        wp_enqueue_script( 'd5tm-admin-script', DIVI5_TM_PLUGIN_URL . 'admin/js/admin-scripts.js', array( 'jquery' ), time(), true );
        
        wp_localize_script( 'd5tm-admin-script', 'd5tm_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'd5tm_import_nonce' )
        ) );
    }

    public function render_admin_page() {
        include DIVI5_TM_PLUGIN_DIR . 'admin/views/main-dashboard.php';
    }
}

// Initialize the plugin
add_action( 'plugins_loaded', array( 'Divi5_Template_Manager', 'get_instance' ) );

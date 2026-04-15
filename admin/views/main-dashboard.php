<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php
// Pull all data
$tm          = Divi5_Template_Manager::get_instance();
$current_status = isset( $_GET['status'] ) && sanitize_text_field( $_GET['status'] ) === 'trash' ? 'trash' : array( 'publish', 'draft', 'private', 'pending' );

$layouts_arr = $tm->layout_manager->get_layouts( array(
    'post_status' => $current_status
) );
$total       = count( $layouts_arr );
$hide_empty_cats = get_option( 'd5tm_hide_empty_cats', 'off' ) === 'on';

// Fetch all categories
$categories_raw = get_terms( array(
    'taxonomy'   => 'layout_category',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
) );

$categories = array();
if ( ! is_wp_error( $categories_raw ) && ! empty( $categories_raw ) ) {
    foreach ( $categories_raw as $cat ) {
        $tagged = new WP_Query( array(
            'post_type'   => 'et_pb_layout',
            'post_status' => $current_status === 'trash' ? 'trash' : array( 'publish', 'draft', 'private', 'pending' ),
            'tax_query'   => array(
                array( 'taxonomy' => 'layout_category', 'field' => 'term_id', 'terms' => $cat->term_id ),
            ),
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ) );
        $cat->count = count( $tagged->posts );
        if ( ! $hide_empty_cats || $cat->count > 0 ) {
            $categories[] = $cat;
        }
        wp_reset_postdata();
    }
}

// Fetch all tags
$layout_tags_raw = get_terms( array(
    'taxonomy'   => 'layout_tag',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
) );

$layout_tags = array();
$tag_counts  = array();
if ( ! is_wp_error( $layout_tags_raw ) && ! empty( $layout_tags_raw ) ) {
    foreach ( $layout_tags_raw as $ltag ) {
        $tagged = new WP_Query( array(
            'post_type'   => 'et_pb_layout',
            'post_status' => $current_status === 'trash' ? 'trash' : array( 'publish', 'draft', 'private', 'pending' ),
            'tax_query'   => array(
                array( 'taxonomy' => 'layout_tag', 'field' => 'term_id', 'terms' => $ltag->term_id ),
            ),
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ) );
        $tag_count = count( $tagged->posts );
        if ( ! $hide_empty_cats || $tag_count > 0 ) {
            $ltag->count = $tag_count;
            $layout_tags[] = $ltag;
            $tag_counts[ $ltag->term_id ] = $tag_count;
        }
        wp_reset_postdata();
    }
}

/**
 * Helper: Auto-assign colors to terms if missing
 */
if ( ! function_exists( 'd5tm_ensure_term_color' ) ) {
    function d5tm_ensure_term_color( $term ) {
        if ( ! $term ) return '#6366f1';
        $color = get_term_meta( $term->term_id, 'd5tm_color', true );
        if ( ! $color ) {
            $palette = array(
                '#6366f1', '#ec4899', '#f43f5e', '#f59e0b', '#10b981', '#06b6d4', '#3b82f6', '#8b5cf6',
                '#64748b', '#ef4444', '#f97316', '#84cc16', '#22c55e', '#14b8a6', '#0ea5e9', '#a855f7'
            );
            $color = $palette[ $term->term_id % count( $palette ) ];
            update_term_meta( $term->term_id, 'd5tm_color', $color );
        }
        return $color;
    }
}

// Ensure all categories have colors
if ( ! is_wp_error( $categories ) ) {
    foreach ( $categories as $cat ) { d5tm_ensure_term_color( $cat ); }
}
if ( ! is_wp_error( $layout_tags ) ) {
    foreach ( $layout_tags as $ltag ) { d5tm_ensure_term_color( $ltag ); }
}

// Fetch visibility settings
$show_builder      = get_option( 'd5tm_show_action_builder', 'on' ) === 'on';
$show_download     = get_option( 'd5tm_show_action_download', 'on' ) === 'on';
$show_quick_edit   = get_option( 'd5tm_show_action_quick_edit', 'on' ) === 'on';
$show_trash        = get_option( 'd5tm_show_action_trash', 'on' ) === 'on';
$tag_branding      = get_option( 'd5tm_tag_branding', 'on' ) === 'on';
$skeleton_branding = get_option( 'd5tm_skeleton_branding', 'off' ) === 'on';
?>

<div class="d5tm-wrap">
<div class="d5tm-app">

    <!-- ===== TOP HEADER ===== -->
    <header class="d5tm-header">
        <div class="d5tm-logo">
            <div class="d5tm-logo-icon">
                <i class="bi bi-grid-fill"></i>
            </div>
            Divi 5 Manager
        </div>

        <nav class="d5tm-header-tabs">
            <button class="d5tm-htab active" data-target="browse-tab" data-tooltip="<?php esc_attr_e( 'View and filter your library', 'divi5-tm' ); ?>"><i class="bi bi-collection"></i> <?php esc_html_e( 'Browse Layouts', 'divi5-tm' ); ?></button>
            <button class="d5tm-htab" data-target="usage-audit-all-tab" data-tooltip="<?php esc_attr_e( 'View site-wide layout usage report', 'divi5-tm' ); ?>"><i class="bi bi-diagram-2"></i> <?php esc_html_e( 'Usage Audit', 'divi5-tm' ); ?></button>
            <button class="d5tm-htab" data-target="import-tab" data-tooltip="<?php esc_attr_e( 'Upload and categorize Divi JSON files', 'divi5-tm' ); ?>"><i class="bi bi-cloud-upload"></i> <?php esc_html_e( 'Import Layout', 'divi5-tm' ); ?></button>
            <button class="d5tm-htab" data-target="settings-tab" data-tooltip="<?php esc_attr_e( 'Configure plugin preferences', 'divi5-tm' ); ?>">
                <i class="bi bi-sliders"></i>
                <?php esc_html_e( 'Settings', 'divi5-tm' ); ?>
            </button>
            <button class="d5tm-htab" data-target="changelog-tab" data-tooltip="<?php esc_attr_e( 'View latest version updates', 'divi5-tm' ); ?>">
                <i class="bi bi-clock-history"></i>
                <?php esc_html_e( 'Changelog', 'divi5-tm' ); ?>
            </button>
        </nav>

        <div style="margin-left: auto; display: flex; align-items: center; gap: 15px;">
            <button class="d5tm-help-btn" id="d5tm-help-trigger" data-tooltip="<?php esc_attr_e( 'Feature Guide & Help', 'divi5-tm' ); ?>">
                <i class="bi bi-question-circle"></i>
            </button>
            <button class="d5tm-theme-toggle" id="d5tm-theme-toggle" data-tooltip="<?php esc_attr_e( 'Toggle Light/Dark Mode', 'divi5-tm' ); ?>">
                <i class="bi bi-sun" id="d5tm-theme-icon"></i>
            </button>
            <span class="d5tm-header-count" data-tooltip="<?php esc_attr_e( 'Number of items in the current view', 'divi5-tm' ); ?>"><?php echo esc_html( $total ); ?> layouts</span>
        </div>
    </header>

    <!-- ===== BROWSE TAB ===== -->
    <div id="browse-tab" class="d5tm-tab active">
        <div class="d5tm-body">

            <!-- Sidebar -->
            <aside class="d5tm-sidebar">
                <div class="d5tm-sidebar-section">
                    <div class="d5tm-sidebar-label" data-tooltip="<?php esc_attr_e( 'Main library navigation', 'divi5-tm' ); ?>">Browse</div>
                    <button class="d5tm-sidebar-link active" data-filter-type="cat" data-filter-val="all" data-tooltip="<?php esc_attr_e( 'Show all layouts and templates', 'divi5-tm' ); ?>">
                        <i class="bi bi-grid-fill"></i>
                        All Layouts
                        <span class="d5tm-sidebar-count"><?php echo esc_html( $total ); ?></span>
                    </button>
                </div>

                <?php if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) : ?>
                <hr class="d5tm-sidebar-divider">
                <div class="d5tm-sidebar-section">
                    <div class="d5tm-sidebar-label" data-tooltip="<?php esc_attr_e( 'Filter by layout category', 'divi5-tm' ); ?>">Categories</div>
                    <?php foreach ( $categories as $cat ) : 
                        $cat_color = get_term_meta( $cat->term_id, 'd5tm_color', true );
                    ?>
                        <div class="d5tm-sidebar-item-wrap">
                            <button class="d5tm-sidebar-link" data-filter-type="cat" data-filter-val="<?php echo esc_attr( $cat->slug ); ?>">
                                <i class="bi bi-folder" style="color: <?php echo esc_attr( $skeleton_branding ? $cat_color : '#cbd5e1' ); ?>;"></i>
                                <?php echo esc_html( $cat->name ); ?>
                                <span class="d5tm-sidebar-count"><?php echo esc_html( $cat->count ); ?></span>
                            </button>
                            <?php if ( $skeleton_branding ) : ?>
                                <input type="color" class="d5tm-term-color-picker" 
                                       value="<?php echo esc_attr( $cat_color ); ?>" 
                                       data-term-id="<?php echo esc_attr( $cat->term_id ); ?>"
                                       data-tooltip="<?php esc_attr_e( 'Change Category Color', 'divi5-tm' ); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ( ! is_wp_error( $layout_tags ) && ! empty( $layout_tags ) ) : ?>
                <hr class="d5tm-sidebar-divider">
                <div class="d5tm-sidebar-section">
                    <div class="d5tm-sidebar-label" data-tooltip="<?php esc_attr_e( 'Filter by layout tags', 'divi5-tm' ); ?>">Tags</div>
                    <?php foreach ( $layout_tags as $ltag ) : 
                        $tag_color = get_term_meta( $ltag->term_id, 'd5tm_color', true );
                    ?>
                        <div class="d5tm-sidebar-item-wrap">
                            <button class="d5tm-sidebar-link" data-filter-type="tag" data-filter-val="<?php echo esc_attr( $ltag->slug ); ?>">
                                <i class="bi bi-tag" style="color: <?php echo esc_attr( $tag_branding ? $tag_color : '#cbd5e1' ); ?>;"></i>
                                <?php echo esc_html( $ltag->name ); ?>
                                <span class="d5tm-sidebar-count"><?php echo esc_html( isset( $tag_counts[ $ltag->term_id ] ) ? $tag_counts[ $ltag->term_id ] : 0 ); ?></span>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </aside>

            <!-- Main content -->
            <main class="d5tm-main">
                <!-- Toolbar -->
                <div class="d5tm-toolbar">
                    <div class="d5tm-toolbar-tabs">
                        <a href="?page=divi5-template-manager" class="d5tm-tab-btn <?php echo esc_attr( $current_status !== 'trash' ? 'active' : '' ); ?>" data-tooltip="<?php esc_attr_e( 'Browse published layouts', 'divi5-tm' ); ?>">Active</a>
                        <a href="?page=divi5-template-manager&status=trash" class="d5tm-tab-btn <?php echo esc_attr( $current_status === 'trash' ? 'active' : '' ); ?>" data-tooltip="<?php esc_attr_e( 'View items in trash', 'divi5-tm' ); ?>">Trash</a>
                        <button type="button" class="d5tm-tab-btn d5tm-action-refresh" data-tooltip="<?php esc_attr_e( 'Refresh Dashboard', 'divi5-tm' ); ?>" style="padding: 6px 10px; cursor: pointer;">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                    <div class="d5tm-search-box" data-tooltip="<?php esc_attr_e( 'Search by layout title...', 'divi5-tm' ); ?>">
                        <i class="bi bi-search"></i>
                        <input type="text" id="d5tm-search-input" placeholder="Search layouts...">
                    </div>
                    <div class="d5tm-toolbar-filters">
                        <select class="d5tm-filter-select" id="d5tm-grid-cols" data-tooltip="<?php esc_attr_e( 'Adjust grid columns', 'divi5-tm' ); ?>">
                            <option value="auto">Auto Grid</option>
                            <option value="1">1 Column</option>
                            <option value="2">2 Columns</option>
                            <option value="3">3 Columns</option>
                            <option value="4">4 Columns</option>
                            <option value="5">5 Columns</option>
                        </select>

                        <select class="d5tm-filter-select" id="d5tm-sort-order" data-tooltip="<?php esc_attr_e( 'Change list order', 'divi5-tm' ); ?>">
                                <option value="name_asc">Sort: A-Z</option>
                                <option value="name_desc">Sort: Z-A</option>
                                <option value="date_desc">Sort: Newest First</option>
                                <option value="date_asc">Sort: Oldest First</option>
                        </select>
                    </div>
                    <div class="d5tm-toolbar-right">
                        <span class="d5tm-result-count" id="d5tm-count-label" style="margin-right: 12px;"><?php echo esc_html( $total ); ?> results</span>
                        <button type="button" class="d5tm-btn d5tm-btn-secondary d5tm-bulk-select-toggle" data-tooltip="<?php esc_attr_e( 'Toggle bulk selection mode', 'divi5-tm' ); ?>">
                            <i class="bi bi-check2-square"></i> Select
                        </button>
                    </div>
                </div>


                <!-- Grid -->
                <div class="d5tm-content-area">
                    <div class="d5tm-grid" id="d5tm-grid">
                        <?php if ( ! empty( $layouts_arr ) ) :
                            foreach ( $layouts_arr as $layout ) :
                                // Meta
                                $layout_cats = get_the_terms( $layout->ID, 'layout_category' );
                                $layout_tags = get_the_terms( $layout->ID, 'layout_tag' );

                                $cat_slugs = $cat_names = $tag_slugs = $tag_names = array();

                                if ( $layout_cats && ! is_wp_error( $layout_cats ) ) {
                                    foreach ( $layout_cats as $c ) {
                                        $cat_slugs[] = $c->slug;
                                        $cat_names[] = array( 'name' => $c->name, 'id' => $c->term_id );
                                    }
                                }

                                if ( $layout_tags && ! is_wp_error( $layout_tags ) ) {
                                    foreach ( $layout_tags as $t ) {
                                        $tag_slugs[] = $t->slug;
                                        $tag_names[] = array( 'name' => $t->name, 'id' => $t->term_id );
                                    }
                                }

                                $search_terms = array_merge( 
                                    array_column( $cat_names, 'name' ), 
                                    array_column( $tag_names, 'name' ) 
                                );
                                $search_data = strtolower( $layout->post_title . ' ' . implode( ' ', $search_terms ) );
                                $cat_data    = implode( ' ', $cat_slugs );
                                $tag_data    = implode( ' ', $tag_slugs );
                                $edit_link   = home_url( '/et_pb_layout/' . $layout->post_name . '/?et_fb=1&PageSpeed=off' );
                                $date_str    = get_the_date( 'M j, Y', $layout->ID );
                                
                                // Specific data for quick edit
                                $thumb_id = get_post_thumbnail_id($layout->ID) ?: 0;
                                $thumb_url = $thumb_id ? get_the_post_thumbnail_url($layout->ID, 'large') : '';
                                
                                // Build the preview URL exactly like Divi's own library does:
                                // Manually construct the permalink using post_name (get_permalink() returns false
                                // for non-publicly-queryable post types like et_pb_layout).
                                $preview_base = home_url( '/et_pb_layout/' . $layout->post_name . '/' );
                                $preview_url  = add_query_arg(
                                    array(
                                        'preview_id'    => $layout->ID,
                                        'preview_nonce' => wp_create_nonce( 'post_preview_' . $layout->ID ),
                                        'preview'       => 'true',
                                    ),
                                    $preview_base
                                );
                                ?>
                                <div class="d5tm-card"
                                    data-layout-id="<?php echo esc_attr( $layout->ID ); ?>"
                                    data-title="<?php echo esc_attr( $layout->post_title ); ?>"
                                    data-date="<?php echo esc_attr( strtotime( $layout->post_date ) ); ?>"
                                    data-status="<?php echo esc_attr( $layout->post_status ); ?>"
                                    data-thumbnail-id="<?php echo esc_attr( $thumb_id ); ?>"
                                    data-thumbnail-url="<?php echo esc_url( $thumb_url ); ?>"
                                    data-cats-json='<?php echo esc_attr( wp_json_encode( wp_list_pluck( $cat_names, 'id' ) ) ); ?>'
                                    data-tags-raw="<?php echo esc_attr( implode( ', ', array_column( $tag_names, 'name' ) ) ); ?>"
                                    data-preview-url="<?php echo esc_url( $preview_url ); ?>"
                                    data-search="<?php echo esc_attr( $search_data ); ?>"
                                    data-category="<?php echo esc_attr( $cat_data ); ?>"
                                    data-tag="<?php echo esc_attr( $tag_data ); ?>"
                                    <?php 
                                    $primary_cat = ( ! empty( $layout_cats ) && ! is_wp_error( $layout_cats ) ) ? array_values( $layout_cats )[0] : null;
                                    $brand_color = $primary_cat ? get_term_meta( $primary_cat->term_id, 'd5tm_color', true ) : '#6366f1';
                                    echo 'style="--d5tm-accent: ' . esc_attr( $brand_color ) . ';"';
                                    ?>>

                                    <!-- Selection Checkbox (Batch Editor) -->
                                    <div class="d5tm-card-select">
                                        <input type="checkbox" class="d5tm-card-checkbox" value="<?php echo esc_attr( $layout->ID ); ?>">
                                    </div>

                                    <?php
                                        $d5tm_thumb_mode = get_option( 'd5tm_thumbnail_mode', 'skeleton' );
                                        $d5tm_shimmer    = get_option( 'd5tm_skeleton_shimmer', 'on' );
                                        $has_thumb       = has_post_thumbnail( $layout->ID );
                                        $thumb_class     = ! $has_thumb && $d5tm_thumb_mode === 'skeleton' ? ' d5tm-thumb--skeleton' : '';
                                        
                                        // Explicit boolean check for shimmer
                                        if ( ! $has_thumb && $d5tm_thumb_mode === 'skeleton' && 'on' === $d5tm_shimmer ) {
                                            $thumb_class .= ' d5tm-shimmer-active';
                                        }
                                    ?>
                                    <div class="d5tm-thumb<?php echo esc_attr( $thumb_class ); ?>">

                                        <?php if ( $has_thumb ) : ?>
                                            <img class="d5tm-thumb-img"
                                                src="<?php echo esc_url( get_the_post_thumbnail_url( $layout->ID, 'large' ) ); ?>"
                                                alt="<?php echo esc_attr( $layout->post_title ); ?>"
                                                loading="lazy">

                                        <?php elseif ( $d5tm_thumb_mode === 'skeleton' ) : ?>
                                            <!-- Generic skeleton shown by default -->
                                            <div class="d5tm-thumb-skeleton d5tm-skel-static">
                                                <?php echo D5TM_Skeleton_Generator::generate( $layout ); ?>
                                            </div>

                                            <!-- Live scrolling preview â€” injected by JS on card hover -->
                                            <div class="d5tm-thumb-live-wrap" aria-hidden="true">
                                                <!-- JS inserts <iframe> here on hover -->
                                            </div>

                                        <?php else : ?>
                                            <div class="d5tm-thumb-empty">
                                                <span class="dashicons dashicons-format-image"></span>
                                                <span>No preview</span>
                                            </div>
                                        <?php endif; ?>
                                        <!-- Hover Overlay: Centered Eye Only -->
                                        <div class="d5tm-thumb-overlay">
                                            <button type="button" class="d5tm-preview-overlay-btn d5tm-action-live-preview" data-id="<?php echo esc_attr( $layout->ID ); ?>" data-tooltip="<?php esc_attr_e( 'Quick Preview', 'divi5-tm' ); ?>">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Card Body -->
                                    <div class="d5tm-card-body">
                                        <div class="d5tm-card-title-row">
                                            <div class="d5tm-card-name" data-tooltip="<?php echo esc_attr( $layout->post_title ); ?>">
                                                <?php echo esc_html( $layout->post_title ); ?>
                                            </div>
                                            <div class="d5tm-card-footer-actions">
                                                <button type="button" class="d5tm-footer-icon d5tm-copy-id-btn" data-id="<?php echo esc_attr( $layout->ID ); ?>" data-tooltip="<?php esc_attr_e( 'Copy Layout ID', 'divi5-tm' ); ?>">
                                                    <i class="bi bi-clipboard"></i>
                                                </button>
                                                <button type="button" class="d5tm-footer-icon d5tm-usage-btn" data-id="<?php echo esc_attr( $layout->ID ); ?>" data-tooltip="<?php esc_attr_e( 'Layout Usage Audit', 'divi5-tm' ); ?>">
                                                    <i class="bi bi-diagram-2"></i>
                                                </button>
                                                <button type="button" class="d5tm-footer-icon d5tm-json-btn" data-id="<?php echo esc_attr( $layout->ID ); ?>" data-tooltip="<?php esc_attr_e( 'View JSON Structure', 'divi5-tm' ); ?>">
                                                    <i class="bi bi-code-slash"></i>
                                                </button>
                                                
                                                <?php if ( $current_status === 'trash' ) : ?>
                                                    <button type="button" class="d5tm-footer-icon d5tm-action-restore" data-tooltip="<?php esc_attr_e( 'Restore', 'divi5-tm' ); ?>">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                    <button type="button" class="d5tm-footer-icon d5tm-action-delete" style="color:#ef4444;" data-tooltip="<?php esc_attr_e( 'Delete Permanently', 'divi5-tm' ); ?>">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                <?php else : ?>
                                                    <?php if ( $show_download ) : ?>
                                                        <button type="button" class="d5tm-footer-icon d5tm-download-btn" data-id="<?php echo esc_attr( $layout->ID ); ?>" data-tooltip="<?php esc_attr_e( 'Download JSON', 'divi5-tm' ); ?>">
                                                            <i class="bi bi-download"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if ( $show_quick_edit ) : ?>
                                                        <button type="button" class="d5tm-footer-icon d5tm-action-quick-edit" data-id="<?php echo esc_attr( $layout->ID ); ?>" data-tooltip="<?php esc_attr_e( 'Quick Edit Labels', 'divi5-tm' ); ?>">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if ( $show_trash ) : ?>
                                                        <button type="button" class="d5tm-footer-icon d5tm-trash-btn" style="color:#ef4444;" data-id="<?php echo esc_attr( $layout->ID ); ?>" data-tooltip="<?php esc_attr_e( 'Move to Trash', 'divi5-tm' ); ?>">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div><!-- /.d5tm-card-title-row -->

                                        <!-- Pills Row -->
                                        <div class="d5tm-card-meta">
                                            <?php if ( ! empty( $cat_names ) ) : ?>
                                                <span class="d5tm-pill">
                                                    <i class="bi bi-folder2"></i> <?php echo esc_html( $cat_names[0]['name'] ); ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if ( ! empty( $tag_names ) ) : ?>
                                                <span class="d5tm-pill">
                                                    <i class="bi bi-tag"></i> <?php echo esc_html( implode( ', ', array_column( array_slice($tag_names, 0, 2), 'name' ) ) ); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach;
                        else : ?>
                        <div class="d5tm-empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 160" width="140" style="display:block; margin: 0 auto 20px;">
                                    <rect width="200" height="160" fill="none"/>
                                    <!-- Folder body -->
                                    <rect x="30" y="55" width="140" height="85" rx="8" fill="#e2e7ed"/>
                                    <!-- Folder tab -->
                                    <path d="M30 55 Q30 42 43 42 L82 42 Q90 42 94 50 L108 55Z" fill="#c8d0da"/>
                                    <!-- Dashed lines (empty content) -->
                                    <rect x="55" y="80" width="90" height="8" rx="4" fill="#c8d0da" opacity="0.7"/>
                                    <rect x="65" y="96" width="70" height="6" rx="3" fill="#c8d0da" opacity="0.5"/>
                                    <rect x="75" y="110" width="50" height="6" rx="3" fill="#c8d0da" opacity="0.35"/>
                                </svg>
                                <h3 style="color: var(--text-2); font-size: 1rem; font-weight: 600; margin-bottom: 6px;">Nothing Here</h3>
                                <p style="color: var(--text-3); font-size: 0.85rem;">No layouts match the current filter. Try a different category or search term.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div><!-- /.d5tm-body -->
    </div><!-- /#browse-tab -->

    <!-- Bulk Action Bar Wrapper (Anchored to Content Area) -->
    <div class="d5tm-bulk-bar-wrap" id="d5tm-bulk-bar-wrap">
        <div class="d5tm-bulk-bar">
            <div class="d5tm-bulk-bar-info">
                <span id="d5tm-bulk-count">0</span>
                <?php esc_html_e( 'layouts selected', 'divi5-tm' ); ?>
            </div>
            
            <div class="d5tm-bulk-bar-actions">
                <div class="d5tm-bulk-dropdown-wrap">
                    <button type="button" class="d5tm-btn d5tm-bulk-dropdown-trigger">
                        <i class="bi bi-folder-plus"></i>
                        <?php esc_html_e( 'Assign Category', 'divi5-tm' ); ?>
                        <i class="bi bi-chevron-up"></i>
                    </button>
                    <div class="d5tm-bulk-dropdown">
                        <?php foreach ( $categories as $cat ) : ?>
                            <button type="button" class="d5tm-bulk-assign-cat" data-cat-id="<?php echo esc_attr( $cat->term_id ); ?>">
                                <?php echo esc_html( $cat->name ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="button" class="d5tm-btn d5tm-btn-danger" id="d5tm-bulk-trash">
                    <i class="bi bi-trash3"></i>
                    <?php esc_html_e( 'Trash Selected', 'divi5-tm' ); ?>
                </button>

                <button type="button" class="d5tm-btn" id="d5tm-bulk-cancel">
                    <?php esc_html_e( 'Cancel', 'divi5-tm' ); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- ===== USAGE AUDIT TAB ===== -->
    <div id="usage-audit-all-tab" class="d5tm-tab">
        <div class="d5tm-usage-report-container">
            <div class="d5tm-report-header">
                <h2><i class="bi bi-diagram-2"></i> Site-Wide Layout Usage Report</h2>
                <p>This report scans all published pages and posts to map where your layouts are currently active.</p>
            </div>
            
            <div class="d5tm-report-table-wrap">
                <table class="d5tm-report-table" id="d5tm-global-usage-table">
                    <thead>
                        <tr>
                            <th>Layout Name</th>
                            <th>Category</th>
                            <th>Usage Count</th>
                            <th>Deployed On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Populated by AJAX -->
                        <tr><td colspan="5" style="text-align:center; padding: 40px;"><i class="bi bi-arrow-repeat d5tm-spin"></i> Analyzing content...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== IMPORT TAB ===== -->
    <div id="import-tab" class="d5tm-tab">
        <div id="d5tm-import-tab">
            <div class="d5tm-dropzone" id="d5tm-dropzone">
                <div class="d5tm-drop-icon">
                    <i class="bi bi-cloud-arrow-up"></i>
                </div>
                <h3>Drag & Drop your Divi 5 .json file here</h3>
                <p>Supports exported Divi Library JSON files.</p>
                <input type="file" id="d5tm-file-input" accept=".json,.jpg,.jpeg,.png,.webp" multiple style="display:none;">
                <button class="d5tm-upload-btn" id="d5tm-browse-btn">
                    <i class="bi bi-plus"></i>
                    Select File
                </button>
                <div id="d5tm-upload-status"></div>
            </div>
        </div>
    </div>

    <!-- ===== SETTINGS TAB ===== -->
    <div id="settings-tab" class="d5tm-tab">
        <?php include DIVI5_TM_PLUGIN_DIR . 'admin/views/settings-page.php'; ?>
    </div>

    <!-- ===== CHANGELOG TAB ===== -->
    <div id="changelog-tab" class="d5tm-tab">
        <?php include DIVI5_TM_PLUGIN_DIR . 'admin/views/changelog.php'; ?>
    </div>

    <!-- Quick Edit Modal -->
    <div id="d5tm-modal-overlay" class="d5tm-modal-overlay">
        <div class="d5tm-modal" id="d5tm-quick-edit-modal">
            <div class="d5tm-modal-header">
                <h2>Quick Edit Layout</h2>
                <button type="button" class="d5tm-modal-close" id="d5tm-modal-close-btn">&times;</button>
            </div>
            <div class="d5tm-modal-body">
                <form id="d5tm-quick-edit-form">
                    <input type="hidden" id="d5tm-qe-layout-id" name="layout_id" value="">
                    
                    <!-- Left Column: Settings -->
                    <div class="d5tm-qe-settings">
                        <div class="d5tm-form-group">
                            <label for="d5tm-qe-title">Title</label>
                            <input type="text" id="d5tm-qe-title" name="post_title" class="d5tm-input" required>
                        </div>

                        <div class="d5tm-form-group">
                            <label for="d5tm-qe-status">Status</label>
                            <select id="d5tm-qe-status" name="post_status" class="d5tm-input">
                                <option value="publish">Published</option>
                                <option value="draft">Draft</option>
                                <option value="private">Private</option>
                            </select>
                        </div>
                        
                        <div class="d5tm-form-group">
                            <label>Categories</label>
                            <div class="d5tm-qe-checkbox-scroll">
                                <?php
                                $all_cats = get_terms( array( 'taxonomy' => 'layout_category', 'hide_empty' => false ) );
                                if ( ! is_wp_error( $all_cats ) && ! empty( $all_cats ) ) {
                                    foreach ( $all_cats as $cat ) {
                                        echo '<label class="d5tm-checkbox-lbl">';
                                        echo '<input type="checkbox" name="layout_categories[]" value="' . esc_attr( $cat->term_id ) . '"> ';
                                        echo esc_html( $cat->name );
                                        echo '</label>';
                                    }
                                }
                                ?>
                            </div>
                        </div>

                        <div class="d5tm-form-group">
                            <label for="d5tm-qe-tags">Tags (comma separated)</label>
                            <input type="text" id="d5tm-qe-tags" name="layout_tags" class="d5tm-input">
                        </div>
                    </div>

                    <!-- Right Column: Thumbnail -->
                    <div class="d5tm-qe-thumbnail-wrapper">
                        <label>Preview Image</label>
                        <div class="d5tm-qe-thumbnail-box" id="d5tm-qe-thumbnail-box">
                            <input type="hidden" id="d5tm-qe-thumbnail-id" name="thumbnail_id" value="">
                            <img id="d5tm-qe-thumbnail-img" src="" style="display:none;" alt="Preview">
                            <div class="d5tm-qe-thumbnail-placeholder" id="d5tm-qe-placeholder">
                                <i class="bi bi-image"></i>
                                                <span>No Preview</span>
                            </div>
                        </div>
                        <div class="d5tm-qe-thumbnail-actions">
                            <button type="button" class="d5tm-btn d5tm-btn-primary" id="d5tm-qe-set-thumb">Set Preview</button>
                            <button type="button" class="d5tm-btn d5tm-btn-danger" id="d5tm-qe-remove-thumb">Remove</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="d5tm-modal-footer">
                <span id="d5tm-qe-status-msg" class="d5tm-status-msg"></span>
                <button type="button" class="d5tm-btn d5tm-btn-secondary" id="d5tm-modal-cancel-btn">Cancel</button>
                <button type="button" class="d5tm-btn d5tm-btn-primary" id="d5tm-qe-save-btn">Update</button>
            </div>
        </div>
    </div>

    <!-- Feature Guide Modal -->
    <div id="d5tm-guide-modal" class="d5tm-modal-overlay">
        <div class="d5tm-modal d5tm-guide-modal">
            <div class="d5tm-modal-header">
                <h2><i class="bi bi-info-circle"></i> Feature Guide & Help</h2>
                <button type="button" class="d5tm-modal-close" id="d5tm-guide-close-btn">&times;</button>
            </div>
            <div class="d5tm-modal-body">
                <div class="d5tm-guide-container">
                    
                    <!-- Feature: Browsing -->
                    <div class="d5tm-guide-item">
                        <div class="d5tm-guide-icon"><i class="bi bi-collection"></i></div>
                        <div class="d5tm-guide-text">
                            <h3>Browse & Search</h3>
                            <p>Organize assets with a professional grid. The search bar now includes <strong>instant highlighting</strong>, and you can use <strong>Quick Sort</strong> (A-Z, Newest, etc.) in the toolbar.</p>
                        </div>
                    </div>

                    <!-- Feature: Interactive Tools -->
                    <div class="d5tm-guide-item">
                        <div class="d5tm-guide-icon"><i class="bi bi-keyboard"></i></div>
                        <div class="d5tm-guide-text">
                            <h3>Keyboard & Shortcuts</h3>
                            <p>Press <code>/</code> to focus search, <code>Esc</code> to reset filters. Every card now has a <strong>Copy ID</strong> button and a <strong>Live Preview</strong> full-screen mode.</p>
                        </div>
                    </div>

                    <!-- Feature: Visual Branding -->
                    <div class="d5tm-guide-item">
                        <div class="d5tm-guide-icon"><i class="bi bi-palette"></i></div>
                        <div class="d5tm-guide-text">
                            <h3>Branding & Shimmer</h3>
                            <p>Skeletons now feature a <strong>shimmer animation</strong>. Use the iOS-style toggle in Settings to switch between <strong>Gray Monochrome</strong> and <strong>Category Colors</strong>.</p>
                        </div>
                    </div>

                    <!-- Feature: Usage Intelligence -->
                    <div class="d5tm-guide-item">
                        <div class="d5tm-guide-icon"><i class="bi bi-diagram-2-fill"></i></div>
                        <div class="d5tm-guide-text">
                            <h3>Usage Intelligence</h3>
                            <p>Use the <strong>Usage Audit</strong> tab for a site-wide report, or click the <strong>Diagram icon</strong> on any card to see exactly where a layout is deployed on your site before editing.</p>
                        </div>
                    </div>

                    <!-- Feature: Management -->
                    <div class="d5tm-guide-item">
                        <div class="d5tm-guide-icon"><i class="bi bi-folder-fill"></i></div>
                        <div class="d5tm-guide-text">
                            <h3>High-Efficiency Cards</h3>
                            <p>Actions like <strong>JSON Inspection, Download, and Trash</strong> are now inline with the title for instant access. Hover the preview thumbnail for a centered live-preview shortcut.</p>
                        </div>
                    </div>

                    <!-- Feature: Grid Controls -->
                    <div class="d5tm-guide-item">
                        <div class="d5tm-guide-icon"><i class="bi bi-grid-3x3-gap"></i></div>
                        <div class="d5tm-guide-text">
                            <h3>Responsive Grid</h3>
                            <p>Switch between <strong>1 to 5 columns</strong>. Your preference is saved automatically to localStorage, ensuring your layout remains identical across sessions.</p>
                        </div>
                    </div>

                    <!-- Disclaimers -->
                    <div class="d5tm-guide-disclaimer">
                        <h4><i class="bi bi-exclamation-triangle"></i> Important Disclaimers</h4>
                        <ul>
                            <li><strong>Divi 5 Only:</strong> This plugin is built specifically for the Divi 5 modular architecture. Divi 4 layouts may not render correctly.</li>
                            <li><strong>Data Safety:</strong> Always export and backup your layouts before performing major migrations or batch deletions.</li>
                            <li><strong>Performance:</strong> Hover previews are lazy-loaded to keep your WordPress admin fast and light on memory.</li>
                        </ul>
                    </div>

                </div>
            </div>
            <div class="d5tm-modal-footer">
                <div class="d5tm-guide-footer-branding">
                    Built by <a href="https://elathi.xyz" target="_blank">Elathi Digital</a> &bull; <a href="https://divi.elathi.xyz" target="_blank">divi.elathi.xyz</a>
                </div>
                <button type="button" class="d5tm-btn d5tm-btn-primary" id="d5tm-guide-ok-btn">Got it, thanks!</button>
            </div>
        </div>
    </div>

    <!-- Usage Audit Modal -->
    <div id="d5tm-usage-modal" class="d5tm-modal-overlay">
        <div class="d5tm-modal">
            <div class="d5tm-modal-header">
                <h2><i class="bi bi-diagram-3"></i> Layout Usage Audit</h2>
                <button type="button" class="d5tm-modal-close" id="d5tm-usage-close-btn">&times;</button>
            </div>
            <div class="d5tm-modal-body">
                <p class="d5tm-guide-text">Scanning theme and content for active usage of this layout...</p>
                <div id="d5tm-usage-results" class="d5tm-usage-list">
                    <!-- Results populated by AJAX -->
                </div>
            </div>
            <div class="d5tm-modal-footer">
                <button type="button" class="d5tm-btn d5tm-btn-secondary" id="d5tm-usage-ok-btn">Close</button>
            </div>
        </div>
    </div>

    <!-- JSON Structure Modal -->
    <div id="d5tm-json-modal" class="d5tm-modal-overlay">
        <div class="d5tm-modal d5tm-modal-lg">
            <div class="d5tm-modal-header">
                <h2><i class="bi bi-code-slash"></i> JSON Structure Inspector</h2>
                <button type="button" class="d5tm-modal-close" id="d5tm-json-close-btn">&times;</button>
            </div>
            <div class="d5tm-modal-body">
                <pre id="d5tm-json-viewer" class="d5tm-json-pre"></pre>
            </div>
            <div class="d5tm-modal-footer">
                <button type="button" class="d5tm-btn d5tm-btn-primary d5tm-json-copy-btn">Copy JSON</button>
                <button type="button" class="d5tm-btn d5tm-btn-secondary" id="d5tm-json-ok-btn">Close</button>
            </div>
        </div>
    </div>

    <!-- Live Preview Modal -->
    <div id="d5tm-live-preview-modal" class="d5tm-lp-modal-overlay">
        <div class="d5tm-lp-modal">
            <div class="d5tm-lp-header">
                <div class="d5tm-lp-header-left">
                    <i class="bi bi-eye"></i>
                    <span id="d5tm-lp-title">Live Preview</span>
                </div>
                <div class="d5tm-lp-header-center">
                    <button type="button" class="d5tm-lp-device-btn active" data-device="desktop" data-tooltip="<?php esc_attr_e( 'Desktop View', 'divi5-tm' ); ?>">
                        <i class="bi bi-display"></i>
                    </button>
                    <button type="button" class="d5tm-lp-device-btn" data-device="tablet" data-tooltip="<?php esc_attr_e( 'Tablet View', 'divi5-tm' ); ?>">
                        <i class="bi bi-tablet"></i>
                    </button>
                    <button type="button" class="d5tm-lp-device-btn" data-device="mobile" data-tooltip="<?php esc_attr_e( 'Mobile View', 'divi5-tm' ); ?>">
                        <i class="bi bi-phone"></i>
                    </button>
                </div>
                <div class="d5tm-lp-header-right">
                    <button type="button" class="d5tm-lp-close-btn" id="d5tm-lp-close"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
            <div class="d5tm-lp-body">
                <div class="d5tm-lp-iframe-wrapper desktop">
                    <iframe id="d5tm-lp-iframe" src="" frameborder="0"></iframe>
                    <div class="d5tm-lp-loader">
                        <i class="bi bi-arrow-repeat d5tm-spin"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div><!-- /.d5tm-app -->
</div><!-- /.d5tm-wrap -->


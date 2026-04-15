<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
// Save settings on POST
if ( isset( $_POST['d5tm_save_settings'] ) && check_admin_referer( 'd5tm_settings_nonce', 'd5tm_settings_nonce_field' ) ) {
    if ( current_user_can( 'manage_options' ) ) {
        $thumbnail_mode = isset( $_POST['d5tm_thumbnail_mode'] ) ? sanitize_text_field( $_POST['d5tm_thumbnail_mode'] ) : 'skeleton';
        
        update_option( 'd5tm_thumbnail_mode', $thumbnail_mode );
        update_option( 'd5tm_skeleton_branding', isset( $_POST['d5tm_skeleton_branding'] ) ? 'on' : 'off' );
        update_option( 'd5tm_tag_branding', isset( $_POST['d5tm_tag_branding'] ) ? 'on' : 'off' );
        
        update_option( 'd5tm_show_action_builder', isset( $_POST['d5tm_show_action_builder'] ) ? 'on' : 'off' );
        update_option( 'd5tm_skeleton_shimmer', isset( $_POST['d5tm_skeleton_shimmer'] ) ? 'on' : 'off' );
        update_option( 'd5tm_show_action_download', isset( $_POST['d5tm_show_action_download'] ) ? 'on' : 'off' );
        update_option( 'd5tm_show_action_quick_edit', isset( $_POST['d5tm_show_action_quick_edit'] ) ? 'on' : 'off' );
        update_option( 'd5tm_show_action_trash', isset( $_POST['d5tm_show_action_trash'] ) ? 'on' : 'off' );
        update_option( 'd5tm_hide_empty_cats', isset( $_POST['d5tm_hide_empty_cats'] ) ? 'on' : 'off' );

        echo '<div class="d5tm-settings-notice success">' . esc_html__( 'Settings saved successfully.', 'divi5-tm' ) . '</div>';
    }
}

$thumbnail_mode      = get_option( 'd5tm_thumbnail_mode', 'skeleton' );
$skeleton_branding   = get_option( 'd5tm_skeleton_branding', 'on' );
$skeleton_shimmer    = get_option( 'd5tm_skeleton_shimmer', 'on' );
$tag_branding        = get_option( 'd5tm_tag_branding', 'on' );
$show_action_builder = get_option( 'd5tm_show_action_builder', 'on' );
$show_action_download   = get_option( 'd5tm_show_action_download', 'on' );
$show_action_quick_edit = get_option( 'd5tm_show_action_quick_edit', 'on' );
$show_action_trash      = get_option( 'd5tm_show_action_trash', 'on' );
$hide_empty_cats        = get_option( 'd5tm_hide_empty_cats', 'off' );
?>

<div class="d5tm-settings-wrap">
    <div class="d5tm-settings-header">
        <h2 data-tooltip="<?php esc_attr_e( 'Global plugin configuration', 'divi5-tm' ); ?>"><i class="bi bi-sliders"></i> Plugin Settings</h2>
        <p>Configure how the Divi 5 Template Manager behaves for all users.</p>
    </div>

    <form method="post" action="">
        <?php wp_nonce_field( 'd5tm_settings_nonce', 'd5tm_settings_nonce_field' ); ?>
        <input type="hidden" name="d5tm_save_settings" value="1">

        <!-- Section 1: Skeleton Color Mode -->
        <div class="d5tm-settings-section">
            <div class="d5tm-settings-section-label" data-tooltip="<?php esc_attr_e( 'Choose the skeleton preview color theme', 'divi5-tm' ); ?>">
                <i class="bi bi-palette"></i>
                Skeleton Color Theme
            </div>
            <p class="d5tm-settings-desc">Choose how skeleton preview cards are colored when no custom thumbnail is set.</p>

            <div class="d5tm-color-mode-toggle">
                <span class="d5tm-toggle-label <?php echo $skeleton_branding !== 'on' ? 'active' : ''; ?>">
                    <i class="bi bi-circle-half"></i> Gray Monochrome
                </span>
                <label class="d5tm-toggle-switch">
                    <input type="checkbox" name="d5tm_skeleton_branding" <?php checked( $skeleton_branding, 'on' ); ?>>
                    <span class="d5tm-toggle-slider"></span>
                </label>
                <span class="d5tm-toggle-label <?php echo $skeleton_branding === 'on' ? 'active' : ''; ?>">
                    <i class="bi bi-palette-fill"></i> Category Colors
                </span>
            </div>
        </div>

        <!-- Section 2: Card Thumbnail Display & Shimmer -->
        <div class="d5tm-settings-section">
            <div class="d5tm-settings-section-label" data-tooltip="<?php esc_attr_e( 'Display fallback when no image exists', 'divi5-tm' ); ?>">
                <i class="bi bi-card-image"></i>
                Card Thumbnail Display
            </div>

            <div class="d5tm-settings-radio-group">
                <label class="d5tm-radio-option <?php echo esc_attr( $thumbnail_mode === 'skeleton' ? 'active' : '' ); ?>">
                    <input type="radio" name="d5tm_thumbnail_mode" value="skeleton" <?php checked( $thumbnail_mode, 'skeleton' ); ?>>
                    <div class="d5tm-radio-content">
                        <div class="d5tm-radio-title">
                            <i class="bi bi-layout-text-window"></i>
                            Skeleton Preview <span class="d5tm-badge">Recommended</span>
                        </div>
                        <div class="d5tm-radio-desc">
                            Shows a unique SVG wireframe themed by your category branding (if enabled).
                        </div>
                        <div style="margin-top: 10px; margin-left: -5px;">
                            <label class="d5tm-checkbox-label">
                                <input type="checkbox" name="d5tm_skeleton_shimmer" <?php checked( $skeleton_shimmer, 'on' ); ?>>
                                <span>Enable Shimmering Animation on Skeletons</span>
                            </label>
                        </div>
                    </div>
                </label>

                <label class="d5tm-radio-option <?php echo esc_attr( $thumbnail_mode === 'none' ? 'active' : '' ); ?>">
                    <input type="radio" name="d5tm_thumbnail_mode" value="none" <?php checked( $thumbnail_mode, 'none' ); ?>>
                    <div class="d5tm-radio-content">
                        <div class="d5tm-radio-title">
                            <i class="bi bi-crop"></i>
                            No Preview (Blank Placeholder)
                        </div>
                        <div class="d5tm-radio-desc">
                            Shows a simple placeholder icon. Uses minimal resources.
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Section 3: Layout Card Actions -->
        <div class="d5tm-settings-section">
            <div class="d5tm-settings-section-label" data-tooltip="<?php esc_attr_e( 'Control button visibility on cards', 'divi5-tm' ); ?>">
                <i class="bi bi-toggles"></i>
                Card Action Buttons
            </div>
            <p class="d5tm-settings-desc">Choose which management actions should be available for layout cards.</p>
            
            <div class="d5tm-settings-toggle-grid">
                <label class="d5tm-checkbox-label">
                    <input type="checkbox" name="d5tm_show_action_builder" <?php checked( $show_action_builder, 'on' ); ?>>
                    <span>Open in Builder (Pencil icon)</span>
                </label>
                <label class="d5tm-checkbox-label">
                    <input type="checkbox" name="d5tm_show_action_download" <?php checked( $show_action_download, 'on' ); ?>>
                    <span>Download JSON (Cloud icon)</span>
                </label>
                <label class="d5tm-checkbox-label">
                    <input type="checkbox" name="d5tm_show_action_quick_edit" <?php checked( $show_action_quick_edit, 'on' ); ?>>
                    <span>Quick Edit (Settings icon)</span>
                </label>
                <label class="d5tm-checkbox-label">
                    <input type="checkbox" name="d5tm_show_action_trash" <?php checked( $show_action_trash, 'on' ); ?>>
                    <span>Move to Trash (Red trash icon)</span>
                </label>
            </div>
        </div>

        <!-- Section 4: Category Management -->
        <div class="d5tm-settings-section">
            <div class="d5tm-settings-section-label" data-tooltip="<?php esc_attr_e( 'Tools for managing sidebar categories', 'divi5-tm' ); ?>">
                <i class="bi bi-folder-fill"></i>
                Category Management
            </div>
            <p class="d5tm-settings-desc">Manage how empty categories appear in the sidebar library.</p>

            <div class="d5tm-settings-toggle-grid">
                <label class="d5tm-checkbox-label">
                    <input type="checkbox" name="d5tm_hide_empty_cats" <?php checked( $hide_empty_cats, 'on' ); ?>>
                    <span>Hide empty categories &amp; tags from sidebar</span>
                </label>
            </div>

            <div class="d5tm-danger-zone">
                <div class="d5tm-danger-zone-info">
                    <strong><i class="bi bi-exclamation-triangle"></i> Danger Zone</strong>
                    <p>Permanently delete all categories and tags that have no layouts assigned to them. <strong>This cannot be undone.</strong></p>
                </div>
                <button type="button" id="d5tm-delete-empty-cats" class="d5tm-danger-btn">
                    <i class="bi bi-trash3"></i>
                    Delete All Empty Categories &amp; Tags
                </button>
                <div id="d5tm-delete-cats-result" style="display:none; margin-top: 10px;"></div>
            </div>
        </div>

        <div class="d5tm-settings-footer">
            <button type="submit" class="d5tm-settings-save-btn">
                <i class="bi bi-check-circle"></i>
                Save Settings
            </button>
        </div>
    </form>
</div>


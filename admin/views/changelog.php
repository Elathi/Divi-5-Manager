<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php
/**
 * Divi 5 Template Manager â€” Changelog
 * Full version history for Divi 5 Template Manager by S. Anand Kumar / Elathi Digital
 */
$d5tm_changelog = array(

    array(
        'version'     => '3.1.4',
        'date'        => '2026-04-14',
        'label'       => 'UI Hotfix',
        'label_class' => 'fix',
        'title'       => 'Cleanup of Redundant Elements',
        'changes'     => array(
            'fix' => array(
                'Duplicate Bar: Removed the legacy bulk action code that was causing a second, misaligned bar to appear at the top of the browser view.',
            ),
        ),
    ),

    array(
        'version'     => '3.1.3',
        'date'        => '2026-04-14',
        'label'       => 'UI Hotfix',
        'label_class' => 'fix',
        'title'       => 'Bulk Bar Polish & Shimmer Fix',
        'changes'     => array(
            'improved' => array(
                'Centering & Alignment: The bulk action drawer is now anchored middle-center with a solid, high-contrast background.',
                'Button Padding: Restored comfortable click-targets for Assign, Trash, and Cancel buttons.',
            ),
            'fix' => array(
                'Shimmer Visibility: Fixed a logic race condition where the shimmering animation would persist even when disabled in settings.',
            ),
        ),
    ),

    array(
        'version'     => '3.1.2',
        'date'        => '2026-04-14',
        'label'       => 'Settings & Logic Patch',
        'label_class' => 'fix',
        'title'       => 'Dynamic Taxonomies & UI Shimmer Option',
        'changes'     => array(
            'new' => array(
                'Added a new setting to toggle the Shimmering Animation on Skeleton Preview cards, returning control to the user over dashboard CSS animations.',
            ),
            'fix' => array(
                'Fixed a bug where newly imported categories (like Testimonials) and tags were completely hidden from the sidebar due to relying on WordPress\'s cached core taxonomy counts, which often ignore backend layout imports.',
            ),
        ),
    ),

    array(
        'version'     => '3.1.1',
        'date'        => '2026-04-14',
        'label'       => 'UI Polish & Hotfix',
        'label_class' => 'fix',
        'title'       => 'Selection & Modal Fixes',
        'changes'     => array(
            'improved' => array(
                'Redesigned the card Bulk Select Checkbox with a modern SVG-powered aesthetic, overriding old Windows-native inputs.',
                'Added glassmorphic dark-ghost styling to the Bulk Action Bar buttons to perfectly match the sleek bottom drawer.',
            ),
            'fix' => array(
                'Fixed an issue where the Quick Preview live-iframe click interaction failed to trigger.',
                'Fixed CSS text-overflow where layout names were being pushed out of the bounds of the card.',
                'Resolved a critical PHP syntax bug in main-dashboard.php that occurred during the layout reconstruction.',
            ),
        ),
    ),

    array(
        'version'     => '3.1.0',
        'date'        => '2026-04-14',
        'label'       => 'High-Fidelity Redesign',
        'label_class' => 'feature',
        'title'       => 'Visual Overhaul & Site-Wide Auditing',
        'changes'     => array(
            'new' => array(
                'Comprehensive Usage Audit Tab: A new top-level view to track every layout across the entire site in a clean table.',
                'Centered Hover Preview: Refined the thumbnail hover state to feature a single, large centered eye icon.',
                'High-Priority Icon Row: Moved JSON, Usage, Download, Edit, and Trash actions to the card footer for better accessibility.',
                'Monochrome Pill Metadata: Simplified tags and categories into clean, professional monochrome labels.',
            ),
            'improved' => array(
                'Card redesign to match high-fidelity marketplace standards.',
                'Bulk Action Bar styling updated with 1:1 match to professional design specs.',
            ),
        ),
    ),

    array(
        'version'     => '3.0.0',
        'date'        => '2026-04-14',
        'label'       => 'Major Release',
        'label_class' => 'feature',
        'title'       => 'Productivity & Insights',
        'changes'     => array(
            'new' => array(
                'Multi-Layout Batch Editor: Select multiple layouts to perform bulk actions like trashing or assigning categories.',
                'Layout Usage Audit: Instantly scan your site to see which pages or posts are using a specific layout.',
                'JSON Structure Inspector: View the raw Divi 5 module tree and block data directly from the dashboard.',
                'Bulk Action Bar: A new floating interface for fast administrative workflows.',
            ),
            'improved' => array(
                'Dashboard card layout optimized for high-density asset management.',
                'Performance improvements in layout metadata retrieval.',
            ),
        ),
    ),

    array(
        'version'     => '2.8.0',
        'date'        => '2026-04-14',
        'label'       => 'Fix & Polish',
        'label_class' => 'fix',
        'title'       => 'Sort Fix, Tag Counts & Settings Polish',
        'changes'     => array(
            'new' => array(
                'Settings: Replaced two separate checkboxes with a single elegant iOS-style toggle switch for Skeleton Color Theme (Gray Monochrome ↔ Category Colors).',
                'Hide WP Footer: The WordPress admin footer is now hidden on the plugin page for a cleaner, more focused interface.',
            ),
            'fixed' => array(
                'Sort Order was broken due to an ID mismatch between the HTML element and the JS handler. Now all four sort modes (A-Z, Z-A, Newest, Oldest) work correctly.',
                'Tag Count in the sidebar always showed 0 — fixed by computing accurate counts directly from et_pb_layout posts instead of the default WordPress term count.',
            ),
            'improved' => array(
                'Settings page now has proper top and bottom padding, reducing the cramped appearance.',
                'Tags in the sidebar now also respect the "Hide empty categories & tags" setting.',
            ),
        ),
    ),

    array(
        'version'     => '2.7.0',
        'date'        => '2026-04-14',
        'label'       => 'Major Update',
        'label_class' => 'feature',
        'title'       => 'UX Polish & Category Management',
        'changes'     => array(
            'new' => array(
                'Skeleton Shimmer: Added a subtle animated light-sweep effect to all skeleton preview cards for a modern, alive feel.',
                'Search Highlighting: Matching text in layout title cards is now highlighted in yellow when using the search box.',
                'Keyboard Shortcuts: Press "/" to instantly focus the search input. Press "Esc" to clear all active filters and search term.',
                'Copy Layout ID: A small clipboard icon now appears on each card on hover, allowing one-click copy of the Layout ID.',
                'Illustrated Empty State: Replaced the plain-text "No layouts found" message with a themed SVG folder illustration.',
                'Category Management Settings: Added a toggle to "Hide Empty Categories & Tags" from the sidebar.',
                'Delete Empty Categories: Added a "Danger Zone" button in settings to permanently remove all unused categories and tags via AJAX.',
            ),
            'improved' => array(
                'Card transitions are now smoother when toggling filters or search.',
            ),
        ),
    ),

    array(
        'version'     => '2.6.0',
        'date'        => '2026-04-14',
        'label'       => 'Refinement',
        'label_class' => 'feature',
        'title'       => 'High-Contrast Monochrome Defaults',
        'changes'     => array(
            'new' => array(
                'High-Contrast Gray Skeletons: Monochrome gray is now the default skeleton theme, featuring a new high-contrast shading engine for better visibility of wireframe structures.',
                'Sidebar Simplification: Removed color pickers for Tags entirely and conditionally hide Category pickers when skeleton branding is disabled.',
                'Refined Shading Engine: Upgraded the SVG generator with smarter light/dark interpolation logic for all color modes.',
            ),
            'improved' => array(
                'Cleaned up sidebar interface to prevent accidental color changes when using the default theme.',
                'Increased contrast for all skeleton preview elements (sections, rows, modules).',
            ),
            'fixed' => array(
                'Fixed "dull gray" issues by implementing a more vibrant, high-contrast base palette for the default view.',
            ),
        ),
    ),

    array(
        'version'     => '2.5.0',
        'date'        => '2026-04-14',
        'label'       => 'Major Update',
        'label_class' => 'feature',
        'title'       => 'Customizable UI & Refined Branding',
        'changes'     => array(
            'new' => array(
                'Added Granular Settings: Users can now toggle "Skeleton Branding" and "Tag Branding" independently.',
                'Added Action Button Visibility Controls: Toggle Open in Builder, Download JSON, Quick Edit, and Trash buttons individually via settings.',
                'Refined Card Visuals: Categories and Tags now have a distinct design. Categories feature folder icons and solid styling; Tags feature tag icons and outlined styling.',
                'Professionalized Settings Layout: Implemented a clean toggle grid for better management of plugin preferences.',
            ),
            'improved' => array(
                'Added neutral gray fallback theme for skeletons when branding is disabled.',
                'Enhanced mobile responsiveness for the settings panel.',
            ),
            'fixed' => array(
                'Corrected variable scoping issues to ensure new settings are applied instantly upon saving.',
            ),
        ),
    ),

    array(
        'version'     => '2.4.0',
        'date'        => '2026-04-14',
        'label'       => 'Major Update',
        'label_class' => 'feature',
        'title'       => 'Visual Branding & Global Portal Tooltips',
        'changes'     => array(
            'new' => array(
                'Implemented "Global Floating Tooltips" (Portal Mode) — tooltips now reside in the document body, ensuring they are never clipped by sidebars or headers.',
                'Added Category Color Management: Users can now assign custom colors to categories and tags via interactive sidebar pickers.',
                'Introduced "Dynamic Monochrome Skeletons" — skeleton placeholders now automatically generate shades based on the primary category color for better library organization.',
                'Added Automatic Color Assignment logic that gives every new category a professional brand color from a curated palette.',
            ),
            'improved' => array(
                'Global Z-Index audit: Tooltips and modals updated to use a prioritized stacking context (1,000,000+) to prevent layering issues with sticky headers.',
                'Added real-time UI feedback for category colors: folder icons in the sidebar update instantly when a new color is selected.',
            ),
            'fixed' => array(
                'Resolved an issue where tooltips were occasionally cut off by restricted `overflow` settings in the WordPress admin area.',
            ),
        ),
    ),

    array(
        'version'     => '2.3.0',
        'date'        => '2026-04-14',
        'label'       => 'Enhancement',
        'label_class' => 'feature',
        'title'       => 'Grid Controls & Deep Data Import',
        'changes'     => array(
            'new' => array(
                'Added a dedicated Feature Guide & Help lightbox accessible via the question mark icon in the header.',
                'Added Grid Column Adjuster to the toolbar (Auto, 1, 2, 3, 4, or 5 columns). Setting is remembered across sessions.',
                'Replaced the simple Sort toggle button with a clean Dropdown menu select for better UX.',
                'Added formal Readme.txt and LICENSE for WordPress repository compliance.',
            ),
            'improved' => array(
                'Import Handler now perfectly preserves `post_meta` data (which natively handles the layout `Type`, built-for flags, and template flags in Divi 5).',
                'Taxonomy importer enhanced to support `layout_type`, `scope`, and `module_width` terms directly out of the imported JSON.',
                'Search box styling fixed so that the new Bootstrap search icon sits perfectly aligned without disrupting the input field.',
                'Logo grid icon changed to solid white so it pops properly against the purple accent background.',
            ),
            'fixed' => array(
                '"Type" column showing blank (—) on imported layouts has been fixed. Divi will now properly recognize the type (Layout, Section, Row, etc.).',
            ),
        ),
    ),

    array(
        'version'     => '2.2.0',
        'date'        => '2026-04-14',
        'label'       => 'Feature',
        'label_class' => 'feature',
        'title'       => 'Modern Icon System â€” Phosphor Icons',
        'changes'     => array(
            'new' => array(
                'Replaced all WordPress Dashicons with Phosphor Icons â€” a modern, MIT-licensed open-source icon set with clean strokes and a consistent design language.',
                'Enqueues @phosphor-icons/web@2.1.0 from jsDelivr CDN with no additional plugin files required.',
                'Spinner animation added for the Live Preview loader (replaced the Dashicons spin hack).',
                'Sun/Moon theme toggle icon now updates dynamically using the ph-sun / ph-moon classes.',
            ),
            'improved' => array(
                'Tab nav icons updated: Browse (browser), Import (upload-simple), Settings (sliders), Changelog (clock-counter-clockwise).',
                'Card action icons updated: Eye (eye), Builder (pencil-ruler), Download (download-simple), Quick Edit (pencil-simple), Trash (trash), Restore (arrow-u-up-left).',
                'Sidebar: category uses ph-folder, tags use ph-tag for better semantic clarity.',
                'Live Preview modal: desktop/tablet/mobile device buttons now use ph-monitor / ph-device-tablet / ph-device-mobile.',
                'Changelog labels use ph-plus (New), ph-arrow-up (Improved), ph-check-circle (Fixed).',
                'Added scoped CSS size overrides so all icons render at the correct size per context without affecting the WordPress admin globally.',
            ),
            'fixed' => array(),
        ),
    ),

    array(
        'version'     => '2.1.0',
        'date'        => '2026-04-14',
        'label'       => 'Patch',
        'label_class' => 'patch',
        'title'       => 'Hover Preview Fixes â€” Eye Icon & White Gap',
        'changes'     => array(
            'fixed' => array(
                'Eye icon (ðŸ‘ Live Preview button) was hidden when hover preview was active â€” fixed by setting z-index:2 on the overlay, placing it correctly above the iframe layer (z-index:1).',
                'White gap appearing at the bottom of the hover preview â€” iframe source height was hardcoded to 700px causing a short scaled result. Now calculated dynamically from the actual card height so the scaled iframe always fills 100% of the card.',
                'Preview was only 70% wide â€” removed stale hardcoded scale(0.2) and replaced with a dynamic JS calculation of scale = cardWidth / 1200, ensuring the preview always fills the full card width.',
                'Backdrop-filter blur was fogging the iframe content â€” disabled blur on the overlay (backdrop-filter: none) when live preview is active, keeping the overlay at 25% opacity so the eye button remains visible and usable.',
            ),
            'improved' => array(
                'Removed scroll animation entirely â€” hover preview now shows a clean static snapshot of the layout top, faster and more reliable.',
                'Iframe source height is now computed per-card from outerHeight() so each card renders its preview to exactly the right dimensions.',
                'Cleanup: removed redundant wrap height / min-height JS overrides that were causing layout shifts on mouse-leave.',
            ),
            'new' => array(),
        ),
    ),

    array(
        'version' => '2.0.0',
        'date'    => '2026-04-14',
        'label'   => 'Major Release',
        'label_class' => 'major',
        'title'   => 'Live Hover Preview & Plugin Branding',
        'changes' => array(
            'new'  => array(
                'Added live hover preview â€” hovering a card now lazy-loads a real scaled-down iframe of the layout that auto-scrolls from top to bottom, showing the full layout in context.',
                'Added a clean generic skeleton wireframe placeholder for cards without a custom thumbnail.',
                'Added plugin branding: Plugin URI (divi.elathi.xyz), Author URI (elathi.xyz), Author Name updated to S. Anand Kumar.',
                'Settings page: added "Card Thumbnail Display" toggle â€” choose between Skeleton Preview or Blank Placeholder.',
                'Added Changelog tab to the plugin dashboard (this page).',
            ),
            'improved' => array(
                'Simplified skeleton generator â€” no longer attempts unreliable content parsing; uses a consistent, beautiful generic SVG instead.',
                'Lazy iframe inject: iframes are only created on hover and destroyed on mouse-leave, preserving browser memory.',
                'Skeleton fades out smoothly when live preview activates.',
            ),
            'fixed' => array(),
        ),
    ),

    array(
        'version' => '1.7.0',
        'date'    => '2026-04-14',
        'label'   => 'Feature',
        'label_class' => 'feature',
        'title'   => 'Live Preview Fix & Smart Image Uploader',
        'changes' => array(
            'new' => array(
                'Smart Batch Import: drag-and-drop multiple JSON + image files simultaneously. Files are auto-paired by filename (e.g. Team-1.json + Team-1.jpg) and imported together.',
                'Matched image is automatically uploaded to the WP Media Library and set as the layout featured thumbnail.',
            ),
            'fixed' => array(
                'Live Preview 404 Fix: the preview URL now uses the WordPress native preview system (preview_id, preview_nonce, preview=true) identical to Divi\'s own library â€” no more 404 errors.',
                'Removed custom template_include interceptor that was incorrectly overriding the WordPress query too late.',
                'Fixed: get_permalink() returning false for non-publicly-queryable post types; now constructs the permalink manually from post_name.',
            ),
            'improved' => array(
                'File input now accepts .json, .jpg, .jpeg, .png, .webp simultaneously.',
                'Upload progress shows "Uploading X of Y: filename..." during batch imports.',
            ),
        ),
    ),

    array(
        'version' => '1.6.0',
        'date'    => '2026-04-14',
        'label'   => 'Feature',
        'label_class' => 'feature',
        'title'   => 'Skeleton Previews & Settings Page',
        'changes' => array(
            'new' => array(
                'Skeleton Preview Generator: automatically generates a unique SVG wireframe from each layout\'s Divi 5 block structure.',
                'Settings Page added via new "Settings" header tab â€” control card thumbnail display mode.',
                'Thumbnail Priority system: Custom Image > Skeleton Preview > Blank Placeholder.',
                'First-time attempt at Divi 5 block format parser (wp:divi/ comment blocks).',
            ),
            'improved' => array(
                'Added CSS classes d5tm-thumb--skeleton and d5tm-thumb-skeleton for clean skeleton rendering.',
            ),
            'fixed' => array(),
        ),
    ),

    array(
        'version' => '1.5.0',
        'date'    => '2026-04-14',
        'label'   => 'Feature',
        'label_class' => 'feature',
        'title'   => 'Masonry Grid, Action Icons & Dashboard Refresh',
        'changes' => array(
            'new' => array(
                'Masonry grid layout: cards now use CSS column-width and break-inside: avoid, allowing thumbnails to render at their natural aspect ratios (Pinterest style).',
                'Refresh Dashboard button added to the toolbar â€” forces a full state reload.',
                'Live Preview Modal: full-screen dark-mode iframe with Desktop / Tablet / Mobile responsive toggles.',
                'Trash workflow: Active / Trash tab switcher added to browse bar.',
                'Download JSON: extracts and exports Divi 5 layout blocks as a portable .json file.',
            ),
            'improved' => array(
                'Icon reorganization: Live Preview eye icon moved to card overlay only. Builder, Quick Edit, Download, and Trash actions moved to the card footer bar for cleaner hover states.',
                'All action buttons are icon-only with tooltip title attributes.',
                'Removed Copy Layout option (deprecated).',
            ),
            'fixed' => array(
                'Fixed Quick Edit modal appearing with transparent/broken background.',
                'Fixed WP Media Library opening behind the Quick Edit modal (z-index layering corrected).',
                'Fixed category and tag data not being detected from imported JSON (now reads from terms[] key).',
                'Fixed hover overlay not showing action buttons (blur applied without opacity transition).',
            ),
        ),
    ),

    array(
        'version' => '1.4.0',
        'date'    => '2026-04-13',
        'label'   => 'Feature',
        'label_class' => 'feature',
        'title'   => 'Quick Edit, Trash / Restore & Secure AJAX',
        'changes' => array(
            'new' => array(
                'Quick Edit Modal: edit layout title, status, categories, tags, and set preview thumbnail via WP Media Library.',
                'Move to Trash action with confirmation.',
                'Restore from Trash action.',
                'Permanent Delete action (with confirmation).',
                'Active / Trash tab filtering in the layout grid.',
            ),
            'improved' => array(
                'All AJAX endpoints now use check_ajax_referer() for nonce verification.',
                'All AJAX endpoints check current_user_can("edit_posts") before executing.',
            ),
            'fixed' => array(),
        ),
    ),

    array(
        'version' => '1.3.0',
        'date'    => '2026-04-13',
        'label'   => 'Feature',
        'label_class' => 'feature',
        'title'   => 'Category / Tag Import & Layout Filter',
        'changes' => array(
            'new' => array(
                'Import now reads layout_category and layout_tag taxonomy terms from exported JSON and assigns them automatically.',
                'Sidebar filter panel: browse layouts by Category or Tag.',
                'Search bar added for live text filtering across layout titles, categories, and tags.',
                'Sort Aâ†’Z / Zâ†’A button.',
            ),
            'improved' => array(
                'Import handler refactored to handle nested terms[] JSON key.',
            ),
            'fixed' => array(),
        ),
    ),

    array(
        'version' => '1.0.0',
        'date'    => '2026-04-13',
        'label'   => 'Initial Release',
        'label_class' => 'initial',
        'title'   => 'Initial Release â€” Core Plugin',
        'changes' => array(
            'new' => array(
                'Browse all et_pb_layout post types in a structured card grid.',
                'Import Divi 5 layouts via JSON file drag-and-drop or file picker.',
                'Open layout in Divi Builder directly from a card.',
                'Custom preview thumbnail support via WP Media Library.',
                'Light / Dark mode toggle.',
                'Header count display showing total layouts.',
                'Secure AJAX architecture with nonce verification.',
            ),
            'improved' => array(),
            'fixed'    => array(),
        ),
    ),
);
?>

<div class="d5tm-changelog-wrap">

    <div class="d5tm-changelog-header">
        <div class="d5tm-changelog-title-row">
            <div>
                <h2 data-tooltip="<?php esc_attr_e( 'Chronological history of improvements', 'divi5-tm' ); ?>">
                    <i class="bi bi-clock-history"></i>
                    Changelog
                </h2>
                <p>Full version history of Divi 5 Template Manager</p>
            </div>
            <div class="d5tm-plugin-meta">
                <div class="d5tm-meta-row">
                    <span class="d5tm-meta-label">Current Version</span>
                    <span class="d5tm-version-badge"><?php echo esc_html( DIVI5_TM_VERSION ); ?></span>
                </div>
                <div class="d5tm-meta-row">
                    <span class="d5tm-meta-label">Author</span>
                    <a href="https://elathi.xyz" target="_blank" rel="noopener" class="d5tm-meta-link">S. Anand Kumar</a>
                </div>
                <div class="d5tm-meta-row">
                    <span class="d5tm-meta-label">Plugin Site</span>
                    <a href="https://divi.elathi.xyz" target="_blank" rel="noopener" class="d5tm-meta-link">divi.elathi.xyz</a>
                </div>
            </div>
        </div>
    </div>

    <div class="d5tm-changelog-list">
        <?php foreach ( $d5tm_changelog as $release ) : ?>
        <div class="d5tm-cl-entry<?php echo $release['version'] === DIVI5_TM_VERSION ? ' d5tm-cl-current' : ''; ?>">

            <div class="d5tm-cl-aside">
                <div class="d5tm-cl-version">v<?php echo esc_html( $release['version'] ); ?></div>
                <div class="d5tm-cl-date"><?php echo esc_html( date_format( date_create( $release['date'] ), 'M j, Y' ) ); ?></div>
                <span class="d5tm-cl-label d5tm-cl-label--<?php echo esc_attr( $release['label_class'] ); ?>">
                    <?php echo esc_html( $release['label'] ); ?>
                </span>
                <?php if ( $release['version'] === DIVI5_TM_VERSION ) : ?>
                <span class="d5tm-cl-current-badge">Current</span>
                <?php endif; ?>
            </div>

            <div class="d5tm-cl-body">
                <h3 class="d5tm-cl-title"><?php echo esc_html( $release['title'] ); ?></h3>

                <?php if ( ! empty( $release['changes']['new'] ) ) : ?>
                <div class="d5tm-cl-group">
                    <div class="d5tm-cl-group-label d5tm-cl-new">
                        <i class="bi bi-plus-circle"></i> New
                    </div>
                    <ul>
                        <?php foreach ( $release['changes']['new'] as $item ) : ?>
                        <li><?php echo esc_html( $item ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( $release['changes']['improved'] ) ) : ?>
                <div class="d5tm-cl-group">
                    <div class="d5tm-cl-group-label d5tm-cl-improved">
                        <i class="bi bi-arrow-up-circle"></i> Improved
                    </div>
                    <ul>
                        <?php foreach ( $release['changes']['improved'] as $item ) : ?>
                        <li><?php echo esc_html( $item ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( $release['changes']['fixed'] ) ) : ?>
                <div class="d5tm-cl-group">
                    <div class="d5tm-cl-group-label d5tm-cl-fixed">
                        <i class="bi bi-check-circle"></i> Fixed
                    </div>
                    <ul>
                        <?php foreach ( $release['changes']['fixed'] as $item ) : ?>
                        <li><?php echo esc_html( $item ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

        </div>
        <?php endforeach; ?>
    </div>

    <div class="d5tm-changelog-footer">
        Built by <a href="https://elathi.xyz" target="_blank" rel="noopener">S. Anand Kumar</a> &mdash;
        <a href="https://elathi.xyz" target="_blank" rel="noopener">Elathi Digital</a> &mdash;
        <a href="https://divi.elathi.xyz" target="_blank" rel="noopener">divi.elathi.xyz</a>
    </div>

</div>

<style>
/* Changelog Styles */
.d5tm-changelog-wrap { max-width: 820px; margin: 0 auto; padding: 28px 24px 40px; }
.d5tm-changelog-header { margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid var(--border); }
.d5tm-changelog-title-row { display:flex; justify-content:space-between; align-items:flex-start; gap:20px; flex-wrap:wrap; }
.d5tm-changelog-header h2 { display:flex; align-items:center; gap:8px; font-size:1.25rem; color:var(--text); margin:0 0 5px; }
.d5tm-changelog-header p { color:var(--text-2); font-size:0.85rem; margin:0; }
.d5tm-plugin-meta { display:flex; flex-direction:column; gap:6px; text-align:right; }
.d5tm-meta-row { display:flex; align-items:center; justify-content:flex-end; gap:8px; font-size:0.82rem; }
.d5tm-meta-label { color:var(--text-3); }
.d5tm-meta-link { color:#818cf8; text-decoration:none; }
.d5tm-meta-link:hover { text-decoration:underline; }
.d5tm-version-badge { background:linear-gradient(135deg,#6366f1,#818cf8); color:#fff; font-size:0.75rem; font-weight:700; padding:3px 10px; border-radius:99px; }

/* Entry */
.d5tm-cl-entry { display:flex; gap:24px; padding:22px 0; border-bottom:1px solid var(--border); }
.d5tm-cl-entry:last-child { border-bottom:none; }
.d5tm-cl-current { background:rgba(99,102,241,0.05); margin:0 -12px; padding:22px 12px; border-radius:10px; border-bottom-color:transparent; }

/* Aside */
.d5tm-cl-aside { min-width:110px; max-width:110px; display:flex; flex-direction:column; gap:5px; align-items:flex-start; padding-top:2px; }
.d5tm-cl-version { font-size:1rem; font-weight:700; color:var(--text); font-family:monospace; }
.d5tm-cl-date { font-size:0.72rem; color:var(--text-3); }
.d5tm-cl-label { font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; padding:2px 7px; border-radius:4px; margin-top:4px; }
.d5tm-cl-label--major   { background:rgba(99,102,241,0.15); color:#818cf8; }
.d5tm-cl-label--feature { background:rgba(34,197,94,0.12);  color:#22c55e; }
.d5tm-cl-label--patch   { background:rgba(251,191,36,0.12); color:#f59e0b; }
.d5tm-cl-label--initial { background:rgba(156,163,175,0.15);color:#9ca3af; }
.d5tm-cl-current-badge { font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; padding:2px 7px; border-radius:4px; background:rgba(99,102,241,0.25); color:#818cf8; margin-top:2px; }

/* Body */
.d5tm-cl-body { flex:1; }
.d5tm-cl-title { font-size:0.95rem; font-weight:600; color:var(--text); margin:0 0 12px; }
.d5tm-cl-group { margin-bottom:10px; }
.d5tm-cl-group-label { font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; display:flex; align-items:center; gap:4px; margin-bottom:5px; }
.d5tm-cl-new .dashicons     { color:#22c55e; }
.d5tm-cl-new                { color:#22c55e; }
.d5tm-cl-improved .dashicons{ color:#f59e0b; }
.d5tm-cl-improved           { color:#f59e0b; }
.d5tm-cl-fixed .dashicons   { color:#818cf8; }
.d5tm-cl-fixed              { color:#818cf8; }
.d5tm-cl-group ul { margin:0; padding:0 0 0 16px; list-style:disc; }
.d5tm-cl-group ul li { font-size:0.83rem; color:var(--text-2); line-height:1.55; margin-bottom:3px; }

/* Footer */
.d5tm-changelog-footer { text-align:center; margin-top:24px; font-size:0.8rem; color:var(--text-3); }
.d5tm-changelog-footer a { color:#818cf8; text-decoration:none; }
.d5tm-changelog-footer a:hover { text-decoration:underline; }
</style>


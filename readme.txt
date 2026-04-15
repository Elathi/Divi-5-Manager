=== Divi 5 Template Manager ===
Contributors: elathidigital
Tags: divi, divi 5, layout manager, template manager, elathi digital
Requires at least: 6.0
Tested up to: 6.5
Stable tag: 2.3.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A professional asset library to browse, manage, preview, import, and export Divi 5 layouts and templates.

== Description ==

Divi 5 Template Manager is a high-performance, purpose-built asset library designed specifically for the new modular architecture of Divi 5. 

It upgrades the standard WordPress layout interface into a modern, masonry-styled dashboard. It introduces a Live Hover Preview system that renders dynamic, scaled iframe previews of your layouts without touching your live pages, ensuring you always know exactly what you are importing or editing.

### Key Features
*   **Modern Dashboard UI / Dark Mode**: Browse your layouts in a sleek, visually appealing interface that supports automatic dark mode toggling.
*   **Live Iframe Previews**: Hover over any layout card to see a live top-of-fold rendering, perfectly scaled to fit.
*   **Drag & Drop Importer**: Seamlessly import Divi JSON export files directly into the manager. Full deep-data support for importing categories, tags, types, and Divi metadata.
*   **Fully Responsive Masonry Grid**: Choose between Auto, 1, 2, 3, 4, or 5 grid columns directly from the toolbar.
*   **Quick Actions**: Instantly view, edit in Builder, quick edit details, download the raw JSON, or send layouts to the trash.
*   **Skeleton Previews**: Fall back to customizable smart skeleton SVGs when no snapshot is provided.

== Installation ==

1. Upload the `divi5-template-manager` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Access the dashboard via the **Divi 5 Manager** menu item in your WordPress admin sidebar.

== Frequently Asked Questions ==

= Does this plugin work with Divi 4? =
It is highly optimized and specifically built to read the JSON structures and metadata signatures produced by the Divi 5 framework. While some Divi 4 layouts may import, it is strictly intended for Divi 5.

= Why are my thumbnails showing skeleton placeholders? =
If you didn't supply a custom thumbnail when saving your layout in the Divi Builder, the plugin will seamlessly fall back to a generic interface skeleton or a live hover preview. You can manage this fallback behavior in the plugin settings.

= Can I mass import layouts? =
Yes. You can drag and drop multiple `.json` format Divi layout files into the Import tab, and the plugin will process them iteratively, preserving their internal categories, tags, and types.

== Screenshots ==

1. **Dashboard** - The main masonry grid showing available layouts with grid-column and sorting controls.
2. **Import Tab** - The drag-and-drop uploader for Divi JSON files.
3. **Settings & Changelog** - In-built controls and an active changelog of latest features.

== Changelog ==

= 2.3.0 =
* Enhancement: Added Grid Column Adjuster to the toolbar (Auto, 1, 2, 3, 4, or 5 columns). Setting is remembered across sessions.
* Enhancement: Replaced the simple Sort toggle button with a clean Dropdown menu select for better UX.
* Enhancement: Import Handler perfectly preserves `post_meta` data natively.
* Enhancement: Taxonomy importer enhanced to support `layout_type`, `scope`, and `module_width` terms.
* Fix: "Type" natively recognized correctly for imported layouts.

= 2.2.0 =
* Feature: Replaced all WordPress Dashicons with Bootstrap Icons for a modern, sleek aesthetic. Supported by seamless jsDelivr CDN integration.

= 2.1.0 =
* Feature: Implemented a highly optimized `<iframe>` based Live Hover Preview system that intelligently scales its contents to fit card contours.
* Enhancement: Upgraded UI to professional SaaS-grade styling with advanced transitions.

= 1.0.0 =
* Initial Release.

=== Pinery ===
Theme URI: https://github.com/liuciming/pinery-theme
Author: PineryPicks
Author URI: https://www.pinery.pro/
Description: Pinery is a clean, lightweight masonry-grid theme for blogs, photography, and image-led content. It features a responsive Pinterest-style card layout, a lightbox image preview, Customizer color options, custom logo and header support, featured images, and a right sidebar. Translation-ready.
Version: 1.7.0
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 8.0
Stable tag: 1.7.0
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: pinery
Tags: blog, photography, grid-layout, two-columns, right-sidebar, custom-background, custom-colors, custom-header, custom-logo, custom-menu, editor-style, featured-images, block-styles, theme-options, threaded-comments, translation-ready

== Description ==

Pinery is a refined, image-led theme for bloggers and content creators. The Pinterest-style masonry grid puts images front and center, and a lightbox overlay lets visitors preview full-size images without leaving the page. Three layout modes, a live Customizer, and a clean, fast, translation-ready front end make it a flexible base for any image-heavy site.

The theme is fully functional on its own. For optional affiliate automation — AI-generated posts, Amazon best-seller import, price overlays, and ad placements — it pairs with the separate Pinery Flow plugin.

== Features ==

* Pinterest-style masonry grid — images, not text, take center stage
* Lightbox image preview — click an image for a full-screen, touch-friendly overlay
* Three layout modes — Masonry, Grid, or Mixed (first post featured large)
* Live Customizer — change colors, fonts, layout, and homepage content with live preview
* Infinite scroll on the homepage (10 posts per batch)
* Reads a per-post affiliate URL and can append your Amazon Associates tag
* rel="sponsored nofollow" added automatically to Amazon links
* Responsive — adapts from desktop to mobile with configurable column counts
* Translation ready (.pot file included)
* Editor styles and block support

== Installation ==

1. In your WordPress admin, go to Appearance > Themes > Add New > Upload Theme.
2. Upload the theme zip and click Install, then Activate.
3. Go to Appearance > Customize to set colors, fonts, and layout.
4. (Optional) Install the Pinery Flow plugin to add the affiliate-link editor, AI content pipeline, ad placements and price overlays.

== Frequently Asked Questions ==

= Can I use this as a normal blog or portfolio theme? =
Yes. The grid and lightbox work for any image-led content — photography, recipes, link roundups, or a standard blog. The affiliate features are optional.

= Can I use it for non-Amazon affiliate links? =
Yes. The per-post affiliate URL field accepts any URL, and the lightbox button text is customizable in the Customizer.

= Does the lightbox work on mobile? =
Yes. It is fully responsive and touch-friendly.

= How do I change the number of posts per page? =
Go to Settings > Reading in your WordPress admin.

= Do I need the Pinery Flow plugin? =
No. The theme works on its own. The plugin only adds optional affiliate automation features.

== Changelog ==

= 1.7.0 =
* Theme-review fixes: customizer CSS variables now attach to the main stylesheet via wp_add_inline_style (no hardcoded style tag); the homepage infinite-scroll script moved to an enqueued file (js/load-more.js) — no inline scripts in templates
* Footer: removed hardcoded page links; the Info column now shows only the configured privacy-policy link; credit link rewritten with proper escaping
* New screenshot.png showing the actual theme rendering

= 1.6.3 =
* WooCommerce catalog now defaults to newest-first (like posts); an explicit owner sorting choice in Product Catalog settings is respected

= 1.6.2 =
* Version sync with Pinery Flow 1.6.2 (no theme changes)

= 1.6.1 =
* Homepage masonry is now row-first: newest posts read left-to-right across the top (each card fills the shortest column); cards no longer reflow when infinite scroll loads more
* Customizer live preview stays in sync when switching layouts or column counts

= 1.6.0 =
* Version sync with Pinery Flow 1.6.0 (no theme changes)

= 1.5.0 =
* Accessibility: skip-to-content link (visible on focus), main#main landmark on every template
* Accessibility: visible keyboard focus outlines for links, menus, form fields and buttons
* Accessibility: links inside post content, comments and text widgets are now underlined
* Lightbox no longer swaps DOM ids at runtime; active/buffer images tracked via CSS classes
* admin-ajax URL and nonce are passed to JavaScript via wp_localize_script (no hardcoded path)
* pinery_adjust_brightness() validates hex input and falls back to a safe default
* Companion-notice styles moved from inline <style> to an enqueued stylesheet
* Image size names prefixed (pinery-card, pinery-hero)
* Requires at least: 6.0, Requires PHP: 8.0

= 1.4.3 =
* Fixed mobile shop grid: WooCommerce core column rules (higher CSS specificity) were shrinking product cards to a quarter of the screen; cards now fill their grid column (half-screen on mobile)

= 1.4.2 =
* Category nav now shows a Shop link first when WooCommerce is active (highlighted on the shop page) — on mobile this strip is the only navigation, so the store entry stays reachable
* Fixed product-card buy button overflowing and clipping its label on narrow mobile cards (now full-width within the card, label wraps)

= 1.4.1 =
* Version sync with Pinery Flow 1.4.1 (no theme changes)

= 1.4.0 =
* WooCommerce support: shop, product, and category pages now render inside the theme layout
* Product grid styled as Pinery cards (warm-white, rounded, hover lift) with theme fonts and colors
* Product gallery zoom, lightbox, and slider enabled
* WooCommerce styles load only when WooCommerce is active — zero overhead otherwise

= 1.3.0 =
* Confirmed compatibility with WordPress 7.0; fonts bundled locally for privacy (no external font requests)
* Theme is now a clean presentation layer; affiliate, ad, and price features delegate to the optional Pinery Flow plugin when active
* Amazon product images are hotlinked, never stored in the media library
* Infinite scroll on the homepage (IntersectionObserver, 10 posts per batch)
* Translation-ready with included .pot file

= 1.2.4 =
* Homepage query limited to a sane posts-per-page with pagination
* Version numbers aligned across style.css, readme.txt, and functions.php

= 1.1.0 =
* Added Customizer integration (colors, fonts, layout, homepage)
* Added three layout modes: masonry, grid, mixed
* Added live Customizer preview
* Added rel="sponsored" auto-tagging for Amazon links
* Added custom 404 template, page, comments, search, and sidebar templates
* Editor styles and block support
* Translation-ready with .pot file

= 1.0 =
* Initial release

== Copyright ==

Pinery WordPress Theme, Copyright 2026 PineryPicks.
Pinery is distributed under the terms of the GNU GPL v2 or later.

This theme bundles the following third-party resources:

* Jost font, Copyright the Jost Project Authors, licensed under the SIL Open Font License 1.1 — https://fonts.google.com/specimen/Jost
* Cormorant Garamond font, Copyright the Cormorant Project Authors, licensed under the SIL Open Font License 1.1 — https://fonts.google.com/specimen/Cormorant+Garamond

The screenshot image is the original work of the theme author and is licensed under GPL v2 or later.

All other code (PHP, CSS, JavaScript including the lightbox script), the inline SVG icons, and the
bundled images/assets (assets/pinery-flow-icon.png) are original works by the theme author and are
licensed under GPL v2 or later. No other third-party libraries are bundled.

<?php
if (!defined("ABSPATH")) exit;

// Pinery Theme — Bootstrap

// Load includes
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/ads.php';
require_once __DIR__ . '/inc/customizer.php';
require_once __DIR__ . '/inc/companion-notice.php';

// ── Theme Setup ──
function pinery_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 240,
        'flex-height' => true,
    ]);
    add_image_size('pinery-card', 800, 0, false);
    add_image_size('pinery-hero', 1200, 800, true);

    add_theme_support('automatic-feed-links');
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');
    add_editor_style('editor-style.css');

    // WooCommerce
    add_theme_support('woocommerce', [
        'thumbnail_image_width' => 800,
        'single_image_width'    => 1000,
    ]);
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    add_theme_support('custom-background', ['default-color' => 'faf7f4']);
    add_theme_support('custom-header', [
        'default-text-color' => '2c2420',
        'width'              => 1200,
        'height'             => 400,
        'flex-height'        => true,
    ]);

    // Load text domain for translations
    load_theme_textdomain('pinery', __DIR__ . '/languages');

    register_nav_menus([
        'primary'    => __('Primary Menu', 'pinery'),
        'categories' => __('Category Menu', 'pinery'),
        'footer'     => __('Footer Menu', 'pinery'),
    ]);
}
add_action('after_setup_theme', 'pinery_setup');

// ── Primary nav: prepend a Shop link when WooCommerce is active ──
function pinery_shop_menu_item_html() {
    if (!class_exists('WooCommerce')) return '';
    $shop_url = wc_get_page_permalink('shop');
    if (!$shop_url) return '';
    return '<li class="menu-item menu-item-shop"><a href="' . esc_url($shop_url) . '">' . esc_html__('Shop', 'pinery') . '</a></li>';
}

add_filter('wp_nav_menu_items', function ($items, $args) {
    if (empty($args->theme_location) || $args->theme_location !== 'primary') return $items;
    $li = pinery_shop_menu_item_html();
    // Skip if the menu already links to the shop page
    if (!$li || (class_exists('WooCommerce') && strpos($items, esc_url(wc_get_page_permalink('shop'))) !== false)) return $items;
    return $li . $items;
}, 10, 2);

// ── WooCommerce: products sort like posts — newest first ──
// Woo's stock default is menu_order (manual order + title), which interleaves new
// products alphabetically. Swap that default for date; an explicit owner choice
// (price / popularity / rating…) in Customizer → Product Catalog is respected.
add_filter('woocommerce_default_catalog_orderby', function ($orderby) {
    return $orderby === 'menu_order' ? 'date' : $orderby;
});

// ── Enqueue Assets ──
function pinery_enqueue() {
    $ver = wp_get_theme()->get('Version') ?: '1.3.0';
    wp_enqueue_style('pinery-style', get_stylesheet_uri(), [], $ver);
    wp_add_inline_style('pinery-style', pinery_inline_css());
    if (class_exists('WooCommerce')) {
        wp_enqueue_style('pinery-woocommerce', get_template_directory_uri() . '/assets/woocommerce.css', ['pinery-style'], $ver);
    }
    wp_enqueue_script('pinery-lightbox', get_template_directory_uri() . '/js/lightbox.js', [], $ver, true);
    wp_enqueue_script('pinery-masonry', get_template_directory_uri() . '/js/masonry.js', [], $ver, true);
    wp_enqueue_script('pinery-load-more', get_template_directory_uri() . '/js/load-more.js', ['pinery-lightbox'], $ver, true);
    wp_localize_script('pinery-lightbox', 'pineryData', [
        'ajaxUrl'    => admin_url('admin-ajax.php'),
        'priceNonce' => wp_create_nonce('pinery_creators_price'),
        'priceTtl'   => (int) get_option('pinery_flow_creators_price_ttl', 60),
    ]);
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'pinery_enqueue');

function pinery_customizer_enqueue() {
    wp_enqueue_script(
        'pinery-customizer',
        get_template_directory_uri() . '/js/customizer.js',
        ['jquery', 'customize-preview'],
        wp_get_theme()->get('Version') ?: '1.3.0',
        true
    );
}
add_action('customize_preview_init', 'pinery_customizer_enqueue');

// ── Inline CSS for customizer values (attached to the main stylesheet — no hardcoded <style>) ──
function pinery_inline_css() {
    $desktop = get_theme_mod('pinery_columns_desktop', 3);
    $tablet  = get_theme_mod('pinery_columns_tablet', 2);
    $mobile  = get_theme_mod('pinery_columns_mobile', 2);
    $font_size    = get_theme_mod('pinery_font_size', 16);

    $accent  = get_theme_mod('pinery_accent_color', '#b07d62');
    $bg      = get_theme_mod('pinery_bg_color', '#faf7f4');
    $text    = get_theme_mod('pinery_text_color', '#3d3028');
    $heading = get_theme_mod('pinery_heading_color', '#2c2420');
    $card_bg = get_theme_mod('pinery_card_bg_color', '#fff9f5');

    // Emit a color variable only when the owner changed it in the Customizer —
    // untouched palettes come straight from style.css (no derivation drift).
    // Derivation offsets mirror the stylesheet's default relationships.
    $vars = [
        '--font-size-base' => $font_size . 'px',
        '--cols-desktop'   => $desktop,
        '--cols-tablet'    => $tablet,
        '--cols-mobile'    => $mobile,
    ];
    if (strtolower($accent) !== '#b07d62') {
        $vars['--accent']       = $accent;
        $vars['--accent-light'] = pinery_adjust_brightness($accent, 14);
    }
    if (strtolower($bg) !== '#faf7f4') {
        $vars['--cream'] = $bg;
        $vars['--linen'] = pinery_adjust_brightness($bg, -5);
        $vars['--sand']  = pinery_adjust_brightness($bg, -10);
    }
    if (strtolower($text) !== '#3d3028') {
        $vars['--text']       = $text;
        $vars['--text-light'] = pinery_adjust_brightness($text, 24);
        $vars['--taupe']      = pinery_adjust_brightness($text, 51);
    }
    if (strtolower($heading) !== '#2c2420') {
        $vars['--dark'] = $heading;
    }
    if (strtolower($card_bg) !== '#fff9f5') {
        $vars['--warm-white'] = $card_bg;
    }

    $css = ':root {';
    foreach ($vars as $k => $v) {
        $css .= "{$k}: {$v};";
    }
    $css .= '}';

    // Show/hide excerpt and meta on cards
    if (!get_theme_mod('pinery_show_excerpt', false)) {
        $css .= '.post-card-excerpt{display:none;}';
    }
    if (!get_theme_mod('pinery_show_meta', false)) {
        $css .= '.post-card-meta{display:none;}';
    }

    return $css;
}

// ── Widgets ──
function pinery_widgets_init() {
    register_sidebar([
        'name'          => __('Sidebar', 'pinery'),
        'id'            => 'sidebar-1',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'pinery_widgets_init');

// ── AJAX Load More (infinite scroll for homepage) ──
add_action('wp_ajax_pinery_load_more', 'pinery_ajax_load_more');
add_action('wp_ajax_nopriv_pinery_load_more', 'pinery_ajax_load_more');
function pinery_ajax_load_more() {
    $page   = max(1, absint($_POST['page'] ?? 1));
    $layout = sanitize_text_field($_POST['layout'] ?? 'masonry');

    $args = [
        'posts_per_page' => 10,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $page,
    ];
    $query = new WP_Query($args);

    ob_start();
    $counter = ($page - 1) * 10;
    while ($query->have_posts()) : $query->the_post();
        $counter++;
        pinery_render_post_card($counter, $layout);
        pinery_maybe_between_ad($counter);
    endwhile;
    wp_reset_postdata();

    $html = ob_get_clean();
    wp_send_json_success([
        'html'     => $html,
        'has_more' => $page < $query->max_num_pages,
    ]);
}

// ── Output CSS variables in <head> ──

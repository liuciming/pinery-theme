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
    add_image_size('card', 800, 0, false);
    add_image_size('hero', 1200, 800, true);

    add_theme_support('automatic-feed-links');
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');
    add_editor_style('editor-style.css');
    add_theme_support('post-formats', ['standard', 'gallery', 'image']);
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

// ── Enqueue Assets ──
function pinery_enqueue() {
    $ver = wp_get_theme()->get('Version') ?: '1.3.0';
    wp_enqueue_style('pinery-style', get_stylesheet_uri(), [], $ver);
    wp_add_inline_style('pinery-style', pinery_inline_css());
    wp_enqueue_script('pinery-lightbox', get_template_directory_uri() . '/js/lightbox.js', [], $ver, true);
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

// ── Inline CSS for customizer values ──
function pinery_inline_css() {
    $desktop = get_theme_mod('pinery_columns_desktop', 3);
    $tablet  = get_theme_mod('pinery_columns_tablet', 2);
    $mobile  = get_theme_mod('pinery_columns_mobile', 2);
    $font_size    = get_theme_mod('pinery_font_size', 16);

    $css = ":root {
        --font-size-base: {$font_size}px;
        --cols-desktop: {$desktop};
        --cols-tablet: {$tablet};
        --cols-mobile: {$mobile};
    }";

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
add_action('wp_head', 'pinery_output_css_variables', 5);

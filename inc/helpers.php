<?php
if (!defined("ABSPATH")) exit;

// Pinery Theme — Helper Functions

// Register Rank Math meta fields in REST API
add_action('init', function() {
    $fields = ['rank_math_title', 'rank_math_description', 'rank_math_focus_keyword'];
    foreach ($fields as $field) {
        register_meta('post', $field, [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'auth_callback' => function() { return current_user_can('edit_posts'); }
        ]);
    }
});

// Get post affiliate URL from meta (set by Pinery Flow plugin or editor metabox).
function pinery_get_affiliate_url($post_id = 0) {
    if (!$post_id) $post_id = get_the_ID();
    $meta = get_post_meta($post_id, 'affiliate_url', true);
    if ($meta) {
        $tag = get_theme_mod('pinery_affiliate_default_tag', '');
        if ($tag && strpos($meta, 'tag=') === false && strpos($meta, 'amazon') !== false) {
            $meta = add_query_arg('tag', $tag, $meta);
        }
        return $meta;
    }
    return '';
}

// Get full-size featured image URL (attachment or Amazon hotlink)
function pinery_get_full_image_url($post_id = 0) {
    if (!$post_id) $post_id = get_the_ID();
    // Check Amazon hotlink first (stored when image_source = "amazon")
    $amazon_img = get_post_meta($post_id, '_pinery_amazon_image', true);
    if (!empty($amazon_img)) return $amazon_img;
    // Standard WordPress attachment
    $img = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');
    return $img ? $img[0] : '';
}

// Customizer CSS variables are attached to the main stylesheet via
// wp_add_inline_style() in pinery_inline_css() — see functions.php.
// (Price-refresh config for JS travels via wp_localize_script 'pineryData'.)

// Adjust hex color brightness. Invalid input returns a safe neutral instead of garbage CSS.
function pinery_adjust_brightness($hex, $percent) {
    $hex = ltrim((string) $hex, '#');
    if (preg_match('/^[0-9a-fA-F]{3}$/', $hex)) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
        return '#2c2420'; // theme default dark — safe fallback for bad theme-mod values
    }
    $r = max(0, min(255, hexdec(substr($hex, 0, 2)) + $percent * 2.55));
    $g = max(0, min(255, hexdec(substr($hex, 2, 2)) + $percent * 2.55));
    $b = max(0, min(255, hexdec(substr($hex, 4, 2)) + $percent * 2.55));
    return sprintf('#%02x%02x%02x', round($r), round($g), round($b));
}

// Excerpt length
function pinery_excerpt_length() { return 20; }
add_filter('excerpt_length', 'pinery_excerpt_length');

// Hotlinked Amazon featured image — never stored in media library (API TOS).
// Tell WordPress "yes there's a thumbnail" when we have an Amazon hotlink.
add_filter('has_post_thumbnail', function($has, $post) {
    if ($has) return $has;
    $post_id = is_object($post) ? $post->ID : (int) $post;
    $amazon_img = get_post_meta($post_id, '_pinery_amazon_image', true);
    return !empty($amazon_img);
}, 10, 2);

add_filter('post_thumbnail_html', function($html, $post_id, $post_thumbnail_id, $size) {
    $amazon_img = get_post_meta($post_id, '_pinery_amazon_image', true);
    if (empty($amazon_img)) return $html;
    // Only intercept when there's no real attachment (post_thumbnail_id is falsy)
    if (!empty($post_thumbnail_id) && (int) $post_thumbnail_id > 0) return $html;

    $alt = get_the_title($post_id);
    $classes = 'attachment-' . esc_attr($size) . ' size-' . esc_attr($size) . ' wp-post-image';
    return '<img src="' . esc_url($amazon_img) . '" alt="' . esc_attr($alt) . '" class="' . $classes . '" loading="lazy" />';
}, 10, 4);

/**
 * Render a single post card. Used by index.php and the AJAX load-more handler.
 * @param int    $counter      1-based absolute post index (for featured-card logic and ad spacing)
 * @param string $layout_style 'masonry', 'mixed', etc.
 */
function pinery_render_post_card($counter, $layout_style = 'masonry') {
    $card_class = 'post-card';
    if ($counter === 1 && $layout_style === 'mixed') {
        $card_class .= ' post-card--featured';
    }
    $prod = get_post_meta(get_the_ID(), '_pinery_flow_product', true);
    $amazon_img = get_post_meta(get_the_ID(), '_pinery_amazon_image', true);
    ?>
    <article class="<?php echo esc_attr($card_class); ?>"
             data-affiliate-url="<?php echo esc_url(pinery_get_affiliate_url()); ?>"
             data-full-img="<?php echo esc_url(pinery_get_full_image_url()); ?>"
             data-post-title="<?php echo esc_attr(get_the_title()); ?>"
             data-post-url="<?php echo esc_url(get_permalink()); ?>"
             data-asin="<?php echo esc_attr($prod['asin'] ?? ''); ?>"
             data-price-ts="<?php echo esc_attr($prod['price_updated'] ?? ''); ?>">
        <div class="post-card-image">
          <a href="#" class="js-lightbox-trigger">
            <?php if (!empty($amazon_img)): ?>
              <img src="<?php echo esc_url($amazon_img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="attachment-pinery-card size-pinery-card wp-post-image" loading="lazy" />
            <?php elseif (has_post_thumbnail()): ?>
              <?php the_post_thumbnail('pinery-card'); ?>
            <?php else: ?>
              <div class="post-card-placeholder"></div>
            <?php endif; ?>
          </a>
        </div>
        <div class="post-card-body">
          <p class="post-card-cat">
            <?php
            $cats = get_the_category();
            if ($cats) echo esc_html($cats[0]->name);
            ?>
          </p>
          <h2 class="post-card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          </h2>
          <p class="post-card-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
          <div class="post-card-meta">
            <span><?php echo get_the_date('M j, Y'); ?></span>
            <span><?php echo ceil(str_word_count(get_the_content()) / 200); ?> min read</span>
          </div>
        </div>
    </article>
    <?php
}

/**
 * Price overlay on post card images.
 * Delegate to Pinery Flow plugin when active; return empty string otherwise.
 */
function pinery_price_overlay() {
    if (function_exists('pinery_flow_price_overlay')) {
        return pinery_flow_price_overlay();
    }
    return '';
}

// Add rel="sponsored" to Amazon links in content
add_filter('the_content', function($content) {
    return preg_replace_callback(
        '/<a\s+[^>]*href=["\'](https?:\/\/(www\.)?(amazon|amzn)\.(com|to)[^"\']*)["\'][^>]*>/i',
        function($m) {
            $tag = $m[0];
            if (strpos($tag, 'rel=') !== false) return $tag;
            return str_replace('<a ', '<a rel="sponsored nofollow" ', $tag);
        },
        $content
    );
}, 20);

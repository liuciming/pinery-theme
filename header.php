<?php
if (!defined("ABSPATH")) exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Skip to content', 'pinery'); ?></a>

<header class="site-header">
  <div class="header-inner">
    <a href="<?php echo esc_url(home_url()); ?>" class="site-logo">
      <?php if (has_custom_logo()): ?>
        <?php the_custom_logo(); ?>
      <?php else: ?>
        <?php bloginfo('name'); ?>
      <?php endif; ?>
    </a>
    <?php if (has_nav_menu('primary')): ?>
    <nav class="main-nav">
      <?php
      // Rendered only when a Primary menu is assigned. Without one, the category
      // strip below is the navigation — a fallback here would just duplicate it.
      wp_nav_menu([
        'theme_location' => 'primary',
        'container'      => false,
      ]); ?>
    </nav>
    <?php endif; ?>
    <button id="header-search-btn" class="header-search" aria-label="<?php esc_attr_e('Search', 'pinery'); ?>">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
      </svg>
    </button>
  </div>

  <nav class="category-nav">
    <div class="category-nav-inner">
      <?php
      // Shop first — on mobile this strip is the only nav, so the store entry must be visible
      if (class_exists('WooCommerce') && function_exists('wc_get_page_id')) {
        $pinery_shop_id = (int) wc_get_page_id('shop');
        if ($pinery_shop_id > 0 && get_post_status($pinery_shop_id) === 'publish') {
          $pinery_shop_active = (function_exists('is_shop') && is_shop()) ? ' active' : '';
          echo '<a href="' . esc_url(get_permalink($pinery_shop_id)) . '" class="cat-link' . $pinery_shop_active . '">' . esc_html(get_the_title($pinery_shop_id)) . '</a>';
        }
      }
      $cats = get_categories(['orderby' => 'count', 'order' => 'DESC', 'number' => 8]);
      if ($cats):
        foreach ($cats as $cat):
          echo '<a href="' . esc_url(get_category_link($cat->term_id)) . '" class="cat-link">' . esc_html($cat->name) . '</a>';
        endforeach;
      endif;
      ?>
    </div>
  </nav>
</header>

<!-- Search Overlay -->
<div class="search-overlay" id="search-overlay">
  <div class="search-overlay-inner">
    <?php get_search_form(); ?>
  </div>
</div>

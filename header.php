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

<header class="site-header">
  <div class="header-inner">
    <a href="<?php echo esc_url(home_url()); ?>" class="site-logo">
      <?php if (has_custom_logo()): ?>
        <?php the_custom_logo(); ?>
      <?php else: ?>
        <?php bloginfo('name'); ?>
      <?php endif; ?>
    </a>
    <nav class="main-nav">
      <?php wp_nav_menu([
        'theme_location' => 'primary',
        'container'      => false,
        'fallback_cb'    => function() {
          echo '<ul>';
          wp_list_categories([
            'title_li' => '',
            'depth'    => 1,
            'number'   => 5,
          ]);
          echo '</ul>';
        }
      ]); ?>
    </nav>
    <button id="header-search-btn" class="header-search" aria-label="<?php esc_attr_e('Search', 'pinery'); ?>">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
      </svg>
    </button>
  </div>

  <nav class="category-nav">
    <div class="category-nav-inner">
      <?php
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

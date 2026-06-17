<?php
if (!defined("ABSPATH")) exit;
 get_header(); ?>

<div class="page-header">
  <div class="page-header-inner">
    <p class="section-label">
      <?php if (is_category()) _e('Category', 'pinery'); elseif (is_tag()) _e('Tag', 'pinery'); else _e('Archive', 'pinery'); ?>
    </p>
    <h1 class="page-header-title" style="font-size:clamp(1.8rem,3vw,2.8rem);">
      <?php single_cat_title(); ?>
    </h1>
    <?php if (category_description()): ?>
      <p class="page-header-desc"><?php echo category_description(); ?></p>
    <?php endif; ?>
  </div>
</div>

<section class="featured-posts">
  <div class="posts-grid layout-<?php echo esc_attr(get_theme_mod('pinery_layout_style', 'masonry')); ?>">
    <?php $counter = 0;
    while (have_posts()) : the_post(); $counter++;

      $card_class = 'post-card';
      if ($counter === 1 && get_theme_mod('pinery_layout_style') === 'mixed') {
        $card_class .= ' post-card--featured';
      }
      $prod = get_post_meta(get_the_ID(), '_pinery_flow_product', true);
    ?>
      <article class="<?php echo esc_attr($card_class); ?>"
               data-affiliate-url="<?php echo esc_url(pinery_get_affiliate_url()); ?>"
               data-full-img="<?php echo esc_url(pinery_get_full_image_url()); ?>"
               data-post-title="<?php echo esc_attr(get_the_title()); ?>"
               data-post-url="<?php echo esc_url(get_permalink()); ?>"
               data-price="<?php echo esc_attr($prod['price'] ?? ''); ?>"
               data-rating="<?php echo esc_attr(str_replace(' stars', '', $prod['rating'] ?? '')); ?>">
        <div class="post-card-image">
          <a href="#" class="js-lightbox-trigger">
            <?php $amz = get_post_meta(get_the_ID(), '_pinery_amazon_image', true);
            if (!empty($amz)): ?>
              <img src="<?php echo esc_url($amz); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="attachment-card size-card wp-post-image" loading="lazy" />
            <?php elseif (has_post_thumbnail()): the_post_thumbnail('card');
            else: echo '<div class="post-card-placeholder"></div>'; endif; ?>
          </a>
          <?php echo pinery_price_overlay(); ?>
        </div>
        <div class="post-card-body">
          <p class="post-card-cat">
            <?php $cats = get_the_category(); if ($cats) echo esc_html($cats[0]->name); ?>
          </p>
          <h2 class="post-card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          </h2>
          <p class="post-card-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
          <div class="post-card-meta">
            <span><?php echo get_the_date('M j, Y'); ?></span>
          </div>
        </div>
      </article>
      <?php pinery_maybe_between_ad($counter); ?>
    <?php endwhile; ?>
  </div>

  <div class="pagination">
    <?php echo paginate_links(['prev_text' => '&larr;', 'next_text' => '&rarr;']); ?>
  </div>
</section>

<?php get_footer(); ?>

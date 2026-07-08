<?php
if (!defined("ABSPATH")) exit;
 get_header(); ?>

<div class="page-header">
  <div class="page-header-inner">
    <p class="section-label">
      <?php _e('Search Results', 'pinery'); ?>
    </p>
    <h1 class="page-header-title" style="font-size:clamp(1.6rem,2.5vw,2.2rem);">
      <?php printf(__('Results for: %s', 'pinery'), '<em>' . esc_html(get_search_query()) . '</em>'); ?>
    </h1>
    <p class="page-header-desc">
      <?php printf(_n('%s result found', '%s results found', (int) $wp_query->found_posts, 'pinery'), number_format_i18n($wp_query->found_posts)); ?>
    </p>
  </div>
</div>

<main id="main" class="featured-posts">
  <?php if (have_posts()): ?>
    <div class="posts-grid layout-<?php echo esc_attr(get_theme_mod('pinery_layout_style', 'masonry')); ?>">
      <?php $counter = 0;
      while (have_posts()) : the_post(); $counter++; ?>
        <article <?php post_class('post-card'); ?>>
          <div class="post-card-image">
            <a href="<?php the_permalink(); ?>">
              <?php $amz = get_post_meta(get_the_ID(), '_pinery_amazon_image', true);
              if (!empty($amz)): ?>
                <img src="<?php echo esc_url($amz); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="attachment-pinery-card size-pinery-card wp-post-image" loading="lazy" />
              <?php elseif (has_post_thumbnail()): the_post_thumbnail('pinery-card');
              else: echo '<div class="post-card-placeholder"></div>'; endif; ?>
            </a>
          </div>
          <div class="post-card-body">
            <p class="post-card-cat">
              <?php $cats = get_the_category(); if ($cats) echo esc_html($cats[0]->name); ?>
            </p>
            <h2 class="post-card-title">
              <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h2>
            <p class="post-card-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
          </div>
        </article>
      <?php endwhile; ?>
    </div>

    <div class="pagination">
      <?php echo paginate_links(['prev_text' => '&larr;', 'next_text' => '&rarr;']); ?>
    </div>

  <?php else: ?>
    <div class="nothing-found">
      <p class="nothing-found-title">
        <?php _e('Nothing found.', 'pinery'); ?>
      </p>
      <p class="nothing-found-desc">
        <?php _e('Try a different search term or browse the categories below.', 'pinery'); ?>
      </p>
      <?php get_search_form(); ?>
    </div>
  <?php endif; ?>
</main>

<?php get_footer(); ?>

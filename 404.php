<?php
if (!defined("ABSPATH")) exit;
 get_header(); ?>

<div class="page-header page-header--large">
  <div class="page-header-inner">
    <p class="page-header-icon">404</p>
    <h1 class="page-header-title" style="font-size:clamp(1.6rem,2.5vw,2.2rem);"><?php _e('Page Not Found', 'pinery'); ?></h1>
    <p style="font-size:1rem; color:var(--text-light); margin-bottom:2rem; line-height:1.8;"><?php _e('The page you\'re looking for doesn\'t exist or has been moved. Browse our latest content below, or use the search to find what you need.', 'pinery'); ?></p>

    <div class="search-form-wrap">
      <?php get_search_form(); ?>
    </div>

    <a href="<?php echo esc_url(home_url()); ?>" class="btn-primary"><?php _e('Back to Homepage', 'pinery'); ?></a>
  </div>
</div>

<section class="featured-posts">
  <div class="section-header">
    <p class="section-label"><?php _e('Latest Posts', 'pinery'); ?></p>
    <h2 class="section-title"><?php _e('Our Most Recent Content', 'pinery'); ?></h2>
  </div>
  <div class="posts-grid">
    <?php
    $recent = new WP_Query(['posts_per_page' => 6, 'post_status' => 'publish']);
    while ($recent->have_posts()) : $recent->the_post(); ?>
      <article class="post-card"
               data-affiliate-url="<?php echo esc_url(pinery_get_affiliate_url(get_the_ID())); ?>"
               data-full-img="<?php $fi = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); echo esc_url($fi ? $fi[0] : ''); ?>"
               data-post-title="<?php echo esc_attr(get_the_title()); ?>">
        <div class="post-card-image">
          <a href="#" class="js-lightbox-trigger">
            <?php if (has_post_thumbnail()) the_post_thumbnail('card');
            else echo '<div class="post-card-placeholder"></div>'; ?>
          </a>
        </div>
        <div class="post-card-body">
          <h2 class="post-card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          </h2>
        </div>
      </article>
    <?php endwhile; wp_reset_postdata(); ?>
  </div>
</section>

<?php get_footer(); ?>

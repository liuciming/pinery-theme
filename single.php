<?php
if (!defined("ABSPATH")) exit;
 get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

<article <?php post_class(); ?>>

<div class="post-hero">
  <div class="post-hero-inner">
    <p class="post-category">
      <?php
      $cats = get_the_category();
      if ($cats) echo '<a href="' . esc_url(get_category_link($cats[0]->term_id)) . '">' . esc_html($cats[0]->name) . '</a>';
      ?>
    </p>
    <h1 class="post-title"><?php the_title(); ?></h1>
    <div class="post-meta">
      <span><?php echo get_the_date('F j, Y'); ?></span>
      <span><?php echo ceil(str_word_count(get_the_content()) / 200); ?> min read</span>
    </div>
  </div>
</div>

<?php if (function_exists('rank_math_the_breadcrumbs')): ?>
<nav class="breadcrumbs">
  <?php rank_math_the_breadcrumbs(); ?>
</nav>
<?php endif; ?>

<?php $amazon_img = get_post_meta(get_the_ID(), '_pinery_amazon_image', true);
if ((has_post_thumbnail() || !empty($amazon_img)) && !post_password_required()): ?>
<div class="post-featured-image">
  <?php if (!empty($amazon_img)): ?>
    <img src="<?php echo esc_url($amazon_img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="attachment-full size-full wp-post-image" loading="lazy" />
  <?php else: the_post_thumbnail('full'); endif; ?>
</div>
<?php endif; ?>

<div class="content-sidebar-wrap">
  <main class="post-content-wrap">
    <div class="post-content">
      <?php the_content(); ?>
      <?php wp_link_pages([
        'before' => '<nav class="page-links">' . __('Pages:', 'pinery'),
        'after'  => '</nav>',
      ]); ?>
    </div>

    <div class="post-footer-meta">
      <?php
      $tags = get_the_tags();
      if ($tags): ?>
        <div class="post-tags-list">
          <?php foreach ($tags as $tag): ?>
            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="tag-link">
              <?php echo esc_html($tag->name); ?>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Product Gallery (Amazon hotlinked images) -->
    <?php
    $product = get_post_meta(get_the_ID(), '_pinery_flow_product', true);
    $asin = $product['asin'] ?? '';
    if ($asin && $asin !== 'N/A'):
      if (class_exists('Pinery_Creators_API') && Pinery_Creators_API::is_available()) {
        $gallery = Pinery_Creators_API::get_product_images($asin);  // cached for 1 h, hotlinked
      } else {
        $gallery = [];
      }
      if (empty($gallery)) $gallery = array_filter([$product['image'] ?? '']);
      if (!empty($gallery)): ?>
        <div class="product-gallery">
          <h3><?php _e('Product Images', 'pinery'); ?></h3>
          <div class="product-gallery-grid">
            <?php foreach ($gallery as $img_url): ?>
              <a href="<?php echo esc_url($img_url); ?>" target="_blank" rel="nofollow noopener">
                <img src="<?php echo esc_url($img_url); ?>" alt="" loading="lazy" />
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif;
    endif; ?>

    <!-- Related Posts -->
    <?php
    $cats = get_the_category();
    if ($cats):
      $related = new WP_Query([
        'category__in'   => [$cats[0]->term_id],
        'posts_per_page' => 3,
        'post__not_in'   => [get_the_ID()],
      ]);
      if ($related->have_posts()): ?>
        <div class="related-posts-section">
          <p class="section-label">
            <?php _e('You May Also Like', 'pinery'); ?>
          </p>
          <div class="related-posts-grid">
            <?php while ($related->have_posts()) : $related->the_post(); ?>
              <article class="post-card">
                <div class="post-card-image">
                  <a href="<?php the_permalink(); ?>">
                    <?php $rel_amazon = get_post_meta(get_the_ID(), '_pinery_amazon_image', true);
                    if (!empty($rel_amazon)): ?>
                      <img src="<?php echo esc_url($rel_amazon); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="attachment-card size-card wp-post-image" loading="lazy" />
                    <?php elseif (has_post_thumbnail()): the_post_thumbnail('card');
                    else: echo '<div class="post-card-placeholder"></div>'; endif; ?>
                  </a>
                </div>
                <div class="post-card-body">
                  <h3 class="post-card-title related-post-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                  </h3>
                  <p class="related-post-date"><?php echo get_the_date('M j, Y'); ?></p>
                </div>
              </article>
            <?php endwhile; wp_reset_postdata(); ?>
          </div>
        </div>
      <?php endif; endif; ?>

    <?php comments_template(); ?>
  </main>

  <?php get_sidebar(); ?>
</div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>

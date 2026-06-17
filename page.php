<?php
if (!defined("ABSPATH")) exit;
 get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

<article <?php post_class(); ?>>

<?php if (has_post_thumbnail() && !post_password_required()): ?>
<div class="post-featured-image">
  <?php the_post_thumbnail('full'); ?>
</div>
<?php endif; ?>

<div class="content-sidebar-wrap">
  <main class="post-content-wrap">
    <div class="post-hero">
      <div class="post-hero-inner">
        <h1 class="post-title"><?php the_title(); ?></h1>
      </div>
    </div>

    <div class="post-content">
      <?php the_content(); ?>
      <?php wp_link_pages([
        'before' => '<nav class="page-links">' . __('Pages:', 'pinery'),
        'after'  => '</nav>',
      ]); ?>
    </div>

    <?php comments_template(); ?>
  </main>

  <?php get_sidebar(); ?>
</div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>

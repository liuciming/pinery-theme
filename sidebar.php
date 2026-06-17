<?php
if (!defined("ABSPATH")) exit;
?>
<aside class="sidebar">
  <?php dynamic_sidebar('sidebar-1'); ?>

  <div class="widget">
    <h3 class="widget-title"><?php _e('Browse by Category', 'pinery'); ?></h3>
    <ul class="sidebar-cat-list">
      <?php
      $cats = get_categories(['orderby' => 'count', 'order' => 'DESC', 'number' => 8]);
      foreach ($cats as $cat): ?>
        <li>
          <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>" class="sidebar-cat-link">
            <?php echo esc_html($cat->name); ?>
            <span class="sidebar-cat-count"><?php echo (int) $cat->count; ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</aside>

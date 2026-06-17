<?php
if (!defined("ABSPATH")) exit;
?>

<!-- NEWSLETTER -->
<section class="newsletter">
  <div class="newsletter-inner">
    <p class="newsletter-label"><?php _e('Stay in the loop', 'pinery'); ?></p>
    <h2 class="newsletter-title"><?php _e('Discover our latest picks,', 'pinery'); ?> <em><?php _e('straight to your inbox.', 'pinery'); ?></em></h2>
    <p class="newsletter-desc"><?php _e('Get our weekly picks of the best Amazon fashion finds straight to your inbox.', 'pinery'); ?></p>
    <?php
    if (shortcode_exists('mc4wp_form')) {
      echo do_shortcode('[mc4wp_form id="1"]');
    } else {
      echo '<p style="color:var(--taupe);font-size:0.85rem;">' . esc_html__('Install Mailchimp for WordPress to enable the newsletter signup form.', 'pinery') . '</p>';
    }
    ?>
  </div>
</section>

<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <a href="<?php echo esc_url(home_url()); ?>" class="site-logo"><?php bloginfo('name'); ?></a>
      <p class="footer-desc"><?php echo esc_html(get_bloginfo('description')); ?></p>
    </div>
    <div class="footer-col">
      <h4><?php _e('Categories', 'pinery'); ?></h4>
      <ul>
        <?php
        $footer_cats = get_categories(['orderby' => 'count', 'order' => 'DESC', 'number' => 5]);
        foreach ($footer_cats as $cat):
          echo '<li><a href="' . esc_url(get_category_link($cat->term_id)) . '">' . esc_html($cat->name) . '</a></li>';
        endforeach;
        ?>
      </ul>
    </div>
    <div class="footer-col">
      <h4><?php _e('Browse', 'pinery'); ?></h4>
      <?php wp_nav_menu([
        'theme_location' => 'footer',
        'container'      => false,
        'fallback_cb'    => function() {
          echo '<ul>';
          wp_list_pages(['title_li' => '', 'depth' => 1, 'number' => 5]);
          echo '</ul>';
        }
      ]); ?>
    </div>
    <div class="footer-col">
      <h4><?php _e('Info', 'pinery'); ?></h4>
      <ul>
        <li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php _e('About Us', 'pinery'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/contact')); ?>"><?php _e('Contact', 'pinery'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php _e('Privacy Policy', 'pinery'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/affiliate-disclosure')); ?>"><?php _e('Affiliate Disclosure', 'pinery'); ?></a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; <?php echo esc_html(gmdate('Y')); ?> <?php bloginfo('name'); ?>
      <?php if (get_theme_mod('pinery_affiliate_default_tag')): ?>
        &mdash; <?php _e('As an Amazon Associate I earn from qualifying purchases.', 'pinery'); ?>
      <?php endif; ?>
      &mdash; <?php printf( __( 'Powered by <a href="%s" rel="nofollow">Pinery</a>', 'pinery' ), 'https://pinery.pro' ); ?>
    </p>
  </div>
</footer>

<?php pinery_ad_zone('footer_above'); ?>

<!-- Lightbox Overlay -->
<div class="lightbox-overlay" id="lightbox">
  <button class="lightbox-close" id="lightbox-close" aria-label="<?php esc_attr_e('Close', 'pinery'); ?>">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
    </svg>
  </button>
  <img src="" alt="" id="lightbox-img" class="lightbox-img">
  <img src="" alt="" id="lightbox-img-next" class="lightbox-img lightbox-img--next">
  <div class="lightbox-content">
    <h3 class="lightbox-title" id="lightbox-title"></h3>
    <div class="lightbox-meta" id="lightbox-meta">
      <span class="lightbox-price" id="lightbox-price"></span>
      <span class="lightbox-rating" id="lightbox-rating"></span>
    </div>
    <div class="lightbox-actions">
      <a href="#" class="lightbox-btn lightbox-btn--detail" id="lightbox-detail-btn">
        <?php _e('View Details', 'pinery'); ?>
      </a>
      <?php $pinery_btn_outline = (get_theme_mod('pinery_affiliate_btn_style', 'solid') === 'outline') ? ' lightbox-btn--outline' : ''; ?>
      <a href="#" class="lightbox-btn lightbox-btn--buy<?php echo esc_attr($pinery_btn_outline); ?>" id="lightbox-btn" target="_blank" rel="nofollow noopener">
        <?php echo esc_html(get_theme_mod('pinery_affiliate_btn_text', 'Buy on Amazon')); ?>
      </a>
    </div>
  </div>
</div>

<?php wp_footer(); ?>

</body>
</html>

<?php
if (!defined("ABSPATH")) exit;

// Pinery — WooCommerce wrapper template.
// All Woo pages (shop, product, category) render inside the theme's container.
get_header(); ?>

<div class="pinery-woo-wrap">
  <main id="main" class="pinery-woo-main">
    <?php woocommerce_content(); ?>
  </main>
</div>

<?php get_footer(); ?>

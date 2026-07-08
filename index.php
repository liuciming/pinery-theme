<?php
if (!defined("ABSPATH")) exit;
 get_header(); ?>

<?php if (get_theme_mod('pinery_show_hero', true)): ?>
<!-- HERO -->
<section class="hero">
  <div class="hero-inner">
    <div class="hero-text">
      <p class="hero-label"><?php _e('New This Week', 'pinery'); ?></p>
      <h1 class="hero-title"><?php echo wp_kses_post(get_theme_mod('pinery_hero_title', 'Discover <em>amazing</em> content.')); ?></h1>
      <p class="hero-desc"><?php echo esc_html(get_theme_mod('pinery_hero_desc', "Curated reviews and recommendations. We find the best products so you don't have to.")); ?></p>
      <a href="<?php echo esc_url(get_theme_mod('pinery_hero_btn_url', home_url('/category/new-arrivals'))); ?>" class="btn-primary">
        <?php echo esc_html(get_theme_mod('pinery_hero_btn_text', 'Browse Latest Posts')); ?>
      </a>
    </div>
    <div class="hero-image-grid">
      <?php
      $hero_posts = new WP_Query(['posts_per_page' => 3, 'post_status' => 'publish']);
      $count = 0;
      while ($hero_posts->have_posts() && $count < 3) :
        $hero_posts->the_post();
        $count++;
      ?>
        <?php
        $hero_prod = get_post_meta(get_the_ID(), '_pinery_flow_product', true);
        $hero_amazon_img = get_post_meta(get_the_ID(), '_pinery_amazon_image', true);
        // Resolve image and link BEFORE rendering the div tag
        if (!empty($hero_amazon_img)) {
            $full_img_url = $hero_amazon_img;
        } elseif (has_post_thumbnail()) {
            $full_img_url = pinery_get_full_image_url();
        } else {
            $full_img_url = '';
        }
        $aff_url = pinery_get_affiliate_url();
        ?>
        <div class="hero-img"
             data-full-img="<?php echo esc_url($full_img_url); ?>"
             data-affiliate-url="<?php echo esc_url($aff_url); ?>"
             data-post-title="<?php echo esc_attr(get_the_title()); ?>"
             data-post-url="<?php echo esc_url(get_permalink()); ?>"
             data-asin="<?php echo esc_attr($hero_prod['asin'] ?? ''); ?>"
             data-price-ts="<?php echo esc_attr($hero_prod['price_updated'] ?? ''); ?>">
          <?php if (!empty($hero_amazon_img)): ?>
            <a href="#" class="js-lightbox-trigger">
              <img src="<?php echo esc_url($hero_amazon_img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="attachment-card size-card wp-post-image" loading="lazy" />
            </a>
          <?php elseif (has_post_thumbnail()): ?>
            <a href="#" class="js-lightbox-trigger">
              <?php the_post_thumbnail('pinery-card'); ?>
            </a>
          <?php else: ?>
            <div class="post-card-placeholder"></div>
          <?php endif; ?>
        </div>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php pinery_ad_zone('hero_below'); ?>

<!-- FEATURED POSTS -->
<main id="main" class="featured-posts">
  <div class="section-header">
    <p class="section-label"><?php _e("Editor's Picks", 'pinery'); ?></p>
    <h2 class="section-title"><?php _e("What We're Loving Right Now", 'pinery'); ?></h2>
  </div>

    <?php
    $paged = max(1, get_query_var('paged'));
    $layout_style = get_theme_mod('pinery_layout_style', 'masonry');
    $args = [
      'posts_per_page' => 10,
      'post_status'    => 'publish',
      'orderby'        => 'date',
      'order'          => 'DESC',
      'paged'          => $paged,
    ];
    $query = new WP_Query($args);
    $max_pages = $query->max_num_pages;
    ?>
  <div class="posts-grid layout-<?php echo esc_attr($layout_style); ?>"
       id="posts-grid"
       data-page="1"
       data-max-pages="<?php echo esc_attr($max_pages); ?>">
    <?php
    $counter = 0;
    while ($query->have_posts()) : $query->the_post(); $counter++;
      pinery_render_post_card($counter, $layout_style);
      pinery_maybe_between_ad($counter);
    endwhile; wp_reset_postdata();
    ?>
  </div>

  <?php if ($max_pages > 1): ?>
  <div id="load-more-sentinel" aria-hidden="true" style="height:1px;"></div>
  <div id="load-more-status" style="text-align:center;padding:20px;display:none;color:var(--taupe);font-size:14px;">
    <?php _e('Loading more posts…', 'pinery'); ?>
  </div>
  <?php endif; ?>

</main>

<?php if ($max_pages > 1): ?>
<script>
(function() {
    var grid = document.getElementById('posts-grid');
    var sentinel = document.getElementById('load-more-sentinel');
    var status = document.getElementById('load-more-status');
    if (!grid || !sentinel) return;

    var page = parseInt(grid.getAttribute('data-page')) || 1;
    var maxPages = parseInt(grid.getAttribute('data-max-pages')) || 1;
    var layout = grid.className.match(/layout-(\w+)/);
    var layoutStyle = layout ? layout[1] : 'masonry';
    var loading = false;

    function loadMore() {
        if (loading || page >= maxPages) return;
        loading = true;
        status.style.display = 'block';

        var formData = new FormData();
        formData.append('action', 'pinery_load_more');
        formData.append('page', page + 1);
        formData.append('layout', layoutStyle);

        fetch('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', {
            method: 'POST',
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success && res.data.html) {
                grid.insertAdjacentHTML('beforeend', res.data.html);
                page++;
                grid.setAttribute('data-page', page);
                if (page >= maxPages) {
                    sentinel.remove();
                    status.remove();
                }
            }
            loading = false;
            status.style.display = 'none';
        })
        .catch(function() {
            loading = false;
            status.style.display = 'none';
        });
    }

    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting) loadMore();
        }, { rootMargin: '400px' });
        observer.observe(sentinel);
    } else {
        var scrollHandler = function() {
            var rect = sentinel.getBoundingClientRect();
            if (rect.top < window.innerHeight + 400) {
                loadMore();
                if (page >= maxPages) {
                    window.removeEventListener('scroll', scrollHandler);
                }
            }
        };
        window.addEventListener('scroll', scrollHandler, { passive: true });
    }
})();
</script>
<?php endif; ?>

<?php get_footer(); ?>

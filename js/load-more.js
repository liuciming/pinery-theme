/**
 * Pinery — homepage infinite scroll.
 * Reads paging state from #posts-grid data attributes and the AJAX endpoint
 * from pineryData (wp_localize_script). No inline scripts in templates.
 */
(function () {
  'use strict';

  function init() {
    var grid = document.getElementById('posts-grid');
    var sentinel = document.getElementById('load-more-sentinel');
    var status = document.getElementById('load-more-status');
    var ajaxUrl = (window.pineryData && window.pineryData.ajaxUrl) || '';
    if (!grid || !sentinel || !ajaxUrl) return;

    var page = parseInt(grid.getAttribute('data-page'), 10) || 1;
    var maxPages = parseInt(grid.getAttribute('data-max-pages'), 10) || 1;
    var layout = grid.className.match(/layout-(\w+)/);
    var layoutStyle = layout ? layout[1] : 'masonry';
    var loading = false;

    if (page >= maxPages) return;

    function loadMore() {
      if (loading || page >= maxPages) return;
      loading = true;
      status.style.display = 'block';

      var formData = new FormData();
      formData.append('action', 'pinery_load_more');
      formData.append('page', page + 1);
      formData.append('layout', layoutStyle);

      fetch(ajaxUrl, { method: 'POST', body: formData })
        .then(function (r) { return r.json(); })
        .then(function (res) {
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
        .catch(function () {
          loading = false;
          status.style.display = 'none';
        });
    }

    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        if (entries[0].isIntersecting) loadMore();
      }, { rootMargin: '400px' });
      observer.observe(sentinel);
    } else {
      var scrollHandler = function () {
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
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

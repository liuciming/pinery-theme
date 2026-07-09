/**
 * Pinery — row-first masonry for the homepage grid.
 *
 * CSS multi-column flows cards down the left column first, so the newest posts
 * read vertically instead of across the top. This script distributes cards into
 * real column wrappers instead: each card (in DOM order = newest first) goes into
 * the currently shortest column, so post #1, #2, #3 sit across the first row and
 * cards appended by infinite scroll never move the ones already placed.
 *
 * No-JS fallback: the original multi-column CSS still applies until the
 * `posts-grid--js` class is added here.
 */
(function () {
  'use strict';

  function init() {
    var grid = document.querySelector('.posts-grid.layout-masonry');
    if (!grid || grid.getAttribute('data-masonry') === 'on') return;
    grid.setAttribute('data-masonry', 'on');

    var cols = [];
    var seq = 0;
    var curN = 0;

    function cssVar(name, fallback) {
      var v = parseInt(getComputedStyle(document.documentElement).getPropertyValue(name), 10);
      return v > 0 ? v : fallback;
    }

    function colCount() {
      var w = window.innerWidth;
      if (w >= 900) return cssVar('--cols-desktop', 3);
      if (w >= 601) return cssVar('--cols-tablet', 2);
      return cssVar('--cols-mobile', 2);
    }

    function shortest() {
      var m = cols[0];
      for (var i = 1; i < cols.length; i++) {
        if (cols[i].offsetHeight < m.offsetHeight) m = cols[i];
      }
      return m;
    }

    // Direct children of the grid that are not column wrappers:
    // the server-rendered cards on first run, AJAX-appended cards later.
    function unplaced() {
      return Array.prototype.filter.call(grid.children, function (c) {
        return c.nodeType === 1 && !c.classList.contains('pinery-mcol');
      });
    }

    function number(items) {
      items.forEach(function (it) {
        if (!it.getAttribute('data-pinery-idx')) it.setAttribute('data-pinery-idx', String(++seq));
      });
    }

    function place(items) {
      items.forEach(function (it) { shortest().appendChild(it); });
    }

    /** (Re)build columns and lay every known card out again in original order. */
    function rebuild() {
      var fresh = unplaced();
      number(fresh);
      var all = Array.prototype.slice.call(grid.querySelectorAll('.pinery-mcol > *')).concat(fresh);
      all.sort(function (a, b) {
        return (parseInt(a.getAttribute('data-pinery-idx'), 10) || 0) -
               (parseInt(b.getAttribute('data-pinery-idx'), 10) || 0);
      });

      cols.forEach(function (c) { c.remove(); });
      cols = [];
      curN = colCount();
      for (var i = 0; i < curN; i++) {
        var d = document.createElement('div');
        d.className = 'pinery-mcol';
        grid.appendChild(d);
        cols.push(d);
      }
      grid.classList.add('posts-grid--js');
      place(all);
    }

    rebuild();

    // Customizer live-preview hooks: rebuild on column-count change,
    // teardown before switching to a non-masonry layout.
    window.pineryMasonry = {
      rebuild: rebuild,
      teardown: function () {
        var all = Array.prototype.slice.call(grid.querySelectorAll('.pinery-mcol > *'));
        all.sort(function (a, b) {
          return (parseInt(a.getAttribute('data-pinery-idx'), 10) || 0) -
                 (parseInt(b.getAttribute('data-pinery-idx'), 10) || 0);
        });
        cols.forEach(function (c) { c.remove(); });
        cols = [];
        all.forEach(function (it) { grid.appendChild(it); });
        grid.classList.remove('posts-grid--js');
        grid.removeAttribute('data-masonry');
        delete window.pineryMasonry;
      }
    };

    // Infinite scroll inserts raw HTML as direct children — sweep new cards
    // into the shortest columns without touching anything already placed.
    new MutationObserver(function () {
      var fresh = unplaced();
      if (!fresh.length) return;
      number(fresh);
      place(fresh);
    }).observe(grid, { childList: true });

    // Rebalance once when all initial images have loaded (real heights known)…
    window.addEventListener('load', rebuild);

    // …and rebuild when a resize crosses a column-count breakpoint.
    var t;
    window.addEventListener('resize', function () {
      clearTimeout(t);
      t = setTimeout(function () { if (colCount() !== curN) rebuild(); }, 150);
    });
  }

  window.pineryMasonryInit = init;

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

/**
 * Pinery Theme Customizer Live Preview
 */
(function(api) {
    'use strict';

    // Helper: update a CSS variable on :root
    function setVar(name, value) {
        document.documentElement.style.setProperty(name, value);
    }

    // Colors
    [
        ['pinery_accent_color', '--accent'],
        ['pinery_bg_color', '--cream'],
        ['pinery_text_color', '--text'],
        ['pinery_heading_color', '--dark'],
        ['pinery_card_bg_color', '--warm-white'],
    ].forEach(function(pair) {
        api(pair[0], function(value) {
            value.bind(function(to) { setVar(pair[1], to); });
        });
    });

    // Font size
    api('pinery_font_size', function(value) {
        value.bind(function(to) {
            setVar('--font-size-base', to + 'px');
        });
    });

    // Layout style
    api('pinery_layout_style', function(value) {
        value.bind(function(to) {
            var grid = document.querySelector('.posts-grid');
            if (grid) {
                grid.classList.remove('layout-masonry', 'layout-grid', 'layout-mixed');
                grid.classList.add('layout-' + to);
            }
        });
    });

    // Column counts
    function updateCols() {
        var desktop = api('pinery_columns_desktop')() || 3;
        var tablet  = api('pinery_columns_tablet')() || 2;
        var mobile  = api('pinery_columns_mobile')() || 2;
        setVar('--cols-desktop', desktop);
        setVar('--cols-tablet', tablet);
        setVar('--cols-mobile', mobile);
    }
    api('pinery_columns_desktop', function(v) { v.bind(updateCols); });
    api('pinery_columns_tablet', function(v) { v.bind(updateCols); });
    api('pinery_columns_mobile', function(v) { v.bind(updateCols); });

    // Card excerpt visibility
    api('pinery_show_excerpt', function(value) {
        value.bind(function(to) {
            document.querySelectorAll('.post-card-excerpt').forEach(function(el) {
                el.style.display = to ? '' : 'none';
            });
        });
    });

    // Card meta visibility
    api('pinery_show_meta', function(value) {
        value.bind(function(to) {
            document.querySelectorAll('.post-card-meta').forEach(function(el) {
                el.style.display = to ? '' : 'none';
            });
        });
    });

    // Affiliate button text
    api('pinery_affiliate_btn_text', function(value) {
        value.bind(function(to) {
            var btn = document.getElementById('lightbox-btn');
            if (btn) btn.textContent = to;
        });
    });

})(wp.customize);

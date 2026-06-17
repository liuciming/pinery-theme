/**
 * Pinery Theme — Lightbox & Search Overlay
 */
(function() {

  // ── Lightbox ──
  var overlay = document.getElementById('lightbox');
  if (overlay) {
    var img = document.getElementById('lightbox-img');
    var imgNext = document.getElementById('lightbox-img-next');
    var title = document.getElementById('lightbox-title');
    var priceEl = document.getElementById('lightbox-price');
    var ratingEl = document.getElementById('lightbox-rating');
    var buyBtn = document.getElementById('lightbox-btn');
    var detailBtn = document.getElementById('lightbox-detail-btn');
    var closeBtn = document.getElementById('lightbox-close');

    var cards = [], currentIndex = -1;
    var touchStartY = 0, touchStartX = 0, touchActive = false;
    var animating = false;

    function buildCards() {
      cards = [];
      var els = document.querySelectorAll('.post-card, .hero-img');
      els.forEach(function(card) {
        var src = card.getAttribute('data-full-img');
        if (src) {
          cards.push({
            src: src,
            aff: card.getAttribute('data-affiliate-url') || '',
            title: card.getAttribute('data-post-title') || '',
            url: card.getAttribute('data-post-url') || '',
            price: card.getAttribute('data-price') || '',
            rating: card.getAttribute('data-rating') || ''
          });
        }
      });
    }

    function updateInfo(index) {
      var c = cards[index];
      title.textContent = c.title;
      priceEl.textContent = c.price || '';
      priceEl.style.display = c.price ? '' : 'none';
      ratingEl.textContent = c.rating ? '★ ' + c.rating : '';
      ratingEl.style.display = c.rating ? '' : 'none';
      buyBtn.href = c.aff || '#';
      buyBtn.style.display = c.aff ? '' : 'none';
      detailBtn.href = c.url || '#';
    }

    function swapRoles() {
      // Swap IDs
      img.id = 'lightbox-img-next';
      imgNext.id = 'lightbox-img';
      // Swap CSS classes
      img.classList.add('lightbox-img--next');
      imgNext.classList.remove('lightbox-img--next');
      // Swap JS references — no transforms changed
      var tmp = img;
      img = imgNext;
      imgNext = tmp;
    }

    function swipeTo(direction) {
      if (animating) return;
      var newIndex = currentIndex + direction;
      if (newIndex < 0 || newIndex >= cards.length) return;
      animating = true;

      var outImg = img;
      var inImg = imgNext;

      // Kill transitions for instant positioning
      outImg.style.transition = 'none';
      inImg.style.transition = 'none';

      // Position buffer offscreen for this swipe direction
      inImg.style.transform = direction > 0 ? 'translateY(100%)' : 'translateY(-100%)';
      inImg.src = cards[newIndex].src;
      inImg.alt = cards[newIndex].title;
      inImg.offsetHeight;

      // Animate
      outImg.style.transition = '';
      inImg.style.transition = '';
      outImg.style.transform = direction > 0 ? 'translateY(-100%)' : 'translateY(100%)';
      inImg.style.transform = 'translateY(0)';

      currentIndex = newIndex;
      updateInfo(currentIndex);

      function onDone() {
        swapRoles();
        animating = false;
        // Preload neighbors
        if (currentIndex + 1 < cards.length) new Image().src = cards[currentIndex + 1].src;
        if (currentIndex > 0) new Image().src = cards[currentIndex - 1].src;
      }

      var done = false;
      outImg.addEventListener('transitionend', function() {
        if (!done) { done = true; onDone(); }
      }, { once: true });
      setTimeout(function() {
        if (!done) { done = true; onDone(); }
      }, 400);
    }

    function open(cardEl) {
      buildCards();
      var src = cardEl.getAttribute('data-full-img');
      var idx = -1;
      for (var i = 0; i < cards.length; i++) {
        if (cards[i].src === src) { idx = i; break; }
      }
      if (idx < 0) {
        cards = [{
          src: src,
          aff: cardEl.getAttribute('data-affiliate-url') || '',
          title: cardEl.getAttribute('data-post-title') || '',
          url: cardEl.getAttribute('data-post-url') || '',
          price: cardEl.getAttribute('data-price') || '',
          rating: cardEl.getAttribute('data-rating') || ''
        }];
        idx = 0;
      }
      // Reset roles: img = primary at center, imgNext = buffer at bottom
      if (img.id !== 'lightbox-img') swapRoles();
      currentIndex = idx;
      img.src = cards[idx].src;
      img.alt = cards[idx].title;
      img.style.transform = '';
      imgNext.style.transform = 'translateY(100%)';
      updateInfo(idx);
      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
      if (idx + 1 < cards.length) new Image().src = cards[idx + 1].src;
    }

    function closeLightbox() {
      overlay.classList.remove('active');
      document.body.style.overflow = '';
      img.style.transform = '';
      imgNext.style.transform = 'translateY(100%)';
      animating = false;
    }

    // Click handler
    document.addEventListener('click', function(e) {
      var trigger = e.target.closest('.js-lightbox-trigger');
      if (trigger) {
        e.preventDefault();
        var card = trigger.closest('.post-card, .hero-img');
        if (card && card.getAttribute('data-full-img')) {
          open(card);
        }
      }
    });

    // Touch swipe (mobile)
    overlay.addEventListener('touchstart', function(e) {
      touchStartY = e.touches[0].clientY;
      touchStartX = e.touches[0].clientX;
      touchActive = true;
    }, { passive: true });

    overlay.addEventListener('touchmove', function(e) {
      if (!touchActive || animating || cards.length < 2) return;
      var dy = e.touches[0].clientY - touchStartY;
      var absDY = Math.abs(dy);
      var absDX = Math.abs(e.touches[0].clientX - touchStartX);
      if (absDY < 10 || absDY < absDX) return;

      e.preventDefault();
      var goingUp = dy < 0;
      if ((goingUp && currentIndex >= cards.length - 1) || (!goingUp && currentIndex <= 0)) return;

      var pct = Math.min(Math.abs(dy) / overlay.offsetHeight, 1);
      var offset = goingUp ? -pct * 100 : pct * 100;
      img.style.transition = 'none';
      img.style.transform = 'translateY(' + offset + '%)';
      if (goingUp) {
        imgNext.style.transition = 'none';
        imgNext.src = cards[currentIndex + 1].src;
        imgNext.alt = cards[currentIndex + 1].title;
        imgNext.style.transform = 'translateY(' + (100 - pct * 100) + '%)';
      } else {
        imgNext.style.transition = 'none';
        imgNext.src = cards[currentIndex - 1].src;
        imgNext.alt = cards[currentIndex - 1].title;
        imgNext.style.transform = 'translateY(' + (-100 + pct * 100) + '%)';
      }
    }, { passive: false });

    overlay.addEventListener('touchend', function(e) {
      if (!touchActive) return;
      touchActive = false;
      if (cards.length < 2) {
        img.style.transform = '';
        imgNext.style.transform = 'translateY(100%)';
        return;
      }

      var dy = touchStartY - (e.changedTouches[0].clientY);
      var dx = touchStartX - (e.changedTouches[0].clientX);
      var absDY = Math.abs(dy);
      var absDX = Math.abs(dx);

      if (absDY > absDX && absDY > 10) {
        if (dy > 0) {
          swipeTo(1);
        } else {
          swipeTo(-1);
        }
      } else {
        img.style.transition = '';
        imgNext.style.transition = '';
        img.style.transform = '';
        imgNext.style.transform = 'translateY(100%)';
      }
    });

    overlay.addEventListener('click', function(e) {
      if (e.target === overlay && !touchActive) closeLightbox();
    });

    closeBtn.addEventListener('click', closeLightbox);

    document.addEventListener('keydown', function(e) {
      if (!overlay.classList.contains('active')) return;
      if (e.key === 'Escape') closeLightbox();
      if (e.key === 'ArrowDown') swipeTo(1);
      if (e.key === 'ArrowUp') swipeTo(-1);
    });
  }

  // ── Search Overlay ──
  var searchBtn = document.getElementById('header-search-btn');
  var searchOverlay = document.getElementById('search-overlay');
  if (searchBtn && searchOverlay) {
    searchBtn.addEventListener('click', function() {
      searchOverlay.classList.toggle('active');
      var input = searchOverlay.querySelector('input');
      if (input && searchOverlay.classList.contains('active')) {
        setTimeout(function() { input.focus(); }, 100);
      }
    });

    searchOverlay.addEventListener('click', function(e) {
      if (e.target === searchOverlay) {
        searchOverlay.classList.remove('active');
      }
    });

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && searchOverlay.classList.contains('active')) {
        searchOverlay.classList.remove('active');
      }
    });
  }

  // ── Real-time Price Refresh (Creators API) ──
  (function() {
    var PRICE_TTL = 60 * 60 * 1000; // 60 min default — updated below from theme settings
    var AJAX_URL = '/wp-admin/admin-ajax.php';
    var AJAX_NONCE = '';
    var refreshing = false;

    // Read TTL and nonce from meta tags
    var ttlMeta = document.querySelector('meta[name="pinery-price-ttl"]');
    if (ttlMeta) {
      var ttlVal = parseInt(ttlMeta.getAttribute('content'));
      if (ttlVal > 0) PRICE_TTL = ttlVal * 60 * 1000;
    }
    var nonceMeta = document.querySelector('meta[name="pinery-price-nonce"]');
    if (nonceMeta) AJAX_NONCE = nonceMeta.getAttribute('content');

    function refreshPrice(card) {
      var asin = card.getAttribute('data-asin');
      if (!asin) return;
      var ts = parseInt(card.getAttribute('data-price-ts')) || 0;
      var now = Math.floor(Date.now() / 1000);
      var age = now - ts;
      if (age < (PRICE_TTL / 1000)) return; // Still fresh

      var formData = new FormData();
      formData.append('action', 'pinery_creators_price');
      formData.append('asin', asin);
      if (AJAX_NONCE) formData.append('nonce', AJAX_NONCE);

      // Mark this element as refreshing so we don't double-fetch
      card.setAttribute('data-price-ts', now.toString());

      fetch(AJAX_URL, { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(res) {
          if (res.success && res.data.price) {
            card.setAttribute('data-price', res.data.price);
            card.setAttribute('data-price-ts', res.data.updated || now);

            // Update overlay price on card
            var overlay = card.querySelector('.overlay-price');
            if (overlay) overlay.textContent = res.data.price;

            // Update in-article price meta timestamp
            updateArticlePriceMeta(asin, res.data.price, res.data.updated || now);

            // Update lightbox price if this card is currently open
            var priceEl = document.getElementById('lightbox-price');
            if (priceEl && overlay && priceEl.textContent === overlay.getAttribute('data-old-price')) {
              priceEl.textContent = res.data.price;
            }
          }
        })
        .catch(function() {
          // Restore old ts so it retries next page load
          card.setAttribute('data-price-ts', ts.toString());
        });
    }

    // Update the in-article price meta paragraph (single post page)
    function updateArticlePriceMeta(asin, price, updatedTs) {
      var metas = document.querySelectorAll('.pinery-price-meta[data-asin="' + asin + '"]');
      metas.forEach(function(meta) {
        meta.setAttribute('data-price-ts', updatedTs.toString());
        // Update the "Price updated X ago" text
        var now = Math.floor(Date.now() / 1000);
        var minutes = Math.floor((now - updatedTs) / 60);
        var timeStr = minutes < 1 ? 'just now' : (minutes < 60 ? minutes + ' min ago' : Math.floor(minutes/60) + ' h ago');
        meta.innerHTML = '<small>Price updated ' + timeStr + '</small>';
      });
    }

    // Refresh stale article price meta on single post pages
    function refreshArticlePrice() {
      var meta = document.querySelector('.pinery-price-meta[data-asin]');
      if (!meta) return;
      var asin = meta.getAttribute('data-asin');
      if (!asin) return;
      var ts = parseInt(meta.getAttribute('data-price-ts')) || 0;
      var now = Math.floor(Date.now() / 1000);
      if ((now - ts) < (PRICE_TTL / 1000)) return; // still fresh

      var formData = new FormData();
      formData.append('action', 'pinery_creators_price');
      formData.append('asin', asin);
      if (AJAX_NONCE) formData.append('nonce', AJAX_NONCE);
      meta.setAttribute('data-price-ts', now.toString());

      fetch(AJAX_URL, { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(res) {
          if (res.success && res.data.price) {
            updateArticlePriceMeta(asin, res.data.price, res.data.updated || now);
          }
        });
    }

    function refreshAllVisible() {
      if (refreshing) return;
      refreshing = true;

      var cards = document.querySelectorAll('.post-card[data-asin]');
      var stale = [];
      var now = Math.floor(Date.now() / 1000);

      cards.forEach(function(card) {
        var asin = card.getAttribute('data-asin');
        if (!asin) return;
        var ts = parseInt(card.getAttribute('data-price-ts')) || 0;
        if ((now - ts) >= (PRICE_TTL / 1000)) {
          stale.push(card);
        }
      });

      // Batch refresh stale cards with 1s spacing (rate limit)
      function next(i) {
        if (i >= stale.length) { refreshing = false; return; }
        refreshPrice(stale[i]);
        setTimeout(function() { next(i + 1); }, 1100);
      }

      if (stale.length > 0) next(0);
      else refreshing = false;
    }

    // Run article price check on load (single post pages)
    refreshArticlePrice();

    // Run on load (delayed so critical content renders first)
    if (document.readyState === 'complete') {
      setTimeout(refreshAllVisible, 2000);
    } else {
      window.addEventListener('load', function() {
        setTimeout(refreshAllVisible, 2000);
      });
    }

    // Also refresh price when lightbox opens for the current card
    var origOpen = typeof open === 'function' ? open : null;
    if (origOpen && typeof overlay !== 'undefined' && overlay) {
      // Hook into lightbox: refresh price on the card being opened
      document.addEventListener('click', function(e) {
        var trigger = e.target.closest('.js-lightbox-trigger');
        if (trigger) {
          var card = trigger.closest('.post-card[data-asin]');
          if (card) refreshPrice(card);
        }
      });
    }
  })();

})();

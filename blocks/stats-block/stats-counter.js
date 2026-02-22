/**
 * ACF Stats Counter — Self-contained animated counter.
 *
 * Uses IntersectionObserver to trigger count-up animation when the
 * block scrolls into view. Zero dependencies, no framework required.
 */
(function () {
  'use strict';

  /**
   * Animate a number from 0 to target.
   *
   * @param {HTMLElement} el       The .acf-stat-value element.
   * @param {number}      target   Final number.
   * @param {number}      duration Animation duration in ms.
   */
  function animateValue(el, target, duration) {
    var startTime = null;
    var isFloat = String(target).indexOf('.') !== -1;
    var decimals = isFloat ? (String(target).split('.')[1] || '').length : 0;

    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      // Ease-out cubic for natural deceleration
      var eased = 1 - Math.pow(1 - progress, 3);
      var current = eased * target;
      el.textContent = isFloat ? current.toFixed(decimals) : Math.floor(current).toLocaleString();
      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        el.textContent = isFloat ? target.toFixed(decimals) : target.toLocaleString();
      }
    }

    requestAnimationFrame(step);
  }

  /**
   * Initialize counter for a single stats block.
   *
   * @param {HTMLElement} block The .acf-stats-block element.
   */
  function initBlock(block) {
    if (block.dataset.acfStatsInit) return;
    block.dataset.acfStatsInit = '1';

    var values = block.querySelectorAll('.acf-stat-value');
    if (!values.length) return;

    // Duration scales with number size (800-2000ms)
    var baseDuration = 1200;

    // Check if animation is enabled
    var hasAnimation = block.classList.contains('acf-has-animation');
    if (!hasAnimation) return;

    // Check for reduced motion preference
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      return; // Keep the static numbers as-is
    }

    // Store targets and reset to 0
    var targets = [];
    values.forEach(function (el) {
      var parent = el.closest('.acf-stat-number');
      var raw = parent ? parent.getAttribute('data-target') : el.textContent;
      var num = parseFloat(raw) || 0;
      targets.push({ el: el, target: num });
      el.textContent = '0';
    });

    // Observe with IntersectionObserver
    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              targets.forEach(function (t, i) {
                // Stagger start by 80ms per item
                setTimeout(function () {
                  animateValue(t.el, t.target, baseDuration);
                }, i * 80);
              });
              observer.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.2, rootMargin: '0px 0px -50px 0px' }
      );
      observer.observe(block);
    } else {
      // Fallback: animate immediately
      targets.forEach(function (t) {
        animateValue(t.el, t.target, baseDuration);
      });
    }
  }

  /**
   * Initialize all stats blocks on the page.
   */
  function initAll() {
    var blocks = document.querySelectorAll('.acf-stats-block.acf-has-animation');
    blocks.forEach(initBlock);
  }

  // Run on DOMContentLoaded or immediately if already loaded
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

  // Also re-init on block editor preview changes (for ACF block previews)
  if (window.acf && window.acf.addAction) {
    window.acf.addAction('render_block_preview/type=stats', function ($el) {
      var block = $el[0].querySelector('.acf-stats-block.acf-has-animation');
      if (block) {
        block.removeAttribute('data-acf-stats-init');
        initBlock(block);
      }
    });
  }
})();

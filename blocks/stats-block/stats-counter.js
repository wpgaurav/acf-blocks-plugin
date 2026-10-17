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

    // Keep the printed number until the count-up actually starts. Zeroing
    // first left the stat at 0 when the block never intersected (closed
    // disclosures, print, or a failed observer).
    var targets = [];
    values.forEach(function (el) {
      var parent = el.closest('.acf-stat-number');
      var raw = parent ? parent.getAttribute('data-target') : el.textContent;
      var num = parseFloat(String(raw).replace(/,/g, '')) || 0;
      targets.push({ el: el, target: num });
    });

    function play() {
      targets.forEach(function (t, i) {
        setTimeout(function () {
          t.el.textContent = '0';
          animateValue(t.el, t.target, baseDuration);
        }, i * 80);
      });
    }

    // Observe with IntersectionObserver
    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              play();
              observer.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.2, rootMargin: '0px 0px -50px 0px' }
      );
      observer.observe(block);
    } else {
      play();
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

  // Infinite scroll and editor previews can insert a stats block after load.
  if (typeof MutationObserver !== 'undefined') {
    new MutationObserver(function (records) {
      var added = records.some(function (record) {
        return Array.prototype.some.call(record.addedNodes, function (node) {
          return node.nodeType === 1 && (node.matches('.acf-stats-block') || node.querySelector('.acf-stats-block'));
        });
      });
      if (added) {
        initAll();
      }
    }).observe(document.documentElement, { childList: true, subtree: true });
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

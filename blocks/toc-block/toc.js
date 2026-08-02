(function() {
    'use strict';

    function connectHeadings(toc) {
        var levels = (toc.dataset.headingLevels || 'h2').split(',').filter(Boolean);
        var scope = toc.closest('article') || document.querySelector('main') || document;
        var includeAcf = toc.dataset.includeAcfHeadings === '1';
        var headings = Array.prototype.filter.call(scope.querySelectorAll(levels.join(',')), function(heading) {
            if (heading.closest('.acf-toc')) return false;
            return includeAcf || !heading.closest('[data-acf-block],.acf-block');
        });
        var links = toc.querySelectorAll('.acf-toc__link');

        Array.prototype.forEach.call(links, function(link, index) {
            var heading = headings[index];
            var href = link.getAttribute('href');
            if (!heading || !href || href.charAt(0) !== '#') return;
            heading.id = href.substring(1);
        });

        return headings.slice(0, links.length);
    }

    function initTocs() {
        var tocs = document.querySelectorAll('.acf-toc');
        if (!tocs.length) return;

        tocs.forEach(function(toc) {
            var links = toc.querySelectorAll('.acf-toc__link');
            var headings = connectHeadings(toc);

            if (!toc.classList.contains('acf-toc--highlight-active') || !headings.length || !('IntersectionObserver' in window)) return;
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (!entry.isIntersecting) return;
                    links.forEach(function(link) { link.classList.remove('acf-toc__link--active'); });
                    var active = Array.prototype.find.call(links, function(link) {
                        return link.getAttribute('href') === '#' + entry.target.id;
                    });
                    if (active) active.classList.add('acf-toc__link--active');
                });
            }, { rootMargin: '-80px 0px -80% 0px', threshold: 0 });

            headings.forEach(function(heading) { observer.observe(heading); });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTocs);
    } else {
        initTocs();
    }
})();

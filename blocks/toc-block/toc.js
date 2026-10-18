(function() {
    'use strict';

    function contentScope(toc) {
        return toc.closest('.entry-content,.wp-block-post-content,[data-acf-toc-scope]') || toc.closest('article') || document.querySelector('main') || document;
    }

    // A TOC rendered by itself (for example in a sidebar) never passes through
    // the_content. Only those pending placeholders need this rendered-DOM path.
    function finalizeStandaloneToc(toc) {
        if (toc.dataset.tocFinalized !== '0') return;
        var scope = contentScope(toc);
        var headings = Array.prototype.filter.call(scope.querySelectorAll('h1,h2,h3,h4,h5,h6'), function(heading) {
            return !heading.closest('.acf-toc');
        });
        var reserved = Object.create(null);
        var used = Object.create(null);
        document.querySelectorAll('[id]').forEach(function(node) {
            if (!node.id) return;
            reserved[node.id] = true;
            if (headings.indexOf(node) === -1) used[node.id] = true;
        });
        headings.forEach(function(heading, index) {
            var id = heading.id;
            if (!id || used[id]) {
                var base = id || heading.textContent.toLowerCase().trim().replace(/\s+/g, '-').replace(/[^a-z0-9_\u00a0-\uffff-]/g, '') || 'heading-' + (index + 1);
                id = base;
                var counter = 2;
                while (reserved[id] || used[id]) id = base + '-' + counter++;
                heading.id = id;
            }
            used[id] = true;
        });
        var levels = (toc.dataset.headingLevels || 'h2').split(',');
        var includeAcf = toc.dataset.includeAcfHeadings === '1';
        var selected = headings.filter(function(heading) {
            return levels.indexOf(heading.tagName.toLowerCase()) !== -1 && (includeAcf || (heading.dataset.acfTocHeading !== '1' && !heading.closest('[data-acf-block],.acf-block')));
        });
        var content = toc.querySelector('.acf-toc__content');
        if (!content) return;
        if (!selected.length) {
            toc.hidden = true;
            return;
        }
        var mode = toc.dataset.listType || 'ul';
        var tag = mode === 'ol' ? 'ol' : 'ul';
        var root = document.createElement(tag);
        root.className = 'acf-toc__list acf-toc__list--' + (mode === 'plain' ? 'plain' : tag) + (toc.dataset.listClass ? ' ' + toc.dataset.listClass : '');
        var minLevel = Math.min.apply(null, selected.map(function(heading) { return Number(heading.tagName.substring(1)); }));
        var stack = [];
        selected.forEach(function(heading) {
            var level = Number(heading.tagName.substring(1));
            var list = root;
            if (mode !== 'plain') {
                while (stack.length > 1 && level < stack[stack.length - 1].level) stack.pop();
                if (stack.length && level > stack[stack.length - 1].level) {
                    list = document.createElement(tag);
                    list.className = 'acf-toc__sublist acf-toc__sublist--depth-' + (level - minLevel);
                    stack[stack.length - 1].item.appendChild(list);
                    stack.push({ level: level, list: list });
                } else if (stack.length) {
                    list = stack[stack.length - 1].list;
                    if (stack.length === 1) stack[0].level = level;
                } else {
                    stack.push({ level: level, list: root });
                }
            }
            var item = document.createElement('li');
            item.className = 'acf-toc__item acf-toc__item--depth-' + (level - minLevel);
            item.dataset.level = String(level);
            var link = document.createElement('a');
            link.className = 'acf-toc__link' + (toc.dataset.linkClass ? ' ' + toc.dataset.linkClass : '');
            link.setAttribute('href', '#' + heading.id);
            link.textContent = heading.textContent.trim();
            item.appendChild(link);
            list.appendChild(item);
            if (mode !== 'plain') stack[stack.length - 1].item = item;
        });
        content.textContent = '';
        content.appendChild(root);
        if (toc.dataset.tocSchema === '1') {
            var schema = document.createElement('script');
            schema.type = 'application/ld+json';
            schema.textContent = JSON.stringify({
                '@context': 'https://schema.org', '@type': 'ItemList',
                itemListElement: selected.map(function(heading, index) {
                    return { '@type': 'SiteNavigationElement', position: index + 1, name: heading.textContent.trim(), url: window.location.href.split('#')[0] + '#' + heading.id };
                })
            });
            toc.appendChild(schema);
        }
        toc.dataset.tocFinalized = '1';
    }

    function connectHeadings(toc) {
        var scope = contentScope(toc);
        var headings = scope.querySelectorAll('h1[id],h2[id],h3[id],h4[id],h5[id],h6[id]');
        var connected = [];
        toc.querySelectorAll('.acf-toc__link').forEach(function(link) {
            var href = link.getAttribute('href');
            if (!href || href.charAt(0) !== '#') return;
            var heading = Array.prototype.find.call(headings, function(candidate) {
                return !candidate.closest('.acf-toc') && candidate.id === href.substring(1);
            });
            if (heading) connected.push(heading);
        });
        // Anchors and links are generated together on the server. Never assign
        // IDs by DOM position: excluded ACF headings and HTML/synced headings
        // change that order and can overwrite an author's custom anchor.
        return connected;
    }

    function initTocs() {
        var tocs = document.querySelectorAll('.acf-toc');
        if (!tocs.length) return;

        tocs.forEach(function(toc) {
            if (toc.dataset.tocInitialized) return;
            toc.dataset.tocInitialized = '1';
            finalizeStandaloneToc(toc);
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

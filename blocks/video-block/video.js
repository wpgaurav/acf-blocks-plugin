(function () {
    'use strict';

    function initVideoFacades() {
        document.querySelectorAll('.acf-video-block .acf-video-facade').forEach(function (facade) {
            if (facade.dataset.initialized) return;
            facade.dataset.initialized = 'true';

            var thumbnail = facade.querySelector('img[data-thumbnail-fallback]');
            if (thumbnail) {
                function useFallback() {
                    var fallback = thumbnail.dataset.thumbnailFallback;
                    if (!fallback) return;
                    delete thumbnail.dataset.thumbnailFallback;
                    thumbnail.src = fallback;
                }

                // YouTube may return a 120px placeholder instead of a 404 when
                // an older video has no HD thumbnail. Keep it usable either way.
                thumbnail.addEventListener('error', useFallback);
                thumbnail.addEventListener('load', function () {
                    if (thumbnail.naturalWidth <= 120) useFallback();
                });
                if (thumbnail.complete && thumbnail.currentSrc && thumbnail.naturalWidth <= 120) useFallback();
            }

            function loadVideo() {
                var videoId = facade.dataset.videoId;
                var videoType = facade.dataset.videoType;
                var params = facade.dataset.params || '';
                var iframe = document.createElement('iframe');

                // Set the policy before navigation, including under a page's
                // restrictive policy. YouTube requires the embedding origin.
                iframe.referrerPolicy = 'strict-origin-when-cross-origin';
                iframe.title = facade.getAttribute('aria-label') || 'Video player';
                iframe.width = '1280';
                iframe.height = '720';
                iframe.frameBorder = '0';
                iframe.allowFullscreen = true;

                if (videoType === 'youtube') {
                    iframe.src = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1' + params;
                    iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                } else if (videoType === 'vimeo') {
                    iframe.src = 'https://player.vimeo.com/video/' + videoId + '?autoplay=1' + params;
                    iframe.allow = 'autoplay; fullscreen; picture-in-picture';
                } else {
                    return;
                }

                facade.replaceWith(iframe);
            }

            facade.addEventListener('click', loadVideo);
            facade.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    loadVideo();
                }
            });
        });
    }

    function initLazyVideos() {
        var videos = document.querySelectorAll('.acf-video-block video[data-lazy-src]:not([data-acf-video-observed])');
        if (!videos.length) return;

        function loadSource(video) {
            if (!video.dataset.lazySrc) return;
            var source = document.createElement('source');
            source.src = video.dataset.lazySrc;
            source.type = video.dataset.lazyType || 'video/mp4';
            video.appendChild(source);
            video.removeAttribute('data-lazy-src');
            video.removeAttribute('data-lazy-type');
            video.removeAttribute('data-acf-video-observed');
        }

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    loadSource(entry.target);
                    observer.unobserve(entry.target);
                });
            }, { rootMargin: '200px' });
            videos.forEach(function (video) {
                video.dataset.acfVideoObserved = '1';
                observer.observe(video);
            });
        } else {
            videos.forEach(loadSource);
        }
    }

    function init() {
        initVideoFacades();
        initLazyVideos();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Infinite scroll can add rendered blocks after DOMContentLoaded. Ignore
    // unrelated DOM changes, and bind each facade/lazy video only once.
    if (typeof MutationObserver !== 'undefined') {
        new MutationObserver(function (records) {
            var addedVideo = records.some(function (record) {
                return Array.prototype.some.call(record.addedNodes, function (node) {
                    return node.nodeType === 1 && (node.matches('.acf-video-block') || node.closest('.acf-video-block') || node.querySelector('.acf-video-block'));
                });
            });
            if (addedVideo) init();
        }).observe(document.documentElement, { childList: true, subtree: true });
    }
})();

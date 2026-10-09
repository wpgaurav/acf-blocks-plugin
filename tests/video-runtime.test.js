const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function renderFacade(thumbnailWidth) {
    const events = {};
    const imageEvents = {};
    const assignments = [];
    let mutationCallback;
    const image = {
        dataset: { thumbnailFallback: 'https://i.ytimg.com/vi/test/hqdefault.jpg' },
        complete: true,
        currentSrc: 'https://i.ytimg.com/vi/test/maxresdefault.jpg',
        naturalWidth: thumbnailWidth,
        src: 'https://i.ytimg.com/vi/test/maxresdefault.jpg',
        addEventListener: (name, callback) => { imageEvents[name] = callback; }
    };
    const facade = {
        dataset: { videoId: 'aqz-KE-bpKQ', videoType: 'youtube', params: '&mute=1&loop=1&playlist=aqz-KE-bpKQ' },
        querySelector: () => image,
        getAttribute: () => 'Test video',
        addEventListener: (name, callback) => { events[name] = callback; },
        replaceWith: iframe => { facade.player = iframe; }
    };
    const document = {
        readyState: 'complete',
        documentElement: {},
        querySelectorAll: selector => selector.includes('.acf-video-facade') ? [facade] : [],
        createElement: () => new Proxy({}, { set(target, name, value) { assignments.push(name); target[name] = value; return true; } })
    };
    class MutationObserver { constructor(callback) { mutationCallback = callback; } observe() {} }
    vm.runInNewContext(fs.readFileSync(__dirname + '/../blocks/video-block/video.js', 'utf8'), { document, MutationObserver });
    return { facade, events, image, imageEvents, assignments, document, mutate: records => mutationCallback(records) };
}

test('HD thumbnails with slightly different dimensions stay sharp', () => {
    // This real YouTube maxres thumbnail is 1278px wide, rather than 1280px.
    const { image, imageEvents } = renderFacade(1278);
    imageEvents.load();
    assert(image.src.endsWith('/maxresdefault.jpg'));
});

test('placeholder and missing HD thumbnails fall back only once', () => {
    const placeholder = renderFacade(120);
    assert(placeholder.image.src.endsWith('/hqdefault.jpg'));
    assert.equal(placeholder.image.dataset.thumbnailFallback, undefined);
    placeholder.imageEvents.error();
    assert(placeholder.image.src.endsWith('/hqdefault.jpg'));
    const missing = renderFacade(1280);
    missing.imageEvents.error();
    assert(missing.image.src.endsWith('/hqdefault.jpg'));
});

for (const action of ['click', 'Enter', ' ']) {
    test(action + ' creates an identified player with preserved playback settings', () => {
        const result = renderFacade(1280);
        if (action === 'click') result.events.click();
        else result.events.keydown({ key: action, preventDefault() {} });
        const player = result.facade.player;
        assert.equal(player.referrerPolicy, 'strict-origin-when-cross-origin');
        assert(result.assignments.indexOf('referrerPolicy') < result.assignments.indexOf('src'));
        assert.equal(player.title, 'Test video');
        assert.equal(player.width, '1280');
        assert.equal(player.height, '720');
        assert.equal(player.allowFullscreen, true);
        assert(player.src.includes('?autoplay=1&mute=1&loop=1&playlist=aqz-KE-bpKQ'));
    });
}

function preview(video) {
    const player = {};
    const window = { location: { search: '?video=' + encodeURIComponent(video) + '&title=Preview%20title' } };
    const document = { querySelector: () => player };
    vm.runInNewContext(fs.readFileSync(__dirname + '/../blocks/video-block/youtube-preview.js', 'utf8'), { window, document, URL, URLSearchParams });
    return player;
}

test('the editor document preserves YouTube playback parameters', () => {
    const url = 'https://www.youtube.com/embed/aqz-KE-bpKQ?mute=1&controls=0';
    assert.equal(preview(url).src, url);
    assert.equal(preview(url).title, 'Preview title');
});

test('the editor document rejects arbitrary URLs and invalid IDs', () => {
    for (const url of ['javascript:alert(1)', 'https://example.com/embed/aqz-KE-bpKQ', 'https://www.youtube.com.evil.test/embed/aqz-KE-bpKQ', 'https://www.youtube.com/embed/invalid', 'https://www.youtube.com/watch?v=aqz-KE-bpKQ']) {
        assert.equal(preview(url).src, undefined);
    }
});

test('AJAX-inserted facades are initialized once and can play', () => {
    const result = renderFacade(1280);
    const events = {};
    const added = {
        ...result.facade,
        dataset: { videoId: 'aqz-KE-bpKQ', videoType: 'youtube' },
        querySelector: () => null,
        addEventListener: (name, callback) => { assert.equal(events[name], undefined); events[name] = callback; },
        replaceWith: iframe => { added.player = iframe; }
    };
    result.document.querySelectorAll = selector => selector.includes('.acf-video-facade') ? [result.facade, added] : [];
    const records = [{ addedNodes: [{ nodeType: 1, matches: () => true }] }];
    result.mutate(records);
    result.mutate(records);
    assert.equal(added.dataset.initialized, 'true');
    events.click();
    assert(added.player.src.includes('/embed/aqz-KE-bpKQ?autoplay=1'));
});

test('lazy initialization loads ACF media and leaves other plugins media alone', () => {
    function video(url) {
        return { dataset: { lazySrc: url }, sources: [], appendChild(source) { this.sources.push(source); }, removeAttribute(name) { delete this.dataset[name === 'data-lazy-src' ? 'lazySrc' : name === 'data-lazy-type' ? 'lazyType' : 'acfVideoObserved']; } };
    }
    const ours = video('/ours.mp4');
    const other = video('/other.mp4');
    const document = {
        readyState: 'complete',
        querySelectorAll: selector => selector.includes('video[data-lazy-src]') ? (selector.startsWith('.acf-video-block ') ? [ours] : [ours, other]) : [],
        createElement: () => ({})
    };
    vm.runInNewContext(fs.readFileSync(__dirname + '/../blocks/video-block/video.js', 'utf8'), { document, window: {} });
    assert.equal(ours.sources[0].src, '/ours.mp4');
    assert.equal(ours.dataset.lazySrc, undefined);
    assert.equal(other.dataset.lazySrc, '/other.mp4');
    assert.equal(other.sources.length, 0);
});

'use strict';
const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function importImage(response, withAcf) {
    const events = {};
    const state = { id: '', changed: 0, rendered: null, source: '', status: '' };
    const empty = { length: 0, first() { return this; } };
    const input = { length: 1, first() { return this; }, val(value) { if (value === undefined) return state.id; state.id = value; return this; }, trigger() { state.changed++; return this; } };
    const control = { addClass() { state.hasImage = true; return this; } };
    const image = { attr(value) { state.preview = value; return this; } };
    const imageField = { length: 1, first() { return this; }, find(selector) { if (selector.includes('hidden')) return input; if (selector.includes('acf-image-uploader')) return control; if (selector.includes('img')) return image; return empty; } };
    const status = { text(value) { state.status = value; return this; }, css() { return this; } };
    const source = { prop() { return this; }, trigger() { state.source = 'local'; return this; } };
    const block = { length: 1, find(selector) {
        if (selector.includes('external_image_url')) return { val: () => 'https://example.com/photo.png' };
        if (selector.includes('local_image')) return imageField;
        if (selector.includes('image_source')) return source;
        if (selector.includes('image_alt')) return { val: () => 'Example image' };
        return empty;
    } };
    const button = { siblings() { return status; }, closest() { return block; }, prop() { return this; } };
    const doc = {};
    function $(object) {
        if (object === doc) return { on: (event, selector, callback) => { events[selector] = callback; } };
        if (object === button) return button;
        if (object === '#post_ID') return { val: () => 4 };
        throw new Error('Unexpected jQuery object: ' + object);
    }
    $.post = () => ({ done(callback) { callback(response); return this; }, fail() { return this; }, always(callback) { callback(); return this; } });
    const acf = withAcf ? { getField: ($field) => {
        assert.equal($field, imageField, 'ACF field lookup must stay inside the clicked block');
        return { render(value) { state.rendered = value; state.id = String(value.id); }, $input: () => input };
    } } : undefined;
    const context = { jQuery: $, document: doc, acfUrlPreview: { ajaxUrl: '/ajax', importNonce: 'test', importingText: 'Importing', successText: 'Done', errorText: 'Error' }, window: { acf }, acf, setTimeout: (callback) => { callback(); return 1; }, clearTimeout() {} };
    vm.runInNewContext(fs.readFileSync(__dirname + '/../blocks/url-preview/admin.js', 'utf8'), context);
    events['.acf-url-preview-import-btn'].call(button, { preventDefault() {} });
    return state;
}

for (const existing of [false, true]) {
    test((existing ? 'existing' : 'new') + ' imported image is assigned and persisted in the originating ACF field', () => {
        const state = importImage({ success: true, data: { attachment_id: 42, url: 'https://site.test/uploads/photo.png', preview_url: 'https://site.test/uploads/photo-300.png', message: existing ? 'Image already imported' : 'Image imported successfully' } }, true);
        assert.equal(state.id, '42');
        assert.equal(state.changed, 1);
        assert.equal(state.rendered.url, 'https://site.test/uploads/photo-300.png');
        assert.equal(state.source, 'local');
        assert(!state.status.includes('Refresh'));
    });
}

test('image field persists its attachment ID without an ACF field instance', () => {
    const state = importImage({ success: true, data: { attachment_id: 42, url: 'https://site.test/uploads/photo.png' } }, false);
    assert.equal(state.id, '42');
    assert.equal(state.changed, 1);
    assert.equal(state.hasImage, true);
});

test('failed import leaves the image field unchanged', () => {
    const state = importImage({ success: false, data: 'Download failed' }, true);
    assert.equal(state.id, '');
    assert.equal(state.changed, 0);
    assert.equal(state.status, 'Download failed');
});

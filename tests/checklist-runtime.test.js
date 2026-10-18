const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

const source = fs.readFileSync(__dirname + '/../blocks/checklist-block/checklist.js', 'utf8');
function checklist(identity, initial = [false, false]) {
    const progress = { style: {}, textContent: '' };
    const boxes = initial.map(checked => {
        const item = { classes: new Set(), classList: { toggle(name, enabled) { enabled ? item.classes.add(name) : item.classes.delete(name); } } };
        return { checked, item, events: {}, closest: () => item, addEventListener(name, callback) { this.events[name] = callback; } };
    });
    return { dataset: { checklistKey: identity }, boxes, progress, querySelectorAll: () => boxes, querySelector: () => progress };
}
function run(blocks, storage = new Map(), pathname = '/article/', denial = '') {
    const keys = [];
    const localStorage = {
        getItem(key) { keys.push(key); if (denial === 'read') throw new Error('Denied'); return storage.get(key) || null; },
        setItem(key, value) { if (denial === 'write') throw new Error('Quota'); storage.set(key, value); }
    };
    const window = { location: { origin: 'https://example.test', pathname } };
    Object.defineProperty(window, 'localStorage', { get() { if (denial === 'getter') throw new Error('Denied getter'); return localStorage; } });
    const document = { readyState: 'complete', querySelectorAll: () => blocks };
    vm.runInNewContext(source, { window, document });
    return { keys, storage };
}
function toggle(block, index = 0, checked = true) {
    block.boxes[index].checked = checked;
    block.boxes[index].events.change();
}

test('persistence survives a rerender and is isolated between pages', () => {
    const first = checklist('persisted-id');
    const { storage } = run([first]);
    toggle(first);
    const reload = checklist('persisted-id');
    run([reload], storage);
    assert.equal(reload.boxes[0].checked, true);
    assert.equal(reload.progress.textContent, '1/2 completed');
    const otherPage = checklist('persisted-id');
    run([otherPage], storage, '/other-article/');
    assert.equal(otherPage.boxes[0].checked, false);
});

test('identical legacy blocks maintain independent states on the same page', () => {
    const first = checklist('content-fingerprint');
    const second = checklist('content-fingerprint');
    const { storage } = run([first, second]);
    toggle(second);
    const reloadFirst = checklist('content-fingerprint');
    const reloadSecond = checklist('content-fingerprint');
    run([reloadFirst, reloadSecond], storage);
    assert.equal(reloadFirst.boxes[0].checked, false);
    assert.equal(reloadSecond.boxes[0].checked, true);
});

for (const denial of ['getter', 'read', 'write']) {
    test('interaction and progress survive storage ' + denial + ' failure', () => {
        const block = checklist('private-window');
        run([block], new Map(), '/article/', denial);
        toggle(block);
        assert.equal(block.progress.style.width, '50%');
        assert.equal(block.progress.textContent, '1/2 completed');
        assert(block.boxes[0].item.classes.has('acf-checklist__item--checked'));
    });
}

test('malformed state and non-boolean values do not change defaults', () => {
    for (const value of ['not json', '{"0":true}', '["false",1]']) {
        const block = checklist('malformed');
        const storage = new Map([['acf-checklist:v2:https://example.test/article/:malformed:0', value]]);
        run([block], storage);
        assert.equal(block.boxes[0].checked, false);
        assert.equal(block.boxes[1].checked, false);
    }
});

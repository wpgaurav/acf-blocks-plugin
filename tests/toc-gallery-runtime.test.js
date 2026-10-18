const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function tocRuntime() {
    const classes = () => ({ contains: name => name === 'acf-toc--highlight-active', remove() {}, add() {} });
    const heading = (id, acf = false) => ({ id, closest: selector => selector === '.acf-toc' ? null : acf ? {} : null });
    const included = heading('intro');
    const excluded = heading('intro-2', true);
    const html = heading('custom-html');
    const synced = heading('synced');
    const headings = [included, excluded, html, synced];
    // An HTML/dynamic heading can appear after the list was built. Existing
    // anchors still must not be rewritten to fill a positional gap.
    const links = ['intro', 'synced'].map(id => ({ getAttribute: () => '#' + id, classList: classes() }));
    const scope = { querySelectorAll: () => headings };
    const toc = {
        dataset: { headingLevels: 'h2', includeAcfHeadings: '0' },
        closest: () => scope,
        querySelectorAll: () => links,
        classList: classes()
    };
    const observed = [];
    class IntersectionObserver {
        constructor(callback) { this.callback = callback; }
        observe(node) { observed.push(node); }
    }
    const document = { readyState: 'complete', querySelectorAll: () => [toc] };
    vm.runInNewContext(fs.readFileSync(process.env.ACF_TOC_SCRIPT || __dirname + '/../blocks/toc-block/toc.js', 'utf8'), { document, window: { IntersectionObserver }, IntersectionObserver });
    return { headings, observed };
}

test('TOC resolves emitted links without rewriting rendered or custom anchors', () => {
    const { headings, observed } = tocRuntime();
    assert.deepEqual(headings.map(node => node.id), ['intro', 'intro-2', 'custom-html', 'synced']);
    assert.deepEqual(observed, [headings[0], headings[3]]);
});

function galleryRuntime(supported = true) {
    const listeners = {};
    const document = { activeElement: null, addEventListener: (name, handler) => { listeners[name] = handler; } };
    const createElement = tag => {
        const node = {
            tagName: tag, children: [], listeners: {}, attributes: {},
            appendChild(child) { this.children.push(child); },
            addEventListener(name, handler) { this.listeners[name] = handler; },
            setAttribute(name, value) { this.attributes[name] = value; },
            removeAttribute(name) { delete this[name]; },
            focus() { document.activeElement = this; }
        };
        if (tag === 'dialog' && supported) {
            node.showModal = () => { node.open = true; };
            node.close = () => { node.open = false; node.listeners.close(); };
        }
        return node;
    };
    document.createElement = createElement;
    document.body = createElement('body');
    vm.runInNewContext(fs.readFileSync(process.env.ACF_GALLERY_SCRIPT || __dirname + '/../blocks/gallery-block/gallery.js', 'utf8'), { document });
    const click = (label = 'One', enabled = true, options = {}) => {
        const gallery = { dataset: { lightboxLabel: 'Image preview', lightboxClose: 'Close image preview' } };
        const link = {
            href: 'https://example.test/' + label + '.jpg', dataset: {}, isConnected: true,
            closest: () => enabled ? gallery : null,
            querySelector: () => ({ alt: label }),
            focus() { document.activeElement = this; }
        };
        const event = { button: 0, target: { closest: () => link }, preventDefault() { this.prevented = true; }, ...options };
        listeners.click(event);
        return { link, event, dialog: document.body.children[0] };
    };
    return { document, click };
}

test('gallery opens an accessible modal with the full image and restores launch focus', () => {
    const { click, document } = galleryRuntime();
    const { link, event, dialog } = click();
    assert.equal(event.prevented, true);
    assert.equal(dialog.open, true);
    assert.equal(dialog.attributes['aria-label'], 'Image preview');
    const [button, image] = dialog.children;
    assert.equal(button.attributes['aria-label'], 'Close image preview');
    assert.equal(image.src, link.href);
    assert.equal(image.alt, 'One');
    assert.equal(document.activeElement, button);
    button.listeners.click();
    assert.equal(dialog.open, false);
    assert.equal(document.activeElement, link);
    assert.equal(image.src, undefined);
});

test('gallery keeps native links without dialog support and ignores disabled or modified clicks', () => {
    const unsupported = galleryRuntime(false).click();
    assert.equal(unsupported.event.prevented, undefined);
    assert.equal(unsupported.dialog, undefined);
    for (const options of [{ ctrlKey: true }, { metaKey: true }, { shiftKey: true }, { altKey: true }, { button: 1 }]) {
        const result = galleryRuntime().click('One', true, options);
        assert.equal(result.event.prevented, undefined);
        assert.equal(result.dialog, undefined);
    }
    const disabled = galleryRuntime().click('One', false);
    assert.equal(disabled.event.prevented, undefined);
    assert.equal(disabled.dialog, undefined);
});

test('delegated gallery handlers reuse the dialog across later inserted blocks', () => {
    const { click, document } = galleryRuntime();
    const first = click();
    first.dialog.close();
    const second = click('Two');
    assert.equal(second.dialog, first.dialog);
    assert.equal(document.body.children.length, 1);
    assert.equal(second.dialog.children[1].alt, 'Two');
    second.dialog.listeners.click({ target: second.dialog });
    assert.equal(second.dialog.open, false);
    assert.equal(document.activeElement, second.link);
});

function standaloneTocRuntime() {
    function node(tag, text = '', classes = '') {
        return {
            tagName: tag.toUpperCase(), className: classes, dataset: {}, id: '', children: [], parent: null, attributes: {}, _text: text,
            classList: { contains: () => false },
            appendChild(child) { this.children.push(child); child.parent = this; },
            setAttribute(name, value) { this.attributes[name] = value; },
            getAttribute(name) { return this.attributes[name]; },
            set textContent(value) { this._text = value; this.children = []; },
            get textContent() { return this._text + this.children.map(child => child.textContent).join(''); },
            closest(selector) {
                for (let current = this; current; current = current.parent) {
                    if (selector.split(',').some(part => part === '.acf-toc' ? current.className.split(' ').includes('acf-toc') : part === '.acf-block' ? current.className.split(' ').includes('acf-block') : false)) return current;
                }
                return null;
            },
            querySelector(selector) { return this.querySelectorAll(selector)[0] || null; },
            querySelectorAll(selector) {
                const descendants = this.children.flatMap(child => [child, ...child.querySelectorAll('*')]);
                return descendants.filter(child => selector === '*' || selector === '[id]' ? selector === '*' || Boolean(child.id) : selector.charAt(0) === '.' ? child.className.split(' ').includes(selector.slice(1)) : selector.split(',').some(part => child.tagName.toLowerCase() === part.replace('[id]', '') && (!part.endsWith('[id]') || child.id)));
            }
        };
    }
    const body = node('body');
    const main = node('main');
    const aside = node('aside');
    body.appendChild(aside); body.appendChild(main);
    const reserved = node('div'); reserved.id = 'intro'; main.appendChild(reserved);
    const intro = node('h2', 'Intro'); main.appendChild(intro);
    const acf = node('section', '', 'acf-block'); main.appendChild(acf);
    const excluded = node('h2', 'Intro'); acf.appendChild(excluded);
    const custom = node('h2', 'Custom'); custom.id = 'intro-2'; main.appendChild(custom);
    const synced = node('h3', 'Synced'); main.appendChild(synced);
    const makeToc = includeAcf => {
        const toc = node('nav', '', 'acf-toc');
        toc.dataset = { tocFinalized: '0', headingLevels: 'h2,h3', includeAcfHeadings: includeAcf ? '1' : '0', listType: includeAcf ? 'plain' : 'ol', listClass: 'sidebar-list', linkClass: 'sidebar-link' };
        toc.appendChild(node('div', '', 'acf-toc__content'));
        aside.appendChild(toc);
        return toc;
    };
    const first = makeToc(false);
    const second = makeToc(true);
    const document = { readyState: 'complete', querySelector: () => main, querySelectorAll: selector => body.querySelectorAll(selector), createElement: tag => node(tag) };
    vm.runInNewContext(fs.readFileSync(process.env.ACF_TOC_SCRIPT || __dirname + '/../blocks/toc-block/toc.js', 'utf8'), { document, window: {} });
    return { first, second, headings: [intro, excluded, custom, synced], reserved };
}

test('standalone sidebar TOCs build rendered lists with shared IDs and per-list options', () => {
    const { first, second, headings, reserved } = standaloneTocRuntime();
    assert.deepEqual(headings.map(heading => heading.id), ['intro-3', 'intro-4', 'intro-2', 'synced']);
    assert.equal(reserved.id, 'intro');
    assert.deepEqual(first.querySelectorAll('.acf-toc__link').map(link => link.getAttribute('href')), ['#intro-3', '#intro-2', '#synced']);
    assert.deepEqual(second.querySelectorAll('.acf-toc__link').map(link => link.getAttribute('href')), ['#intro-3', '#intro-4', '#intro-2', '#synced']);
    assert.equal(first.querySelector('.acf-toc__list').tagName, 'OL');
    assert(first.querySelector('.acf-toc__list').className.includes('sidebar-list'));
    assert(second.querySelector('.acf-toc__list').className.includes('acf-toc__list--plain'));
    assert(first.querySelector('.acf-toc__link').className.includes('sidebar-link'));
    assert.equal(first.dataset.tocFinalized, '1');
    assert.equal(second.dataset.tocFinalized, '1');
});

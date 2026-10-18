/** Optional offline browser regression: NODE_PATH=<playwright runtime> node tests/toc-gallery-browser.js */
const { chromium } = require('playwright');
const { execFileSync } = require('node:child_process');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');

(async () => {
    const fixturePath = path.join(os.tmpdir(), 'acfb-toc-gallery-fixture.html');
    fs.writeFileSync(fixturePath, execFileSync('php', [path.join(__dirname, 'fixtures/toc-gallery-render.php')]));
    const browser = await chromium.launch({ headless: true, channel: process.env.PLAYWRIGHT_BROWSER_CHANNEL || 'chrome' });
    try {
        const page = await browser.newPage({ viewport: { width: 1280, height: 1000 } });
        const errors = [];
        page.on('pageerror', error => errors.push(error.message));
        await page.goto('file://' + fixturePath);
        const inventory = await page.evaluate(() => ({
            ids: Array.from(document.querySelectorAll('[id]'), node => node.id),
            links: Array.from(document.querySelectorAll('#contents .acf-toc__link'), link => ({ id: link.getAttribute('href').slice(1), label: link.textContent, target: document.getElementById(link.getAttribute('href').slice(1)).textContent })),
            included: Array.from(document.querySelectorAll('#including-acf .acf-toc__link'), link => link.getAttribute('href')),
            schema: Array.from(document.querySelectorAll('#contents script[type="application/ld+json"]'), node => JSON.parse(node.textContent).itemListElement),
            theme: document.querySelector('body > h2').id
        }));
        assert.equal(new Set(inventory.ids).size, inventory.ids.length);
        assert.equal(inventory.theme, 'theme-heading');
        assert.deepEqual(inventory.links.map(item => item.id), ['intro-3', 'intro-2', 'html-heading', 'synced-heading']);
        assert(inventory.links.every(item => item.label === item.target));
        assert(inventory.included.includes('#intro-4'));
        assert.deepEqual(inventory.schema[0].map(item => item.url.split('#')[1]), inventory.links.map(item => item.id));
        assert.equal(await page.locator('#contents').getAttribute('data-toc-finalized'), '1');
        assert.equal(await page.locator('#contents script[type="application/ld+json"]').count(), 1);
        assert.equal(await page.locator('#disabled-gallery a').count(), 0);
        await page.locator('#enabled-gallery a').focus();
        await page.keyboard.press('Enter');
        await page.locator('dialog[open]').waitFor();
        assert.equal(await page.locator('dialog img').getAttribute('alt'), 'Blue circle');
        assert.equal(await page.evaluate(() => document.activeElement.className), 'acf-gallery-lightbox__close');
        await page.keyboard.press('Tab');
        assert.equal(await page.evaluate(() => document.activeElement.className), 'acf-gallery-lightbox__close');
        const screenshotPath = path.join(os.tmpdir(), 'acfb-toc-gallery-lightbox.png');
        await page.screenshot({ path: screenshotPath });
        await page.keyboard.press('Escape');
        assert.equal(await page.locator('dialog[open]').count(), 0);
        assert.equal(await page.evaluate(() => document.activeElement.closest('section').id), 'enabled-gallery');
        await page.locator('#second-gallery a').click();
        await page.locator('dialog button').click();
        assert.equal(await page.evaluate(() => document.activeElement.closest('section').id), 'second-gallery');
        await page.evaluate(() => {
            const added = document.querySelector('#enabled-gallery').cloneNode(true);
            added.id = 'dynamic-gallery';
            document.querySelector('article').append(added);
        });
        await page.locator('#dynamic-gallery a').click();
        assert.equal(await page.locator('dialog').count(), 1);
        await page.keyboard.press('Escape');
        assert.equal(await page.evaluate(() => document.activeElement.closest('section').id), 'dynamic-gallery');
        // Standalone/sidebar rendering never reaches the_content. Its pending
        // placeholders must derive links from the already rendered main scope.
        const standalonePath = path.join(os.tmpdir(), 'acfb-toc-gallery-sidebar-fixture.html');
        fs.writeFileSync(standalonePath, execFileSync('php', [path.join(__dirname, 'fixtures/toc-gallery-render.php'), 'standalone']));
        await page.goto('file://' + standalonePath);
        const sidebar = await page.evaluate(() => ({
            ids: Array.from(document.querySelectorAll('[id]'), node => node.id),
            links: Array.from(document.querySelectorAll('#contents .acf-toc__link'), link => ({ id: link.getAttribute('href').slice(1), label: link.textContent, target: document.getElementById(link.getAttribute('href').slice(1)).textContent })),
            included: Array.from(document.querySelectorAll('#including-acf .acf-toc__link'), link => link.getAttribute('href')),
            finalized: Array.from(document.querySelectorAll('.acf-toc'), toc => toc.dataset.tocFinalized),
            schema: Array.from(document.querySelectorAll('#contents script[type="application/ld+json"]'), node => JSON.parse(node.textContent).itemListElement),
            outside: !document.querySelector('#contents').closest('main'),
            ordered: document.querySelector('#contents .acf-toc__list').tagName,
            customList: document.querySelector('#contents .acf-toc__list').classList.contains('sidebar-list'),
            customLink: document.querySelector('#contents .acf-toc__link').classList.contains('sidebar-link'),
            plain: document.querySelector('#including-acf .acf-toc__list').classList.contains('acf-toc__list--plain')
        }));
        assert.equal(new Set(sidebar.ids).size, sidebar.ids.length);
        assert.equal(sidebar.outside, true);
        assert.deepEqual(sidebar.links.map(item => item.id), ['intro-3', 'intro-2', 'html-heading', 'synced-heading']);
        assert(sidebar.links.every(item => item.label === item.target));
        assert(sidebar.included.includes('#intro-4'));
        assert.deepEqual(sidebar.finalized, ['1', '1']);
        assert.deepEqual(sidebar.schema[0].map(item => item.url.split('#')[1]), sidebar.links.map(item => item.id));
        assert.equal(sidebar.ordered, 'OL');
        assert(sidebar.customList && sidebar.customLink && sidebar.plain);
        assert.deepEqual(errors, []);
        console.log(JSON.stringify({ passed: true, fixture: fixturePath, screenshot: screenshotPath, checks: ['unique preserved anchors', 'HTML and synced headings', 'ACF false/true', 'matching schema', 'disabled gallery', 'keyboard activation', 'focus trap', 'Escape and focus restoration', 'multiple galleries', 'dynamic insertion', 'standalone sidebar TOCs', 'shared DOM anchors and custom list settings', 'no runtime errors'] }));
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });

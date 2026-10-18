/** Offline CSS regressions. NODE_PATH=<Playwright modules> node tests/theme-visual-browser.js */
const { chromium } = require('playwright');
const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
const root = path.join(__dirname, '..');
const tokens = fs.readFileSync(path.join(root, 'assets/css/tokens.css'), 'utf8');
const preview = fs.readFileSync(path.join(root, 'blocks/url-preview/url-preview.css'), 'utf8');
(async () => {
 const browser=await chromium.launch({headless:true,channel:process.env.PLAYWRIGHT_BROWSER_CHANNEL||'chrome'});
 const checks=[];
 try {
  const page=await browser.newPage({viewport:{width:390,height:844}});
  async function check(name,fn){try{await fn();checks.push({name,pass:true});}catch(e){checks.push({name,pass:false,error:e.message});}}
  async function scene(palette,scheme){await page.emulateMedia({colorScheme:scheme});await page.setContent(`<style>${tokens}\n${preview}\n:root{${palette}}body{margin:0;color:var(--wp--preset--color--contrast,#4e4e4e);background:var(--wp--preset--color--base,#fff)}.frame{width:320px}.secondary-text{color:var(--acfb-text-sec)}.accent-text{color:var(--acfb-primary)}</style><div class="frame"><section class="acf-block"><h2>Readable heading</h2><p class="secondary-text">Supporting text</p><p class="accent-text">Accent text</p></section><div class="acf-url-preview acf-url-preview--featured acf-block"><div class="acf-url-preview__content"><div class="acf-url-preview__buttons"><a class="acf-url-preview__button" href="#">Primary</a><a class="acf-url-preview__button acf-url-preview__button--secondary" href="#">Secondary</a></div></div></div></div>`);}
  await scene('--wp--preset--color--base:#fff;--wp--preset--color--contrast:#111','dark');
  await check('light WordPress palette wins over dark OS preference',async()=>assert.equal(await page.locator('h2').evaluate(n=>getComputedStyle(n).color),'rgb(17, 17, 17)'));
  const darkBorder=await page.locator('.acf-url-preview').evaluate(n=>getComputedStyle(n).borderTopColor);
  const darkSupporting=await page.locator('.secondary-text').evaluate(n=>getComputedStyle(n).color);
  const darkAccent=await page.locator('.accent-text').evaluate(n=>getComputedStyle(n).color);
  await page.emulateMedia({colorScheme:'light'});
  await check('card border follows the palette rather than the OS',async()=>assert.equal(await page.locator('.acf-url-preview').evaluate(n=>getComputedStyle(n).borderTopColor),darkBorder));
  await check('supporting text follows the palette rather than the OS',async()=>assert.equal(await page.locator('.secondary-text').evaluate(n=>getComputedStyle(n).color),darkSupporting));
  await check('theme accent fallback stays stable across OS preferences',async()=>assert.equal(await page.locator('.accent-text').evaluate(n=>getComputedStyle(n).color),darkAccent));
  await page.emulateMedia({colorScheme:'dark'});
  await check('secondary featured action does not inherit primary gradient',async()=>assert.equal(await page.locator('.acf-url-preview__button--secondary').evaluate(n=>getComputedStyle(n).backgroundImage),'none'));
  await check('preview border stays inside the declared width',async()=>assert((await page.locator('.acf-url-preview').boundingBox()).width<=320));
  await page.locator('.acf-url-preview__button--secondary').hover();
  await check('secondary hover keeps text distinct from fill',async()=>{const s=await page.locator('.acf-url-preview__button--secondary').evaluate(n=>({color:getComputedStyle(n).color,bg:getComputedStyle(n).backgroundColor,image:getComputedStyle(n).backgroundImage}));assert.equal(s.image,'none');assert.notEqual(s.color,s.bg);});
  await scene('--wp--preset--color--base:#111;--wp--preset--color--contrast:#eee','light');
  await check('dark WordPress palette wins over light OS preference',async()=>assert.equal(await page.locator('h2').evaluate(n=>getComputedStyle(n).color),'rgb(238, 238, 238)'));
  await scene('--wp--preset--color--base:#fff;--wp--preset--color--contrast:#111;--color-content-bg:#222;--color-text:#ddd;--color-headline:#eee','dark');
  await check('explicit theme variables retain priority',async()=>assert.equal(await page.locator('h2').evaluate(n=>getComputedStyle(n).color),'rgb(238, 238, 238)'));
  await scene('','dark');
  await check('standalone dark fallback remains available',async()=>assert.equal(await page.locator('h2').evaluate(n=>getComputedStyle(n).color),'rgb(255, 255, 255)'));
 } finally {await browser.close();}
 console.log(JSON.stringify(checks,null,2));
 if(checks.some(c=>!c.pass))process.exitCode=1;
})().catch(e=>{console.error(e);process.exitCode=1;});

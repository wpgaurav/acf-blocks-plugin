'use strict';
const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

async function submit(type, configuration) {
    const events = {};
    const state = { calls: [], submitted: 0, reset: 0 };
    const button = { textContent: 'Subscribe', disabled: false, dataset: {} };
    const success = { hidden: true };
    const error = { hidden: true };
    const form = {
        dataset: { formType: type, configId: 'signup' },
        addEventListener: (event, callback) => { events[event] = callback; },
        removeEventListener() {},
        submit() { state.submitted++; },
        reset() { state.reset++; },
        querySelector: selector => ({ '.acf-submit': button, '.acf-email-form-success': success, '.acf-email-form-error': error })[selector]
    };
    const document = { readyState: 'complete', querySelectorAll: () => [form] };
    const context = {
        window: { acfEmailFormConfigs: configuration ? { signup: configuration } : {} }, document,
        FormData: function () { return { forEach(callback) { callback('reader@example.com', 'email_form_email'); callback('list-a', 'list'); } }; },
        fetch: (url, options) => { state.calls.push({ url, options }); return Promise.resolve({ ok: true }); }
    };
    vm.runInNewContext(fs.readFileSync(__dirname + '/../blocks/email-form/email-form.js', 'utf8'), context);
    events.submit({ preventDefault() {}, target: form });
    await new Promise(resolve => setImmediate(resolve));
    return { ...state, button, success, error };
}

test('webhook submission sends only its stable reference and form fields', async () => {
    const state = await submit('webhook', { formRef: 'signed-source-reference', proxyUrl: '/wp-json/email-form-proxy/v1/submit' });
    assert.equal(state.calls.length, 1);
    assert.equal(state.calls[0].url, '/wp-json/email-form-proxy/v1/submit');
    assert.deepEqual(JSON.parse(state.calls[0].options.body), { formRef: 'signed-source-reference', data: { email_form_email: 'reader@example.com', list: 'list-a' } });
    assert.equal(state.reset, 1);
    assert.equal(state.success.hidden, false);
    assert.equal(state.button.disabled, false);
});

test('standard form action still submits natively', async () => {
    const state = await submit('form_action');
    assert.equal(state.submitted, 1);
    assert.equal(state.calls.length, 0);
});

test('missing webhook configuration displays error and re-enables submission', async () => {
    const state = await submit('webhook');
    assert.equal(state.calls.length, 0);
    assert.equal(state.error.hidden, false);
    assert.equal(state.button.disabled, false);
});

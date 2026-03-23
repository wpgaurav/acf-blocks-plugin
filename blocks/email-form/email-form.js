(function () {
	'use strict';

	function init() {
		var forms = document.querySelectorAll('.acf-email-form[data-form-type]');
		forms.forEach(function (form) {
			if (form.dataset.bound) return;
			form.dataset.bound = '1';
			form.addEventListener('submit', handleSubmit);
		});
	}

	function handleSubmit(event) {
		event.preventDefault();
		var form = event.target;
		var formType = form.dataset.formType;
		var submitBtn = form.querySelector('.acf-submit');
		var successEl = form.querySelector('.acf-email-form-success');
		var errorEl = form.querySelector('.acf-email-form-error');
		var originalText = submitBtn ? submitBtn.textContent : '';

		// Reset messages
		if (successEl) successEl.hidden = true;
		if (errorEl) errorEl.hidden = true;

		// Loading state
		if (submitBtn) {
			submitBtn.disabled = true;
			submitBtn.textContent = submitBtn.dataset.loadingText || 'Sending\u2026';
		}

		if (formType === 'form_action') {
			form.removeEventListener('submit', handleSubmit);
			form.submit();
			return;
		}

		if (formType !== 'webhook') return;

		var formData = new FormData(form);
		var data = {};
		formData.forEach(function (value, key) {
			data[key] = value;
		});

		var configId = form.dataset.configId;
		if (!configId) {
			showError(form, errorEl, submitBtn, originalText, 'Form configuration missing.');
			return;
		}

		var config = (window.acfEmailFormConfigs || {})[configId];
		if (!config) {
			showError(form, errorEl, submitBtn, originalText, 'Form configuration not found.');
			return;
		}

		fetch(config.proxyUrl, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({
				webhookUrl: config.webhookUrl,
				webhookAuthHeaders: config.authHeaders,
				data: data,
			}),
		})
			.then(function (response) {
				if (!response.ok) throw new Error('Server returned ' + response.status);
				if (successEl) {
					successEl.hidden = false;
				}
				form.reset();
				if (submitBtn) {
					submitBtn.disabled = false;
					submitBtn.textContent = originalText;
				}
			})
			.catch(function () {
				showError(
					form,
					errorEl,
					submitBtn,
					originalText,
					form.dataset.errorMessage || 'Something went wrong. Please try again.'
				);
			});
	}

	function showError(form, errorEl, submitBtn, originalText, message) {
		if (errorEl) {
			errorEl.textContent = message;
			errorEl.hidden = false;
		}
		if (submitBtn) {
			submitBtn.disabled = false;
			submitBtn.textContent = originalText;
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();

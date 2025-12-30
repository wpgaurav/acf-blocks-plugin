(function () {
    'use strict';

    function disableForm(form) {
        var inputs = form.querySelectorAll('input, button');
        inputs.forEach(function (input) { input.disabled = true; });
        form.classList.add('acf-star-rating__form--disabled');
    }

    function enableForm(form) {
        var inputs = form.querySelectorAll('input, button');
        inputs.forEach(function (input) { input.disabled = false; });
        form.classList.remove('acf-star-rating__form--disabled');
    }

    function updateStars(container, average) {
        if (!container) return;
        var stars = container.querySelectorAll('.star');
        stars.forEach(function (star, index) {
            var value = index + 1;
            star.classList.remove('filled', 'half');
            if (average >= value) {
                star.classList.add('filled');
            } else if (average >= value - 0.5) {
                star.classList.add('half');
            }
        });
    }

    function updateAggregate(wrapper, data) {
        if (!wrapper) return;
        var averageEl = wrapper.querySelector('.acf-star-rating__average');
        var countEl = wrapper.querySelector('.acf-star-rating__count');
        var starsContainer = wrapper.querySelector('.acf-star-rating__stars');
        if (averageEl) averageEl.textContent = data.averageFormatted;
        if (countEl) countEl.textContent = data.countText;
        updateStars(starsContainer, data.average);
    }

    function getStorageKey(postId, blockId) {
        return 'acfStarRating:' + postId + ':' + blockId;
    }

    function markRated(postId, blockId) {
        try { window.localStorage.setItem(getStorageKey(postId, blockId), '1'); } catch (e) {}
    }

    function hasRated(postId, blockId) {
        try { return window.localStorage.getItem(getStorageKey(postId, blockId)) === '1'; } catch (e) { return false; }
    }

    function showMessage(container, selector, message) {
        if (!container) return;
        var el = container.querySelector(selector);
        if (!el) return;
        el.textContent = message;
        el.hidden = !message;
    }

    function handleSubmit(event) {
        var form = event.target.closest('.acf-star-rating__form');
        if (!form) return;
        event.preventDefault();

        var wrapper = form.closest('.acf-star-rating');
        var ratingInput = form.querySelector('input[name="acf_star_rating"]:checked');
        var blockId = form.dataset.blockId;
        var postId = form.dataset.postId;
        var thankYou = form.dataset.thankYou || '';
        var nonce = form.dataset.nonce;
        var settings = window.acfStarRating || {};

        if (!ratingInput) {
            showMessage(wrapper, '.acf-star-rating__error', form.dataset.error || '');
            return;
        }

        if (!settings.ajaxUrl) return;

        showMessage(wrapper, '.acf-star-rating__error', '');
        disableForm(form);

        var formData = new FormData();
        formData.append('action', 'acf_star_rating_submit');
        formData.append('rating', ratingInput.value);
        formData.append('blockId', blockId);
        formData.append('postId', postId);
        formData.append('nonce', nonce || settings.nonce || '');

        fetch(settings.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: formData })
            .then(function (r) { if (!r.ok) throw r; return r.json(); })
            .then(function (payload) {
                if (!payload || !payload.success) throw payload;
                updateAggregate(wrapper, payload.data);
                showMessage(wrapper, '.acf-star-rating__thank-you', thankYou);
                markRated(postId, blockId);
            })
            .catch(function (error) {
                var msg = settings.errorMessage || 'Error';
                if (error && typeof error.json === 'function') {
                    error.json().then(function (d) {
                        showMessage(wrapper, '.acf-star-rating__error', (d && d.data && d.data.message) || msg);
                    });
                } else {
                    showMessage(wrapper, '.acf-star-rating__error', (error && error.data && error.data.message) || msg);
                }
                enableForm(form);
            });
    }

    function prepareForms() {
        document.querySelectorAll('.acf-star-rating__form').forEach(function (form) {
            var blockId = form.dataset.blockId;
            var postId = form.dataset.postId;
            var wrapper = form.closest('.acf-star-rating');
            var thankYou = form.dataset.thankYou || '';

            if (!postId || postId === '0') { disableForm(form); return; }
            if (hasRated(postId, blockId)) {
                disableForm(form);
                showMessage(wrapper, '.acf-star-rating__thank-you', thankYou);
            }
        });
    }

    document.addEventListener('submit', handleSubmit);
    document.addEventListener('DOMContentLoaded', prepareForms);
})();

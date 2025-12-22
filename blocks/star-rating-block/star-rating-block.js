(function () {
    'use strict';

    function disableForm(form) {
        var inputs = form.querySelectorAll('input, button');
        inputs.forEach(function (input) {
            input.disabled = true;
        });
        form.classList.add('md-star-rating__form--disabled');
    }

    function enableForm(form) {
        var inputs = form.querySelectorAll('input, button');
        inputs.forEach(function (input) {
            input.disabled = false;
        });
        form.classList.remove('md-star-rating__form--disabled');
    }

    function updateStars(container, average) {
        if (!container) {
            return;
        }

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
        if (!wrapper) {
            return;
        }

        var averageEl = wrapper.querySelector('.md-star-rating__average');
        var countEl = wrapper.querySelector('.md-star-rating__count');
        var starsContainer = wrapper.querySelector('.md-star-rating__stars');

        if (averageEl) {
            averageEl.textContent = data.averageFormatted;
        }

        if (countEl) {
            countEl.textContent = data.countText;
        }

        updateStars(starsContainer, data.average);
    }

    function getStorageKey(postId, blockId) {
        return 'mdStarRating:' + postId + ':' + blockId;
    }

    function markRated(postId, blockId) {
        try {
            window.localStorage.setItem(getStorageKey(postId, blockId), '1');
        } catch (err) {
            // Ignore storage issues (private browsing, etc.).
        }
    }

    function hasRated(postId, blockId) {
        try {
            return window.localStorage.getItem(getStorageKey(postId, blockId)) === '1';
        } catch (err) {
            return false;
        }
    }

    function showMessage(container, selector, message) {
        if (!container) {
            return;
        }
        var el = container.querySelector(selector);
        if (!el) {
            return;
        }
        el.textContent = message;
        el.hidden = !message;
    }

    function handleSubmit(event) {
        var form = event.target.closest('.md-star-rating__form');
        if (!form) {
            return;
        }

        event.preventDefault();

        var wrapper = form.closest('.md-star-rating');
        var ratingInput = form.querySelector('input[name="md_star_rating"]:checked');
        var blockId = form.getAttribute('data-block-id');
        var postId = form.getAttribute('data-post-id');
        var thankYou = form.getAttribute('data-thank-you') || '';
        var nonce = form.getAttribute('data-nonce');

        if (!ratingInput) {
            showMessage(wrapper, '.md-star-rating__error', form.getAttribute('data-error') || '');
            return;
        }

        var settings = window.mdStarRating || {};

        if (!settings.ajaxUrl) {
            return;
        }

        showMessage(wrapper, '.md-star-rating__error', '');
        disableForm(form);

        var formData = new window.FormData();
        formData.append('action', 'md_star_rating_submit');
        formData.append('rating', ratingInput.value);
        formData.append('blockId', blockId);
        formData.append('postId', postId);
        formData.append('nonce', nonce || settings.nonce || '');

        window.fetch(settings.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData,
        })
            .then(function (response) {
                if (!response.ok) {
                    throw response;
                }
                return response.json();
            })
            .then(function (payload) {
                if (!payload || !payload.success) {
                    throw payload;
                }
                updateAggregate(wrapper, payload.data);
                showMessage(wrapper, '.md-star-rating__thank-you', thankYou);
                markRated(postId, blockId);
            })
            .catch(function (error) {
                if (error && typeof error.json === 'function') {
                    error.json().then(function (data) {
                        if (data && data.data && data.data.message) {
                            showMessage(wrapper, '.md-star-rating__error', data.data.message);
                        } else {
                            showMessage(wrapper, '.md-star-rating__error', settings.errorMessage || '');
                        }
                    });
                } else if (error && error.data && error.data.message) {
                    showMessage(wrapper, '.md-star-rating__error', error.data.message);
                } else {
                    showMessage(wrapper, '.md-star-rating__error', settings.errorMessage || '');
                }
                enableForm(form);
            });
    }

    function prepareForms() {
        var forms = document.querySelectorAll('.md-star-rating__form');
        if (!forms.length) {
            return;
        }

        forms.forEach(function (form) {
            var blockId = form.getAttribute('data-block-id');
            var postId = form.getAttribute('data-post-id');
            var wrapper = form.closest('.md-star-rating');
            var thankYou = form.getAttribute('data-thank-you') || '';

            if (!postId || postId === '0') {
                disableForm(form);
                return;
            }

            if (hasRated(postId, blockId)) {
                disableForm(form);
                showMessage(wrapper, '.md-star-rating__thank-you', thankYou);
            }
        });
    }

    document.addEventListener('submit', handleSubmit);
    document.addEventListener('DOMContentLoaded', prepareForms);
})();

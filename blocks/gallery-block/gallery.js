(function() {
    'use strict';

    var dialog;
    var image;
    var closeButton;
    var launcher;

    function createLightbox() {
        var element = document.createElement('dialog');
        // Keep the full-size image link's normal behavior in older browsers.
        if (typeof element.showModal !== 'function') return false;
        element.className = 'acf-gallery-lightbox';
        closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'acf-gallery-lightbox__close';
        closeButton.textContent = '\u00d7';
        image = document.createElement('img');
        image.className = 'acf-gallery-lightbox__image';
        element.appendChild(closeButton);
        element.appendChild(image);
        closeButton.addEventListener('click', function() { element.close(); });
        element.addEventListener('click', function(event) {
            if (event.target === element) element.close();
        });
        element.addEventListener('keydown', function(event) {
            // The close button is the dialog's only interactive control. Keep
            // Tab/Shift+Tab there instead of letting focus reach browser chrome.
            if (event.key === 'Tab') {
                event.preventDefault();
                closeButton.focus();
            }
        });
        element.addEventListener('close', function() {
            image.removeAttribute('src');
            if (launcher && launcher.isConnected) launcher.focus();
            launcher = null;
        });
        // Native modal dialogs trap focus and close on Escape. The close event
        // restores the exact launch link, including in later-inserted galleries.
        document.body.appendChild(element);
        dialog = element;
        return true;
    }

    document.addEventListener('click', function(event) {
        if (event.defaultPrevented || (typeof event.button === 'number' && event.button !== 0) || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        var link = event.target && event.target.closest ? event.target.closest('a.acf-gallery-link[data-lightbox]') : null;
        if (!link) return;
        var gallery = link.closest('.acf-gallery-has-lightbox');
        if (!gallery || (!dialog && !createLightbox())) return;
        var thumbnail = link.querySelector('img');
        image.src = link.href;
        image.alt = thumbnail ? thumbnail.alt : link.dataset.title || '';
        dialog.setAttribute('aria-label', gallery.dataset.lightboxLabel || 'Image preview');
        closeButton.setAttribute('aria-label', gallery.dataset.lightboxClose || 'Close image preview');
        launcher = link;
        try {
            dialog.showModal();
        } catch (error) {
            launcher = null;
            return;
        }
        event.preventDefault();
        closeButton.focus();
    });
})();

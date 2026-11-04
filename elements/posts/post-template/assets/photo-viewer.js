(function () {
    'use strict';
    function initialize() {
        document.querySelectorAll('.pwe-photo-viewer').forEach(function (dialog) {
            if (dialog.dataset.initialized || typeof dialog.showModal !== 'function') return;
            const root = dialog.previousElementSibling;
            if (!root || !root.matches('.pwe-post-summary, .pwe-post-medal-ceremony')) return;
            const photos = Array.from(root.querySelectorAll('.pwe-post-summary__gallery-item img, .pwe-post-medal-ceremony__gallery img'));
            if (!photos.length) return;
            dialog.dataset.initialized = 'true';
            if (root.matches('.pwe-post-medal-ceremony')) dialog.classList.add('pwe-photo-viewer--medal');
            const image = dialog.querySelector('.pwe-photo-viewer__image');
            const counter = dialog.querySelector('.pwe-photo-viewer__counter');
            const caption = dialog.querySelector('.pwe-photo-viewer__caption');
            const previous = dialog.querySelector('[data-photo-previous]');
            const next = dialog.querySelector('[data-photo-next]');
            const close = dialog.querySelector('[data-photo-close]');
            let index = 0;
            let trigger = null;
            let oldOverflow = '';
            function display(position) {
                index = (position + photos.length) % photos.length;
                const photo = photos[index];
                image.src = photo.currentSrc || photo.src;
                image.alt = photo.alt || '';
                caption.textContent = photo.alt || '';
                caption.hidden = !photo.alt;
                counter.textContent = (index + 1) + ' / ' + photos.length;
                previous.hidden = next.hidden = photos.length < 2;
            }
            photos.forEach(function (photo, position) {
                const link = photo.closest('a');
                const target = link || photo;
                target.classList.add('pwe-photo-trigger');
                if (!link) {
                    target.tabIndex = 0;
                    target.setAttribute('role', 'button');
                    target.setAttribute('aria-label', dialog.dataset.openLabel + (photo.alt ? ': ' + photo.alt : ''));
                }
                target.setAttribute('aria-haspopup', 'dialog');
                function open(event) {
                    if (event.type === 'click' && (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey)) return;
                    event.preventDefault();
                    trigger = target;
                    display(position);
                    oldOverflow = document.body.style.overflow;
                    dialog.showModal();
                    document.body.style.overflow = 'hidden';
                    close.focus();
                }
                target.addEventListener('click', open);
                target.addEventListener('keydown', function (event) {
                    if (event.key === ' ' || (!link && event.key === 'Enter')) open(event);
                });
            });
            close.addEventListener('click', function () { dialog.close(); });
            previous.addEventListener('click', function () { display(index - 1); });
            next.addEventListener('click', function () { display(index + 1); });
            dialog.addEventListener('click', function (event) { if (event.target === dialog) dialog.close(); });
            dialog.addEventListener('keydown', function (event) {
                if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
                    event.preventDefault();
                    display(index + (event.key === 'ArrowRight' ? 1 : -1));
                }
            });
            dialog.addEventListener('close', function () {
                document.body.style.overflow = oldOverflow;
                image.removeAttribute('src');
                if (trigger && trigger.isConnected) trigger.focus({preventScroll: true});
            });
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize);
    else initialize();
})();

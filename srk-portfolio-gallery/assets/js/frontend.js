(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    ready(function () {
        document.querySelectorAll('.tpg-portfolio-wrap').forEach(function (wrap) {
            var filters = wrap.querySelectorAll('.tpg-filter');
            var cards = wrap.querySelectorAll('.tpg-card');

            function applyFilter(button) {
                var filter = button.getAttribute('data-filter');

                filters.forEach(function (item) {
                    item.classList.remove('is-active');
                });
                button.classList.add('is-active');

                cards.forEach(function (card) {
                    var categories = (card.getAttribute('data-categories') || '').split(/\s+/);
                    var show = filter === 'all' || categories.indexOf(filter) !== -1;
                    card.classList.toggle('is-hidden', !show);
                });
            }

            filters.forEach(function (button) {
                button.addEventListener('click', function () {
                    applyFilter(button);
                });
            });

            var initialFilter = wrap.querySelector('.tpg-filter.is-active');
            if (initialFilter) {
                applyFilter(initialFilter);
            }
        });

        var lightbox = document.querySelector('.tpg-lightbox');
        if (!lightbox) {
            return;
        }

        var lightboxImage = lightbox.querySelector('.tpg-lightbox-image');
        var lightboxCaption = lightbox.querySelector('.tpg-lightbox-caption');
        var closeButton = lightbox.querySelector('.tpg-lightbox-close');
        var prevButton = lightbox.querySelector('.tpg-lightbox-prev');
        var nextButton = lightbox.querySelector('.tpg-lightbox-next');
        var currentItems = [];
        var currentIndex = 0;

        function collectGroup(trigger) {
            var group = trigger.getAttribute('data-lightbox-group');
            if (group) {
                return Array.prototype.slice.call(document.querySelectorAll('.tpg-open-lightbox[data-lightbox-group="' + group + '"]'));
            }

            var container = trigger.closest('.tpg-portfolio-wrap') || document;
            return Array.prototype.slice.call(container.querySelectorAll('.tpg-open-lightbox'));
        }

        function renderCurrent() {
            if (!currentItems.length) {
                return;
            }

            var current = currentItems[currentIndex];
            lightboxImage.src = current.getAttribute('data-lightbox-src') || '';
            lightboxImage.alt = current.getAttribute('data-lightbox-title') || '';
            lightboxCaption.textContent = current.getAttribute('data-lightbox-title') || '';

            var hasMany = currentItems.length > 1;
            prevButton.style.display = hasMany ? '' : 'none';
            nextButton.style.display = hasMany ? '' : 'none';
        }

        function openLightbox(trigger) {
            currentItems = collectGroup(trigger);
            currentIndex = Math.max(0, currentItems.indexOf(trigger));
            renderCurrent();
            lightbox.classList.add('is-open');
            lightbox.setAttribute('aria-hidden', 'false');
            document.documentElement.classList.add('tpg-lightbox-open');
            closeButton.focus();
        }

        function closeLightbox() {
            lightbox.classList.remove('is-open');
            lightbox.setAttribute('aria-hidden', 'true');
            document.documentElement.classList.remove('tpg-lightbox-open');
            lightboxImage.src = '';
        }

        function previous() {
            if (!currentItems.length) return;
            currentIndex = (currentIndex - 1 + currentItems.length) % currentItems.length;
            renderCurrent();
        }

        function next() {
            if (!currentItems.length) return;
            currentIndex = (currentIndex + 1) % currentItems.length;
            renderCurrent();
        }

        document.addEventListener('click', function (event) {
            var trigger = event.target.closest('.tpg-open-lightbox');
            if (trigger) {
                event.preventDefault();
                openLightbox(trigger);
            }
        });

        closeButton.addEventListener('click', closeLightbox);
        prevButton.addEventListener('click', previous);
        nextButton.addEventListener('click', next);

        lightbox.addEventListener('click', function (event) {
            if (event.target === lightbox) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (!lightbox.classList.contains('is-open')) {
                return;
            }

            if (event.key === 'Escape') {
                closeLightbox();
            } else if (event.key === 'ArrowLeft') {
                previous();
            } else if (event.key === 'ArrowRight') {
                next();
            }
        });
    });
})();

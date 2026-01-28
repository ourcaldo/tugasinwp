/**
 * Archive AJAX Filter
 * Handles category/taxonomy filtering without page reload
 * Option A: No URL change (SEO friendly - original URLs stay intact)
 * 
 * @package TugasinWP
 * @since 2.5.0
 */

(function () {
    'use strict';

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function () {
        initArchiveFilter();
    });

    function initArchiveFilter() {
        var filterContainer = document.querySelector('.archive-filter-container');
        if (!filterContainer) {
            return;
        }

        var filterButtons = filterContainer.querySelectorAll('.archive-filter-btn');
        var gridContainer = document.getElementById('archive-grid');
        var paginationContainer = document.getElementById('archive-pagination');

        if (!gridContainer) {
            return;
        }

        // Get archive settings from data attributes
        var postType = filterContainer.dataset.postType || 'post';
        var taxonomy = filterContainer.dataset.taxonomy || 'category';

        filterButtons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var termId = this.dataset.termId || '';

                // Update active state
                filterButtons.forEach(function (b) {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline');
                });
                this.classList.remove('btn-outline');
                this.classList.add('btn-primary');

                // Show loading state with skeleton loaders
                gridContainer.classList.add('loading');
                showSkeletonLoaders(gridContainer, 6);

                // Make AJAX request
                fetchFilteredContent(postType, taxonomy, termId, 1, gridContainer, paginationContainer);
            });
        });

        // Handle pagination clicks
        if (paginationContainer) {
            paginationContainer.addEventListener('click', function (e) {
                var link = e.target.closest('.pagination-link');
                if (!link) return;

                e.preventDefault();
                e.stopPropagation();

                var page = link.dataset.page || 1;
                var activeBtn = filterContainer.querySelector('.archive-filter-btn.btn-primary');
                var termId = activeBtn ? (activeBtn.dataset.termId || '') : '';

                gridContainer.classList.add('loading');
                fetchFilteredContent(postType, taxonomy, termId, page, gridContainer, paginationContainer);
            });
        }
    }

    function fetchFilteredContent(postType, taxonomy, termId, page, gridContainer, paginationContainer) {
        // Check if tugasinAjax is defined
        if (typeof tugasinAjax === 'undefined') {
            gridContainer.classList.remove('loading');
            return;
        }

        var formData = new FormData();
        formData.append('action', 'tugasin_filter_archive');
        formData.append('post_type', postType);
        formData.append('taxonomy', taxonomy);
        formData.append('term_id', termId);
        formData.append('paged', page);
        formData.append('nonce', tugasinAjax.nonce);

        fetch(tugasinAjax.ajax_url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(function (data) {
                if (data.success && data.data) {
                    // Update grid content
                    if (data.data.html) {
                        gridContainer.innerHTML = data.data.html;
                    } else {
                        gridContainer.innerHTML = '<div class="archive-no-results"><p>' + tugasinAjax.i18n.noResults + '</p></div>';
                    }

                    // Update pagination
                    if (paginationContainer && typeof data.data.pagination !== 'undefined') {
                        paginationContainer.innerHTML = data.data.pagination;
                    }

                    // Scroll to grid with offset for header
                    var headerOffset = 100;
                    var elementPosition = gridContainer.getBoundingClientRect().top;
                    var offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }

                // Remove loading state
                gridContainer.classList.remove('loading');
            })
            .catch(function (error) {
                gridContainer.classList.remove('loading');
                gridContainer.innerHTML = '<div class="archive-no-results"><p>' + tugasinAjax.i18n.loadError + '</p></div>';
            });
    }

    /**
     * Show skeleton loaders during AJAX loading
     * @param {HTMLElement} container - The grid container
     * @param {number} count - Number of skeleton cards to show
     */
    function showSkeletonLoaders(container, count) {
        var skeletonHTML = '';
        for (var i = 0; i < count; i++) {
            skeletonHTML += '<div class="skeleton-card">' +
                '<div class="skeleton-image"></div>' +
                '<div class="skeleton-content">' +
                '<div class="skeleton-line short"></div>' +
                '<div class="skeleton-line"></div>' +
                '<div class="skeleton-line medium"></div>' +
                '</div>' +
                '</div>';
        }
        container.innerHTML = skeletonHTML;
    }
})();

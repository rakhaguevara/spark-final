/**
 * Page Loader - Handles loading overlay during page transitions
 */

(function () {
    'use strict';

    // Hide loader when page is fully loaded
    window.addEventListener('load', function () {
        const loader = document.querySelector('.page-loader');
        if (loader) {
            // Add fade-out class
            loader.classList.add('fade-out');

            // Remove from DOM after animation
            setTimeout(function () {
                loader.remove();
            }, 300);
        }

        // Mark body as loaded
        document.body.classList.add('loaded');
    });

    // Show loader before navigating to another page
    document.addEventListener('DOMContentLoaded', function () {
        // Find all internal navigation links
        const navLinks = document.querySelectorAll('a[href*=".php"]');

        navLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                // Check if it's an internal link (not external or #)
                const href = link.getAttribute('href');
                if (href && !href.startsWith('#') && !href.startsWith('http') && !link.hasAttribute('target')) {
                    // Show loader
                    showLoader();
                }
            });
        });
    });

    function showLoader() {
        // Check if loader already exists
        if (document.querySelector('.page-loader')) {
            return;
        }

        // Create loader element
        const loader = document.createElement('div');
        loader.className = 'page-loader';
        loader.innerHTML = `
            <div class="loader-content">
                <div class="loader-logo">
                    <img src="${window.BASEURL || '..'}/assets/img/logo.png" alt="SPARK">
                    <span class="loader-logo-text">SPARK</span>
                </div>
                <div class="loader-spinner"></div>
                <div class="loader-text">Loading...</div>
            </div>
        `;

        document.body.appendChild(loader);

        // Force reflow to trigger animation
        loader.offsetHeight;
    }
})();

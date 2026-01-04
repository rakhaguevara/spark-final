/**
 * SIDEBAR TOGGLE FUNCTIONALITY
 * Handles collapse/expand (Desktop) and Slide-in (Mobile)
 */

(function () {
    'use strict';

    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const mobileBtn = document.getElementById('mobileMenuBtn'); // New Mobile Button
    const STORAGE_KEY = 'spark_sidebar_collapsed';

    // Initialize sidebar state from localStorage (Desktop only)
    function initSidebar() {
        const isCollapsed = localStorage.getItem(STORAGE_KEY) === 'true';
        if (isCollapsed && sidebar && window.innerWidth > 768) {
            sidebar.classList.add('collapsed');
        }
    }

    // Toggle sidebar state (Desktop)
    function toggleSidebar() {
        if (!sidebar) return;
        sidebar.classList.toggle('collapsed');
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem(STORAGE_KEY, isCollapsed);
    }

    // Toggle mobile sidebar
    function toggleMobileMenu() {
        if (!sidebar) return;
        sidebar.classList.toggle('mobile-open');

        // Optional: Toggle icon state
        const icon = mobileBtn.querySelector('i');
        if (icon) {
            if (sidebar.classList.contains('mobile-open')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 &&
            sidebar &&
            sidebar.classList.contains('mobile-open') &&
            !sidebar.contains(e.target) &&
            !mobileBtn.contains(e.target)) {

            toggleMobileMenu();
        }
    });

    // Event listeners
    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleSidebar);
    }

    if (mobileBtn) {
        mobileBtn.addEventListener('click', toggleMobileMenu);
    }

    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebar);
    } else {
        initSidebar();
    }
})();

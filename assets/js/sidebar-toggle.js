/**
 * SIDEBAR TOGGLE FUNCTIONALITY
 * Handles collapse/expand with localStorage persistence
 * Adjusts dashboard container for proper layout
 */

(function () {
    'use strict';

    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const dashboardContainer = document.querySelector('.dashboard-container');
    const STORAGE_KEY = 'spark_sidebar_collapsed';

    // Initialize sidebar state from localStorage
    function initSidebar() {
        const isCollapsed = localStorage.getItem(STORAGE_KEY) === 'true';
        if (isCollapsed && sidebar) {
            sidebar.classList.add('collapsed');
        }
    }

    // Toggle sidebar state
    function toggleSidebar() {
        if (!sidebar) return;

        sidebar.classList.toggle('collapsed');
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem(STORAGE_KEY, isCollapsed);
    }

    // Event listeners
    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleSidebar);
    }

    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebar);
    } else {
        initSidebar();
    }
})();

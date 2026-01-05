/**
 * NOTIFICATION BADGE UPDATER
 * Updates notification badge count in navbar across all pages
 */

(function () {
    'use strict';

    const BASEURL = window.BASEURL || '..';

    /**
     * Fetch and update notification badge
     */
    function updateNotificationBadge() {
        const badge = document.getElementById('notificationBadge');
        if (!badge) return;

        fetch(BASEURL + '/api/notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.unread_count > 0) {
                    badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error fetching notification count:', error);
            });
    }

    // Update on page load
    document.addEventListener('DOMContentLoaded', updateNotificationBadge);

    // Update every 30 seconds
    setInterval(updateNotificationBadge, 30000);
})();

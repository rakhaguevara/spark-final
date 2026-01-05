/**
 * NOTIFICATIONS PAGE INTERACTIONS
 * Handles loading, displaying, and managing notifications
 */

(function () {
    'use strict';

    const BASEURL = window.BASEURL || '..';

    // DOM Elements
    const loadingEl = document.getElementById('notificationsLoading');
    const containerEl = document.getElementById('notificationsContainer');
    const emptyStateEl = document.getElementById('emptyState');
    const markAllBtn = document.getElementById('markAllReadBtn');
    const notificationBadge = document.getElementById('notificationBadge');

    // Groups
    const todayGroup = document.getElementById('todayGroup');
    const yesterdayGroup = document.getElementById('yesterdayGroup');
    const earlierGroup = document.getElementById('earlierGroup');
    const todayList = document.getElementById('todayList');
    const yesterdayList = document.getElementById('yesterdayList');
    const earlierList = document.getElementById('earlierList');

    let currentNotifications = null;
    let unreadCount = 0;

    /**
     * Load notifications from API
     */
    function loadNotifications() {
        showLoading();

        fetch(BASEURL + '/api/notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentNotifications = data.notifications;
                    unreadCount = data.unread_count;

                    displayNotifications(data.notifications);
                    updateUnreadBadge(data.unread_count);

                    // Enable mark all button if there are unread notifications
                    if (data.unread_count > 0) {
                        markAllBtn.disabled = false;
                    }
                } else {
                    showError('Failed to load notifications');
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                showError('Network error');
            })
            .finally(() => {
                hideLoading();
            });
    }

    /**
     * Display notifications grouped by date
     */
    function displayNotifications(notifications) {
        // Clear existing
        todayList.innerHTML = '';
        yesterdayList.innerHTML = '';
        earlierList.innerHTML = '';

        // Hide all groups initially
        todayGroup.style.display = 'none';
        yesterdayGroup.style.display = 'none';
        earlierGroup.style.display = 'none';

        // Check if empty
        const totalCount = notifications.today.length +
            notifications.yesterday.length +
            notifications.earlier.length;

        if (totalCount === 0) {
            containerEl.style.display = 'none';
            emptyStateEl.style.display = 'block';
            return;
        }

        containerEl.style.display = 'block';
        emptyStateEl.style.display = 'none';

        // Display each group
        if (notifications.today.length > 0) {
            todayGroup.style.display = 'block';
            notifications.today.forEach(notif => {
                todayList.appendChild(createNotificationCard(notif));
            });
        }

        if (notifications.yesterday.length > 0) {
            yesterdayGroup.style.display = 'block';
            notifications.yesterday.forEach(notif => {
                yesterdayList.appendChild(createNotificationCard(notif));
            });
        }

        if (notifications.earlier.length > 0) {
            earlierGroup.style.display = 'block';
            notifications.earlier.forEach(notif => {
                earlierList.appendChild(createNotificationCard(notif));
            });
        }
    }

    /**
     * Create notification card element
     */
    function createNotificationCard(notif) {
        const card = document.createElement('div');
        card.className = 'notification-card';
        card.dataset.notifId = notif.id_notif;

        if (notif.is_read === '0') {
            card.classList.add('unread');
        } else {
            card.classList.add('read');
        }

        // Left indicator
        const indicator = document.createElement('div');
        indicator.className = `notification-indicator ${notif.type}`;
        card.appendChild(indicator);

        // Unread dot (only for unread)
        if (notif.is_read === '0') {
            const dot = document.createElement('div');
            dot.className = 'notification-unread-dot';
            card.appendChild(dot);
        }

        // Content
        const content = document.createElement('div');
        content.className = 'notification-content';

        const title = document.createElement('h3');
        title.className = 'notification-title';
        title.textContent = notif.judul;
        content.appendChild(title);

        const message = document.createElement('p');
        message.className = 'notification-message';
        message.textContent = notif.pesan;
        content.appendChild(message);

        card.appendChild(content);

        // Time
        const time = document.createElement('div');
        time.className = 'notification-time';
        time.textContent = notif.time_ago;
        card.appendChild(time);

        // Click handler
        card.addEventListener('click', () => {
            handleNotificationClick(notif.id_notif, card);
        });

        return card;
    }

    /**
     * Handle notification click
     */
    function handleNotificationClick(notifId, cardEl) {
        // If already read, do nothing
        if (cardEl.classList.contains('read')) {
            return;
        }

        // Mark as read
        fetch(BASEURL + '/api/mark-notification-read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'notif_id=' + notifId
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI
                    cardEl.classList.remove('unread');
                    cardEl.classList.add('read');

                    // Remove unread dot
                    const dot = cardEl.querySelector('.notification-unread-dot');
                    if (dot) {
                        dot.remove();
                    }

                    // Update badge
                    unreadCount = data.unread_count;
                    updateUnreadBadge(data.unread_count);

                    // Disable mark all button if no more unread
                    if (data.unread_count === 0) {
                        markAllBtn.disabled = true;
                    }
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
    }

    /**
     * Mark all notifications as read
     */
    function markAllAsRead() {
        markAllBtn.disabled = true;
        markAllBtn.textContent = 'Marking...';

        fetch(BASEURL + '/api/mark-all-notifications-read.php', {
            method: 'POST'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update all cards
                    document.querySelectorAll('.notification-card.unread').forEach(card => {
                        card.classList.remove('unread');
                        card.classList.add('read');

                        const dot = card.querySelector('.notification-unread-dot');
                        if (dot) {
                            dot.remove();
                        }
                    });

                    // Update badge
                    unreadCount = 0;
                    updateUnreadBadge(0);

                    markAllBtn.textContent = 'Mark all as read';
                } else {
                    markAllBtn.disabled = false;
                    markAllBtn.textContent = 'Mark all as read';
                    alert('Failed to mark all as read');
                }
            })
            .catch(error => {
                console.error('Error marking all as read:', error);
                markAllBtn.disabled = false;
                markAllBtn.textContent = 'Mark all as read';
            });
    }

    /**
     * Update unread badge
     */
    function updateUnreadBadge(count) {
        if (count > 0) {
            notificationBadge.textContent = count > 99 ? '99+' : count;
            notificationBadge.style.display = 'block';
        } else {
            notificationBadge.style.display = 'none';
        }
    }

    /**
     * Show loading state
     */
    function showLoading() {
        loadingEl.style.display = 'block';
        containerEl.style.display = 'none';
        emptyStateEl.style.display = 'none';
    }

    /**
     * Hide loading state
     */
    function hideLoading() {
        loadingEl.style.display = 'none';
    }

    /**
     * Show error message
     */
    function showError(message) {
        emptyStateEl.querySelector('h3').textContent = 'Error';
        emptyStateEl.querySelector('p').textContent = message;
        emptyStateEl.style.display = 'block';
        containerEl.style.display = 'none';
    }

    // Event Listeners
    markAllBtn.addEventListener('click', markAllAsRead);

    // Load notifications on page load
    document.addEventListener('DOMContentLoaded', loadNotifications);
})();

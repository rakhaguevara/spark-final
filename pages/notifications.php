<?php
/**
 * NOTIFICATIONS PAGE
 * Displays user-specific notifications
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/auth.php';

// Start session
startSession();

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ' . BASEURL . '/pages/login.php');
    exit;
}

// Get user
$user = getCurrentUser();

// Helper function to get first 2 words of name
function getShortName($fullName) {
    $words = explode(' ', trim($fullName));
    $shortName = implode(' ', array_slice($words, 0, 2));
    return $shortName;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Notifications | SPARK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" type="image/png" href="<?= BASEURL ?>/assets/img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/loading-overlay.css">
    <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/dashboard-user.css">
    <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/notifications.css">
</head>

<body>
    <!-- LOADING OVERLAY -->
    <div class="page-loader">
        <div class="loader-content">
            <div class="loader-logo">
                <img src="<?= BASEURL ?>/assets/img/logo.png" alt="SPARK">
                <span class="loader-logo-text">SPARK</span>
            </div>
            <div class="loader-spinner"></div>
            <div class="loader-text">Loading...</div>
        </div>
    </div>

    <!-- NAVBAR -->
    <nav class="dashboard-navbar">
        <a href="<?= BASEURL ?>/pages/dashboard.php" class="brand-wrapper">
            <img src="<?= BASEURL ?>/assets/img/logo.png" alt="Spark Logo">
            SPARK
        </a>

        <div class="search-bar">
            <input type="text" placeholder="Search notifications...">
        </div>

        <div class="user-actions">
            <button class="icon-btn" title="Notifications" style="opacity: 0.5; cursor: default;">
                <i class="fas fa-bell"></i>
                <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
            </button>
            <div class="profile-chip">
                <div class="profile-avatar">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="<?= BASEURL ?>/uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <?= strtoupper(substr($user['nama_pengguna'] ?? 'U', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <span><?= htmlspecialchars(getShortName($user['nama_pengguna'] ?? 'User')) ?></span>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTAINER -->
    <div class="dashboard-container">
        <!-- SIDEBAR -->
        <aside class="dashboard-sidebar" id="sidebar">
            <button class="sidebar-toggle-edge" id="sidebarToggle" aria-label="Toggle Sidebar">
                <i class="fas fa-chevron-left"></i>
            </button>

            <nav class="sidebar-nav">
                <ul class="sidebar-menu">
                    <li>
                        <a href="<?= BASEURL ?>/pages/dashboard.php" data-tooltip="Find Parking">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>Find Parking</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASEURL ?>/pages/my-ticket.php" data-tooltip="My Ticket">
                            <i class="fas fa-ticket-alt"></i>
                            <span>My Ticket</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASEURL ?>/pages/history.php" data-tooltip="History">
                            <i class="fas fa-history"></i>
                            <span>History</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASEURL ?>/pages/wallet.php" data-tooltip="Wallet">
                            <i class="fas fa-wallet"></i>
                            <span>Wallet</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-bottom">
                <a href="<?= BASEURL ?>/pages/profile.php" data-tooltip="Settings">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <a href="<?= BASEURL ?>/logout.php" class="logout-link" data-tooltip="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="notifications-main">
            <div class="notifications-header">
                <div>
                    <h1>Notifications</h1>
                    <p>Stay up to date with your parking activity</p>
                </div>
                <button class="mark-all-read-btn" id="markAllReadBtn" disabled>
                    Mark all as read
                </button>
            </div>

            <!-- Loading Skeleton -->
            <div id="notificationsLoading">
                <div class="notification-skeleton">
                    <div class="skeleton-dot"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-title"></div>
                        <div class="skeleton-message"></div>
                    </div>
                </div>
                <div class="notification-skeleton">
                    <div class="skeleton-dot"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-title"></div>
                        <div class="skeleton-message"></div>
                    </div>
                </div>
                <div class="notification-skeleton">
                    <div class="skeleton-dot"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-title"></div>
                        <div class="skeleton-message"></div>
                    </div>
                </div>
            </div>

            <!-- Notifications Container -->
            <div id="notificationsContainer" style="display: none;">
                <!-- Today -->
                <div class="notification-group" id="todayGroup" style="display: none;">
                    <h2>Today</h2>
                    <div class="notification-list" id="todayList"></div>
                </div>

                <!-- Yesterday -->
                <div class="notification-group" id="yesterdayGroup" style="display: none;">
                    <h2>Yesterday</h2>
                    <div class="notification-list" id="yesterdayList"></div>
                </div>

                <!-- Earlier -->
                <div class="notification-group" id="earlierGroup" style="display: none;">
                    <h2>Earlier</h2>
                    <div class="notification-list" id="earlierList"></div>
                </div>
            </div>

            <!-- Empty State -->
            <div class="notifications-empty" id="emptyState" style="display: none;">
                <div class="notifications-empty-icon">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <h3>You're all caught up 🎉</h3>
                <p>No notifications to show right now</p>
            </div>
        </main>
    </div>

    <script>
        window.BASEURL = '<?= BASEURL ?>';
    </script>
    <script src="<?= BASEURL ?>/assets/js/sidebar-toggle.js"></script>
    <script src="<?= BASEURL ?>/assets/js/notifications.js"></script>
    <script src="<?= BASEURL ?>/assets/js/page-loader.js"></script>
</body>
</html>

<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside class="dashboard-sidebar">
    <ul class="sidebar-menu">

        <li>
            <a href="<?= BASEURL ?>/pages/dashboard.php"
               class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                <span class="icon">🏠</span>
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <a href="<?= BASEURL ?>/pages/my-bookings.php"
               class="<?= $currentPage === 'my-bookings.php' ? 'active' : '' ?>">
                <span class="icon">📋</span>
                <span>My Bookings</span>
            </a>
        </li>

        <li>
            <a href="<?= BASEURL ?>/pages/history.php"
               class="<?= $currentPage === 'history.php' ? 'active' : '' ?>">
                <span class="icon">📜</span>
                <span>History</span>
            </a>
        </li>

        <li>
            <a href="<?= BASEURL ?>/pages/favorites.php"
               class="<?= $currentPage === 'favorites.php' ? 'active' : '' ?>">
                <span class="icon">⭐</span>
                <span>Favorites</span>
            </a>
        </li>

        <li>
            <a href="<?= BASEURL ?>/pages/payment.php"
               class="<?= $currentPage === 'payment.php' ? 'active' : '' ?>">
                <span class="icon">💳</span>
                <span>Payment</span>
            </a>
        </li>

        <li>
            <a href="<?= BASEURL ?>/pages/profile.php"
               class="<?= $currentPage === 'profile.php' ? 'active' : '' ?>">
                <span class="icon">👤</span>
                <span>Profile</span>
            </a>
        </li>

        <li>
            <a href="<?= BASEURL ?>/pages/settings.php"
               class="<?= $currentPage === 'settings.php' ? 'active' : '' ?>">
                <span class="icon">⚙️</span>
                <span>Settings</span>
            </a>
        </li>

    </ul>

    <div class="sidebar-footer">
        <a href="<?= BASEURL ?>/functions/logout.php"
           onclick="return confirm('Are you sure you want to logout?')">
            <span class="icon">🚪</span>
            <span>Logout</span>
        </a>
    </div>
</aside>

<?php
if (!isset($user)) {
    $user = getCurrentUser();
}
?>

<nav class="dashboard-navbar">
    <a href="<?= BASEURL ?>/pages/dashboard.php" class="navbar-brand">
        <img src="<?= BASEURL ?>/assets/img/logoSpark.png" alt="SPARK">
    </a>

    <div class="navbar-search">
        <input
            type="text"
            id="searchInput"
            placeholder="Search parking locations..."
            onkeyup="searchParking(this.value)"
        >
    </div>

    <div class="navbar-actions">
        <button class="icon-btn" title="Notifications">🔔</button>
        <button class="icon-btn" title="Settings">⚙️</button>
    </div>
</nav>

<nav class="admin-navbar">
    <h1 class="admin-navbar-title"><?= $pageTitle ?? 'Dashboard' ?></h1>
    <div class="admin-navbar-user">
        <div class="admin-user-info">
            <div class="admin-user-name"><?= htmlspecialchars($admin['nama_pengguna'] ?? 'Admin') ?></div>
            <div class="admin-user-role">Administrator</div>
        </div>
        <a href="<?= BASEURL ?>/admin/logout.php" class="admin-logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</nav>


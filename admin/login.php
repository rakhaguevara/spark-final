<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../functions/admin-auth.php';

startSession();

// Redirect if already logged in
if (isAdminLoggedIn()) {
    header('Location: ' . BASEURL . '/admin/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | SPARK</title>
    
    <link rel="icon" type="image/png" href="<?= BASEURL ?>/assets/img/logo.png">
    <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/login-style.css">
</head>
<body>

<div class="login-container">
    <div class="login-left">
        <div class="login-box">

            <!-- SWITCH -->
            <div class="login-switch">
                <button type="button" class="switch-btn active">
                    Admin Login
                </button>
                <button type="button" class="switch-btn" 
                        onclick="window.location.href='<?= BASEURL ?>/pages/login.php'">
                    User Login
                </button>
            </div>

            <h1>Welcome to SPARK Admin!</h1>
            <p class="subtitle">Please enter your admin credentials to login.</p>

            <!-- ERROR MESSAGE -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert-error">
                    ❌ <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- SUCCESS MESSAGE -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert-success">
                    ✅ <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- LOGIN FORM -->
            <form action="<?= BASEURL ?>/functions/admin-login-proses.php" method="POST">

                <label>Email Address</label>
                <input type="email" name="email" required placeholder="admin@spark.com" autofocus>

                <div class="password-row">
                    <label>Password</label>
                </div>

                <input type="password" name="password" required placeholder="Enter your password">

                <button type="submit" class="btn-primary">Log In</button>
            </form>

            <p class="signup-text">
                <a href="<?= BASEURL ?>">
                    <i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama
                </a>
            </p>
        </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="login-right">
        <div class="login-bg-slider active"></div>
        <div class="login-bg-slider next"></div>

        <div class="quote-card">
            <p class="quote">
                "SPARK Admin Panel provides comprehensive control over parking management—from monitoring 
                parking spaces, managing providers, tracking transactions, to analyzing usage statistics 
                for better decision making."
            </p>
            <div class="author">
                <strong>SPARK Admin Team</strong>
                <span>Administrator</span>
                <span>SPARK Management System</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const images = [
        '<?= BASEURL ?>/assets/img/login1.jpg',
        '<?= BASEURL ?>/assets/img/login2.jpg',
        '<?= BASEURL ?>/assets/img/login3.jpg'
    ];

    let index = 0;
    let active = document.querySelector('.login-bg-slider.active');
    let next = document.querySelector('.login-bg-slider.next');

    if (!active || !next) return;

    active.style.backgroundImage = `url(${images[index]})`;

    setInterval(() => {
        const nextIndex = (index + 1) % images.length;
        next.style.backgroundImage = `url(${images[nextIndex]})`;

        active.classList.add('slide-out');
        next.classList.add('slide-in');

        setTimeout(() => {
            active.classList.remove('slide-out', 'active');
            next.classList.remove('slide-in', 'next');

            active.classList.add('next');
            next.classList.add('active');

            [active, next] = [next, active];
            index = nextIndex;
        }, 800);
    }, 4000);
});
</script>

</body>
</html>


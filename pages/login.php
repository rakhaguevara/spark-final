<?php
// Enable error reporting (DEV ONLY)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load config
require_once __DIR__ . '/../config/app.php';

// Start session
session_start();

// Debug log
error_log("=== LOGIN PAGE LOADED ===");
error_log("BASEURL: " . BASEURL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | SPARK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/png" href="<?= BASEURL ?>/assets/img/logo.png">
    <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/login-style.css">
</head>
<body>

<div class="login-container">
    <div class="login-left">
        <div class="login-box">

            <!-- SWITCH -->
            <div class="login-switch">
                <button type="button" class="switch-btn active"
                        onclick="window.location.href='<?= BASEURL ?>/pages/login.php'">
                    Login
                </button>
                <button type="button" class="switch-btn"
                        onclick="window.location.href='<?= BASEURL ?>/pages/register.php'">
                    Sign Up
                </button>
            </div>

            <h1>Welcome to SPARK!</h1>
            <p class="subtitle">Please enter your details to login.</p>

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
            <form action="<?= BASEURL ?>/functions/login-proses.php" method="POST">

                <label>Email Address</label>
                <input type="email" name="email" required placeholder="your@email.com">

                <div class="password-row">
                    <label>Password</label>
                    <a href="<?= BASEURL ?>/pages/forgot-password.php">
                        Forgot Password?
                    </a>
                </div>

                <input type="password" name="password" required placeholder="Enter your password">

                <button type="submit" class="btn-primary">Log In</button>

                <div class="divider">OR</div>

                <button type="button" class="btn-outline google">
                    <img src="<?= BASEURL ?>/assets/img/google.svg" alt="Google">
                    Continue with Google
                </button>
            </form>

            <p class="signup-text">
                Don't have an account yet?
                <a href="<?= BASEURL ?>/pages/register.php">Sign Up</a>
            </p>
        </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="login-right">
        <div class="login-bg-slider active"></div>
        <div class="login-bg-slider next"></div>

        <div class="quote-card">
            <p class="quote">
                "With SPARK, I can manage my parking effortlessly—from finding available spaces nearby,
                reserving a spot in advance, to parking with confidence without wasting time."
            </p>
            <div class="author">
                <strong>Stephen Curry</strong>
                <span>Professional Athlete</span>
                <span>PARKSTER since 2022</span>
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

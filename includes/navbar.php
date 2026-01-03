<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$isHome = $currentPage === 'index.php';
?>
<nav class="spark-navbar">
  <div class="spark-nav-wrapper">

    <!-- LEFT -->
    <a class="spark-left" href="<?= BASEURL ?>">
      <img src="<?= BASEURL ?>/assets/img/logoSpark.png" alt="SPARK Logo">
    </a>

    <!-- CENTER -->
<ul class="spark-center d-none d-lg-flex">
<li>
  <a class="nav-link"
     href="<?= $isHome ? '#home' : BASEURL . '/#home' ?>">
     Home
  </a>
</li>

<li>
  <a class="nav-link"
     href="<?= $isHome ? '#service' : BASEURL . '/#service' ?>">
     Service
  </a>
</li>

<li>
  <a class="nav-link fw-semibold"
     href="<?= $isHome ? '#reserve' : BASEURL . '/#reserve' ?>">
     Reserved Park Now!
  </a>
</li>
</ul>



    <!-- RIGHT -->
    <div class="spark-right d-none d-lg-block">
      <?php if (!isset($_SESSION['user'])): ?>
        <a href="<?= BASEURL ?>/pages/login.php" class="spark-login-btn">Login</a>
      <?php else: ?>
        <a href="<?= BASEURL ?>/dashboard.php" class="spark-login-btn">
          <?= htmlspecialchars($_SESSION['user']['nama_pengguna']) ?>
        </a>
      <?php endif; ?>
    </div>

    <!-- MOBILE TOGGLER -->
    <button class="navbar-toggler d-lg-none" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#sparkMobileNav">
      ☰
    </button>
  </div>

  <!-- MOBILE MENU -->
  <div class="collapse d-lg-none" id="sparkMobileNav">
<div class="spark-mobile-menu">
<a href="<?= $isHome ? '#home' : BASEURL . '/#home' ?>">Home</a>
<a href="<?= $isHome ? '#service' : BASEURL . '/#service' ?>">Service</a>
<a href="<?= $isHome ? '#reserve' : BASEURL . '/#reserve' ?>">Reserved Park Now!</a>
<a href="<?= $isHome ? '#contact' : BASEURL . '/#contact' ?>">Contact</a>
</div>

  </div>
</nav>

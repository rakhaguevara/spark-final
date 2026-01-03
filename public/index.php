<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// ⬇️ WAJIB PALING ATAS
require_once __DIR__ . '/../config/config.php';

// ⬇️ baru include layout
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../pages/home.php';
require_once __DIR__ . '/../includes/footer.php';


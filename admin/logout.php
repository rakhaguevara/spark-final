<?php
require_once __DIR__ . '/../config/app.php';

session_start();

// Destroy admin session
unset($_SESSION['admin_id']);
unset($_SESSION['admin']);

$_SESSION['success'] = 'Anda telah logout dari admin panel';
header('Location: ' . BASEURL . '/admin/login.php');
exit;


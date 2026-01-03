<?php
/**
 * LOGOUT.PHP
 * Handles user logout by destroying session and redirecting to login
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/functions/auth.php';

// Start session
startSession();

// Destroy all session data
session_unset();
session_destroy();

// Prevent caching to avoid back button access
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to login page
header('Location: ' . BASEURL . '/index.php');
exit;

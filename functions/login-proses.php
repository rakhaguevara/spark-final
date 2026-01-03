<?php
require_once __DIR__ . '/../config/app.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASEURL . '/pages/login.php');
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    die('Email dan password wajib diisi');
}

/*
  DI SINI:
  - cek ke database
  - password_verify()
  - dll
*/

$_SESSION['user'] = [
    'email' => $email
];

header('Location: ' . BASEURL . '/index.php');
exit;

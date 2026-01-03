<?php
// TAMPILKAN ERROR (DEV ONLY)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

session_start();

/* ================= VALIDASI METHOD ================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASEURL . '/pages/register.php');
    exit;
}

/* ================= AMBIL INPUT ================= */
$nama             = trim($_POST['nama'] ?? '');
$email            = trim($_POST['email'] ?? '');
$password         = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$no_hp            = trim($_POST['no_hp'] ?? '');

/* ================= VALIDASI ================= */
if ($nama === '' || $email === '' || $password === '') {
    $_SESSION['error'] = 'Semua field wajib diisi';
    header('Location: ' . BASEURL . '/pages/register.php');
    exit;
}

if ($password !== $confirm_password) {
    $_SESSION['error'] = 'Password dan konfirmasi password tidak cocok';
    header('Location: ' . BASEURL . '/pages/register.php');
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['error'] = 'Password minimal 6 karakter';
    header('Location: ' . BASEURL . '/pages/register.php');
    exit;
}

/* ================= PROSES DATABASE ================= */
try {
    $pdo = getDBConnection();

    // Cek email sudah terdaftar
    $stmt = $pdo->prepare(
        "SELECT id_pengguna FROM data_pengguna WHERE email_pengguna = ? LIMIT 1"
    );
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Email sudah terdaftar';
        header('Location: ' . BASEURL . '/pages/register.php');
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user baru (role default = 1 / user)
    $stmt = $pdo->prepare("
        INSERT INTO data_pengguna 
            (role_pengguna, nama_pengguna, email_pengguna, password_pengguna, noHp_pengguna)
        VALUES 
            (1, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $nama,
        $email,
        $hashedPassword,
        $no_hp
    ]);

    $_SESSION['success'] = 'Registrasi berhasil! Silakan login.';
    header('Location: ' . BASEURL . '/pages/login.php');
    exit;

} catch (PDOException $e) {

    // LOG ERROR (jangan tampilkan ke user di production)
    error_log('REGISTER ERROR: ' . $e->getMessage());

    $_SESSION['error'] = 'Terjadi kesalahan sistem. Silakan coba lagi.';
    header('Location: ' . BASEURL . '/pages/register.php');
    exit;
}

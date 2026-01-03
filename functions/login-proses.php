<?php
// TAMPILKAN ERROR (DEV ONLY)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

session_start();

/* ================= VALIDASI METHOD ================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASEURL . '/pages/login.php');
    exit;
}

/* ================= AMBIL INPUT ================= */
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $_SESSION['error'] = 'Email dan password wajib diisi';
    header('Location: ' . BASEURL . '/pages/login.php');
    exit;
}

try {
    $pdo = getDBConnection();

    /* ================= QUERY USER ================= */
    $stmt = $pdo->prepare("
        SELECT 
            dp.id_pengguna,
            dp.nama_pengguna,
            dp.email_pengguna,
            dp.password_pengguna,
            dp.noHp_pengguna,
            dp.role_pengguna,
            rp.nama_role
        FROM data_pengguna dp
        LEFT JOIN role_pengguna rp 
            ON dp.role_pengguna = rp.id_role
        WHERE dp.email_pengguna = ?
        LIMIT 1
    ");
    $stmt->execute([$email]);

    // Fetch associative array (lebih aman & jelas)
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['error'] = 'Email tidak terdaftar';
        header('Location: ' . BASEURL . '/pages/login.php');
        exit;
    }

    /* ================= VERIFIKASI PASSWORD ================= */
    if (!password_verify($password, $user['password_pengguna'])) {
        $_SESSION['error'] = 'Password salah';
        header('Location: ' . BASEURL . '/pages/login.php');
        exit;
    }

    /* ================= SET SESSION ================= */
    $_SESSION['id_pengguna'] = $user['id_pengguna'];
    $_SESSION['user'] = [
        'id'            => $user['id_pengguna'],
        'email'         => $user['email_pengguna'],
        'nama_pengguna' => $user['nama_pengguna'],
        'no_hp'         => $user['noHp_pengguna'],
        'role'          => $user['nama_role'] ?? 'user'
    ];

    $_SESSION['success'] =
        'Login berhasil! Selamat datang ' . $user['nama_pengguna'];

    header('Location: ' . BASEURL . '/pages/dashboard.php');
    exit;

} catch (PDOException $e) {

    // LOG ERROR (jangan tampilkan detail ke user di production)
    error_log('LOGIN ERROR: ' . $e->getMessage());

    $_SESSION['error'] = 'Terjadi kesalahan sistem. Silakan coba lagi.';
    header('Location: ' . BASEURL . '/pages/login.php');
    exit;
}

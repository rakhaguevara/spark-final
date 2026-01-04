<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASEURL . '/admin/login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $_SESSION['error'] = 'Email dan password wajib diisi';
    header('Location: ' . BASEURL . '/admin/login.php');
    exit;
}

try {
    $pdo = getDBConnection();

    $stmt = $pdo->prepare("
        SELECT 
            dp.id_pengguna,
            dp.nama_pengguna,
            dp.email_pengguna,
            dp.password_pengguna,
            dp.role_pengguna,
            rp.nama_role
        FROM data_pengguna dp
        JOIN role_pengguna rp ON dp.role_pengguna = rp.id_role
        WHERE dp.email_pengguna = ? AND rp.nama_role = 'admin'
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        $_SESSION['error'] = 'Email tidak terdaftar atau bukan admin';
        header('Location: ' . BASEURL . '/admin/login.php');
        exit;
    }

    // Verifikasi password (gunakan password_verify jika sudah di-hash)
    // Untuk sementara, jika password masih plain text:
    if ($admin['password_pengguna'] !== $password && !password_verify($password, $admin['password_pengguna'])) {
        $_SESSION['error'] = 'Password salah';
        header('Location: ' . BASEURL . '/admin/login.php');
        exit;
    }

    $_SESSION['admin_id'] = $admin['id_pengguna'];
    $_SESSION['admin'] = [
        'id' => $admin['id_pengguna'],
        'nama' => $admin['nama_pengguna'],
        'email' => $admin['email_pengguna'],
        'role' => $admin['nama_role']
    ];

    $_SESSION['success'] = 'Login berhasil! Selamat datang ' . $admin['nama_pengguna'];
    header('Location: ' . BASEURL . '/admin/dashboard.php');
    exit;

} catch (PDOException $e) {
    error_log('ADMIN LOGIN ERROR: ' . $e->getMessage());
    $_SESSION['error'] = 'Terjadi kesalahan sistem. Silakan coba lagi.';
    header('Location: ' . BASEURL . '/admin/login.php');
    exit;
}


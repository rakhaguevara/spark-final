<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

session_start();

// Pastikan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASEURL . '/pages/register.php');
    exit;
}

// Ambil input
$nama     = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';
$phone    = trim($_POST['phone'] ?? '');

// ================= VALIDASI =================
if ($nama === '' || $email === '' || $password === '' || $confirm === '' || $phone === '') {
    die('❌ Semua field wajib diisi.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('❌ Format email tidak valid.');
}

if (strlen($password) < 8) {
    die('❌ Password minimal 8 karakter.');
}

if ($password !== $confirm) {
    die('❌ Password dan konfirmasi tidak sama.');
}

try {
    $pdo = getDBConnection();

    // Cek email sudah ada
    $cek = $pdo->prepare("
        SELECT id_pengguna 
        FROM data_pengguna 
        WHERE email_pengguna = :email
        LIMIT 1
    ");
    $cek->execute(['email' => $email]);

    if ($cek->fetch()) {
        die('❌ Email sudah terdaftar.');
    }

    // HASH PASSWORD (WAJIB)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // INSERT USER BARU (ROLE USER = 1)
    $insert = $pdo->prepare("
        INSERT INTO data_pengguna 
        (role_pengguna, nama_pengguna, email_pengguna, password_pengguna, noHp_pengguna)
        VALUES 
        (1, :nama, :email, :password, :phone)
    ");

    $insert->execute([
        'nama'     => $nama,
        'email'    => $email,
        'password' => $hashedPassword,
        'phone'    => $phone
    ]);

    // AUTO LOGIN
    $_SESSION['user'] = [
        'id_pengguna'   => $pdo->lastInsertId(),
        'nama_pengguna' => $nama,
        'email'         => $email,
        'role'          => 'user'
    ];

    // REDIRECT
    header('Location: ' . BASEURL . '/index.php');
    exit;

} catch (PDOException $e) {
    // DEBUG (hapus saat production)
    die('❌ Database error: ' . $e->getMessage());
}

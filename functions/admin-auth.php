<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isAdminLoggedIn(): bool {
    startSession();
    return isset($_SESSION['admin_id']);
}

function getCurrentAdmin() {
    if (!isAdminLoggedIn()) return null;

    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT dp.*, rp.nama_role
        FROM data_pengguna dp
        JOIN role_pengguna rp ON dp.role_pengguna = rp.id_role
        WHERE dp.id_pengguna = ? AND rp.nama_role = 'admin'
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function requireAdminLogin() {
    startSession();
    if (!isAdminLoggedIn()) {
        header('Location: ' . BASEURL . '/admin/login.php');
        exit;
    }
}


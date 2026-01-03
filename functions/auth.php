<?php
require_once __DIR__ . '/../config/database.php';

function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn(): bool {
    return isset($_SESSION['id_pengguna']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;

    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT dp.*, rp.nama_role
        FROM data_pengguna dp
        JOIN role_pengguna rp ON dp.role_pengguna = rp.id_role
        WHERE dp.id_pengguna = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['id_pengguna']]);
    return $stmt->fetch();
}

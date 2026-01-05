<?php
/**
 * MARK ALL NOTIFICATIONS AS READ
 * Updates all user notifications to read status
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/auth.php';

header('Content-Type: application/json');

// Start session
startSession();

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$user = getCurrentUser();
$pdo = getDBConnection();

try {
    // Update all notifications for current user to read
    $stmt = $pdo->prepare("
        UPDATE notifikasi_pengguna 
        SET is_read = 1 
        WHERE id_pengguna = ? AND is_read = 0
    ");
    
    $stmt->execute([$user['id_pengguna']]);
    $updatedCount = $stmt->rowCount();
    
    echo json_encode([
        'success' => true,
        'message' => 'All notifications marked as read',
        'updated_count' => $updatedCount,
        'unread_count' => 0
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

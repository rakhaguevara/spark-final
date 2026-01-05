<?php
/**
 * MARK NOTIFICATION AS READ
 * Updates single notification read status
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

// Get notification ID from POST
$notif_id = $_POST['notif_id'] ?? null;

if (!$notif_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Notification ID required']);
    exit;
}

try {
    // Verify notification belongs to current user before updating
    $checkStmt = $pdo->prepare("
        SELECT id_notif 
        FROM notifikasi_pengguna 
        WHERE id_notif = ? AND id_pengguna = ?
    ");
    $checkStmt->execute([$notif_id, $user['id_pengguna']]);
    
    if ($checkStmt->rowCount() === 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Notification not found or access denied']);
        exit;
    }
    
    // Update notification to read
    $stmt = $pdo->prepare("
        UPDATE notifikasi_pengguna 
        SET is_read = 1 
        WHERE id_notif = ? AND id_pengguna = ?
    ");
    
    $stmt->execute([$notif_id, $user['id_pengguna']]);
    
    // Get updated unread count
    $unreadStmt = $pdo->prepare("
        SELECT COUNT(*) as unread_count
        FROM notifikasi_pengguna
        WHERE id_pengguna = ? AND is_read = 0
    ");
    $unreadStmt->execute([$user['id_pengguna']]);
    $unreadCount = $unreadStmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Notification marked as read',
        'unread_count' => $unreadCount
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

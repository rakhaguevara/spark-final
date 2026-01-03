<?php
/**
 * QR TOKEN REFRESH API
 * Generates new QR token every 10 seconds
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/auth.php';

// Start session
startSession();

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get user
$user = getCurrentUser();

// Validate input
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$ticket_id = $_POST['ticket_id'] ?? '';

if (empty($ticket_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing ticket_id']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Verify ticket exists and belongs to user
    $stmt = $pdo->prepare("
        SELECT ticket_id, ticket_status 
        FROM tickets 
        WHERE ticket_id = ? AND id_pengguna = ?
    ");
    $stmt->execute([$ticket_id, $user['id_pengguna']]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ticket) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Ticket not found']);
        exit;
    }
    
    // Check ticket is active or checked_in
    if (!in_array($ticket['ticket_status'], ['active', 'checked_in'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ticket not active']);
        exit;
    }
    
    // Delete expired tokens for this ticket
    $stmt = $pdo->prepare("
        DELETE FROM qr_sessions 
        WHERE ticket_id = ? AND expires_at < NOW()
    ");
    $stmt->execute([$ticket_id]);
    
    // Generate new token
    $qr_token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', time() + 10); // 10 seconds
    
    // Insert new token
    $stmt = $pdo->prepare("
        INSERT INTO qr_sessions (ticket_id, qr_token, expires_at)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$ticket_id, $qr_token, $expires_at]);
    
    // Return new token
    echo json_encode([
        'success' => true,
        'qr_token' => $qr_token,
        'expires_at' => $expires_at
    ]);
    
} catch (PDOException $e) {
    error_log("QR token refresh error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}

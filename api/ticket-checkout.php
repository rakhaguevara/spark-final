<?php
/**
 * TICKET CHECK-OUT API
 * Updates ticket status to checked_out
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

// Validate input
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$qr_token = $_POST['qr_token'] ?? '';

if (empty($qr_token)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing QR token']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Find and validate QR session
    $stmt = $pdo->prepare("
        SELECT qs.ticket_id, qs.expires_at, t.ticket_status
        FROM qr_sessions qs
        JOIN tickets t ON qs.ticket_id = t.ticket_id
        WHERE qs.qr_token = ?
    ");
    $stmt->execute([$qr_token]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Invalid QR code']);
        exit;
    }
    
    // Check expiry
    if (strtotime($result['expires_at']) < time()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'QR code expired']);
        exit;
    }
    
    // Check status
    if ($result['ticket_status'] !== 'checked_in') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ticket not checked in']);
        exit;
    }
    
    // Update ticket status
    $stmt = $pdo->prepare("
        UPDATE tickets 
        SET ticket_status = 'checked_out', checkout_time = NOW()
        WHERE ticket_id = ?
    ");
    $stmt->execute([$result['ticket_id']]);
    
    // Invalidate all QR sessions for this ticket
    $stmt = $pdo->prepare("DELETE FROM qr_sessions WHERE ticket_id = ?");
    $stmt->execute([$result['ticket_id']]);
    
    echo json_encode([
        'success' => true,
        'ticket_id' => $result['ticket_id'],
        'message' => 'Check-out successful'
    ]);
    
} catch (PDOException $e) {
    error_log("Check-out error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}

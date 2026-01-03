<?php
/**
 * QR TOKEN REFRESH API
 * Generates new QR token every 10 seconds for security
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
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user = getCurrentUser();
$booking_id = $_POST['booking_id'] ?? '';

if (empty($booking_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing booking_id']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Verify booking belongs to user
    $stmt = $pdo->prepare("
        SELECT id_booking, waktu_selesai 
        FROM booking_parkir 
        WHERE id_booking = ? AND id_pengguna = ? AND status_booking = 'confirmed'
    ");
    $stmt->execute([$booking_id, $user['id_pengguna']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Booking not found']);
        exit;
    }
    
    // Generate new token
    $new_token = hash('sha256', $booking_id . time() . bin2hex(random_bytes(16)));
    
    // Calculate expiry (10 seconds from now)
    $expires_at = date('Y-m-d H:i:s', time() + 10);
    
    // Check if QR session exists
    $stmt = $pdo->prepare("SELECT id_qr FROM qr_session WHERE id_booking = ?");
    $stmt->execute([$booking_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        // Update existing session
        $stmt = $pdo->prepare("
            UPDATE qr_session 
            SET qr_token = ?, expires_at = ?
            WHERE id_booking = ?
        ");
        $stmt->execute([$new_token, $expires_at, $booking_id]);
    } else {
        // Insert new session
        $stmt = $pdo->prepare("
            INSERT INTO qr_session (id_booking, qr_token, expires_at)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$booking_id, $new_token, $expires_at]);
    }
    
    echo json_encode([
        'success' => true,
        'qr_token' => $new_token,
        'expires_at' => $expires_at,
        'expires_in' => 10
    ]);
    
} catch (Exception $e) {
    error_log("QR refresh error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
}

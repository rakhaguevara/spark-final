<?php
/**
 * FIX MISSING QR TOKENS
 * Generates QR tokens for bookings that don't have them
 */

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

// Get booking_id from request
$booking_id = $_GET['booking_id'] ?? $_POST['booking_id'] ?? '';

if (empty($booking_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing booking_id']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Verify booking belongs to user and is active
    $stmt = $pdo->prepare("
        SELECT id_booking, qr_secret, waktu_selesai, status_booking
        FROM booking_parkir 
        WHERE id_booking = ? AND id_pengguna = ?
    ");
    $stmt->execute([$booking_id, $user['id_pengguna']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Booking not found']);
        exit;
    }
    
    // Generate qr_secret if it doesn't exist
    $qr_secret = $booking['qr_secret'];
    if (empty($qr_secret)) {
        $qr_secret = bin2hex(random_bytes(32));
        
        // Update booking with qr_secret
        $stmt = $pdo->prepare("UPDATE booking_parkir SET qr_secret = ? WHERE id_booking = ?");
        $stmt->execute([$qr_secret, $booking_id]);
    }
    
    // Check if QR session already exists
    $stmt = $pdo->prepare("SELECT id_qr FROM qr_session WHERE id_booking = ?");
    $stmt->execute([$booking_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        // QR session exists, just refresh the token
        $new_token = hash('sha256', $qr_secret . $booking_id . time() . bin2hex(random_bytes(16)));
        $expires_at = date('Y-m-d H:i:s', time() + 10);
        
        $stmt = $pdo->prepare("
            UPDATE qr_session 
            SET qr_token = ?, expires_at = ?
            WHERE id_booking = ?
        ");
        $stmt->execute([$new_token, $expires_at, $booking_id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'QR token refreshed',
            'qr_token' => $new_token
        ]);
    } else {
        // Create new QR session
        $qr_token = hash('sha256', $qr_secret . $booking_id . time() . bin2hex(random_bytes(16)));
        $expires_at = date('Y-m-d H:i:s', time() + 10);
        
        $stmt = $pdo->prepare("
            INSERT INTO qr_session (id_booking, qr_token, expires_at)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$booking_id, $qr_token, $expires_at]);
        
        echo json_encode([
            'success' => true,
            'message' => 'QR session created',
            'qr_token' => $qr_token
        ]);
    }
    
} catch (Exception $e) {
    error_log("Fix QR error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

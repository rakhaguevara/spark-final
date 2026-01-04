<?php
/**
 * TICKET GENERATION
 * Called after payment confirmation to generate secure parking ticket
 */

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

$booking_id = $_POST['booking_id'] ?? '';
$vehicle_plate = $_POST['vehicle_plate'] ?? '';

if (empty($booking_id) || empty($vehicle_plate)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Verify booking exists and belongs to user
    $stmt = $pdo->prepare("
        SELECT booking_id, status 
        FROM booking_parkir 
        WHERE booking_id = ? AND id_pengguna = ?
    ");
    $stmt->execute([$booking_id, $user['id_pengguna']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }
    
    // Check if booking is confirmed
    if ($booking['status'] !== 'confirmed') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Booking not confirmed']);
        exit;
    }
    
    // Generate unique ticket ID
    $ticket_id = 'TKT-' . strtoupper(substr(uniqid(), -10));
    
    // Hash vehicle plate for security
    $vehicle_plate_hash = hash('sha256', $vehicle_plate . SECRET_SALT);
    
    // Get plate hint (last 4 chars)
    $vehicle_plate_hint = substr($vehicle_plate, -4);
    
    // Insert ticket
    $stmt = $pdo->prepare("
        INSERT INTO tickets (
            ticket_id, booking_id, id_pengguna, 
            vehicle_plate_hash, vehicle_plate_hint, ticket_status
        ) VALUES (?, ?, ?, ?, ?, 'active')
    ");
    
    $stmt->execute([
        $ticket_id,
        $booking_id,
        $user['id_pengguna'],
        $vehicle_plate_hash,
        $vehicle_plate_hint
    ]);
    
    // Generate initial QR token
    $qr_token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', time() + 10); // 10 seconds
    
    $stmt = $pdo->prepare("
        INSERT INTO qr_sessions (ticket_id, qr_token, expires_at)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$ticket_id, $qr_token, $expires_at]);
    
    // Return success
    echo json_encode([
        'success' => true,
        'ticket_id' => $ticket_id,
        'qr_token' => $qr_token,
        'message' => 'Ticket generated successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("Ticket generation error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}

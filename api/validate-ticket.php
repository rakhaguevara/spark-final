<?php
/**
 * TICKET VALIDATION API
 * Validates scanned QR token (for parking officers)
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
    
    // Find QR session
    $stmt = $pdo->prepare("
        SELECT qs.ticket_id, qs.expires_at,
               t.ticket_status, t.vehicle_plate_hint, t.checkin_time,
               b.id_parkir, b.id_jenis_kendaraan, b.nomor_plat,
               p.nama_tempat_parkir,
               dp.nama_pengguna,
               jk.jenis_kendaraan
        FROM qr_sessions qs
        JOIN tickets t ON qs.ticket_id = t.ticket_id
        JOIN booking_parkir b ON t.booking_id = b.booking_id
        JOIN tempat_parkir p ON b.id_parkir = p.id_parkir
        JOIN data_pengguna dp ON t.id_pengguna = dp.id_pengguna
        JOIN jenis_kendaraan jk ON b.id_jenis_kendaraan = jk.id_jenis_kendaraan
        WHERE qs.qr_token = ?
    ");
    $stmt->execute([$qr_token]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Invalid QR code']);
        exit;
    }
    
    // Check if token expired
    if (strtotime($result['expires_at']) < time()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'QR code expired']);
        exit;
    }
    
    // Check ticket status
    if ($result['ticket_status'] === 'expired' || $result['ticket_status'] === 'checked_out') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ticket no longer valid']);
        exit;
    }
    
    // Return ticket info
    echo json_encode([
        'success' => true,
        'ticket_id' => $result['ticket_id'],
        'status' => $result['ticket_status'],
        'user_name' => $result['nama_pengguna'],
        'parking_name' => $result['nama_tempat_parkir'],
        'vehicle_type' => $result['jenis_kendaraan'],
        'vehicle_plate_hint' => '***' . $result['vehicle_plate_hint'],
        'checkin_time' => $result['checkin_time'],
        'message' => 'Valid ticket'
    ]);
    
} catch (PDOException $e) {
    error_log("Ticket validation error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}

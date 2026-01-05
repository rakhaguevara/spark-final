<?php
/**
 * QR CODE VALIDATION API
 * Validates scanned QR code and returns booking details
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/auth.php';

// Start session
startSession();

// Check if user is logged in (owner/staff)
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized. Please login first.'
    ]);
    exit;
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$qr_data = $input['qr_data'] ?? '';
$scan_type = $input['scan_type'] ?? 'entry';

// Validate input
if (empty($qr_data)) {
    echo json_encode([
        'success' => false,
        'message' => 'QR data is required'
    ]);
    exit;
}

// Validate scan type
if (!in_array($scan_type, ['entry', 'exit', 'stay'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid scan type. Must be entry, exit, or stay'
    ]);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Extract token from QR data (format: qr:TOKEN)
    if (strpos($qr_data, 'qr:') !== 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid QR code format'
        ]);
        exit;
    }
    
    $token = substr($qr_data, 3); // Remove 'qr:' prefix
    
    // Find QR session
    $stmt = $pdo->prepare("
        SELECT 
            q.qr_id,
            q.id_booking,
            q.qr_token,
            q.expires_at,
            b.id_pengguna,
            b.id_tempat,
            b.id_kendaraan,
            b.waktu_mulai,
            b.waktu_selesai,
            b.total_harga,
            b.status_booking,
            t.nama_tempat,
            t.alamat_tempat,
            p.nama_pengguna,
            p.email,
            k.plat_hint,
            jk.nama_jenis
        FROM qr_session q
        JOIN booking_parkir b ON q.id_booking = b.id_booking
        JOIN tempat_parkir t ON b.id_tempat = t.id_tempat
        JOIN pengguna p ON b.id_pengguna = p.id_pengguna
        JOIN kendaraan_pengguna k ON b.id_kendaraan = k.id_kendaraan
        JOIN jenis_kendaraan jk ON k.id_jenis = jk.id_jenis
        WHERE q.qr_token = ?
        LIMIT 1
    ");
    $stmt->execute([$token]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$session) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid QR code. Token not found.'
        ]);
        exit;
    }
    
    // Check if token is expired
    if (strtotime($session['expires_at']) < time()) {
        echo json_encode([
            'success' => false,
            'message' => 'QR code has expired. Please refresh the ticket.'
        ]);
        exit;
    }
    
    // Validate scan type against booking status
    $can_scan = false;
    $validation_message = '';
    
    switch ($scan_type) {
        case 'entry':
            if ($session['status_booking'] === 'confirmed') {
                $can_scan = true;
                $validation_message = 'Valid ticket for entry scan';
            } else if ($session['status_booking'] === 'ongoing') {
                $validation_message = 'Entry already scanned. Vehicle is currently parked.';
            } else if ($session['status_booking'] === 'completed') {
                $validation_message = 'Booking already completed.';
            } else if ($session['status_booking'] === 'cancelled') {
                $validation_message = 'Booking has been cancelled.';
            } else {
                $validation_message = 'Invalid booking status for entry scan.';
            }
            break;
            
        case 'exit':
            if ($session['status_booking'] === 'ongoing') {
                $can_scan = true;
                $validation_message = 'Valid ticket for exit scan';
            } else if ($session['status_booking'] === 'confirmed') {
                $validation_message = 'Entry scan required first.';
            } else if ($session['status_booking'] === 'completed') {
                $validation_message = 'Exit already scanned. Booking completed.';
            } else if ($session['status_booking'] === 'cancelled') {
                $validation_message = 'Booking has been cancelled.';
            } else {
                $validation_message = 'Invalid booking status for exit scan.';
            }
            break;
            
        case 'stay':
            if ($session['status_booking'] === 'ongoing') {
                $can_scan = true;
                $validation_message = 'Valid ticket for stay confirmation';
            } else if ($session['status_booking'] === 'confirmed') {
                $validation_message = 'Entry scan required first.';
            } else if ($session['status_booking'] === 'completed') {
                $validation_message = 'Booking already completed.';
            } else if ($session['status_booking'] === 'cancelled') {
                $validation_message = 'Booking has been cancelled.';
            } else {
                $validation_message = 'Invalid booking status for stay scan.';
            }
            break;
    }
    
    // Return booking details
    echo json_encode([
        'success' => true,
        'can_scan' => $can_scan,
        'message' => $validation_message,
        'booking' => [
            'id_booking' => $session['id_booking'],
            'nama_tempat' => $session['nama_tempat'],
            'alamat_tempat' => $session['alamat_tempat'],
            'nama_pengguna' => $session['nama_pengguna'],
            'email' => $session['email'],
            'nama_jenis' => $session['nama_jenis'],
            'plat_kendaraan' => '***' . $session['plat_hint'],
            'waktu_mulai' => $session['waktu_mulai'],
            'waktu_selesai' => $session['waktu_selesai'],
            'total_harga' => $session['total_harga'],
            'status_booking' => $session['status_booking']
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

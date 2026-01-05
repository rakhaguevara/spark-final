<?php
/**
 * CONFIRM SCAN API
 * Confirms scan and updates booking status
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
$id_booking = $input['id_booking'] ?? 0;
$scan_type = $input['scan_type'] ?? 'entry';
$scanned_by = $input['scanned_by'] ?? null;
$scan_location = $input['scan_location'] ?? null;

// Validate input
if (empty($id_booking)) {
    echo json_encode([
        'success' => false,
        'message' => 'Booking ID is required'
    ]);
    exit;
}

// Validate scan type
if (!in_array($scan_type, ['entry', 'exit', 'stay'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid scan type'
    ]);
    exit;
}

try {
    $pdo = getDBConnection();
    $pdo->beginTransaction();
    
    // Get current booking status
    $stmt = $pdo->prepare("
        SELECT 
            b.id_booking,
            b.status_booking,
            b.id_pengguna,
            t.nama_tempat,
            p.nama_pengguna
        FROM booking_parkir b
        JOIN tempat_parkir t ON b.id_tempat = t.id_tempat
        JOIN pengguna p ON b.id_pengguna = p.id_pengguna
        WHERE b.id_booking = ?
    ");
    $stmt->execute([$id_booking]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        $pdo->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Booking not found'
        ]);
        exit;
    }
    
    $current_status = $booking['status_booking'];
    $new_status = $current_status;
    $scan_status = 'success';
    $scan_message = '';
    $notification_title = '';
    $notification_message = '';
    
    // Determine new status and notification based on scan type
    switch ($scan_type) {
        case 'entry':
            if ($current_status === 'confirmed') {
                $new_status = 'ongoing';
                $scan_message = 'Entry scan successful. Status changed to ongoing.';
                $notification_title = 'Entry Scan Success';
                $notification_message = 'You have successfully entered the parking area at ' . $booking['nama_tempat'] . '. Enjoy your visit!';
            } else {
                $scan_status = 'failed';
                $scan_message = 'Entry scan failed. Invalid booking status: ' . $current_status;
                $pdo->rollBack();
                echo json_encode([
                    'success' => false,
                    'message' => $scan_message
                ]);
                exit;
            }
            break;
            
        case 'exit':
            if ($current_status === 'ongoing') {
                $new_status = 'completed';
                $scan_message = 'Exit scan successful. Booking completed.';
                $notification_title = 'Exit Scan Success';
                $notification_message = 'You have successfully exited the parking area at ' . $booking['nama_tempat'] . '. Thank you for using SPARK!';
            } else {
                $scan_status = 'failed';
                $scan_message = 'Exit scan failed. Invalid booking status: ' . $current_status;
                $pdo->rollBack();
                echo json_encode([
                    'success' => false,
                    'message' => $scan_message
                ]);
                exit;
            }
            break;
            
        case 'stay':
            if ($current_status === 'ongoing') {
                $new_status = 'ongoing'; // Stay doesn't change status
                $scan_message = 'Stay scan confirmed. Parking session is active.';
                $notification_title = 'Stay Scan Confirmed';
                $notification_message = 'Your parking session at ' . $booking['nama_tempat'] . ' is still active.';
            } else {
                $scan_status = 'failed';
                $scan_message = 'Stay scan failed. Invalid booking status: ' . $current_status;
                $pdo->rollBack();
                echo json_encode([
                    'success' => false,
                    'message' => $scan_message
                ]);
                exit;
            }
            break;
    }
    
    // Update booking status if changed
    if ($new_status !== $current_status) {
        $stmt = $pdo->prepare("
            UPDATE booking_parkir 
            SET status_booking = ?
            WHERE id_booking = ?
        ");
        $stmt->execute([$new_status, $id_booking]);
    }
    
    // Insert scan history
    $stmt = $pdo->prepare("
        INSERT INTO scan_history 
        (id_booking, scan_type, scanned_by, scan_location, scan_status, scan_message, scanned_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $id_booking,
        $scan_type,
        $scanned_by,
        $scan_location,
        $scan_status,
        $scan_message
    ]);
    
    // Insert notification for user
    $stmt = $pdo->prepare("
        INSERT INTO notifikasi_pengguna 
        (id_pengguna, judul, pesan, is_read, created_at)
        VALUES (?, ?, ?, 0, NOW())
    ");
    $stmt->execute([
        $booking['id_pengguna'],
        $notification_title,
        $notification_message
    ]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => $scan_message,
        'old_status' => $current_status,
        'new_status' => $new_status,
        'scan_type' => $scan_type
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

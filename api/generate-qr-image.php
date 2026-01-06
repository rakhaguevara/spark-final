<?php

/**
 * QR CODE GENERATOR - OFFLINE (phpqrcode library)
 * Generates QR code using local phpqrcode library
 */

// Enable error logging
error_log("=== QR Image Generation Request ===");

// Get booking ID
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

error_log("Booking ID: " . $booking_id);

if (empty($booking_id)) {
    error_log("ERROR: Missing booking ID");
    header('Content-Type: image/png');
    $im = imagecreatetruecolor(300, 300);
    $bg = imagecolorallocate($im, 255, 240, 200);
    $text = imagecolorallocate($im, 150, 100, 0);
    imagefilledrectangle($im, 0, 0, 300, 300, $bg);
    imagestring($im, 5, 60, 140, 'Missing booking ID', $text);
    imagepng($im);
    imagedestroy($im);
    exit;
}

try {
    // Load database config
    require_once __DIR__ . '/../config/database.php';

    // Load phpqrcode library
    require_once __DIR__ . '/../lib/phpqrcode-2010100721_1.1.4/phpqrcode/qrlib.php';

    // Get database connection
    $pdo = getDBConnection();
    error_log("Database connection established");

    // Get QR token
    $stmt = $pdo->prepare("SELECT qr_token FROM qr_session WHERE id_booking = ? LIMIT 1");
    $stmt->execute([$booking_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    error_log("Query executed. Result: " . ($row ? "Found" : "Not found"));
    
    if ($row) {
        error_log("QR Token: " . substr($row['qr_token'], 0, 10) . "...");
    }

    if (!$row || empty($row['qr_token'])) {
        error_log("ERROR: No QR token found for booking " . $booking_id);
        
        // Check if booking exists
        $check = $pdo->prepare("SELECT id_booking FROM booking_parkir WHERE id_booking = ?");
        $check->execute([$booking_id]);
        $booking_exists = $check->fetch(PDO::FETCH_ASSOC);
        
        error_log("Booking exists in booking_parkir: " . ($booking_exists ? "Yes" : "No"));
        
        header('Content-Type: image/png');
        $im = imagecreatetruecolor(300, 300);
        $bg = imagecolorallocate($im, 255, 240, 240);
        $text = imagecolorallocate($im, 200, 0, 0);
        imagefilledrectangle($im, 0, 0, 300, 300, $bg);
        imagestring($im, 5, 70, 130, 'No QR Token', $text);
        imagestring($im, 3, 50, 150, 'Booking: ' . $booking_id, $text);
        imagestring($im, 2, 30, 170, 'Check server logs', $text);
        imagepng($im);
        imagedestroy($im);
        exit;
    }

    // QR content - format: qr:TOKEN
    $qr_content = 'qr:' . $row['qr_token'];

    error_log("Generating QR code for content length: " . strlen($qr_content));

    // Generate QR code directly to output (no file save)
    // Parameters: data, filename (false=output), error_correction, size, margin
    header('Content-Type: image/png');
    QRcode::png($qr_content, false, QR_ECLEVEL_M, 10, 2);
    
    error_log("QR code generated successfully");
} catch (Exception $e) {
    error_log("EXCEPTION: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    header('Content-Type: image/png');
    $im = imagecreatetruecolor(300, 300);
    $bg = imagecolorallocate($im, 255, 200, 200);
    $text = imagecolorallocate($im, 150, 0, 0);
    imagefilledrectangle($im, 0, 0, 300, 300, $bg);
    imagestring($im, 5, 90, 130, 'QR Error', $text);
    $msg = substr($e->getMessage(), 0, 30);
    imagestring($im, 3, 40, 150, $msg, $text);
    imagestring($im, 2, 30, 170, 'Check server logs', $text);
    imagepng($im);
    imagedestroy($im);
}

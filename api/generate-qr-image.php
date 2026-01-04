<?php
/**
 * QR CODE GENERATOR - OFFLINE (phpqrcode library)
 * Generates QR code using local phpqrcode library
 */

// Get booking ID
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : '';

if (empty($booking_id)) {
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
    // Load phpqrcode library
    require_once __DIR__ . '/../lib/phpqrcode-2010100721_1.1.4/phpqrcode/qrlib.php';
    
    // Database connection
    $pdo = new PDO("mysql:host=localhost;dbname=spark", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get QR token
    $stmt = $pdo->prepare("SELECT qr_token FROM qr_session WHERE id_booking = ?");
    $stmt->execute([$booking_id]);
    $row = $stmt->fetch();
    
    if (!$row || empty($row['qr_token'])) {
        header('Content-Type: image/png');
        $im = imagecreatetruecolor(300, 300);
        $bg = imagecolorallocate($im, 255, 240, 240);
        $text = imagecolorallocate($im, 200, 0, 0);
        imagefilledrectangle($im, 0, 0, 300, 300, $bg);
        imagestring($im, 5, 70, 130, 'No QR Token', $text);
        imagestring($im, 3, 50, 150, 'Booking: ' . $booking_id, $text);
        imagepng($im);
        imagedestroy($im);
        exit;
    }
    
    // QR content
    $qr_content = 'qr:' . $row['qr_token'];
    
    // Generate QR code directly to output (no file save)
    // Parameters: data, filename (false=output), error_correction, size, margin
    QRcode::png($qr_content, false, QR_ECLEVEL_M, 10, 2);
    
} catch (Exception $e) {
    header('Content-Type: image/png');
    $im = imagecreatetruecolor(300, 300);
    $bg = imagecolorallocate($im, 255, 200, 200);
    $text = imagecolorallocate($im, 150, 0, 0);
    imagefilledrectangle($im, 0, 0, 300, 300, $bg);
    imagestring($im, 5, 90, 130, 'QR Error', $text);
    $msg = substr($e->getMessage(), 0, 30);
    imagestring($im, 3, 40, 150, $msg, $text);
    imagepng($im);
    imagedestroy($im);
}

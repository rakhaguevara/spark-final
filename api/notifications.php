<?php
/**
 * NOTIFICATIONS API
 * Handles fetching user notifications
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/auth.php';

header('Content-Type: application/json');

// Start session
startSession();

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user = getCurrentUser();
$pdo = getDBConnection();

try {
    // Fetch all notifications for current user
    $stmt = $pdo->prepare("
        SELECT 
            id_notif,
            judul,
            pesan,
            is_read,
            created_at
        FROM notifikasi_pengguna
        WHERE id_pengguna = ?
        ORDER BY created_at DESC
        LIMIT 100
    ");
    
    $stmt->execute([$user['id_pengguna']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group notifications by date
    $grouped = [
        'today' => [],
        'yesterday' => [],
        'earlier' => []
    ];
    
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    foreach ($notifications as $notif) {
        $notifDate = date('Y-m-d', strtotime($notif['created_at']));
        
        // Determine notification type based on title/message
        $type = 'system'; // default
        $judul_lower = strtolower($notif['judul']);
        
        if (strpos($judul_lower, 'confirmed') !== false || strpos($judul_lower, 'success') !== false) {
            $type = 'success';
        } elseif (strpos($judul_lower, 'cancelled') !== false || strpos($judul_lower, 'failed') !== false) {
            $type = 'error';
        } elseif (strpos($judul_lower, 'reminder') !== false || strpos($judul_lower, 'upcoming') !== false) {
            $type = 'reminder';
        }
        
        $notif['type'] = $type;
        $notif['time_ago'] = getTimeAgo($notif['created_at']);
        
        if ($notifDate === $today) {
            $grouped['today'][] = $notif;
        } elseif ($notifDate === $yesterday) {
            $grouped['yesterday'][] = $notif;
        } else {
            $grouped['earlier'][] = $notif;
        }
    }
    
    // Count unread notifications
    $unreadStmt = $pdo->prepare("
        SELECT COUNT(*) as unread_count
        FROM notifikasi_pengguna
        WHERE id_pengguna = ? AND is_read = 0
    ");
    $unreadStmt->execute([$user['id_pengguna']]);
    $unreadCount = $unreadStmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
    
    echo json_encode([
        'success' => true,
        'notifications' => $grouped,
        'unread_count' => $unreadCount,
        'total_count' => count($notifications)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

/**
 * Helper function to get human-readable time ago
 */
function getTimeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' min' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}

<?php
/**
 * BOOKING-DATA.PHP
 * Database queries for booking modal
 * Returns parking details, availability, and pricing
 */

function getBookingData($pdo, $id_tempat) {
    // Get parking details
    $sql = "
        SELECT 
            tp.id_tempat,
            tp.nama_tempat,
            tp.alamat_tempat,
            tp.latitude,
            tp.longitude,
            tp.harga_per_jam,
            tp.jam_buka,
            tp.jam_tutup,
            tp.foto_tempat,
            tp.total_spot,
            COALESCE(AVG(ut.rating), 4.5) as avg_rating,
            COUNT(DISTINCT ut.id_ulasan) as total_review
        FROM tempat_parkir tp
        LEFT JOIN ulasan_tempat ut ON tp.id_tempat = ut.id_tempat
        WHERE tp.id_tempat = :id_tempat
        GROUP BY tp.id_tempat
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_tempat' => $id_tempat]);
    $parking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$parking) {
        return null;
    }
    
    // Get available slots per vehicle type
    $sql = "
        SELECT 
            jk.nama_jenis,
            COUNT(sp.id_slot) as available_count
        FROM slot_parkir sp
        INNER JOIN jenis_kendaraan jk ON sp.id_jenis = jk.id_jenis
        WHERE sp.id_tempat = :id_tempat 
        AND sp.status_slot = 'available'
        GROUP BY jk.id_jenis, jk.nama_jenis
        HAVING available_count > 0
        ORDER BY jk.nama_jenis ASC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_tempat' => $id_tempat]);
    $parking['vehicle_availability'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total available slots
    $parking['total_available'] = 0;
    foreach ($parking['vehicle_availability'] as $vehicle) {
        $parking['total_available'] += $vehicle['available_count'];
    }
    
    // Get facilities (from helper function)
    $parking['facilities'] = getParkingFacilities($parking['jam_buka'], $parking['jam_tutup']);
    
    // Format image path
    if (!empty($parking['foto_tempat'])) {
        $parking['image_url'] = '/assets/img/' . $parking['foto_tempat'];
    } else {
        $parking['image_url'] = '/assets/img/content-1.png';
    }
    
    return $parking;
}

// Helper function for facilities (if not already defined)
if (!function_exists('getParkingFacilities')) {
    function getParkingFacilities($jam_buka, $jam_tutup) {
        $facilities = [];
        
        if ($jam_buka == '00:00:00' && $jam_tutup == '23:59:59') {
            $facilities[] = ['icon' => 'clock', 'text' => '24 Hours'];
        }
        
        $facilities[] = ['icon' => 'camera', 'text' => 'CCTV'];
        $facilities[] = ['icon' => 'shield-alt', 'text' => 'Secure'];
        $facilities[] = ['icon' => 'parking', 'text' => 'Covered'];
        
        return $facilities;
    }
}
?>

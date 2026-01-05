-- =====================================================
-- SCAN HISTORY TABLE
-- Tracks all QR scan events for audit trail
-- =====================================================

CREATE TABLE IF NOT EXISTS scan_history (
    id_scan INT AUTO_INCREMENT PRIMARY KEY,
    id_booking INT NOT NULL,
    scan_type ENUM('entry', 'exit', 'stay') NOT NULL,
    scanned_by INT NULL COMMENT 'ID of owner/staff who performed scan',
    scan_location VARCHAR(255) NULL COMMENT 'Location name or GPS coordinates',
    scan_status ENUM('success', 'failed', 'expired', 'invalid') NOT NULL,
    scan_message TEXT NULL COMMENT 'Additional details or error message',
    scanned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_booking) REFERENCES booking_parkir(id_booking) ON DELETE CASCADE,
    
    INDEX idx_booking (id_booking),
    INDEX idx_scanned_at (scanned_at),
    INDEX idx_scan_type (scan_type),
    INDEX idx_scan_status (scan_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample query to view scan history
-- SELECT 
--     sh.id_scan,
--     sh.scan_type,
--     sh.scan_status,
--     sh.scanned_at,
--     b.id_booking,
--     t.nama_tempat,
--     p.nama_pengguna
-- FROM scan_history sh
-- JOIN booking_parkir b ON sh.id_booking = b.id_booking
-- JOIN tempat_parkir t ON b.id_tempat = t.id_tempat
-- JOIN pengguna p ON b.id_pengguna = p.id_pengguna
-- ORDER BY sh.scanned_at DESC
-- LIMIT 20;

-- =====================================================
-- TICKETS TABLE
-- Stores parking ticket information
-- =====================================================

CREATE TABLE IF NOT EXISTS tickets (
    ticket_id VARCHAR(20) PRIMARY KEY,
    booking_id VARCHAR(20) NOT NULL,
    id_pengguna INT NOT NULL,
    vehicle_plate_hash VARCHAR(64) NOT NULL,
    vehicle_plate_hint VARCHAR(10),
    ticket_status ENUM('active', 'checked_in', 'checked_out', 'expired') DEFAULT 'active',
    checkin_time DATETIME NULL,
    checkout_time DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES booking_parkir(booking_id) ON DELETE CASCADE,
    FOREIGN KEY (id_pengguna) REFERENCES data_pengguna(id_pengguna) ON DELETE CASCADE,
    
    INDEX idx_booking (booking_id),
    INDEX idx_user (id_pengguna),
    INDEX idx_status (ticket_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

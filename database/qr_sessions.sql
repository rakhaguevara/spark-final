-- =====================================================
-- QR SESSIONS TABLE
-- Stores rotating QR tokens with 10-second expiry
-- =====================================================

CREATE TABLE IF NOT EXISTS qr_sessions (
    qr_id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id VARCHAR(20) NOT NULL,
    qr_token VARCHAR(64) UNIQUE NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (ticket_id) REFERENCES tickets(ticket_id) ON DELETE CASCADE,
    
    INDEX idx_token (qr_token),
    INDEX idx_expires (expires_at),
    INDEX idx_ticket (ticket_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Auto-cleanup expired tokens (optional, can be done via cron or PHP)
-- DELETE FROM qr_sessions WHERE expires_at < NOW();

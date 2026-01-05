-- =====================================================
-- QR SESSION TABLE FIX
-- Fix foreign key to reference booking_parkir
-- =====================================================

-- Step 1: Check if foreign key exists and drop it
SET @fk_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'qr_session'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);

-- Drop foreign key if exists
SET @drop_fk = IF(@fk_exists > 0, 
    'ALTER TABLE qr_session DROP FOREIGN KEY qr_session_ibfk_1', 
    'SELECT "No foreign key to drop"');
PREPARE stmt FROM @drop_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Check if column is named ticket_id and rename to id_booking
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'qr_session'
    AND COLUMN_NAME = 'ticket_id'
);

SET @rename_col = IF(@col_exists > 0,
    'ALTER TABLE qr_session CHANGE COLUMN ticket_id id_booking INT NOT NULL',
    'SELECT "Column already named id_booking"');
PREPARE stmt FROM @rename_col;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 3: Add new foreign key to booking_parkir
ALTER TABLE qr_session 
ADD CONSTRAINT fk_qr_booking 
FOREIGN KEY (id_booking) REFERENCES booking_parkir(id_booking) ON DELETE CASCADE;

-- Step 4: Verify the change
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'qr_session'
AND REFERENCED_TABLE_NAME IS NOT NULL;

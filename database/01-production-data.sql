-- ========================================
-- SPARK PRODUCTION DATA
-- Connected data: Admin -> Owner -> Parking -> User -> Booking
-- ========================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ========================================
-- CLEAR EXISTING DATA (Prevent Duplicates)
-- ========================================

TRUNCATE TABLE `parking_photos`;
TRUNCATE TABLE `wallet_methods`;
TRUNCATE TABLE `ulasan_tempat`;
TRUNCATE TABLE `kendaraan_pengguna`;
TRUNCATE TABLE `harga_parkir`;
TRUNCATE TABLE `slot_parkir`;
TRUNCATE TABLE `tempat_parkir`;
TRUNCATE TABLE `owner_parkir`;
DELETE FROM `data_pengguna` WHERE id_pengguna > 0;

SET FOREIGN_KEY_CHECKS = 1;
SET FOREIGN_KEY_CHECKS = 0;

-- ========================================
-- 1. ADMIN ACCOUNTS (2 admins)
-- ========================================

-- Admin 1: Super Admin (password: admin123)
INSERT INTO `data_pengguna` (`id_pengguna`, `role_pengguna`, `nama_pengguna`, `email_pengguna`, `password_pengguna`, `noHp_pengguna`) 
VALUES (1, 2, 'Super Admin', 'admin@spark.com', '$2y$10$h6ig7eYcremrVSNcBENfIeOfLhPQeS4ZxuAI7A2e/77GdqLhFwkZ2', '081234567890')
ON DUPLICATE KEY UPDATE `nama_pengguna` = VALUES(`nama_pengguna`);

-- Admin 2: System Manager (password: admin123)
INSERT INTO `data_pengguna` (`id_pengguna`, `role_pengguna`, `nama_pengguna`, `email_pengguna`, `password_pengguna`, `noHp_pengguna`) 
VALUES (2, 2, 'System Manager', 'manager@spark.com', '$2y$10$h6ig7eYcremrVSNcBENfIeOfLhPQeS4ZxuAI7A2e/77GdqLhFwkZ2', '081234567891')
ON DUPLICATE KEY UPDATE `nama_pengguna` = VALUES(`nama_pengguna`);

-- ========================================
-- 2. PARKING OWNERS (3 owners)
-- ========================================

-- Owner 1: Jakarta Parking Group (password: owner123)
INSERT INTO `data_pengguna` (`id_pengguna`, `role_pengguna`, `nama_pengguna`, `email_pengguna`, `password_pengguna`, `noHp_pengguna`) 
VALUES (3, 3, 'Jakarta Parking Group', 'owner.jakarta@spark.com', '$2y$10$WqN6GSZ1dBXojEvf8qvPLOok/RVZ.Ah/T2V7gp/qyZr/7CTe6CtHa', '081234567892')
ON DUPLICATE KEY UPDATE `nama_pengguna` = VALUES(`nama_pengguna`);

-- Owner 2: Bandung Parking Solutions (password: owner123)
INSERT INTO `data_pengguna` (`id_pengguna`, `role_pengguna`, `nama_pengguna`, `email_pengguna`, `password_pengguna`, `noHp_pengguna`) 
VALUES (4, 3, 'Bandung Parking Solutions', 'owner.bandung@spark.com', '$2y$10$WqN6GSZ1dBXojEvf8qvPLOok/RVZ.Ah/T2V7gp/qyZr/7CTe6CtHa', '081234567893')
ON DUPLICATE KEY UPDATE `nama_pengguna` = VALUES(`nama_pengguna`);

-- Owner 3: Surabaya Parking Network (password: owner123)
INSERT INTO `data_pengguna` (`id_pengguna`, `role_pengguna`, `nama_pengguna`, `email_pengguna`, `password_pengguna`, `noHp_pengguna`) 
VALUES (5, 3, 'Surabaya Parking Network', 'owner.surabaya@spark.com', '$2y$10$WqN6GSZ1dBXojEvf8qvPLOok/RVZ.Ah/T2V7gp/qyZr/7CTe6CtHa', '081234567894')
ON DUPLICATE KEY UPDATE `nama_pengguna` = VALUES(`nama_pengguna`);

-- ========================================
-- 3. OWNER PARKING PROFILES
-- ========================================

INSERT INTO `owner_parkir` (`id_owner`, `nama_parkir`, `deskripsi_parkir`, `lokasi_parkir`) VALUES
(3, 'Jakarta Parking Group', 'Jaringan parkir terbesar di Jakarta dengan 4 lokasi strategis', 'Jakarta Pusat'),
(4, 'Bandung Parking Solutions', 'Solusi parkir modern di kota Bandung dengan 3 lokasi premium', 'Bandung Kota'),
(5, 'Surabaya Parking Network', 'Parkir aman dan nyaman di Surabaya dengan 3 lokasi utama', 'Surabaya Pusat')
ON DUPLICATE KEY UPDATE `nama_parkir` = VALUES(`nama_parkir`);

-- ========================================
-- 4. REGULAR USERS (5 users)
-- ========================================

-- User 1: Budi Santoso (password: user123)
INSERT INTO `data_pengguna` (`id_pengguna`, `role_pengguna`, `nama_pengguna`, `email_pengguna`, `password_pengguna`, `noHp_pengguna`) 
VALUES (6, 1, 'Budi Santoso', 'budi.santoso@email.com', '$2y$10$4IR0CM9YH3rIsMZYWTOlN.JC0e76lOF5V7hbKO6ADr/YXHrl8Yfde', '081234567895')
ON DUPLICATE KEY UPDATE `nama_pengguna` = VALUES(`nama_pengguna`);

-- User 2: Siti Nurhaliza (password: user123)
INSERT INTO `data_pengguna` (`id_pengguna`, `role_pengguna`, `nama_pengguna`, `email_pengguna`, `password_pengguna`, `noHp_pengguna`) 
VALUES (7, 1, 'Siti Nurhaliza', 'siti.nurhaliza@email.com', '$2y$10$4IR0CM9YH3rIsMZYWTOlN.JC0e76lOF5V7hbKO6ADr/YXHrl8Yfde', '081234567896')
ON DUPLICATE KEY UPDATE `nama_pengguna` = VALUES(`nama_pengguna`);

-- User 3: Ahmad Wijaya (password: user123)
INSERT INTO `data_pengguna` (`id_pengguna`, `role_pengguna`, `nama_pengguna`, `email_pengguna`, `password_pengguna`, `noHp_pengguna`) 
VALUES (8, 1, 'Ahmad Wijaya', 'ahmad.wijaya@email.com', '$2y$10$4IR0CM9YH3rIsMZYWTOlN.JC0e76lOF5V7hbKO6ADr/YXHrl8Yfde', '081234567897')
ON DUPLICATE KEY UPDATE `nama_pengguna` = VALUES(`nama_pengguna`);

-- User 4: Dewi Lestari (password: user123)
INSERT INTO `data_pengguna` (`id_pengguna`, `role_pengguna`, `nama_pengguna`, `email_pengguna`, `password_pengguna`, `noHp_pengguna`) 
VALUES (9, 1, 'Dewi Lestari', 'dewi.lestari@email.com', '$2y$10$4IR0CM9YH3rIsMZYWTOlN.JC0e76lOF5V7hbKO6ADr/YXHrl8Yfde', '081234567898')
ON DUPLICATE KEY UPDATE `nama_pengguna` = VALUES(`nama_pengguna`);

-- User 5: Rudi Hermawan (password: user123)
INSERT INTO `data_pengguna` (`id_pengguna`, `role_pengguna`, `nama_pengguna`, `email_pengguna`, `password_pengguna`, `noHp_pengguna`) 
VALUES (10, 1, 'Rudi Hermawan', 'rudi.hermawan@email.com', '$2y$10$4IR0CM9YH3rIsMZYWTOlN.JC0e76lOF5V7hbKO6ADr/YXHrl8Yfde', '081234567899')
ON DUPLICATE KEY UPDATE `nama_pengguna` = VALUES(`nama_pengguna`);

-- ========================================
-- 5. PARKING LOCATIONS (3 locations - 1 per owner)
-- Minimal data, sisanya akan ditambah manual
-- ========================================

-- Jakarta Parking Group (Owner ID: 3) - 1 location
INSERT INTO `tempat_parkir` (`id_tempat`, `id_pemilik`, `nama_tempat`, `alamat_tempat`, `latitude`, `longitude`, `total_spot`, `harga_per_jam`, `jam_buka`, `jam_tutup`, `is_plat_required`, `foto_tempat`) VALUES
(1, 3, 'SPARK Grand Indonesia', 'Jl. MH Thamrin No. 1, Jakarta Pusat', -6.195396, 106.823141, 5, 10000, '08:00:00', '22:00:00', 1, 'parking-area/01.jpg')
ON DUPLICATE KEY UPDATE `nama_tempat` = VALUES(`nama_tempat`);

-- Bandung Parking Solutions (Owner ID: 4) - 1 location
INSERT INTO `tempat_parkir` (`id_tempat`, `id_pemilik`, `nama_tempat`, `alamat_tempat`, `latitude`, `longitude`, `total_spot`, `harga_per_jam`, `jam_buka`, `jam_tutup`, `is_plat_required`, `foto_tempat`) VALUES
(2, 4, 'SPARK Paris Van Java', 'Jl. Sukajadi No. 137-139, Bandung', -6.900000, 107.610000, 5, 8000, '08:00:00', '22:00:00', 1, 'parking-area/02.jpg')
ON DUPLICATE KEY UPDATE `nama_tempat` = VALUES(`nama_tempat`);

-- Surabaya Parking Network (Owner ID: 5) - 1 location
INSERT INTO `tempat_parkir` (`id_tempat`, `id_pemilik`, `nama_tempat`, `alamat_tempat`, `latitude`, `longitude`, `total_spot`, `harga_per_jam`, `jam_buka`, `jam_tutup`, `is_plat_required`, `foto_tempat`) VALUES
(3, 5, 'SPARK Tunjungan Plaza', 'Jl. Basuki Rahmat No. 8-12, Surabaya', -7.263056, 112.738056, 5, 9000, '08:00:00', '22:00:00', 1, 'parking-area/03.jpg')
ON DUPLICATE KEY UPDATE `nama_tempat` = VALUES(`nama_tempat`);

-- ========================================
-- 6. PARKING SLOTS (Minimal - 5 slots per location)
-- Sisanya akan ditambah manual oleh owner
-- ========================================

-- Location 1: SPARK Grand Indonesia (3 motor + 2 mobil)
INSERT INTO `slot_parkir` (`id_tempat`, `nomor_slot`, `status_slot`, `id_jenis`) VALUES
(1, 'M-01', 'available', 1),
(1, 'M-02', 'available', 1),
(1, 'M-03', 'available', 1),
(1, 'C-01', 'available', 2),
(1, 'C-02', 'available', 2)
ON DUPLICATE KEY UPDATE `status_slot` = VALUES(`status_slot`);

-- Location 2: SPARK Paris Van Java (3 motor + 2 mobil)
INSERT INTO `slot_parkir` (`id_tempat`, `nomor_slot`, `status_slot`, `id_jenis`) VALUES
(2, 'M-01', 'available', 1),
(2, 'M-02', 'available', 1),
(2, 'M-03', 'available', 1),
(2, 'C-01', 'available', 2),
(2, 'C-02', 'available', 2)
ON DUPLICATE KEY UPDATE `status_slot` = VALUES(`status_slot`);

-- Location 3: SPARK Tunjungan Plaza (3 motor + 2 mobil)
INSERT INTO `slot_parkir` (`id_tempat`, `nomor_slot`, `status_slot`, `id_jenis`) VALUES
(3, 'M-01', 'available', 1),
(3, 'M-02', 'available', 1),
(3, 'M-03', 'available', 1),
(3, 'C-01', 'available', 2),
(3, 'C-02', 'available', 2)
ON DUPLICATE KEY UPDATE `status_slot` = VALUES(`status_slot`);

-- ========================================
-- 7. PRICING (Per location and vehicle type)
-- ========================================

INSERT INTO `harga_parkir` (`id_tempat`, `id_jenis`, `harga_per_jam`) VALUES
-- Location 1: SPARK Grand Indonesia
(1, 1, 5000), (1, 2, 10000),
-- Location 2: SPARK Paris Van Java
(2, 1, 4000), (2, 2, 8000),
-- Location 3: SPARK Tunjungan Plaza
(3, 1, 4500), (3, 2, 9000)
ON DUPLICATE KEY UPDATE `harga_per_jam` = VALUES(`harga_per_jam`);

-- ========================================
-- 8. USER VEHICLES (Connected to users)
-- ========================================

-- Budi Santoso - Motor
INSERT INTO `kendaraan_pengguna` (`id_pengguna`, `id_jenis`, `plat_hash`, `plat_hint`) VALUES
(6, 1, SHA2('B1234XYZ', 256), 'B 123')
ON DUPLICATE KEY UPDATE `plat_hint` = VALUES(`plat_hint`);

-- Siti Nurhaliza - Mobil
INSERT INTO `kendaraan_pengguna` (`id_pengguna`, `id_jenis`, `plat_hash`, `plat_hint`) VALUES
(7, 2, SHA2('B5678ABC', 256), 'B 567')
ON DUPLICATE KEY UPDATE `plat_hint` = VALUES(`plat_hint`);

-- Ahmad Wijaya - Motor
INSERT INTO `kendaraan_pengguna` (`id_pengguna`, `id_jenis`, `plat_hash`, `plat_hint`) VALUES
(8, 1, SHA2('D9012DEF', 256), 'D 901')
ON DUPLICATE KEY UPDATE `plat_hint` = VALUES(`plat_hint`);

-- Dewi Lestari - Mobil
INSERT INTO `kendaraan_pengguna` (`id_pengguna`, `id_jenis`, `plat_hash`, `plat_hint`) VALUES
(9, 2, SHA2('D3456GHI', 256), 'D 345')
ON DUPLICATE KEY UPDATE `plat_hint` = VALUES(`plat_hint`);

-- Rudi Hermawan - Motor
INSERT INTO `kendaraan_pengguna` (`id_pengguna`, `id_jenis`, `plat_hash`, `plat_hint`) VALUES
(10, 1, SHA2('L7890JKL', 256), 'L 789')
ON DUPLICATE KEY UPDATE `plat_hint` = VALUES(`plat_hint`);

-- ========================================
-- SUMMARY OF CONNECTIONS (MINIMAL DATA)
-- ========================================

-- ADMIN (2):
--   - Super Admin (admin@spark.com)
--   - System Manager (manager@spark.com)
--
-- OWNERS (3):
--   - Jakarta Parking Group → 1 location (Grand Indonesia) with 5 slots
--   - Bandung Parking Solutions → 1 location (Paris Van Java) with 5 slots
--   - Surabaya Parking Network → 1 location (Tunjungan Plaza) with 5 slots
--
-- USERS (5):
--   - Budi Santoso (Motor) → Can book at any location
--   - Siti Nurhaliza (Mobil) → Can book at any location
--   - Ahmad Wijaya (Motor) → Can book at any location
--   - Dewi Lestari (Mobil) → Can book at any location
--   - Rudi Hermawan (Motor) → Can book at any location
--
-- PARKING LOCATIONS (3):
--   - Each has 5 slots (3 motor + 2 mobil)
--   - Each has pricing for both vehicle types
--   - Connected to specific owners
--   - Sisanya akan ditambah manual oleh owner
--
-- ALL PASSWORDS: admin123 / owner123 / user123

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Production data with connections loaded successfully!' AS status;
SELECT '2 Admins, 3 Owners (3 locations with 5 slots each), 5 Users' AS summary;

-- ========================================
-- BOOKING_PARKIR TABLE
-- For storing parking reservations
-- ========================================

CREATE TABLE `booking_parkir` (
  `id_booking` int(11) NOT NULL AUTO_INCREMENT,
  `id_tempat` int(11) NOT NULL,
  `id_pengguna` int(11) DEFAULT NULL COMMENT 'NULL for guest bookings',
  `email` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `nomor_telepon` varchar(20) DEFAULT NULL,
  `jenis_kendaraan` varchar(50) NOT NULL,
  `nomor_plat` varchar(20) DEFAULT NULL,
  `tanggal_booking` date NOT NULL,
  `waktu_mulai` time NOT NULL,
  `waktu_selesai` time NOT NULL,
  `durasi_jam` int(11) NOT NULL,
  `harga_per_jam` decimal(10,2) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status_booking` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `qr_token` char(64) DEFAULT NULL COMMENT 'Unique token for QR code',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_booking`),
  UNIQUE KEY `qr_token` (`qr_token`),
  KEY `id_tempat` (`id_tempat`),
  KEY `id_pengguna` (`id_pengguna`),
  KEY `status_booking` (`status_booking`),
  CONSTRAINT `booking_parkir_ibfk_1` FOREIGN KEY (`id_tempat`) REFERENCES `tempat_parkir` (`id_tempat`) ON DELETE CASCADE,
  CONSTRAINT `booking_parkir_ibfk_2` FOREIGN KEY (`id_pengguna`) REFERENCES `data_pengguna` (`id_pengguna`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

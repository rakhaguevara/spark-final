-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 02 Jan 2026 pada 02.44
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spark`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `booking_parkir`
--

CREATE TABLE `booking_parkir` (
  `id_booking` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_tempat` int(11) NOT NULL,
  `id_slot` int(11) NOT NULL,
  `waktu_mulai` datetime NOT NULL,
  `waktu_selesai` datetime NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status_booking` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_kendaraan` int(11) NOT NULL,
  `qr_secret` char(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_pengguna`
--

CREATE TABLE `data_pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `role_pengguna` int(11) NOT NULL,
  `nama_pengguna` varchar(255) NOT NULL,
  `email_pengguna` varchar(255) NOT NULL,
  `password_pengguna` varchar(255) NOT NULL,
  `noHp_pengguna` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_pengguna`
--

INSERT INTO `data_pengguna` (`id_pengguna`, `role_pengguna`, `nama_pengguna`, `email_pengguna`, `password_pengguna`, `noHp_pengguna`, `created_at`) VALUES
(3, 1, 'Rakha', 'rakha.guevara2505@gmail.com', 'Rakha25', '081374954260', '2025-12-20 03:30:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `harga_parkir`
--

CREATE TABLE `harga_parkir` (
  `id_harga` int(11) NOT NULL,
  `id_tempat` int(11) NOT NULL,
  `id_jenis` int(11) NOT NULL,
  `harga_per_jam` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_kendaraan`
--

CREATE TABLE `jenis_kendaraan` (
  `id_jenis` int(11) NOT NULL,
  `nama_jenis` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kendaraan_pengguna`
--

CREATE TABLE `kendaraan_pengguna` (
  `id_kendaraan` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_jenis` int(11) NOT NULL,
  `plat_hash` char(64) NOT NULL,
  `plat_hint` varchar(5) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi_pengguna`
--

CREATE TABLE `notifikasi_pengguna` (
  `id_notif` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `pesan` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran_booking`
--

CREATE TABLE `pembayaran_booking` (
  `id_pembayaran` int(11) NOT NULL,
  `id_booking` int(11) NOT NULL,
  `metode` varchar(50) NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `transaksi_id` varchar(255) NOT NULL,
  `status` enum('pending','success','failed') NOT NULL,
  `waktu_bayar` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `qr_session`
--

CREATE TABLE `qr_session` (
  `id_qr` int(11) NOT NULL,
  `id_booking` int(11) NOT NULL,
  `qr_token` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_pengguna`
--

CREATE TABLE `role_pengguna` (
  `id_role` int(11) NOT NULL,
  `nama_role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `role_pengguna`
--

INSERT INTO `role_pengguna` (`id_role`, `nama_role`) VALUES
(1, 'user'),
(2, 'admin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `slot_parkir`
--

CREATE TABLE `slot_parkir` (
  `id_slot` int(11) NOT NULL,
  `id_tempat` int(11) NOT NULL,
  `nomor_slot` varchar(20) NOT NULL,
  `status_slot` enum('available','booked','maintenance') NOT NULL DEFAULT 'available',
  `id_jenis` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tempat_parkir`
--

CREATE TABLE `tempat_parkir` (
  `id_tempat` int(11) NOT NULL,
  `id_pemilik` int(11) NOT NULL,
  `nama_tempat` varchar(255) NOT NULL,
  `alamat_tempat` text NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `total_spot` int(11) NOT NULL,
  `harga_per_jam` decimal(10,2) NOT NULL,
  `jam_buka` time NOT NULL,
  `jam_tutup` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_plat_required` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `ulasan_tempat`
--

CREATE TABLE `ulasan_tempat` (
  `id_ulasan` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_tempat` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `komentar` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `booking_parkir`
--
ALTER TABLE `booking_parkir`
  ADD PRIMARY KEY (`id_booking`),
  ADD KEY `fk_booking_pengguna` (`id_pengguna`),
  ADD KEY `fk_booking_tempat` (`id_tempat`),
  ADD KEY `fk_booking_slot` (`id_slot`),
  ADD KEY `fk_booking_kendaraan` (`id_kendaraan`);

--
-- Indeks untuk tabel `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `data_pengguna`
--
ALTER TABLE `data_pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD KEY `fk_role_pengguna` (`role_pengguna`);

--
-- Indeks untuk tabel `harga_parkir`
--
ALTER TABLE `harga_parkir`
  ADD PRIMARY KEY (`id_harga`),
  ADD UNIQUE KEY `id_tempat` (`id_tempat`,`id_jenis`),
  ADD KEY `id_jenis` (`id_jenis`);

--
-- Indeks untuk tabel `jenis_kendaraan`
--
ALTER TABLE `jenis_kendaraan`
  ADD PRIMARY KEY (`id_jenis`);

--
-- Indeks untuk tabel `kendaraan_pengguna`
--
ALTER TABLE `kendaraan_pengguna`
  ADD PRIMARY KEY (`id_kendaraan`),
  ADD UNIQUE KEY `plat_hash` (`plat_hash`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `id_jenis` (`id_jenis`);

--
-- Indeks untuk tabel `notifikasi_pengguna`
--
ALTER TABLE `notifikasi_pengguna`
  ADD PRIMARY KEY (`id_notif`),
  ADD KEY `fk_notif_pengguna` (`id_pengguna`);

--
-- Indeks untuk tabel `pembayaran_booking`
--
ALTER TABLE `pembayaran_booking`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `fk_pembayaran_booking` (`id_booking`);

--
-- Indeks untuk tabel `qr_session`
--
ALTER TABLE `qr_session`
  ADD PRIMARY KEY (`id_qr`),
  ADD KEY `id_booking` (`id_booking`),
  ADD KEY `qr_token` (`qr_token`),
  ADD KEY `expires_at` (`expires_at`);

--
-- Indeks untuk tabel `role_pengguna`
--
ALTER TABLE `role_pengguna`
  ADD PRIMARY KEY (`id_role`);

--
-- Indeks untuk tabel `slot_parkir`
--
ALTER TABLE `slot_parkir`
  ADD PRIMARY KEY (`id_slot`),
  ADD KEY `fk_slot_tempat` (`id_tempat`),
  ADD KEY `fk_slot_jenis` (`id_jenis`);

--
-- Indeks untuk tabel `tempat_parkir`
--
ALTER TABLE `tempat_parkir`
  ADD PRIMARY KEY (`id_tempat`),
  ADD KEY `fk_tempat_pemilik` (`id_pemilik`);

--
-- Indeks untuk tabel `ulasan_tempat`
--
ALTER TABLE `ulasan_tempat`
  ADD PRIMARY KEY (`id_ulasan`),
  ADD KEY `fk_ulasan_pengguna` (`id_pengguna`),
  ADD KEY `fk_ulasan_tempat` (`id_tempat`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `booking_parkir`
--
ALTER TABLE `booking_parkir`
  MODIFY `id_booking` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `data_pengguna`
--
ALTER TABLE `data_pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `harga_parkir`
--
ALTER TABLE `harga_parkir`
  MODIFY `id_harga` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jenis_kendaraan`
--
ALTER TABLE `jenis_kendaraan`
  MODIFY `id_jenis` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kendaraan_pengguna`
--
ALTER TABLE `kendaraan_pengguna`
  MODIFY `id_kendaraan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `notifikasi_pengguna`
--
ALTER TABLE `notifikasi_pengguna`
  MODIFY `id_notif` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pembayaran_booking`
--
ALTER TABLE `pembayaran_booking`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `qr_session`
--
ALTER TABLE `qr_session`
  MODIFY `id_qr` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `role_pengguna`
--
ALTER TABLE `role_pengguna`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `slot_parkir`
--
ALTER TABLE `slot_parkir`
  MODIFY `id_slot` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tempat_parkir`
--
ALTER TABLE `tempat_parkir`
  MODIFY `id_tempat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `ulasan_tempat`
--
ALTER TABLE `ulasan_tempat`
  MODIFY `id_ulasan` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `booking_parkir`
--
ALTER TABLE `booking_parkir`
  ADD CONSTRAINT `fk_booking_kendaraan` FOREIGN KEY (`id_kendaraan`) REFERENCES `kendaraan_pengguna` (`id_kendaraan`),
  ADD CONSTRAINT `fk_booking_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `data_pengguna` (`id_pengguna`),
  ADD CONSTRAINT `fk_booking_slot` FOREIGN KEY (`id_slot`) REFERENCES `slot_parkir` (`id_slot`),
  ADD CONSTRAINT `fk_booking_tempat` FOREIGN KEY (`id_tempat`) REFERENCES `tempat_parkir` (`id_tempat`);

--
-- Ketidakleluasaan untuk tabel `data_pengguna`
--
ALTER TABLE `data_pengguna`
  ADD CONSTRAINT `fk_role_pengguna` FOREIGN KEY (`role_pengguna`) REFERENCES `role_pengguna` (`id_role`);

--
-- Ketidakleluasaan untuk tabel `harga_parkir`
--
ALTER TABLE `harga_parkir`
  ADD CONSTRAINT `harga_parkir_ibfk_1` FOREIGN KEY (`id_tempat`) REFERENCES `tempat_parkir` (`id_tempat`),
  ADD CONSTRAINT `harga_parkir_ibfk_2` FOREIGN KEY (`id_jenis`) REFERENCES `jenis_kendaraan` (`id_jenis`);

--
-- Ketidakleluasaan untuk tabel `kendaraan_pengguna`
--
ALTER TABLE `kendaraan_pengguna`
  ADD CONSTRAINT `kendaraan_pengguna_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `data_pengguna` (`id_pengguna`),
  ADD CONSTRAINT `kendaraan_pengguna_ibfk_2` FOREIGN KEY (`id_jenis`) REFERENCES `jenis_kendaraan` (`id_jenis`);

--
-- Ketidakleluasaan untuk tabel `notifikasi_pengguna`
--
ALTER TABLE `notifikasi_pengguna`
  ADD CONSTRAINT `fk_notif_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `data_pengguna` (`id_pengguna`);

--
-- Ketidakleluasaan untuk tabel `pembayaran_booking`
--
ALTER TABLE `pembayaran_booking`
  ADD CONSTRAINT `fk_pembayaran_booking` FOREIGN KEY (`id_booking`) REFERENCES `booking_parkir` (`id_booking`);

--
-- Ketidakleluasaan untuk tabel `qr_session`
--
ALTER TABLE `qr_session`
  ADD CONSTRAINT `qr_session_ibfk_1` FOREIGN KEY (`id_booking`) REFERENCES `booking_parkir` (`id_booking`);

--
-- Ketidakleluasaan untuk tabel `slot_parkir`
--
ALTER TABLE `slot_parkir`
  ADD CONSTRAINT `fk_slot_jenis` FOREIGN KEY (`id_jenis`) REFERENCES `jenis_kendaraan` (`id_jenis`),
  ADD CONSTRAINT `fk_slot_tempat` FOREIGN KEY (`id_tempat`) REFERENCES `tempat_parkir` (`id_tempat`);

--
-- Ketidakleluasaan untuk tabel `tempat_parkir`
--
ALTER TABLE `tempat_parkir`
  ADD CONSTRAINT `fk_tempat_pemilik` FOREIGN KEY (`id_pemilik`) REFERENCES `data_pengguna` (`id_pengguna`);

--
-- Ketidakleluasaan untuk tabel `ulasan_tempat`
--
ALTER TABLE `ulasan_tempat`
  ADD CONSTRAINT `fk_ulasan_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `data_pengguna` (`id_pengguna`),
  ADD CONSTRAINT `fk_ulasan_tempat` FOREIGN KEY (`id_tempat`) REFERENCES `tempat_parkir` (`id_tempat`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

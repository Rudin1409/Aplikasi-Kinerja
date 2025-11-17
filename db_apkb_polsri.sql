-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 15 Nov 2025 pada 17.57
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
-- Database: `db_apkb_polsri`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pimpinan','jurusan','prodi') NOT NULL,
  `status` enum('aktif','non-aktif') NOT NULL DEFAULT 'aktif',
  `relasi_kode` varchar(100) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `email`, `password`, `role`, `status`, `relasi_kode`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 'Admin Utama', 'admin@polsri.ac.id', '$2y$10$onpOzD.CbbCP/8POSO3WHuPEhnRS8T5MKQo81TM.53ntqpTDAyASO', 'admin', 'aktif', NULL, '1763223413_2377d6adbe86117a1f5f.png', NULL, '2025-11-15 16:23:56'),
(2, 'pimpinan', 'pimpinan@polsri.ac.id', '$2y$10$hM1eS5cAB6VL65XmT0zoC.qx3HA5yVROzy016fwTS2AGZtv7wdz5y', 'pimpinan', 'aktif', NULL, NULL, '2025-11-15 15:23:24', '2025-11-15 15:23:24'),
(3, 'DIII Sipil', 'd3sipil@polsri.ac.id', '$2y$10$h4/oAAOPJStPfKVrA5I10O5Fcsd0JBXEZsEbJaFu1XVK1tOO6BbCe', 'prodi', 'aktif', 'J01|P01', '1763224727_cc9e3a4d654a4f71e98a.png', '2025-11-15 15:23:56', '2025-11-15 16:38:47'),
(4, 'Teknik Elektro', 'kajur_te@polsri.ac.id', '$2y$10$KXrToeQT8bnlUgLC/wslwOzT7NjWg.XeEV7jDtCh1RpK3CzGJ6/ry', 'jurusan', 'aktif', 'J03', NULL, '2025-11-15 15:24:27', '2025-11-15 15:24:27');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

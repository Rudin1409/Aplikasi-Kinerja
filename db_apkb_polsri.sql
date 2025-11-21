-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 21 Nov 2025 pada 04.34
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
-- Struktur dari tabel `jurusan`
--

CREATE TABLE `jurusan` (
  `id` int(10) UNSIGNED NOT NULL,
  `kode_jurusan` varchar(8) NOT NULL,
  `nama_jurusan` varchar(150) NOT NULL,
  `lokasi` varchar(50) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jurusan`
--

INSERT INTO `jurusan` (`id`, `kode_jurusan`, `nama_jurusan`, `lokasi`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'J01', 'Teknik Sipil', NULL, 'aktif', '2025-11-21 08:46:53', NULL, NULL),
(2, 'J02', 'Teknik Mesin', NULL, 'aktif', '2025-11-21 08:46:53', NULL, NULL),
(3, 'J03', 'Teknik Elektro', NULL, 'aktif', '2025-11-21 08:46:53', NULL, NULL),
(4, 'J04', 'Teknik Kimia', NULL, 'aktif', '2025-11-21 08:46:53', NULL, NULL),
(5, 'J05', 'Teknik Komputer', NULL, 'aktif', '2025-11-21 08:46:53', NULL, NULL),
(6, 'J06', 'Akuntansi', NULL, 'aktif', '2025-11-21 08:46:53', NULL, NULL),
(7, 'J07', 'Administrasi Bisnis', NULL, 'aktif', '2025-11-21 08:46:53', NULL, NULL),
(8, 'J08', 'Manajemen Informatika', NULL, 'aktif', '2025-11-21 08:46:53', NULL, NULL),
(9, 'J09', 'Bahasa Inggris', NULL, 'aktif', '2025-11-21 08:46:53', NULL, NULL),
(10, 'J10', 'Agribisnis', NULL, 'aktif', '2025-11-21 08:46:53', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `prodi`
--

CREATE TABLE `prodi` (
  `id` int(10) UNSIGNED NOT NULL,
  `jurusan_id` int(10) UNSIGNED NOT NULL,
  `kode_prodi` varchar(20) NOT NULL,
  `nama_prodi` varchar(200) NOT NULL,
  `jenjang` enum('DIII','DIV','S1','S2','S3') NOT NULL,
  `lokasi` varchar(50) DEFAULT NULL,
  `jumlah_mahasiswa_aktif` int(10) UNSIGNED DEFAULT 0,
  `jumlah_dosen` int(10) UNSIGNED DEFAULT 0,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `prodi`
--

INSERT INTO `prodi` (`id`, `jurusan_id`, `kode_prodi`, `nama_prodi`, `jenjang`, `lokasi`, `jumlah_mahasiswa_aktif`, `jumlah_dosen`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'TSIP-D3', 'Teknik Sipil', 'DIII', 'Palembang', 250, 15, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(2, 1, 'PJJP', 'Perancangan Jalan dan Jembatan', 'DIV', 'Palembang', 180, 10, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(3, 1, 'PJJO', 'Perancangan Jalan dan Jembatan PSDKU OKU', 'DIV', 'OKU', 80, 5, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(4, 1, 'ABG', 'Arsitektur Bangunan Gedung', 'DIV', 'Palembang', 150, 8, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(5, 2, 'TMES-D3', 'Teknik Mesin', 'DIII', 'Palembang', 300, 20, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(6, 2, 'TMPP', 'Teknik Mesin Produksi dan Perawatan', 'DIV', 'Palembang', 120, 12, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(7, 2, 'PAB', 'Pemeliharaan Alat Berat', 'DIII', 'Palembang', 90, 7, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(8, 2, 'TMPP-S', 'Teknik Mesin Produksi dan Perawatan PSDKU Siak', 'DIV', 'Siak', 60, 4, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(9, 3, 'TLES-D3', 'Teknik Listrik', 'DIII', 'Palembang', 280, 18, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(10, 3, 'TELE-D4', 'Teknik Elektro', 'DIV', 'Palembang', 100, 10, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(11, 3, 'TELK-D3', 'Teknik Elektronika', 'DIII', 'Palembang', 110, 9, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(12, 3, 'TTEL-D3', 'Teknik Telekomunikasi', 'DIII', 'Palembang', 95, 8, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(13, 3, 'TTEL-D4', 'Teknik Telekomunikasi', 'DIV', 'Palembang', 85, 7, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(14, 3, 'TREL-D4', 'Teknologi Rekayasa Instalasi Listrik', 'DIV', 'Palembang', 70, 6, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(15, 4, 'TKIM-D3', 'Teknik Kimia', 'DIII', 'Palembang', 160, 14, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(16, 4, 'TKIN-D4', 'Teknologi Kimia Industri', 'DIV', 'Palembang', 110, 9, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(17, 4, 'TET-S2', 'Teknik Energi Terbarukan', 'S2', 'Palembang', 40, 5, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(18, 4, 'TKIM-S', 'Teknik Kimia PSDKU Siak', 'DIII', 'Siak', 50, 4, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(19, 4, 'TENR-D4', 'Teknik Energi', 'DIV', 'Palembang', 60, 5, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(20, 5, 'TKOM-D3', 'Teknik Komputer', 'DIII', 'Palembang', 220, 16, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(21, 5, 'TIMD', 'Teknologi Informatika Multimedia Digital', 'DIV', 'Palembang', 140, 10, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(22, 6, 'AKUN-D3', 'Akuntansi', 'DIII', 'Palembang', 350, 25, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(23, 6, 'ASP-D4', 'Akuntansi Sektor Publik', 'DIV', 'Palembang', 190, 15, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(24, 6, 'ASP-OKU', 'Akuntansi Sektor Publik PSDKU OKU', 'DIV', 'OKU', 70, 6, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(25, 6, 'ASP-SIAK', 'Akuntansi Sektor Publik PSDKU Siak', 'DIV', 'Siak', 60, 5, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(26, 7, 'ADBI-D3', 'Administrasi Bisnis', 'DIII', 'Palembang', 320, 22, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(27, 7, 'MBIS-D4', 'Manajemen Bisnis', 'DIV', 'Palembang', 160, 14, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(28, 7, 'PIT-S2', 'Pemasaran, Inovasi, dan Teknologi', 'S2', 'Palembang', 50, 6, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(29, 7, 'ADBI-OKU', 'Administrasi Bisnis PSDKU OKU', 'DIII', 'OKU', 65, 5, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(30, 7, 'BDIG-D4', 'Bisnis Digital', 'DIV', 'Palembang', 90, 7, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(31, 7, 'UPW-D4', 'Usaha Perjalanan Wisata', 'DIV', 'Palembang', 45, 4, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(32, 8, 'MINF-D3', 'Manajemen Informatika', 'DIII', 'Palembang', 260, 18, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(33, 8, 'MINF-D4', 'Manajemen Informatika', 'DIV', 'Palembang', 130, 11, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(34, 9, 'BING-D3', 'Bahasa Inggris', 'DIII', 'Palembang', 180, 15, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(35, 9, 'BIKBP-D4', 'Bahasa Inggris untuk Komunikasi Bisnis dan Profesional', 'DIV', 'Palembang', 100, 8, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(36, 10, 'TPNG-D3', 'Teknologi Pangan', 'DIII', 'Banyuasin', 120, 10, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(37, 10, 'TPTP-D4', 'Teknologi Produksi Tanaman Perkebunan', 'DIV', 'Banyuasin', 80, 7, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(38, 10, 'AGRP-D4', 'Agribisnis Pangan', 'DIV', 'Banyuasin', 75, 6, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(39, 10, 'MAGR-D4', 'Manajemen Agribisnis', 'DIV', 'Banyuasin', 70, 6, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(40, 10, 'TAQU-D4', 'Teknologi Akuakultur', 'DIV', 'Banyuasin', 60, 5, 'aktif', '2025-11-21 08:57:19', NULL, NULL),
(41, 10, 'TRPG-D4', 'Teknologi Rekayasa Pangan', 'DIV', 'Banyuasin', 55, 5, 'aktif', '2025-11-21 08:57:19', NULL, NULL);

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
--login admin: admin@polsri.ac.id / password: admin123
-- login pimpinan: pimpinan@polsri.ac.id / password: pimpinan123
--login prodi: d3sipil@polsri.ac.id / password: d3sipil
--login jurusan: kajur_te@polsri.ac.id / password: kajurte

INSERT INTO `users` (`id`, `nama_lengkap`, `email`, `password`, `role`, `status`, `relasi_kode`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 'Admin Utama', 'admin@polsri.ac.id', '$2y$10$onpOzD.CbbCP/8POSO3WHuPEhnRS8T5MKQo81TM.53ntqpTDAyASO', 'admin', 'aktif', NULL, '1763223413_2377d6adbe86117a1f5f.png', NULL, '2025-11-15 16:23:56'),
(2, 'pimpinan', 'pimpinan@polsri.ac.id', '$2y$10$hM1eS5cAB6VL65XmT0zoC.qx3HA5yVROzy016fwTS2AGZtv7wdz5y', 'pimpinan', 'aktif', NULL, NULL, '2025-11-15 15:23:24', '2025-11-15 15:23:24'),
(3, 'DIII Sipil', 'd3sipil@polsri.ac.id', '$2y$10$h4/oAAOPJStPfKVrA5I10O5Fcsd0JBXEZsEbJaFu1XVK1tOO6BbCe', 'prodi', 'aktif', 'J01|P01', '1763224727_cc9e3a4d654a4f71e98a.png', '2025-11-15 15:23:56', '2025-11-15 16:38:47'),
(4, 'Teknik Elektro', 'kajur_te@polsri.ac.id', '$2y$10$KXrToeQT8bnlUgLC/wslwOzT7NjWg.XeEV7jDtCh1RpK3CzGJ6/ry', 'jurusan', 'aktif', 'J03', NULL, '2025-11-15 15:24:27', '2025-11-15 15:24:27');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_jurusan` (`kode_jurusan`);

--
-- Indeks untuk tabel `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_prodi` (`kode_prodi`),
  ADD UNIQUE KEY `uk_prodi_per_jurusan` (`jurusan_id`,`nama_prodi`,`jenjang`);

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
-- AUTO_INCREMENT untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `prodi`
--
ALTER TABLE `prodi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `prodi`
--
ALTER TABLE `prodi`
  ADD CONSTRAINT `fk_prodi_jurusan` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

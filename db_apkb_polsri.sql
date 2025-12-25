-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Des 2025 pada 10.57
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
-- Struktur dari tabel `iku_dua_dua`
--

CREATE TABLE `iku_dua_dua` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `triwulan_id` int(11) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `status_kepegawaian` varchar(100) DEFAULT NULL,
  `nidn` varchar(50) DEFAULT NULL,
  `nidk` varchar(50) DEFAULT NULL,
  `nup` varchar(50) DEFAULT NULL,
  `pangkat_golongan` varchar(100) DEFAULT NULL,
  `pendidikan_terakhir` varchar(50) DEFAULT NULL,
  `bidang_ilmu` varchar(255) DEFAULT NULL,
  `prodi` varchar(255) DEFAULT NULL,
  `lembaga_sertifikasi` varchar(255) DEFAULT NULL,
  `link_sertifikat` text DEFAULT NULL,
  `dunia_usaha_industri` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `iku_dua_satu`
--

CREATE TABLE `iku_dua_satu` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `triwulan_id` int(11) UNSIGNED DEFAULT NULL,
  `nama` varchar(255) NOT NULL COMMENT 'Nama Dosen',
  `tanggal_lahir` date DEFAULT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `kepangkatan` varchar(100) DEFAULT NULL,
  `status_kepegawaian` varchar(100) DEFAULT NULL,
  `nidn` varchar(50) DEFAULT NULL,
  `nidk` varchar(50) DEFAULT NULL,
  `pangkat_golongan` varchar(100) DEFAULT NULL,
  `pendidikan_terakhir` varchar(50) DEFAULT NULL COMMENT 'S1/S2/S3',
  `bidang_ilmu` varchar(255) DEFAULT NULL,
  `prodi` varchar(255) DEFAULT NULL,
  `tridharma_pt_lain` text DEFAULT NULL COMMENT 'Tridharma di Perguruan Tinggi Lain (kolom 3)',
  `tridharma_dudi` text DEFAULT NULL COMMENT 'Tridharma di DUDI by vokasi (kolom 4)',
  `tempat_praktisi` varchar(255) DEFAULT NULL,
  `nama_mahasiswa` varchar(255) DEFAULT NULL,
  `prodi_mahasiswa` varchar(255) DEFAULT NULL,
  `keterangan_praktisi` text DEFAULT NULL,
  `bukti_link` varchar(500) DEFAULT NULL COMMENT 'Bukti/link/foto',
  `point` decimal(3,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `iku_dua_tiga`
--

CREATE TABLE `iku_dua_tiga` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `triwulan_id` int(11) UNSIGNED DEFAULT NULL,
  `nama` varchar(255) NOT NULL COMMENT 'Nama Dosen',
  `tanggal_lahir` date DEFAULT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `status_kepegawaian` varchar(100) DEFAULT NULL,
  `nidn` varchar(50) DEFAULT NULL,
  `nidk` varchar(50) DEFAULT NULL,
  `pangkat_golongan` varchar(100) DEFAULT NULL,
  `pendidikan_terakhir` varchar(50) DEFAULT NULL,
  `bidang_ilmu` varchar(255) DEFAULT NULL,
  `prodi` varchar(255) DEFAULT NULL,
  `judul_karya` text DEFAULT NULL COMMENT 'Judul Artikel / Karya',
  `link_karya_ilmiah` varchar(500) DEFAULT NULL COMMENT 'Link Publikasi Karya Tulis Ilmiah',
  `link_karya_terapan` varchar(500) DEFAULT NULL COMMENT 'Link Karya Terapan',
  `link_karya_seni` varchar(500) DEFAULT NULL COMMENT 'Link Karya Seni',
  `point` decimal(3,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `iku_satu_dua`
--

CREATE TABLE `iku_satu_dua` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Relasi ke tabel users (Prodi)',
  `triwulan_id` int(11) NOT NULL COMMENT 'Relasi ke tabel triwulan',
  `nama` varchar(255) NOT NULL,
  `nim` varchar(50) NOT NULL,
  `prodi` varchar(255) DEFAULT NULL,
  `fakultas` varchar(255) DEFAULT NULL,
  `jenis_kegiatan` enum('MBKM','Prestasi') NOT NULL DEFAULT 'MBKM',
  `nama_program_mbkm` varchar(255) DEFAULT NULL COMMENT 'Contoh: Magang di PT X, KKN Desa Y',
  `jenis_mbkm` enum('Magang','Proyek Desa','Mengajar','Pertukaran Pelajar','Penelitian','Wirausaha','Studi Independen','Proyek Kemanusiaan','Bela Negara','Lainnya') DEFAULT NULL,
  `masa_pelaksanaan` varchar(100) DEFAULT NULL COMMENT 'Contoh: 6 Bulan / 1 Semester',
  `sks_diakui` float DEFAULT 0 COMMENT 'SKS yang diakui (Target: >= 20 SKS)',
  `dosen_pembimbing` varchar(255) DEFAULT NULL,
  `judul_perlombaan` varchar(255) DEFAULT NULL,
  `juara` varchar(50) DEFAULT NULL COMMENT 'Juara 1, 2, 3',
  `tingkat_prestasi` enum('Internasional','Nasional','Provinsi','Lokal') DEFAULT NULL,
  `penyelenggara` varchar(255) DEFAULT NULL,
  `link_bukti` text DEFAULT NULL COMMENT 'SK Konversi SKS atau Sertifikat Juara',
  `point` float DEFAULT 0 COMMENT '1.0 jika (SKS >= 20) ATAU (Juara 1-3 Min. Provinsi)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `iku_satu_satu`
--

CREATE TABLE `iku_satu_satu` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Relasi ke tabel users',
  `triwulan_id` int(11) NOT NULL COMMENT 'Relasi ke tabel triwulan',
  `nama` varchar(255) NOT NULL,
  `nim` varchar(50) NOT NULL,
  `prodi` varchar(255) DEFAULT NULL,
  `no_ijazah` varchar(100) DEFAULT NULL COMMENT 'Nomor Ijazah',
  `tanggal_ijazah` date DEFAULT NULL COMMENT 'Tanggal Yudisium/Ijazah',
  `tahun_lulus` year(4) NOT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `no_telp` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('Bekerja','Wirausaha','Melanjutkan Studi','Sedang Mencari Kerja') NOT NULL,
  `nama_tempat` varchar(255) NOT NULL COMMENT 'Nama Perusahaan/Usaha/Kampus',
  `pendapatan` decimal(15,2) DEFAULT 0.00 COMMENT 'Gaji per bulan',
  `ump` decimal(15,2) DEFAULT 0.00 COMMENT 'UMP Wilayah Kerja',
  `tanggal_mulai` date DEFAULT NULL,
  `masa_tunggu` int(11) DEFAULT 0 COMMENT 'Dalam bulan',
  `tingkat` enum('Lokal/Wilayah','Nasional','Internasional') DEFAULT NULL,
  `link_bukti` text DEFAULT NULL,
  `point` float DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `iku_satu_satu`
--

INSERT INTO `iku_satu_satu` (`id`, `user_id`, `triwulan_id`, `nama`, `nim`, `prodi`, `no_ijazah`, `tanggal_ijazah`, `tahun_lulus`, `nik`, `no_telp`, `email`, `status`, `nama_tempat`, `pendapatan`, `ump`, `tanggal_mulai`, `masa_tunggu`, `tingkat`, `link_bukti`, `point`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 'Bella Kartika', '062030500164', 'Akuntansi', '0624012023000739', '2025-09-15', '2025', '', '', '', 'Bekerja', 'Polres muratara', 1000000.00, 3627622.00, '2025-11-23', 2, 'Lokal/Wilayah', '', 0, '2025-11-22 23:28:45', '2025-11-23 08:43:32');

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
-- Struktur dari tabel `triwulan`
--

CREATE TABLE `triwulan` (
  `id` int(11) NOT NULL,
  `nama_triwulan` varchar(100) NOT NULL COMMENT 'Contoh: Triwulan 1 2025',
  `periode_mulai` date DEFAULT NULL,
  `periode_selesai` date DEFAULT NULL,
  `status` enum('Aktif','Tutup') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `triwulan`
--

INSERT INTO `triwulan` (`id`, `nama_triwulan`, `periode_mulai`, `periode_selesai`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Triwulan 1', '2025-01-01', '2025-03-31', 'Aktif', '2025-11-23 05:11:20', '2025-11-23 05:11:20'),
(2, 'Triwulan 2', '2025-04-01', '2025-06-30', 'Tutup', '2025-11-23 05:11:20', '2025-11-23 05:11:20'),
(3, 'Triwulan 3', '2025-07-01', '2025-09-30', 'Tutup', '2025-11-23 05:11:20', '2025-11-23 05:11:20'),
(4, 'Triwulan 4', '2025-10-01', '2025-12-31', 'Tutup', '2025-11-23 05:11:20', '2025-11-23 05:11:20');

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
(1, 'Admin Utama', 'admin@polsri.ac.id', '$2y$10$onpOzD.CbbCP/8POSO3WHuPEhnRS8T5MKQo81TM.53ntqpTDAyASO', 'admin', 'aktif', NULL, '1763918206_b7be83f75bc4d266d3c1.png', NULL, '2025-11-23 17:16:46'),
(2, 'pimpinan', 'pimpinan@polsri.ac.id', '$2y$10$hM1eS5cAB6VL65XmT0zoC.qx3HA5yVROzy016fwTS2AGZtv7wdz5y', 'pimpinan', 'aktif', NULL, NULL, '2025-11-15 15:23:24', '2025-11-15 15:23:24'),
(3, 'DIII Sipil', 'd3sipil@polsri.ac.id', '$2y$10$h4/oAAOPJStPfKVrA5I10O5Fcsd0JBXEZsEbJaFu1XVK1tOO6BbCe', 'prodi', 'aktif', 'J01|P01', '1763224727_cc9e3a4d654a4f71e98a.png', '2025-11-15 15:23:56', '2025-11-15 16:38:47'),
(4, 'Teknik Elektro', 'kajur_te@polsri.ac.id', '$2y$10$KXrToeQT8bnlUgLC/wslwOzT7NjWg.XeEV7jDtCh1RpK3CzGJ6/ry', 'jurusan', 'aktif', 'J03', NULL, '2025-11-15 15:24:27', '2025-11-15 15:24:27');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `iku_dua_dua`
--
ALTER TABLE `iku_dua_dua`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `iku_dua_satu`
--
ALTER TABLE `iku_dua_satu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_triwulan` (`triwulan_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_prodi` (`prodi`);

--
-- Indeks untuk tabel `iku_dua_tiga`
--
ALTER TABLE `iku_dua_tiga`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_triwulan` (`triwulan_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_prodi` (`prodi`);

--
-- Indeks untuk tabel `iku_satu_dua`
--
ALTER TABLE `iku_satu_dua`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_iku12` (`user_id`),
  ADD KEY `idx_triwulan_iku12` (`triwulan_id`);

--
-- Indeks untuk tabel `iku_satu_satu`
--
ALTER TABLE `iku_satu_satu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_triwulan` (`triwulan_id`);

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
-- Indeks untuk tabel `triwulan`
--
ALTER TABLE `triwulan`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT untuk tabel `iku_dua_dua`
--
ALTER TABLE `iku_dua_dua`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `iku_dua_satu`
--
ALTER TABLE `iku_dua_satu`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `iku_dua_tiga`
--
ALTER TABLE `iku_dua_tiga`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `iku_satu_dua`
--
ALTER TABLE `iku_satu_dua`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `iku_satu_satu`
--
ALTER TABLE `iku_satu_satu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT untuk tabel `triwulan`
--
ALTER TABLE `triwulan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `iku_satu_dua`
--
ALTER TABLE `iku_satu_dua`
  ADD CONSTRAINT `fk_iku12_triwulan` FOREIGN KEY (`triwulan_id`) REFERENCES `triwulan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_iku12_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `iku_satu_satu`
--
ALTER TABLE `iku_satu_satu`
  ADD CONSTRAINT `fk_iku11_triwulan` FOREIGN KEY (`triwulan_id`) REFERENCES `triwulan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_iku11_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `prodi`
--
ALTER TABLE `prodi`
  ADD CONSTRAINT `fk_prodi_jurusan` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

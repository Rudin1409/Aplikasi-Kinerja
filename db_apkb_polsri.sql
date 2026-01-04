-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 04, 2026 at 01:35 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.28

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
-- Table structure for table `jurusan`
--

CREATE TABLE `jurusan` (
  `id` int UNSIGNED NOT NULL,
  `kode_jurusan` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_jurusan` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `lokasi` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_general_ci DEFAULT 'aktif',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jurusan`
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
-- Table structure for table `master_iku`
--

CREATE TABLE `master_iku` (
  `id` int UNSIGNED NOT NULL,
  `kode` varchar(10) NOT NULL,
  `sasaran` varchar(255) NOT NULL,
  `indikator` text NOT NULL,
  `jenis` enum('Wajib','Pilihan') NOT NULL DEFAULT 'Wajib',
  `tabel_tujuan` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `master_iku`
--

INSERT INTO `master_iku` (`id`, `kode`, `sasaran`, `indikator`, `jenis`, `tabel_tujuan`, `created_at`, `updated_at`) VALUES
(1, '1', 'Talenta', 'Angka Efisiensi Edukasi Perguruan Tinggi. (*)', 'Wajib', NULL, '2025-12-24 14:15:16', '2025-12-24 14:15:16'),
(2, '2', 'Talenta', 'Persentase lulusan pendidikan tinggi & vokasi yang langsung bekerja/melanjutkan jenjang pendidikan berikutnya dalam jangka waktu 1 tahun setelah kelulusan. (*)', 'Wajib', 'iku_satu_satu', '2025-12-24 14:15:16', '2025-12-24 14:15:16'),
(3, '3', 'Talenta', 'Persentase mahasiswa S1/D4/D3/D2/D1 berkegiatan /meraih prestasi di luar program studi. (*)', 'Wajib', 'iku_satu_dua', '2025-12-24 14:15:16', '2025-12-24 14:15:16'),
(4, '4', 'Talenta', 'Jumlah Dosen PT yang mendapatkan rekognisi internasional.', 'Pilihan', 'iku_dua_tiga', '2025-12-24 14:15:16', '2025-12-24 14:15:16'),
(5, '5', 'Inovasi', 'Rasio luaran hasil kerja sama antara PT dan start-up/industri/Lembaga. (*)', 'Wajib', NULL, '2025-12-24 14:15:16', '2025-12-24 14:15:16'),
(6, '6', 'Inovasi', 'Persentase publikasi bereputasi internasional (Scopus/WoS).(**)', 'Pilihan', NULL, '2025-12-24 14:15:16', '2025-12-24 14:15:16'),
(7, '7', 'Kontribusi pada Masyarakat', 'Persentase keterlibatan Perguruan Tinggi dalam SDG 1 ((Tanpa Kemiskinan), SDG 4 (Pendidikan Berkualitas), SDG 17 (Kemitraan) dan 2 (dua) SDGs lain sesuai keunggulan.*', 'Wajib', NULL, '2025-12-24 14:15:16', '2025-12-24 14:15:16'),
(8, '8', 'Kontribusi pada Masyarakat', 'Jumlah SDM PT (dosen, peneliti) yang terlibat langsung dalam penyusunan kebijakan (nasional/daerah/industri)', 'Pilihan', NULL, '2025-12-24 14:15:16', '2025-12-24 14:15:16'),
(9, '9', 'Tata Kelola Berintegritas', 'Persentase Pendapatan Non Pendidikan/UKT*', 'Wajib', NULL, '2025-12-24 14:15:16', '2025-12-24 14:15:16'),
(10, '10', 'Tata Kelola Berintegritas', 'Jumlah usulan Zona Integritas – WBK/WBBM', 'Pilihan', NULL, '2025-12-24 14:15:16', '2025-12-24 14:15:16'),
(11, '11.1', 'Tata Kelola Berintegritas', 'Opini WTP atas Laporan Keuangan Perguruan Tinggi (Alt 1)', 'Pilihan', NULL, '2025-12-24 14:15:16', '2025-12-24 14:15:16'),
(12, '11.2', 'Tata Kelola Berintegritas', 'Predikat SAKIP Perguruan Tinggi (Alt 2)', 'Pilihan', NULL, '2025-12-24 14:15:16', '2025-12-24 14:15:16'),
(13, '11.3', 'Tata Kelola Berintegritas', 'Jumlah Laporan Pelanggaran Integritas Akademik (Alt 3)', 'Pilihan', NULL, '2025-12-24 14:15:16', '2025-12-24 14:15:16'),
(14, '11.4', 'Tata Kelola Berintegritas', 'Pencegahan dan Penanganan Anti Kekerasan, Anti Narkoba, dan Anti Korupsi (Alt 4)', 'Pilihan', NULL, '2025-12-24 14:15:16', '2025-12-24 14:15:16');

-- --------------------------------------------------------

--
-- Table structure for table `prodi`
--

CREATE TABLE `prodi` (
  `id` int UNSIGNED NOT NULL,
  `jurusan_id` int UNSIGNED NOT NULL,
  `kode_prodi` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_prodi` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `jenjang` enum('DIII','DIV','S1','S2','S3') COLLATE utf8mb4_general_ci NOT NULL,
  `lokasi` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jumlah_mahasiswa_aktif` int UNSIGNED DEFAULT '0',
  `jumlah_dosen` int UNSIGNED DEFAULT '0',
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_general_ci DEFAULT 'aktif',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prodi`
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
-- Table structure for table `tb_iku_1_lulusan`
--

CREATE TABLE `tb_iku_1_lulusan` (
  `id` int NOT NULL,
  `nim` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_triwulan` int NOT NULL,
  `tanggal_yudisium` date NOT NULL,
  `masa_studi_bulan` int DEFAULT NULL,
  `status_kelulusan` enum('Tepat Waktu','Terlambat') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_iku_1_lulusan`
--

INSERT INTO `tb_iku_1_lulusan` (`id`, `nim`, `id_triwulan`, `tanggal_yudisium`, `masa_studi_bulan`, `status_kelulusan`, `created_at`) VALUES
(1, '062430801658', 1, '2027-09-25', 36, 'Tepat Waktu', '2025-12-25 10:24:17'),
(35, '62030311001', 1, '2024-08-05', 47, 'Tepat Waktu', '2025-12-31 17:32:21'),
(36, '62030311002', 1, '2024-08-26', 47, 'Tepat Waktu', '2025-12-31 17:32:21'),
(37, '62030311003', 1, '2024-09-15', 48, 'Tepat Waktu', '2025-12-31 17:32:21');

-- --------------------------------------------------------

--
-- Table structure for table `tb_iku_2_lulusan`
--

CREATE TABLE `tb_iku_2_lulusan` (
  `id` int UNSIGNED NOT NULL,
  `nim` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_triwulan` int UNSIGNED NOT NULL DEFAULT '1',
  `jenis_aktivitas` enum('Bekerja','Wirausaha','Lanjut Studi','Mencari Kerja') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Mencari Kerja',
  `nama_tempat` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `provinsi_tempat_kerja` int DEFAULT NULL COMMENT 'FK to tb_ref_ump.id',
  `tanggal_mulai` date DEFAULT NULL,
  `gaji_bulan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `masa_tunggu_bulan` int NOT NULL DEFAULT '0',
  `posisi_wirausaha` enum('Pendiri','Freelance') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bukti_validasi` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_validasi` enum('Valid','Invalid','Menunggu') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Menunggu',
  `nilai_bobot` decimal(3,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_iku_2_lulusan`
--

INSERT INTO `tb_iku_2_lulusan` (`id`, `nim`, `id_triwulan`, `jenis_aktivitas`, `nama_tempat`, `provinsi_tempat_kerja`, `tanggal_mulai`, `gaji_bulan`, `masa_tunggu_bulan`, `posisi_wirausaha`, `bukti_validasi`, `status_validasi`, `nilai_bobot`, `created_at`, `updated_at`) VALUES
(40, '62030311001', 1, 'Bekerja', 'PT. Tekno Digital Jaya', 11, '2024-01-10', 6500000.00, 6, NULL, NULL, '', 0.60, NULL, NULL),
(42, '62030311003', 1, 'Lanjut Studi', 'Institut Teknologi Bandung', NULL, '2024-09-28', 0.00, 0, NULL, NULL, '', 1.00, NULL, NULL),
(44, '62030311002', 1, 'Wirausaha', 'Kopi Senja Nusantara', NULL, '2024-09-30', 0.00, 1, 'Pendiri', NULL, 'Menunggu', 0.75, '2025-12-31 10:38:28', '2025-12-31 10:38:28'),
(45, '062430801658', 1, 'Bekerja', 'PT DIGITALKU', 6, '2027-11-22', 5000000.00, 1, NULL, NULL, 'Menunggu', 1.00, '2025-12-31 12:22:24', '2025-12-31 12:22:24');

-- --------------------------------------------------------

--
-- Table structure for table `tb_iku_2_tracer`
--

CREATE TABLE `tb_iku_2_tracer` (
  `id` int NOT NULL,
  `nim` varchar(20) NOT NULL,
  `id_triwulan` int NOT NULL,
  `jenis_aktivitas` enum('Bekerja','Wirausaha','Lanjut Studi') NOT NULL,
  `nama_tempat` varchar(255) NOT NULL,
  `penghasilan` decimal(15,2) DEFAULT '0.00',
  `status_ump` enum('Layak','Belum Layak') DEFAULT NULL,
  `masa_tunggu_bulan` int DEFAULT '0',
  `kesesuaian_bidang` enum('Ya','Tidak') DEFAULT 'Ya',
  `bukti_validasi` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_iku_3_prestasi`
--

CREATE TABLE `tb_iku_3_prestasi` (
  `id` int NOT NULL,
  `nim` varchar(20) NOT NULL,
  `id_triwulan` int NOT NULL,
  `jenis_kegiatan` enum('Lomba','Magang','Proyek Desa','Riset','Lainnya') NOT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `tingkat` enum('Provinsi','Nasional','Internasional') NOT NULL,
  `sks_diakui` int DEFAULT '0',
  `bukti_sertifikat` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_iku_5_kerjasama`
--

CREATE TABLE `tb_iku_5_kerjasama` (
  `id` int NOT NULL,
  `nidn` varchar(20) NOT NULL,
  `id_mitra` int UNSIGNED NOT NULL,
  `id_triwulan` int NOT NULL,
  `jenis_luaran` enum('Jurnal','Produk','Kebijakan','Kurikulum') NOT NULL,
  `judul_luaran` text NOT NULL,
  `link_bukti` text,
  `sdg_tags` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_m_dosen`
--

CREATE TABLE `tb_m_dosen` (
  `nidn` varchar(20) NOT NULL,
  `nidk` varchar(20) DEFAULT NULL,
  `nup` varchar(20) DEFAULT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `kode_prodi` varchar(20) NOT NULL,
  `status_kepegawaian` enum('Tetap','Tidak Tetap') DEFAULT 'Tetap',
  `jabatan_fungsional` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_m_mahasiswa`
--

CREATE TABLE `tb_m_mahasiswa` (
  `nim` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_lengkap` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nik` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kode_prodi` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `tahun_masuk` int NOT NULL,
  `tanggal_yudisium` date DEFAULT NULL,
  `semester_masuk` enum('Ganjil','Genap') COLLATE utf8mb4_general_ci DEFAULT 'Ganjil',
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_hp` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Aktif','Lulus','Cuti','DO','Non-Aktif') COLLATE utf8mb4_general_ci DEFAULT 'Aktif',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_m_mahasiswa`
--

INSERT INTO `tb_m_mahasiswa` (`nim`, `nama_lengkap`, `nik`, `kode_prodi`, `tahun_masuk`, `tanggal_yudisium`, `semester_masuk`, `jenis_kelamin`, `email`, `no_hp`, `status`, `created_at`, `updated_at`) VALUES
('062430801658', 'M BAHRUDIN', '1605161409060001', 'ABG', 2024, '2027-09-25', 'Ganjil', 'L', 'muhammadbahrudin1409@gmail.com', '081216621271', 'Lulus', '2025-12-25 10:24:17', '2025-12-31 03:57:44'),
('62030311001', 'AHMAD SUKSES', NULL, 'ABG', 2020, '2024-08-05', 'Ganjil', NULL, NULL, NULL, 'Lulus', '2025-12-31 10:17:34', '2025-12-31 10:32:21'),
('62030311002', 'SITI PENGUSAHA', NULL, 'ABG', 2020, '2024-08-26', 'Ganjil', NULL, NULL, NULL, 'Lulus', '2025-12-31 10:17:34', '2025-12-31 10:32:21'),
('62030311003', 'BUDI SARJANA', NULL, 'ABG', 2020, '2024-09-15', 'Ganjil', NULL, NULL, NULL, 'Lulus', '2025-12-31 10:17:34', '2025-12-31 10:32:21');

-- --------------------------------------------------------

--
-- Table structure for table `tb_m_mitra`
--

CREATE TABLE `tb_m_mitra` (
  `id` int UNSIGNED NOT NULL,
  `nama_mitra` varchar(255) NOT NULL,
  `kategori` enum('Industri','Pemerintah','Pendidikan','NGO','Lainnya') NOT NULL,
  `skala` enum('Lokal','Nasional','Internasional') DEFAULT 'Nasional',
  `lokasi_provinsi` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_ref_ump`
--

CREATE TABLE `tb_ref_ump` (
  `id` int NOT NULL,
  `provinsi` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tahun` int NOT NULL,
  `nilai_ump` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_ref_ump`
--

INSERT INTO `tb_ref_ump` (`id`, `provinsi`, `tahun`, `nilai_ump`) VALUES
(1, 'Aceh', 2025, 3685615.00),
(2, 'Sumatera Utara', 2025, 2992599.00),
(3, 'Sumatera Barat', 2025, 2994193.00),
(4, 'Riau', 2025, 3508775.00),
(5, 'Jambi', 2025, 3234533.00),
(6, 'Sumatera Selatan', 2025, 3942963.00),
(7, 'Bengkulu', 2025, 2670039.00),
(8, 'Lampung', 2025, 2893069.00),
(9, 'Kep. Bangka Belitung', 2025, 3876600.00),
(10, 'Kepulauan Riau', 2025, 3623653.00),
(11, 'DKI Jakarta', 2025, 5396761.00),
(12, 'Jawa Barat', 2025, 2191238.00),
(13, 'Jawa Tengah', 2025, 2169348.00),
(14, 'DI Yogyakarta', 2025, 2264080.00),
(15, 'Jawa Timur', 2025, 2305984.00),
(16, 'Banten', 2025, 2905119.00),
(17, 'Bali', 2025, 2996560.00),
(18, 'Nusa Tenggara Barat', 2025, 2602931.00),
(19, 'Nusa Tenggara Timur', 2025, 2328969.00),
(20, 'Kalimantan Barat', 2025, 2878286.00),
(21, 'Kalimantan Tengah', 2025, 3473621.00),
(22, 'Kalimantan Selatan', 2025, 3496194.00),
(23, 'Kalimantan Timur', 2025, 3579313.00),
(24, 'Kalimantan Utara', 2025, 3580160.00),
(25, 'Sulawesi Utara', 2025, 3775425.00),
(26, 'Sulawesi Tengah', 2025, 2914583.00),
(27, 'Sulawesi Selatan', 2025, 3657527.00),
(28, 'Sulawesi Tenggara', 2025, 3073551.00),
(29, 'Gorontalo', 2025, 3221731.00),
(30, 'Sulawesi Barat', 2025, 3104430.00),
(31, 'Maluku', 2025, 3141699.00),
(32, 'Maluku Utara', 2025, 3408000.00),
(33, 'Papua', 2025, 4285848.00),
(34, 'Papua Tengah', 2025, 4285848.00),
(35, 'Papua Pegunungan', 2025, 4285848.00),
(36, 'Papua Selatan', 2025, 4285848.00),
(37, 'Papua Barat', 2025, 3615000.00),
(38, 'Papua Barat Daya', 2025, 3615000.00);

-- --------------------------------------------------------

--
-- Table structure for table `triwulan`
--

CREATE TABLE `triwulan` (
  `id` int NOT NULL,
  `nama_triwulan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Contoh: Triwulan 1 2025',
  `periode_mulai` date DEFAULT NULL,
  `periode_selesai` date DEFAULT NULL,
  `status` enum('Aktif','Tutup') COLLATE utf8mb4_general_ci DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `triwulan`
--

INSERT INTO `triwulan` (`id`, `nama_triwulan`, `periode_mulai`, `periode_selesai`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Triwulan 1', '2025-01-01', '2025-03-31', 'Aktif', '2025-11-23 05:11:20', '2025-11-23 05:11:20'),
(2, 'Triwulan 2', '2025-04-01', '2025-06-30', 'Tutup', '2025-11-23 05:11:20', '2025-11-23 05:11:20'),
(3, 'Triwulan 3', '2025-07-01', '2025-09-30', 'Tutup', '2025-11-23 05:11:20', '2025-11-23 05:11:20'),
(4, 'Triwulan 4', '2025-10-01', '2025-12-31', 'Tutup', '2025-11-23 05:11:20', '2025-11-23 05:11:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama_lengkap` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','pimpinan','jurusan','prodi') COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('aktif','non-aktif') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'aktif',
  `relasi_kode` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
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
-- Indexes for table `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_jurusan` (`kode_jurusan`);

--
-- Indexes for table `master_iku`
--
ALTER TABLE `master_iku`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_prodi` (`kode_prodi`),
  ADD UNIQUE KEY `uk_prodi_per_jurusan` (`jurusan_id`,`nama_prodi`,`jenjang`);

--
-- Indexes for table `tb_iku_1_lulusan`
--
ALTER TABLE `tb_iku_1_lulusan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nim` (`nim`),
  ADD KEY `id_triwulan` (`id_triwulan`);

--
-- Indexes for table `tb_iku_2_lulusan`
--
ALTER TABLE `tb_iku_2_lulusan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nim` (`nim`),
  ADD KEY `idx_triwulan` (`id_triwulan`),
  ADD KEY `idx_aktivitas` (`jenis_aktivitas`);

--
-- Indexes for table `tb_iku_2_tracer`
--
ALTER TABLE `tb_iku_2_tracer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nim` (`nim`),
  ADD KEY `id_triwulan` (`id_triwulan`);

--
-- Indexes for table `tb_iku_3_prestasi`
--
ALTER TABLE `tb_iku_3_prestasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nim` (`nim`),
  ADD KEY `id_triwulan` (`id_triwulan`);

--
-- Indexes for table `tb_iku_5_kerjasama`
--
ALTER TABLE `tb_iku_5_kerjasama`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nidn` (`nidn`),
  ADD KEY `id_mitra` (`id_mitra`),
  ADD KEY `id_triwulan` (`id_triwulan`);

--
-- Indexes for table `tb_m_dosen`
--
ALTER TABLE `tb_m_dosen`
  ADD PRIMARY KEY (`nidn`);

--
-- Indexes for table `tb_m_mahasiswa`
--
ALTER TABLE `tb_m_mahasiswa`
  ADD PRIMARY KEY (`nim`),
  ADD KEY `idx_prodi_mhs` (`kode_prodi`);

--
-- Indexes for table `tb_m_mitra`
--
ALTER TABLE `tb_m_mitra`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_ref_ump`
--
ALTER TABLE `tb_ref_ump`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `triwulan`
--
ALTER TABLE `triwulan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `master_iku`
--
ALTER TABLE `master_iku`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `prodi`
--
ALTER TABLE `prodi`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `tb_iku_1_lulusan`
--
ALTER TABLE `tb_iku_1_lulusan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `tb_iku_2_lulusan`
--
ALTER TABLE `tb_iku_2_lulusan`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `tb_iku_2_tracer`
--
ALTER TABLE `tb_iku_2_tracer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_iku_3_prestasi`
--
ALTER TABLE `tb_iku_3_prestasi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_iku_5_kerjasama`
--
ALTER TABLE `tb_iku_5_kerjasama`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_m_mitra`
--
ALTER TABLE `tb_m_mitra`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_ref_ump`
--
ALTER TABLE `tb_ref_ump`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `triwulan`
--
ALTER TABLE `triwulan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `prodi`
--
ALTER TABLE `prodi`
  ADD CONSTRAINT `fk_prodi_jurusan` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tb_iku_1_lulusan`
--
ALTER TABLE `tb_iku_1_lulusan`
  ADD CONSTRAINT `tb_iku_1_lulusan_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `tb_m_mahasiswa` (`nim`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_iku_1_lulusan_ibfk_2` FOREIGN KEY (`id_triwulan`) REFERENCES `triwulan` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `tb_iku_2_tracer`
--
ALTER TABLE `tb_iku_2_tracer`
  ADD CONSTRAINT `tb_iku_2_tracer_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `tb_m_mahasiswa` (`nim`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_iku_2_tracer_ibfk_2` FOREIGN KEY (`id_triwulan`) REFERENCES `triwulan` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `tb_iku_3_prestasi`
--
ALTER TABLE `tb_iku_3_prestasi`
  ADD CONSTRAINT `tb_iku_3_prestasi_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `tb_m_mahasiswa` (`nim`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_iku_3_prestasi_ibfk_2` FOREIGN KEY (`id_triwulan`) REFERENCES `triwulan` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `tb_iku_5_kerjasama`
--
ALTER TABLE `tb_iku_5_kerjasama`
  ADD CONSTRAINT `tb_iku_5_kerjasama_ibfk_1` FOREIGN KEY (`nidn`) REFERENCES `tb_m_dosen` (`nidn`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_iku_5_kerjasama_ibfk_2` FOREIGN KEY (`id_mitra`) REFERENCES `tb_m_mitra` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_iku_5_kerjasama_ibfk_3` FOREIGN KEY (`id_triwulan`) REFERENCES `triwulan` (`id`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ============================================
-- SCRIPT PERBAIKAN COLLATION DATABASE
-- Jalankan script ini di phpMyAdmin
-- ============================================

-- 1. Perbaiki collation tabel tb_iku_1_lulusan
ALTER TABLE `tb_iku_1_lulusan` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 2. Perbaiki collation tabel tb_iku_2_tracer (jika ada)
ALTER TABLE `tb_iku_2_tracer` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- 3. Perbaiki collation tabel master_iku
ALTER TABLE `master_iku` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- SELESAI! Silakan kembali ke aplikasi.

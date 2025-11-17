# =====================================================

# PANDUAN LENGKAP: Menambah Fitur Avatar ke Database

# =====================================================

## LANGKAH 1: Jalankan SQL via MySQL CMD

## =====================================================

### Cara 1: Via CMD (Windows)

1. Buka CMD (Command Prompt)
2. Masuk ke MySQL:
   ```
   cd C:\xampp\mysql\bin
   mysql -u root -p
   ```
3. Masukkan password MySQL (biasanya kosong, tekan Enter)
4. Pilih database Anda:
   ```
   USE nama_database_anda;
   ```
5. Jalankan SQL berikut:
   ```sql
   ALTER TABLE `users`
   ADD COLUMN `avatar` VARCHAR(255) NULL DEFAULT NULL AFTER `relasi_kode`;
   ```
6. Verifikasi kolom sudah ditambahkan:
   ```sql
   DESC users;
   ```
7. Keluar dari MySQL:
   ```
   exit;
   ```

### Cara 2: Via phpMyAdmin

1. Buka browser, akses: http://localhost/phpmyadmin
2. Pilih database Anda di sidebar kiri
3. Klik tab "SQL"
4. Copy-paste SQL berikut, lalu klik "Go":
   ```sql
   ALTER TABLE `users`
   ADD COLUMN `avatar` VARCHAR(255) NULL DEFAULT NULL AFTER `relasi_kode`;
   ```
5. Klik tab "Structure" untuk melihat kolom baru

---

## LANGKAH 2: Struktur Tabel Users Setelah Update

## =====================================================

Tabel `users` sekarang akan memiliki kolom:

- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- nama_lengkap (VARCHAR)
- email (VARCHAR, UNIQUE)
- password (VARCHAR, hashed)
- role (ENUM: admin, pimpinan, jurusan, prodi)
- status (ENUM: aktif, nonaktif)
- relasi_kode (VARCHAR) - untuk J01, J01|P01, dll
- avatar (VARCHAR) ← KOLOM BARU untuk nama file avatar
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

---

## LANGKAH 3: Testing Upload Avatar

## =====================================================

1. Login ke aplikasi
2. Buka menu "Pengaturan" di sidebar
3. Di tab "Profil", klik tombol "Ubah Foto"
4. Pilih gambar (JPG, PNG, max 2MB recommended)
5. Klik "Simpan Perubahan"
6. Avatar akan:
   - Tersimpan di: `writable/uploads/avatars/random_name.jpg`
   - Path disimpan di database kolom `avatar`
   - Muncul di header (kanan atas)
   - Muncul di halaman Pengaturan

---

## LANGKAH 4: Troubleshooting

## =====================================================

### Error: "Column 'avatar' doesn't exist"

- Pastikan SQL sudah dijalankan dengan benar
- Cek dengan: `DESC users;` di MySQL

### Error: "Failed to upload file"

- Pastikan folder `writable/uploads/avatars/` ada dan writable (777)
- Di CMD:
  ```
  cd e:\XAMPP\htdocs\Project-Sistem_Kinerja
  mkdir writable\uploads\avatars
  ```

### Avatar tidak muncul

- Pastikan file `.htaccess` ada di `writable/uploads/`
- Cek URL avatar: http://localhost/Project-Sistem_Kinerja/writable/uploads/avatars/nama_file.jpg
- Pastikan Apache `mod_rewrite` aktif di XAMPP

### Avatar masih menampilkan inisial huruf

- Clear session: logout lalu login lagi
- Cek apakah file benar-benar ada di folder `writable/uploads/avatars/`
- Cek database apakah kolom `avatar` terisi dengan nama file

---

## LANGKAH 5: SQL Lengkap (Copy-Paste Ready)

## =====================================================

```sql
-- Gunakan database Anda
USE nama_database_anda;

-- Tambah kolom avatar
ALTER TABLE `users`
ADD COLUMN `avatar` VARCHAR(255) NULL DEFAULT NULL AFTER `relasi_kode`;

-- Verifikasi struktur tabel
DESC `users`;

-- (Optional) Lihat semua user
SELECT id, nama_lengkap, email, role, avatar FROM users;
```

---

## FILE YANG SUDAH DIUPDATE:

## =====================================================

✅ app/Models/UserModel.php - Tambah 'avatar' di allowedFields
✅ app/Controllers/AdminController.php - Update fungsi updateProfil()
✅ app/Controllers/Auth.php - Simpan avatar ke session saat login
✅ app/Views/layouts/admin_template.php - Tampilkan avatar di header
✅ app/Views/admin/pengaturan.php - Tampilkan avatar di halaman Pengaturan
✅ writable/uploads/.htaccess - Izinkan akses public ke avatar
✅ writable/uploads/avatars/ - Folder untuk simpan avatar

---

## NOTES:

- Avatar disimpan dengan nama random untuk keamanan
- Path relatif: writable/uploads/avatars/random_name.jpg
- Database hanya simpan nama file, bukan full path
- Jika avatar NULL atau file tidak ada, tampilkan inisial nama

Selamat mencoba! 🚀

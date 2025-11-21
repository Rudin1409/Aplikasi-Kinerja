<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\JurusanModel;
use App\Models\ProdiModel;
use App\Models\KampusModel;

class AdminController extends BaseController
{
    public function dashboard()
    {
        $grafikData = [
            'labels' => ['IKU 1.1', 'IKU 1.2', 'IKU 2.1', 'IKU 2.2', 'IKU 2.3', 'IKU 3.1', 'IKU 3.2', 'IKU 3.3'],
            'values' => [85, 78, 92, 88, 75, 90, 82, 65]
        ];

        $jurusanModel = new JurusanModel();
        $jurusan_list = $jurusanModel->getList();
        $capaianJurusan = [];
        foreach ($jurusan_list as $j) {
            $capaianJurusan[] = ['nama' => $j['nama'], 'capaian' => rand(70, 95)];
        }

        $data = [
            'title' => 'Dashboard Admin',
            'page'  => 'dashboard',
            'grafikData' => $grafikData,
            'capaianJurusan' => $capaianJurusan
        ];

        return view('admin/dashboard', $data);
    }

    public function jurusan()
    {
        $jurusanModel = new JurusanModel();
        $jurusan_list = $jurusanModel->getList();

        $data = [
            'title'        => 'Master Data Jurusan',
            'page'         => 'jurusan',
            'jurusan_list' => $jurusan_list,
        ];

        return view('admin/jurusan', $data);
    }

    // Detail halaman jurusan (akun info per jurusan)
    public function jurusanDetail($kode_jurusan = null)
    {
        if (empty($kode_jurusan)) {
            return redirect()->to('admin/jurusan')->with('error', 'Kode jurusan tidak diberikan.');
        }

        $jurusanModel = new JurusanModel();
        $prodiModel = new ProdiModel();
        $kampusModel = new KampusModel();

        $jurusan = $jurusanModel->findByKode($kode_jurusan);
        if (!$jurusan) {
            return redirect()->to('admin/jurusan')->with('error', 'Jurusan tidak ditemukan.');
        }

        // Ambil daftar prodi untuk jurusan ini
        $prodi_rows = $prodiModel->getByJurusanKode($kode_jurusan);
        $jumlah_prodi = count($prodi_rows);
        $nama_nama_prodi = [];
        $sum_mahasiswa = 0;
        $sum_dosen = 0;
        $sum_lulusan = 0;
        foreach ($prodi_rows as $p) {
            $nama_nama_prodi[] = $p['nama_prodi'];
            // gunakan field jumlah_mahasiswa_aktif dan jumlah_dosen bila ada
            $sum_mahasiswa += (int) ($p['jumlah_mahasiswa_aktif'] ?? 0);
            $sum_dosen += (int) ($p['jumlah_dosen'] ?? 0);
            // jika tabel prodi memiliki kolom jumlah_lulusan_satu_tahun, gunakan itu
            if (isset($p['jumlah_lulusan_satu_tahun'])) {
                $sum_lulusan += (int) $p['jumlah_lulusan_satu_tahun'];
            }
        }

        // Jika tidak ada kolom per-prodi untuk lulusan, coba ambil dari kampus (fallback)
        $kampus_row = null;
        try {
            $kampus_row = $kampusModel->getInfo();
        } catch (\Exception $e) {
            $kampus_row = null;
            log_message('error', 'KampusModel->getInfo() failed in jurusanDetail: ' . $e->getMessage());
        }

        if ($sum_lulusan === 0) {
            $sum_lulusan = $kampus_row ? (int) ($kampus_row['jumlah_lulusan_satu_tahun'] ?? 0) : 0;
        }

        $data = [
            'title' => 'Detail Jurusan',
            'page' => 'jurusan',
            'jurusan_kode' => $kode_jurusan,
            'nama_jurusan' => $jurusan['nama_jurusan'] ?? $jurusan['kode_jurusan'],
            'kampus_nama' => $kampus_row['nama'] ?? 'Politeknik Negeri Sriwijaya',
            'jumlah_prodi' => $jumlah_prodi,
            'nama_nama_prodi' => $nama_nama_prodi,
            'lokasi' => $jurusan['lokasi'] ?? '-',
            'jumlah_mahasiswa_aktif' => $sum_mahasiswa,
            'jumlah_lulusan_satu_tahun' => $sum_lulusan,
            'jumlah_dosen' => $sum_dosen
        ];

        return view('admin/jurusan_detail', $data);
    }
    
    // FUNGSI BARU UNTUK HALAMAN PANDUAN
    public function panduan()
    {
        $data = [
            'title'     => 'Panduan Pengguna',
            'page'      => 'panduan', // OK
        ];

        return view('admin/panduan', $data);
    }
    
    // FUNGSI BARU UNTUK HALAMAN LAPORAN
    public function laporan()
    {
        $data = [
            'title'     => 'Unduh Laporan',
            'page'      => 'laporan', // OK
        ];

        return view('admin/laporan', $data);
    }
    
    // GANTI FUNGSI user() ANDA DENGAN INI (Untuk Mengirim Data Relasi dengan Kode Prodi)
    public function user()
    {
        $model = new UserModel();

        // 1. Data Jurusan (untuk dropdown) dari database
        $jurusanModel = new JurusanModel();
        $daftar_jurusan = $jurusanModel->getList();
        
        // 2. Ambil prodi lengkap dari fungsi yang sudah ada
        $prodi_list = $this->_getCompleteProdiList();

        // 3. Kelompokkan prodi berdasarkan jurusan_kode untuk JavaScript
        $prodi_grouped = [];
        foreach ($prodi_list as $prodi) {
            // Value untuk dropdown prodi akan menjadi: "Nama Prodi/Jenjang"
            $prodi['value'] = $prodi['nama_prodi'] . '/' . $prodi['jenjang'];
            // Teks yang ditampilkan di dropdown: "Nama Prodi (Jenjang)"
            $prodi['text'] = $prodi['nama_prodi'] . ' (' . $prodi['jenjang'] . ')';
            $prodi_grouped[$prodi['jurusan_kode']][] = $prodi;
        }

        $data = [
            'title'           => 'Master Data User',
            'page'            => 'user', 
            'user_list'       => $model->findAll(),
            'jurusan_list'    => $daftar_jurusan,
            // Kirim data prodi yang sudah diformat dan dikelompokkan sebagai JSON
            'prodi_list_json' => json_encode($prodi_grouped)
        ];

        return view('admin/user', $data);
    }

    // FUNGSI saveUser() YANG DIPERBARUI - FORMAT BARU KODE RELASI
    public function saveUser()
    {
        $model = new UserModel();
        
        $role = $this->request->getPost('role');
        $relasi_kode = null; // Defaultnya null

        // Logika untuk membuat relasi_kode berdasarkan role
        // Format penyimpanan:
        // - role=jurusan: "J01" (hanya kode jurusan)
        // - role=prodi: "J01|P01" (kode jurusan | kode prodi)
        
        if ($role === 'jurusan') {
            // Jika role adalah "jurusan", relasi_kode adalah kode jurusan saja
            $relasi_kode = $this->request->getPost('relasi_kode'); // Contoh: "J01"

        } elseif ($role === 'prodi') {
            // Jika role adalah "prodi", relasi_kode sudah dalam format "J01|P01"
            $relasi_kode = $this->request->getPost('relasi_kode'); // Contoh: "J01|P01"
        }

        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'email'        => $this->request->getPost('email'),
            'password'     => $this->request->getPost('password'),
            'role'         => $role,
            'status'       => $this->request->getPost('status'),
            'relasi_kode'  => $relasi_kode // Simpan kode dalam format baru
        ];
        
        // Model akan otomatis hash password karena ada callback beforeInsert di UserModel
        $model->save($data);
        
        return redirect()->to('admin/user')->with('success', 'User berhasil ditambahkan.');
    }

    // ===== FUNGSI BARU INI WAJIB DITAMBAHKAN DI BAWAH =====
    private function _getCompleteProdiList() 
    {
        // Data ini diambil dari fungsi prodiCapaian() Anda
        return [
            ['nama_prodi' => 'Teknik Sipil', 'jenjang' => 'DIII', 'jurusan_kode' => 'J01'],
            ['nama_prodi' => 'Perancangan Jalan dan Jembatan', 'jenjang' => 'DIV', 'jurusan_kode' => 'J01'],
            ['nama_prodi' => 'Perancangan Jalan dan Jembatan PSDKU OKU', 'jenjang' => 'DIV', 'jurusan_kode' => 'J01'],
            ['nama_prodi' => 'Arsitektur Bangunan Gedung', 'jenjang' => 'DIV', 'jurusan_kode' => 'J01'],
            // J02: Teknik Mesin
            ['nama_prodi' => 'Teknik Mesin', 'jenjang' => 'DIII', 'jurusan_kode' => 'J02'],
            ['nama_prodi' => 'Teknik Mesin Produksi dan Perawatan', 'jenjang' => 'DIV', 'jurusan_kode' => 'J02'],
            ['nama_prodi' => 'Pemeliharaan Alat Berat', 'jenjang' => 'DIII', 'jurusan_kode' => 'J02'],
            ['nama_prodi' => 'Teknik Mesin Produksi dan Perawatan PSDKU Siak', 'jenjang' => 'DIV', 'jurusan_kode' => 'J02'],
            // J03: Teknik Elektro
            ['nama_prodi' => 'Teknik Listrik', 'jenjang' => 'DIII', 'jurusan_kode' => 'J03'],
            ['nama_prodi' => 'Teknik Elektro', 'jenjang' => 'DIV', 'jurusan_kode' => 'J03'],
            ['nama_prodi' => 'Teknik Elektronika', 'jenjang' => 'DIII', 'jurusan_kode' => 'J03'],
            ['nama_prodi' => 'Teknik Telekomunikasi', 'jenjang' => 'DIII', 'jurusan_kode' => 'J03'],
            ['nama_prodi' => 'Teknik Telekomunikasi', 'jenjang' => 'DIV', 'jurusan_kode' => 'J03'],
            ['nama_prodi' => 'Teknologi Rekayasa Instalasi Listrik', 'jenjang' => 'DIV', 'jurusan_kode' => 'J03'],
            // J04: Teknik Kimia
            ['nama_prodi' => 'Teknik Kimia', 'jenjang' => 'DIII', 'jurusan_kode' => 'J04'],
            ['nama_prodi' => 'Teknologi Kimia Industri', 'jenjang' => 'DIV', 'jurusan_kode' => 'J04'],
            ['nama_prodi' => 'Teknik Energi Terbarukan', 'jenjang' => 'S2', 'jurusan_kode' => 'J04'],
            ['nama_prodi' => 'Teknik Kimia PSDKU Siak', 'jenjang' => 'DIII', 'jurusan_kode' => 'J04'],
            ['nama_prodi' => 'Teknik Energi', 'jenjang' => 'DIV', 'jurusan_kode' => 'J04'],
            // J05: Teknik Komputer
            ['nama_prodi' => 'Teknik Komputer', 'jenjang' => 'DIII', 'jurusan_kode' => 'J05'],
            ['nama_prodi' => 'Teknologi Informatika Multimedia Digital', 'jenjang' => 'DIV', 'jurusan_kode' => 'J05'],
            // J06: Akuntansi
            ['nama_prodi' => 'Akuntansi', 'jenjang' => 'DIII', 'jurusan_kode' => 'J06'],
            ['nama_prodi' => 'Akuntansi Sektor Publik', 'jenjang' => 'DIV', 'jurusan_kode' => 'J06'],
            ['nama_prodi' => 'Akuntansi Sektor Publik PSDKU OKU', 'jenjang' => 'DIV', 'jurusan_kode' => 'J06'],
            ['nama_prodi' => 'Akuntansi Sektor Publik PSDKU Siak', 'jenjang' => 'DIV', 'jurusan_kode' => 'J06'],
            // J07: Administrasi Bisnis
            ['nama_prodi' => 'Administrasi Bisnis', 'jenjang' => 'DIII', 'jurusan_kode' => 'J07'],
            ['nama_prodi' => 'Manajemen Bisnis', 'jenjang' => 'DIV', 'jurusan_kode' => 'J07'],
            ['nama_prodi' => 'Pemasaran, Inovasi, dan Teknologi', 'jenjang' => 'S2', 'jurusan_kode' => 'J07'],
            ['nama_prodi' => 'Administrasi Bisnis PSDKU OKU', 'jenjang' => 'DIII', 'jurusan_kode' => 'J07'],
            ['nama_prodi' => 'Bisnis Digital', 'jenjang' => 'DIV', 'jurusan_kode' => 'J07'],
            ['nama_prodi' => 'Usaha Perjalanan Wisata', 'jenjang' => 'DIV', 'jurusan_kode' => 'J07'],
            // J08: Manajemen Informatika
            ['nama_prodi' => 'Manajemen Informatika', 'jenjang' => 'DIII', 'jurusan_kode' => 'J08'],
            ['nama_prodi' => 'Manajemen Informatika', 'jenjang' => 'DIV', 'jurusan_kode' => 'J08'],
            // J09: Bahasa Inggris
            ['nama_prodi' => 'Bahasa Inggris', 'jenjang' => 'DIII', 'jurusan_kode' => 'J09'],
            ['nama_prodi' => 'Bahasa Inggris untuk Komunikasi Bisnis dan Profesional', 'jenjang' => 'DIV', 'jurusan_kode' => 'J09'],
            // J10: Agribisnis
            ['nama_prodi' => 'Teknologi Pangan', 'jenjang' => 'DIII', 'jurusan_kode' => 'J10'],
            ['nama_prodi' => 'Teknologi Produksi Tanaman Perkebunan', 'jenjang' => 'DIV', 'jurusan_kode' => 'J10'],
            ['nama_prodi' => 'Agribisnis Pangan', 'jenjang' => 'DIV', 'jurusan_kode' => 'J10'],
            ['nama_prodi' => 'Manajemen Agribisnis', 'jenjang' => 'DIV', 'jurusan_kode' => 'J10'],
            ['nama_prodi' => 'Teknologi Akuakultur', 'jenjang' => 'DIV', 'jurusan_kode' => 'J10'],
            ['nama_prodi' => 'Teknologi Rekayasa Pangan', 'jenjang' => 'DIV', 'jurusan_kode' => 'J10'],
        ];
    }
    
    // FUNGSI BARU UNTUK HALAMAN MASTER DATA IKU
    public function iku()
    {
        // Data 8 IKU
        $daftar_iku = [
            ['kode' => 'IKU 1.1', 'nama' => 'Lulusan Mendapat Pekerjaan/Studi/Wirausaha', 'sasaran' => 'S 1.0: Kualitas Lulusan'],
            ['kode' => 'IKU 1.2', 'nama' => 'Mahasiswa Mendapat Pengalaman di Luar Prodi', 'sasaran' => 'S 1.0: Kualitas Lulusan'],
            ['kode' => 'IKU 2.1', 'nama' => 'Kegiatan Dosen di Luar Kampus', 'sasaran' => 'S 2.0: Kualitas Dosen'],
            ['kode' => 'IKU 2.2', 'nama' => 'Kualifikasi Dosen & Praktisi Mengajar', 'sasaran' => 'S 2.0: Kualitas Dosen'],
            ['kode' => 'IKU 2.3', 'nama' => 'Hasil Karya Dosen (Rekognisi/Diterapkan)', 'sasaran' => 'S 2.0: Kualitas Dosen'],
            ['kode' => 'IKU 3.1', 'nama' => 'Kerjasama Program Studi dengan Mitra', 'sasaran' => 'S 3.0: Kualitas Kurikulum'],
            ['kode' => 'IKU 3.2', 'nama' => 'Metode Pembelajaran (Case/Project Based)', 'sasaran' => 'S 3.0: Kualitas Kurikulum'],
            ['kode' => 'IKU 3.3', 'nama' => 'Akreditasi Internasional Program Studi', 'sasaran' => 'S 3.0: Kualitas Kurikulum'],
        ];

        $data = [
            'title'     => 'Master Data IKU',
            'page'      => 'iku', // OK
            'iku_list' => $daftar_iku
        ];

        return view('admin/iku', $data);
    }
    // ... (setelah fungsi saveProdi() ... )

    // FUNGSI BARU UNTUK HALAMAN PENGATURAN AKUN
    public function pengaturan()
    {
        $data = [
            'title'     => 'Pengaturan',
            'page'      => 'pengaturan',
        ];

        return view('admin/pengaturan', $data);
    }
    
    // FUNGSI BARU UNTUK UPDATE PROFIL
    public function updateProfil()
    {
        $session = \Config\Services::session();
        $userId = $session->get('user_id');
        
        // Validasi user_id ada di session
        if (!$userId) {
            return redirect()->to('admin/pengaturan')->with('error', 'Session tidak valid, silakan login kembali.');
        }
        
        // Ambil data dari form
        $nama_lengkap = $this->request->getPost('nama_lengkap');
        $email = $this->request->getPost('email');
        
        // Siapkan data update
        $updateData = [
            'nama_lengkap' => $nama_lengkap,
            'email' => $email
        ];
        
        // Handle avatar upload (optional)
        $avatar = $this->request->getFile('avatar');
        if ($avatar && $avatar->isValid() && !$avatar->hasMoved()) {
            $allowedTypes = ['image/jpeg','image/jpg','image/png','image/gif'];
            if (!in_array($avatar->getMimeType(), $allowedTypes)) {
                return redirect()->to('admin/pengaturan')->with('error','Format avatar tidak didukung (gunakan JPG/PNG/GIF).');
            }
            if ($avatar->getSize() > 2_048_000) {
                return redirect()->to('admin/pengaturan')->with('error','Ukuran avatar melebihi 2MB.');
            }
            $newName = $avatar->getRandomName();
            $writablePath = WRITEPATH . 'uploads/avatars';
            if (!is_dir($writablePath)) {
                mkdir($writablePath, 0777, true);
            }
            if (!$avatar->move($writablePath, $newName)) {
                return redirect()->to('admin/pengaturan')->with('error','Gagal menyimpan file avatar.');
            }
            $updateData['avatar'] = $newName;
            $session->set('avatar', $newName);
        }
        
        // Update ke database
        $model = new UserModel();
        $updated = $model->update($userId, $updateData);
        
        if (!$updated) {
            return redirect()->to('admin/pengaturan')->with('error', 'Gagal memperbarui database.');
        }
        
        // Update session nama & email
        $session->set([
            'nama_lengkap' => $nama_lengkap,
            'email' => $email
        ]);
        
        return redirect()->to('admin/pengaturan')->with('success', 'Profil berhasil diperbarui!');
    }

    // STREAM AVATAR (Option B) - layani file dari writable/uploads/avatars
    public function avatar($filename)
    {
        // Sanitasi nama file (hindari path traversal)
        $basename = basename($filename);
        // Validasi pola aman (huruf, angka, underscore, dash, titik)
        if (!preg_match('/^[A-Za-z0-9_\.-]+$/', $basename)) {
            return $this->response->setStatusCode(400)->setBody('Invalid filename');
        }
        $path = WRITEPATH . 'uploads/avatars/' . $basename;
        if (!is_file($path)) {
            return $this->response->setStatusCode(404)->setBody('Avatar not found');
        }
        $mime = mime_content_type($path) ?: 'application/octet-stream';
        // Cache 1 jam
        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Cache-Control', 'public, max-age=3600')
            ->setHeader('Content-Length', (string) filesize($path))
            ->setBody(file_get_contents($path));
    }
    
    // FUNGSI BARU UNTUK UPDATE PASSWORD
    public function updatePassword()
    {
        $session = \Config\Services::session();
        $userId = $session->get('user_id');
        
        $password_lama = $this->request->getPost('password_lama');
        $password_baru = $this->request->getPost('password_baru');
        $konfirmasi_password = $this->request->getPost('konfirmasi_password');
        
        // Validasi password baru sama dengan konfirmasi
        if ($password_baru !== $konfirmasi_password) {
            return redirect()->to('admin/pengaturan')->with('error', 'Password baru dan konfirmasi tidak cocok!');
        }
        
        // Validasi minimal 8 karakter
        if (strlen($password_baru) < 8) {
            return redirect()->to('admin/pengaturan')->with('error', 'Password minimal 8 karakter!');
        }
        
        // Ambil user dari database untuk cek password lama
        $model = new UserModel();
        $user = $model->find($userId);
        
        if (!$user || !password_verify($password_lama, $user['password'])) {
            return redirect()->to('admin/pengaturan')->with('error', 'Password lama tidak sesuai!');
        }
        
        // Update password baru (hash otomatis via callback di UserModel)
        $model->update($userId, ['password' => $password_baru]);
        
        return redirect()->to('admin/pengaturan')->with('success', 'Password berhasil diperbarui!');
    }
    
    // FUNGSI BARU UNTUK UPDATE TAMPILAN
    public function updateTampilan()
    {
        $theme = $this->request->getPost('theme');
        $sidebar_mini = $this->request->getPost('sidebar_mini') ? 1 : 0;
        
        // Simpan preferensi ke session atau database user
        $session = \Config\Services::session();
        $session->set([
            'theme_preference' => $theme,
            'sidebar_mini_default' => $sidebar_mini
        ]);
        
        return redirect()->to('admin/pengaturan')->with('success', 'Preferensi tampilan berhasil disimpan!');
    }
    
    // FUNGSI LAMA updatePengaturan (deprecated, bisa dihapus nanti)
    public function updatePengaturan()
    {
        return redirect()->to('admin/pengaturan')->with('success', 'Pengaturan berhasil diperbarui!');
    }
    
    // Fungsi untuk menyimpan data prodi (dari route 'prodi/save')
    public function saveProdi()
    {
        $post = $this->request->getPost();

        $kode_prodi = trim($post['kode_prodi'] ?? '');
        $nama_prodi = trim($post['nama_prodi'] ?? '');
        $jenjang = trim($post['jenjang'] ?? '');
        $jurusan_kode = trim($post['jurusan_id'] ?? ''); // form sends jurusan code

        if (empty($kode_prodi) || empty($nama_prodi) || empty($jenjang) || empty($jurusan_kode)) {
            return redirect()->to('admin/prodi')->with('error', 'Semua field wajib diisi.');
        }

        $jurusanModel = new JurusanModel();
        $jurusan = $jurusanModel->findByKode($jurusan_kode);
        if (!$jurusan) {
            return redirect()->to('admin/prodi')->with('error', 'Jurusan tidak ditemukan.');
        }

        $prodiModel = new ProdiModel();
        $data = [
            'jurusan_id' => $jurusan['id'],
            'kode_prodi' => $kode_prodi,
            'nama_prodi' => $nama_prodi,
            'jenjang'    => $jenjang,
            'status'     => 'active'
        ];

        try {
            $prodiModel->insert($data);
        } catch (\Exception $e) {
            return redirect()->to('admin/prodi')->with('error', 'Gagal menyimpan prodi: ' . $e->getMessage());
        }

        return redirect()->to('admin/prodi')->with('success', 'Prodi berhasil disimpan.');
    }

    // FUNGSI BARU UNTUK REDIRECT LOGIN PRODI (INI ADALAH LANDING PAGE PRODI)
    public function prodiDashboardRedirect()
    {
        $session = \Config\Services::session();
        $relasi_kode = $session->get('relasi_kode'); // Ambil format baru: J01|P01

        // 1. Validasi format relasi_kode
        if (empty($relasi_kode) || !str_contains($relasi_kode, '|')) {
            return redirect()->to('admin/dashboard')->with('error', 'Relasi Prodi tidak valid.');
        }

        // 2. Pecah kode relasi menjadi 2 segmen: kode_jurusan dan kode_prodi
        $segments = explode('|', $relasi_kode);
        if (count($segments) !== 2) {
            return redirect()->to('admin/dashboard')->with('error', 'Format Relasi Prodi tidak sesuai.');
        }

        $jurusan_kode = $segments[0]; // Contoh: J01
        $prodi_kode = $segments[1];   // Contoh: P01

        // 3. Cari data prodi berdasarkan kode prodi dari list lengkap
        $prodi_list = $this->_getCompleteProdiList();
        
        $prodi_data = null;
        $counter = 1;
        foreach ($prodi_list as $prodi) {
            // Generate kode prodi: P01, P02, dst
            if ('P' . str_pad($counter, 2, '0', STR_PAD_LEFT) === $prodi_kode) {
                $prodi_data = $prodi;
                break;
            }
            $counter++;
        }

        if (!$prodi_data) {
            return redirect()->to('admin/dashboard')->with('error', 'Data Prodi tidak ditemukan.');
        }

        $nama_prodi = $prodi_data['nama_prodi'];
        $jenjang = $prodi_data['jenjang'];

        // 4. Redirect akhir ke halaman IKU Prodi
        // Target URL: /admin/iku-prodi/J01/Teknik%20Sipil/DIII
        return redirect()->to('admin/iku-prodi/' . $jurusan_kode . '/' . rawurlencode($nama_prodi) . '/' . $jenjang);
    }
    
    // Tampilkan halaman IKU untuk sebuah prodi
    public function ikuProdi($jurusan_kode = null, $nama_prodi = null, $jenjang = null)
    {
        if (!$jurusan_kode || !$nama_prodi || !$jenjang) {
            return redirect()->to('admin/prodi')->with('error', 'Parameter prodi tidak lengkap.');
        }

        $jurusanModel = new JurusanModel();
        $prodiModel = new ProdiModel();

        $map = $jurusanModel->getMap();
        $nama_jurusan = $map[$jurusan_kode] ?? $jurusan_kode;

        // Cari prodi berdasarkan nama+jenjang+jurusan_kode
        $candidates = $prodiModel->getByJurusanKode($jurusan_kode);
        $found = null;
        foreach ($candidates as $p) {
            if (strcasecmp($p['nama_prodi'], rawurldecode($nama_prodi)) === 0 && strcasecmp($p['jenjang'], $jenjang) === 0) {
                $found = $p; break;
            }
        }
        if (!$found && !empty($candidates)) {
            $found = $candidates[0];
        }

        // Gunakan nilai IKU tetap sesuai screenshot contoh (agar total sesuai permintaan)
        $iku_data = [
            ['kode'=>'IKU 1.1','nama'=>'Lulusan Mendapat Pekerjaan/Studi/Wirausaha','persentase'=>73,'icon'=>'trophy-outline'],
            ['kode'=>'IKU 1.2','nama'=>'Mahasiswa Mendapat Pengalaman di Luar Prodi','persentase'=>80,'icon'=>'school-outline'],
            ['kode'=>'IKU 2.1','nama'=>'Kegiatan Dosen di Luar Kampus','persentase'=>90,'icon'=>'person-outline'],
            ['kode'=>'IKU 2.2','nama'=>'Kualifikasi Dosen & Praktisi Mengajar','persentase'=>84,'icon'=>'sparkles-outline'],
            ['kode'=>'IKU 2.3','nama'=>'Hasil Karya Dosen (Rekognisi/Diterapkan)','persentase'=>76,'icon'=>'trophy-outline'],
            ['kode'=>'IKU 3.1','nama'=>'Kerjasama Program Studi dengan Mitra','persentase'=>93,'icon'=>'people-circle-outline'],
            ['kode'=>'IKU 3.2','nama'=>'Metode Pembelajaran (Case/Project Based)','persentase'=>72,'icon'=>'bulb-outline'],
            ['kode'=>'IKU 3.3','nama'=>'Akreditasi Internasional Program Studi','persentase'=>17,'icon'=>'globe-outline'],
        ];

        $data = [
            'title' => 'IKU Prodi',
            'page'  => 'iku-prodi',
            'nama_jurusan' => $nama_jurusan,
            'nama_prodi' => rawurldecode($nama_prodi),
            'jenjang' => $jenjang,
            'jurusan_kode' => $jurusan_kode,
            'iku_data' => $iku_data,
            'prodi' => $found
        ];

        return view('admin/iku_prodi', $data);
    }

    // Tampilkan detail IKU
    public function ikuDetail($iku_code = null, $jurusan_kode = null, $nama_prodi = null, $jenjang = null)
    {
        if (!$iku_code || !$jurusan_kode || !$nama_prodi || !$jenjang) {
            return redirect()->to('admin/dashboard')->with('error', 'Parameter IKU tidak lengkap.');
        }

        $jurusanModel = new JurusanModel();
        $map = $jurusanModel->getMap();
        $nama_jurusan = $map[$jurusan_kode] ?? $jurusan_kode;

        // Simulasi detail data
        $detail = [
            'iku' => rawurldecode($iku_code),
            'deskripsi' => 'Deskripsi/penjelasan untuk ' . rawurldecode($iku_code) . '.',
            'nilai' => rand(40,98),
            'target' => 85,
            'catatan' => 'Data masih simulasi karena belum ada penyimpanan IKU di database.'
        ];

        // Prepare view-specific data dependent on IKU code
        $decoded_iku = rawurldecode($iku_code);
        // Map some IKU codes to human-friendly titles (fall back to kode itself)
        $iku_titles = [
            'IKU 1.1' => 'IKU 1.1: Capaian Lulusan',
            'IKU 1.2' => 'IKU 1.2: Pengalaman Mahasiswa di Luar Prodi',
            'IKU 2.1' => 'IKU 2.1: Kegiatan Dosen di Luar Kampus',
            'IKU 2.2' => 'IKU 2.2: Kualifikasi Dosen & Praktisi',
            'IKU 2.3' => 'IKU 2.3: Hasil Karya Dosen',
            'IKU 3.1' => 'IKU 3.1: Kerjasama Program Studi',
            'IKU 3.2' => 'IKU 3.2: Metode Pembelajaran',
            'IKU 3.3' => 'IKU 3.3: Akreditasi Internasional',
        ];

        $iku_title = $iku_titles[$decoded_iku] ?? $decoded_iku;

        // Default triwulan text (could be made dynamic)
        $triwulan_text = 'TW 1 (Januari - Maret 2025)';

        // Table headers and sample data tailored for IKU 1.1 (Capaian Lulusan)
        if (stripos($decoded_iku, '1.1') !== false) {
            $table_headers = [
                'nama_lulusan' => 'NAMA LULUSAN',
                'nim' => 'NIM',
                'status' => 'STATUS (BEKERJA/STUDI/WIRAUSAHA)',
                'bukti' => 'BUKTI (SK/NIB)'
            ];

            $data_list = [
                ['nama_lulusan' => 'Ahmad Budi', 'nim' => '0623...1', 'status' => 'Bekerja', 'bukti' => 'SK.pdf'],
                ['nama_lulusan' => 'Citra Lestari', 'nim' => '0623...2', 'status' => 'Wirausaha', 'bukti' => 'NIB.pdf'],
            ];
        } else {
            // Generic headers for other IKU types
            $table_headers = [
                'keterangan' => 'Keterangan',
                'nilai' => 'Nilai',
                'bukti' => 'Bukti'
            ];
            $data_list = [
                ['keterangan' => 'Contoh data 1', 'nilai' => '75%', 'bukti' => '-'],
                ['keterangan' => 'Contoh data 2', 'nilai' => '82%', 'bukti' => '-'],
            ];
        }

        $data = [
            'title' => $iku_title,
            'page' => 'iku-detail',
            'iku_detail' => $detail,
            'nama_jurusan' => $nama_jurusan,
            'nama_prodi' => rawurldecode($nama_prodi),
            'jenjang' => $jenjang,
            'jurusan_kode' => $jurusan_kode,
            'iku_title' => $iku_title,
            'triwulan_text' => $triwulan_text,
            'table_headers' => $table_headers,
            'data_list' => $data_list,
            'tambah_button_text' => 'Tambah Data Lulusan',
            'back_url' => site_url('admin/iku-prodi/' . $jurusan_kode . '/' . rawurlencode($nama_prodi) . '/' . $jenjang)
        ];

        return view('admin/iku_detail', $data);
    }
    // Halaman laporan capaian per jurusan
    public function jurusanCapaian()
    {
        $jurusanModel = new JurusanModel();
        $rows = $jurusanModel->getList();

        $jurusan_list = [];
        $total = 0;
        foreach ($rows as $r) {
            $pers = rand(30, 95);
            $jurusan_list[] = [
                'kode' => $r['kode'],
                'nama' => $r['nama'],
                'persentase' => $pers
            ];
            $total += $pers;
        }

        $total_jurusan = count($jurusan_list);
        $rata_rata = $total_jurusan ? round($total / $total_jurusan, 1) : 0;

        $data = [
            'title' => 'Laporan Capaian Jurusan',
            'page'  => 'jurusan',
            'jurusan_list' => $jurusan_list,
            'total_jurusan' => $total_jurusan,
            'rata_rata_capaian' => $rata_rata
        ];

        return view('admin/jurusan_capaian', $data);
    }

    // Halaman laporan capaian per prodi untuk sebuah jurusan
    public function prodiCapaian($jurusan_kode = null)
    {
        if (empty($jurusan_kode)) {
            return redirect()->to('admin/jurusan-capaian')->with('error', 'Kode jurusan tidak diberikan.');
        }

        $prodiModel = new ProdiModel();
        $jurusanModel = new JurusanModel();

        $prodi_rows = $prodiModel->getByJurusanKode($jurusan_kode);
        $prodi_list = [];
        $total = 0;
        foreach ($prodi_rows as $p) {
            $pers = rand(40, 98);
            $p['persentase'] = $pers;
            $prodi_list[] = $p;
            $total += $pers;
        }

        $total_prodi = count($prodi_list);
        $rata = $total_prodi ? round($total / $total_prodi, 1) : 0;

        // Nama jurusan
        $map = $jurusanModel->getMap();
        $nama_jurusan = isset($map[$jurusan_kode]) ? $map[$jurusan_kode] : $jurusan_kode;

        $data = [
            'title' => 'Laporan Capaian Prodi',
            'page'  => 'jurusan',
            'nama_jurusan' => $nama_jurusan,
            'jurusan_kode' => $jurusan_kode,
            'prodi_list' => $prodi_list,
            'total_prodi' => $total_prodi,
            'rata_rata_capaian' => $rata
        ];

        return view('admin/prodi_capaian', $data);
    }

    // Halaman master data prodi
    public function prodi()
    {
        $jurusanModel = new JurusanModel();
        $prodiModel = new ProdiModel();

        $jurusan_list = $jurusanModel->getList();
        $prodi_list = $prodiModel->getAllWithJurusan();

        $data = [
            'title' => 'Master Data Prodi',
            'page'  => 'prodi',
            'jurusan_list' => $jurusan_list,
            'prodi_list' => $prodi_list
        ];

        return view('admin/prodi', $data);
    }

    // Tampilkan halaman edit prodi (full form)
    public function prodiEdit($id = null)
    {
        if (empty($id)) {
            return redirect()->to('admin/prodi')->with('error', 'ID Prodi tidak diberikan.');
        }
        $prodiModel = new ProdiModel();
        $jurusanModel = new JurusanModel();

        $prodi = $prodiModel->find($id);
        if (!$prodi) {
            return redirect()->to('admin/prodi')->with('error', 'Prodi tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Prodi',
            'page' => 'prodi',
            'prodi' => $prodi,
            'jurusan_list' => $jurusanModel->getList()
        ];

        return view('admin/prodi_edit', $data);
    }

    // Proses update prodi
    public function prodiUpdate($id = null)
    {
        if (empty($id)) {
            return redirect()->to('admin/prodi')->with('error', 'ID Prodi tidak diberikan.');
        }
        $prodiModel = new ProdiModel();
        $jurusanModel = new JurusanModel();

        $post = $this->request->getPost();

        // Ambil existing prodi
        $existing = $prodiModel->find($id);
        if (!$existing) {
            return redirect()->to('admin/prodi')->with('error', 'Prodi tidak ditemukan.');
        }

        // Jangan izinkan ubah kode_prodi
        $update = [
            'jurusan_id' => null,
            'nama_prodi' => trim($post['nama_prodi'] ?? $existing['nama_prodi']),
            'jenjang' => trim($post['jenjang'] ?? $existing['jenjang']),
            'lokasi' => trim($post['lokasi'] ?? $existing['lokasi']),
            'status' => $post['status'] ?? ($existing['status'] ?? 'active'),
        ];

        // Jika user memilih jurusan kode di form, cari id jurusan
        if (!empty($post['jurusan_id'])) {
            $jur = $jurusanModel->findByKode($post['jurusan_id']);
            if ($jur) {
                $update['jurusan_id'] = $jur['id'];
            }
        } else {
            // fallback ke existing jurusan_id
            $update['jurusan_id'] = $existing['jurusan_id'];
        }

        try {
            $prodiModel->update($id, $update);
        } catch (\Exception $e) {
            return redirect()->to('admin/prodi')->with('error', 'Gagal memperbarui prodi: ' . $e->getMessage());
        }

        return redirect()->to('admin/prodi')->with('success', 'Prodi berhasil diperbarui.');
    }

    // Hapus prodi
    public function prodiDelete($id = null)
    {
        if (empty($id)) {
            return redirect()->to('admin/prodi')->with('error', 'ID Prodi tidak diberikan.');
        }
        $prodiModel = new ProdiModel();
        $existing = $prodiModel->find($id);
        if (!$existing) {
            return redirect()->to('admin/prodi')->with('error', 'Prodi tidak ditemukan.');
        }
        try {
            $prodiModel->delete($id);
        } catch (\Exception $e) {
            return redirect()->to('admin/prodi')->with('error', 'Gagal menghapus prodi: ' . $e->getMessage());
        }
        return redirect()->to('admin/prodi')->with('success', 'Prodi berhasil dihapus.');
    }

    // Halaman akun / informasi kampus
    public function akun()
    {
        $jurusanModel = new JurusanModel();
        $prodiModel = new ProdiModel();
        $kampusModel = new KampusModel();

        $jurusan_rows = $jurusanModel->getList();
        $jurusan_names = array_map(function($r){ return $r['nama']; }, $jurusan_rows);
        $prodi_rows = $prodiModel->getAllWithJurusan();
        // Compute totals from `prodi` table (if values exist there)
        $sum_mahasiswa = 0;
        $sum_dosen = 0;
        try {
            $qb = $prodiModel->db->table('prodi');
            $qb->selectSum('jumlah_mahasiswa_aktif', 'sum_mahasiswa');
            $qb->selectSum('jumlah_dosen', 'sum_dosen');
            $row = $qb->get()->getRowArray();
            $sum_mahasiswa = (int) ($row['sum_mahasiswa'] ?? 0);
            $sum_dosen = (int) ($row['sum_dosen'] ?? 0);
        } catch (\Exception $e) {
            log_message('error', 'Failed to aggregate prodi totals: ' . $e->getMessage());
            $sum_mahasiswa = 0;
            $sum_dosen = 0;
        }

        // Try to fetch kampus info from DB for campus name and lulusan count;
        // if kampus table missing, fall back to defaults but still use prodi sums when available.
        $kampus_row = null;
        try {
            $kampus_row = $kampusModel->getInfo();
        } catch (\Exception $e) {
            $kampus_row = null;
            log_message('error', 'KampusModel->getInfo() failed: ' . $e->getMessage());
        }

        if ($kampus_row) {
            $kampus = [ 'nama' => $kampus_row['nama'] ];
            // Prefer aggregated prodi sums for mahasiswa and dosen when present (non-zero)
            $jumlah_mahasiswa_aktif = $sum_mahasiswa > 0 ? $sum_mahasiswa : (int) ($kampus_row['jumlah_mahasiswa_aktif'] ?? 0);
            $jumlah_dosen = $sum_dosen > 0 ? $sum_dosen : (int) ($kampus_row['jumlah_dosen'] ?? 0);
            $jumlah_lulusan_satu_tahun = (int) ($kampus_row['jumlah_lulusan_satu_tahun'] ?? 0);
        } else {
            $kampus = [ 'nama' => 'Politeknik Negeri Sriwijaya' ];
            $jumlah_mahasiswa_aktif = $sum_mahasiswa > 0 ? $sum_mahasiswa : 3100;
            $jumlah_lulusan_satu_tahun = 3000;
            $jumlah_dosen = $sum_dosen > 0 ? $sum_dosen : 500;
        }

        $data = [
            'title' => 'Informasi Kampus',
            'page' => 'akun',
            'kampus_data' => $kampus,
            'jurusan_list' => $jurusan_names,
            'jumlah_jurusan' => count($jurusan_names),
            'jumlah_prodi' => count($prodi_rows),
            'jumlah_mahasiswa_aktif' => $jumlah_mahasiswa_aktif,
            'jumlah_lulusan_satu_tahun' => $jumlah_lulusan_satu_tahun,
            'jumlah_dosen' => $jumlah_dosen
        ];

        return view('admin/akun', $data);
    }

    // Show edit form for kampus/account info
    public function akunEdit()
    {
        $kampusModel = new KampusModel();
        try {
            $kampus = $kampusModel->getInfo();
        } catch (\Exception $e) {
            $kampus = null;
            log_message('error', 'KampusModel->getInfo() failed in akunEdit: ' . $e->getMessage());
            // Inform user via flashdata when they open the edit page
            session()->setFlashdata('error', 'Tabel `kampus` tidak ditemukan di database. Anda dapat membuat tabel atau gunakan default values.');
        }
        $data = [
            'title' => 'Edit Informasi Kampus',
            'page' => 'akun',
            'kampus' => $kampus
        ];
        return view('admin/akun_edit', $data);
    }

    // Save kampus/account info
    public function akunSave()
    {
        $kampusModel = new KampusModel();

        $post = $this->request->getPost();
        // Only accept the three numeric fields for editing. Do not overwrite `nama` unless provided.
        $payload = [
            'jumlah_mahasiswa_aktif' => (int) ($post['jumlah_mahasiswa_aktif'] ?? 0),
            'jumlah_lulusan_satu_tahun' => (int) ($post['jumlah_lulusan_satu_tahun'] ?? 0),
            'jumlah_dosen' => (int) ($post['jumlah_dosen'] ?? 0),
        ];
        // If a name is explicitly provided (rare), include it; otherwise keep existing name.
        if (!empty($post['nama'])) {
            $payload['nama'] = $post['nama'];
        }
        try {
            $existing = $kampusModel->getInfo();
            if ($existing) {
                $kampusModel->update($existing['id'], $payload);
            } else {
                // If inserting and name not provided, set a sensible default.
                if (empty($payload['nama'])) {
                    $payload['nama'] = 'Politeknik Negeri Sriwijaya';
                }
                $kampusModel->insert($payload);
            }
        } catch (\Exception $e) {
            // Likely the table doesn't exist — return a helpful message to the user
            log_message('error', 'Failed to save kampus info: ' . $e->getMessage());
            $msg = 'Gagal menyimpan: tabel `kampus` tidak ditemukan. Buat tabel terlebih dahulu dengan SQL berikut:' . "\n" .
                "CREATE TABLE kampus (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(255) NOT NULL, jumlah_mahasiswa_aktif INT DEFAULT 0, jumlah_lulusan_satu_tahun INT DEFAULT 0, jumlah_dosen INT DEFAULT 0);";
            return redirect()->to('admin/akun')->with('error', $msg);
        }

        return redirect()->to('admin/akun')->with('success', 'Informasi kampus berhasil disimpan.');
    }

}
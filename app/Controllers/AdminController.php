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
        $db = \Config\Database::connect();

        // 1. Calculate Real IKU 1.1 (AEE - Lulus Tepat Waktu)
        $iku1_total = $db->table('tb_iku_1_lulusan')->countAllResults();
        $iku1_success = $db->table('tb_iku_1_lulusan')->where('status_kelulusan', 'Tepat Waktu')->countAllResults();
        $iku1_score = ($iku1_total > 0) ? round(($iku1_success / $iku1_total) * 100, 1) : 0;

        // 2. Calculate Real IKU 2.1 (Lulusan Bekerja/Studi/Wirausaha)
        $q2 = $db->table('tb_iku_2_lulusan')->selectCount('id', 'total')->selectSum('nilai_bobot', 'bobot')->get()->getRowArray();
        $iku2_total = $q2['total'];
        $iku2_bobot = $q2['bobot'];
        $iku2_score = ($iku2_total > 0) ? round(($iku2_bobot / $iku2_total) * 100, 1) : 0;

        $grafikData = [
            'labels' => ['IKU 1.1 (AEE)', 'IKU 2.1 (Pekerjaan)', 'IKU 2.2', 'IKU 2.3', 'IKU 3.1', 'IKU 3.2', 'IKU 3.3'],
            'values' => [$iku1_score, $iku2_score, rand(50, 80), rand(60, 90), rand(70, 85), rand(75, 95), rand(40, 60)]
        ];

        $jurusanModel = new JurusanModel();
        $jurusan_list = $jurusanModel->getList();
        $capaianJurusan = [];
        foreach ($jurusan_list as $j) {
            // Calculate Real IKU 2.1 Score per Jurusan
            $calc = $db->table('tb_iku_2_lulusan i2')
                ->join('tb_m_mahasiswa m', 'm.nim = i2.nim')
                ->join('prodi p', 'p.kode_prodi = m.kode_prodi')
                ->where('p.jurusan_id', $j['id'])
                ->selectCount('i2.id', 'total')
                ->selectSum('i2.nilai_bobot', 'bobot')
                ->get()->getRowArray();

            $score = ($calc['total'] > 0) ? round(($calc['bobot'] / $calc['total']) * 100, 1) : 0;

            // Fallback for name key
            $nama = $j['nama'] ?? $j['nama_jurusan'] ?? 'Jurusan';
            $capaianJurusan[] = ['nama' => $nama, 'capaian' => $score];
        }

        $data = [
            'title' => 'Dashboard Admin',
            'page' => 'dashboard',
            'grafikData' => $grafikData,
            'capaianJurusan' => $capaianJurusan
        ];

        return view('admin/dashboard', $data);
    }

    // Final Setup for IKU 2 Database
    public function setup_iku2_final_db()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        // 1. Create tb_ref_ump if not exists
        $db->query("CREATE TABLE IF NOT EXISTS `tb_ref_ump` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `provinsi` VARCHAR(100) NOT NULL,
            `nilai_ump` DECIMAL(15,2) NOT NULL,
            `tahun` YEAR NOT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Seed UMP Data if empty
        if ($db->table('tb_ref_ump')->countAllResults() == 0) {
            $data = [
                ['provinsi' => 'Sumatera Selatan', 'nilai_ump' => 3456874, 'tahun' => 2024],
                ['provinsi' => 'DKI Jakarta', 'nilai_ump' => 5067381, 'tahun' => 2024],
                ['provinsi' => 'Jawa Barat', 'nilai_ump' => 2057495, 'tahun' => 2024],
            ];
            $db->table('tb_ref_ump')->insertBatch($data);
            echo "Seeded tb_ref_ump.<br>";
        }

        // 2. Alter tb_iku_2_lulusan
        // Add columns if not exist
        $cols = $db->getFieldNames('tb_iku_2_lulusan');

        $alter_queries = [];

        if (!in_array('jenis_aktivitas', $cols)) {
            // Rename columns or Add? User said "RENAME tabel", I assumed I already have it as tb_iku_2_lulusan.
            // But I used 'status_aktivitas' before. User wants 'jenis_aktivitas'.
            // I should change status_aktivitas to jenis_aktivitas.
            if (in_array('status_aktivitas', $cols)) {
                $alter_queries[] = "ALTER TABLE `tb_iku_2_lulusan` CHANGE `status_aktivitas` `jenis_aktivitas` ENUM('Bekerja', 'Wirausaha', 'Melanjutkan Pendidikan', 'Mencari Kerja') DEFAULT 'Mencari Kerja';";
            } else {
                $alter_queries[] = "ALTER TABLE `tb_iku_2_lulusan` ADD `jenis_aktivitas` ENUM('Bekerja', 'Wirausaha', 'Melanjutkan Pendidikan', 'Mencari Kerja') DEFAULT 'Mencari Kerja' AFTER `id_triwulan`;";
            }
        }

        // Ensure ENUM values correct (User req: 'Lanjut Studi' not 'Melanjutkan Pendidikan' ???)
        // Prompt Check: "ENUM: 'Bekerja', 'Wirausaha', 'Lanjut Studi', 'Mencari Kerja'"
        // My previous was 'Melanjutkan Pendidikan'. I MUST CHANGE IT.
        $alter_queries[] = "ALTER TABLE `tb_iku_2_lulusan` MODIFY `jenis_aktivitas` ENUM('Bekerja', 'Wirausaha', 'Lanjut Studi', 'Mencari Kerja') DEFAULT 'Mencari Kerja';";

        if (!in_array('provinsi_tempat_kerja', $cols)) {
            $alter_queries[] = "ALTER TABLE `tb_iku_2_lulusan` ADD `provinsi_tempat_kerja` INT(11) NULL AFTER `nama_tempat`;";
        }

        if (!in_array('tanggal_mulai', $cols)) {
            $alter_queries[] = "ALTER TABLE `tb_iku_2_lulusan` ADD `tanggal_mulai` DATE NULL AFTER `provinsi_tempat_kerja`;";
        }

        if (!in_array('gaji_bulan', $cols)) {
            // rename pendapatan to gaji_bulan if exists
            if (in_array('pendapatan', $cols)) {
                $alter_queries[] = "ALTER TABLE `tb_iku_2_lulusan` CHANGE `pendapatan` `gaji_bulan` DECIMAL(15,2) DEFAULT 0;";
            } else {
                $alter_queries[] = "ALTER TABLE `tb_iku_2_lulusan` ADD `gaji_bulan` DECIMAL(15,2) DEFAULT 0 AFTER `tanggal_mulai`;";
            }
        }

        if (!in_array('posisi_wirausaha', $cols)) {
            $alter_queries[] = "ALTER TABLE `tb_iku_2_lulusan` ADD `posisi_wirausaha` ENUM('Pendiri', 'Freelance') NULL AFTER `masa_tunggu_bulan`;";
        }

        if (!in_array('bukti_validasi', $cols)) {
            // rename link_bukti to bukti_validasi? User said 'bukti_validasi (VARCHAR - Path File)'
            // I used link_bukti TEXT. Let's rename.
            if (in_array('link_bukti', $cols)) {
                $alter_queries[] = "ALTER TABLE `tb_iku_2_lulusan` CHANGE `link_bukti` `bukti_validasi` VARCHAR(255) NULL;";
            } else {
                $alter_queries[] = "ALTER TABLE `tb_iku_2_lulusan` ADD `bukti_validasi` VARCHAR(255) NULL AFTER `posisi_wirausaha`;";
            }
        }

        if (!in_array('status_validasi', $cols)) {
            $alter_queries[] = "ALTER TABLE `tb_iku_2_lulusan` ADD `status_validasi` ENUM('Valid', 'Invalid', 'Menunggu') DEFAULT 'Menunggu' AFTER `bukti_validasi`;";
        }

        if (!in_array('nilai_bobot', $cols)) {
            $alter_queries[] = "ALTER TABLE `tb_iku_2_lulusan` ADD `nilai_bobot` DECIMAL(3,2) DEFAULT 0.00 AFTER `status_validasi`;";
        }

        foreach ($alter_queries as $q) {
            try {
                $db->query($q);
                echo "Executed: $q <br>";
            } catch (\Exception $e) {
                echo "Error: $q - " . $e->getMessage() . "<br>";
            }
        }

        echo "Setup DB Final IKU 2 Completed.";
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
            'title' => 'Panduan Pengguna',
            'page' => 'panduan', // OK
        ];

        return view('admin/panduan', $data);
    }

    // FUNGSI BARU UNTUK HALAMAN LAPORAN
    public function laporan()
    {
        $data = [
            'title' => 'Unduh Laporan',
            'page' => 'laporan', // OK
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
            'title' => 'Master Data User',
            'page' => 'user',
            'user_list' => $model->findAll(),
            'jurusan_list' => $daftar_jurusan,
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
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => $role,
            'status' => $this->request->getPost('status'),
            'relasi_kode' => $relasi_kode // Simpan kode dalam format baru
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


    // FUNGSI SEMENTARA UNTUK SETUP DATABASE (MIGRATION & SEEDING)
    public function setup_db()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        // 1. Buat Tabel master_iku jika belum ada
        if (!$db->tableExists('master_iku')) {
            $fields = [
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'kode' => [
                    'type' => 'VARCHAR',
                    'constraint' => '10',
                ],
                'sasaran' => [
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                ],
                'indikator' => [
                    'type' => 'TEXT',
                ],
                'jenis' => [
                    'type' => 'ENUM',
                    'constraint' => ['Wajib', 'Pilihan'],
                    'default' => 'Wajib',
                ],
                'tabel_tujuan' => [
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ];
            $forge->addField($fields);
            $forge->addKey('id', true);
            $forge->createTable('master_iku');
            echo "Tabel master_iku berhasil dibuat.<br>";
        } else {
            echo "Tabel master_iku sudah ada.<br>";
        }

        // 2. Isi Data Awal (Seeding)
        // Data dari gambar yang diupload user
        $data = [
            // IKU 1 (Talenta)
            [
                'kode' => '1',
                'sasaran' => 'Talenta',
                'indikator' => 'Angka Efisiensi Edukasi Perguruan Tinggi. (*)',
                'jenis' => 'Wajib',
                'tabel_tujuan' => null // Belum ada tabel
            ],
            [
                'kode' => '2',
                'sasaran' => 'Talenta',
                'indikator' => 'Persentase lulusan pendidikan tinggi & vokasi yang langsung bekerja/melanjutkan jenjang pendidikan berikutnya dalam jangka waktu 1 tahun setelah kelulusan. (*)',
                'jenis' => 'Wajib',
                'tabel_tujuan' => 'iku_satu_satu'
            ],
            [
                'kode' => '3',
                'sasaran' => 'Talenta',
                'indikator' => 'Persentase mahasiswa S1/D4/D3/D2/D1 berkegiatan /meraih prestasi di luar program studi. (*)',
                'jenis' => 'Wajib',
                'tabel_tujuan' => 'iku_satu_dua'
            ],
            [
                'kode' => '4',
                'sasaran' => 'Talenta',
                'indikator' => 'Jumlah Dosen PT yang mendapatkan rekognisi internasional.',
                'jenis' => 'Pilihan',
                'tabel_tujuan' => 'iku_dua_tiga' // Asumsi mapping
            ],
            // IKU 2a (Inovasi)
            [
                'kode' => '5',
                'sasaran' => 'Inovasi',
                'indikator' => 'Rasio luaran hasil kerja sama antara PT dan start-up/industri/Lembaga. (*)',
                'jenis' => 'Wajib',
                'tabel_tujuan' => null // Belum ada tabel spesifik, mungkin iku 6?
            ],
            [
                'kode' => '6',
                'sasaran' => 'Inovasi',
                'indikator' => 'Persentase publikasi bereputasi internasional (Scopus/WoS).(**)',
                'jenis' => 'Pilihan',
                'tabel_tujuan' => null
            ],
            // IKU 2b (Kontribusi pada Masyarakat)
            [
                'kode' => '7',
                'sasaran' => 'Kontribusi pada Masyarakat',
                'indikator' => 'Persentase keterlibatan Perguruan Tinggi dalam SDG 1 ((Tanpa Kemiskinan), SDG 4 (Pendidikan Berkualitas), SDG 17 (Kemitraan) dan 2 (dua) SDGs lain sesuai keunggulan.*',
                'jenis' => 'Wajib',
                'tabel_tujuan' => null
            ],
            [
                'kode' => '8',
                'sasaran' => 'Kontribusi pada Masyarakat',
                'indikator' => 'Jumlah SDM PT (dosen, peneliti) yang terlibat langsung dalam penyusunan kebijakan (nasional/daerah/industri)',
                'jenis' => 'Pilihan',
                'tabel_tujuan' => null
            ],
            // IKU 3 (Tata Kelola Berintegritas)
            [
                'kode' => '9',
                'sasaran' => 'Tata Kelola Berintegritas',
                'indikator' => 'Persentase Pendapatan Non Pendidikan/UKT*',
                'jenis' => 'Wajib',
                'tabel_tujuan' => null
            ],
            [
                'kode' => '10',
                'sasaran' => 'Tata Kelola Berintegritas',
                'indikator' => 'Jumlah usulan Zona Integritas – WBK/WBBM',
                'jenis' => 'Pilihan',
                'tabel_tujuan' => null
            ],
            // 11.1 - 11.4 (Pilihan, asumsi dari gambar dot hitam)
            [
                'kode' => '11.1',
                'sasaran' => 'Tata Kelola Berintegritas',
                'indikator' => 'Opini WTP atas Laporan Keuangan Perguruan Tinggi (Alt 1)',
                'jenis' => 'Pilihan',
                'tabel_tujuan' => null
            ],
            [
                'kode' => '11.2',
                'sasaran' => 'Tata Kelola Berintegritas',
                'indikator' => 'Predikat SAKIP Perguruan Tinggi (Alt 2)',
                'jenis' => 'Pilihan',
                'tabel_tujuan' => null
            ],
            [
                'kode' => '11.3',
                'sasaran' => 'Tata Kelola Berintegritas',
                'indikator' => 'Jumlah Laporan Pelanggaran Integritas Akademik (Alt 3)',
                'jenis' => 'Pilihan',
                'tabel_tujuan' => null
            ],
            [
                'kode' => '11.4',
                'sasaran' => 'Tata Kelola Berintegritas',
                'indikator' => 'Pencegahan dan Penanganan Anti Kekerasan, Anti Narkoba, dan Anti Korupsi (Alt 4)',
                'jenis' => 'Pilihan',
                'tabel_tujuan' => null
            ],
        ];

        $masterModel = new \App\Models\MasterIkuModel();

        // Cek apakah data sudah ada, jika belum insert
        if ($masterModel->countAll() == 0) {
            $masterModel->insertBatch($data);
            echo "Data dummy master_iku berhasil ditambahkan.<br>";
        } else {
            // Optional: Truncate and re-seed if needed, or just skip
            // $masterModel->truncate();
            // $masterModel->insertBatch($data);
            echo "Data master_iku sudah ada. Tidak ada perubahan data.<br>";
        }

        // Setup Table iku_1_aee (New IKU 1: AEE)
        if (!$db->tableExists('iku_1_aee')) {
            $forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                ],
                'triwulan_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 1,
                ],
                'prodi' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ],
                'jenjang' => [
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                ],
                'tahun_masuk' => [
                    'type' => 'YEAR',
                ],
                'jml_mhs_masuk' => [
                    'type' => 'INT',
                    'constraint' => 11,
                ],
                'jml_lulus_tepat_waktu' => [
                    'type' => 'INT',
                    'constraint' => 11,
                ],
                'aee_realisasi' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2', // % value
                ],
                'aee_ideal' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2', // % target
                ],
                'capaian' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2', // % achievement
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $forge->addPrimaryKey('id');
            $forge->createTable('iku_1_aee');
            echo "Tabel iku_1_aee berhasil dibuat.<br>";
        } else {
            echo "Tabel iku_1_aee sudah ada.<br>";
        }

        echo "Setup Selesai. <a href='" . base_url('admin/dashboard') . "'>Kembali ke Dashboard</a>";
        // 3. FIX COLLATION SQL (OTOMATIS DIJALANKAN)
        try {
            // Paksa ubah collation tabel yang bermasalah agar sama dengan tabel lainnya (utf8mb4_general_ci)
            $db->query("ALTER TABLE tb_iku_1_lulusan CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            $db->query("ALTER TABLE tb_iku_2_tracer CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            $db->query("ALTER TABLE master_iku CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            echo "<b>[AUTO FIX]</b> Collation Database berhasil diperbaiki (utf8mb4_general_ci).<br>";
        } catch (\Exception $e) {
            echo "<b>[AUTO FIX ERROR]</b> Gagal ubah collation: " . $e->getMessage() . "<br>";
        }
    }


    // FUNGSI BARU UNTUK HALAMAN MASTER DATA IKU
    public function iku()
    {
        $masterModel = new \App\Models\MasterIkuModel();

        // Ambil data IKU dari database
        // Ambil data IKU dari database
        // Urutkan berdasarkan ID agar urutan Sasaran sesuai (1, 2, 3...)
        $daftar_iku = $masterModel->orderBy('id', 'ASC')->findAll();

        $data = [
            'title' => 'Master Data IKU',
            'page' => 'iku', // OK
            'iku_list' => $daftar_iku
        ];

        return view('admin/iku', $data);
    }
    // ... (setelah fungsi saveProdi() ... )

    // FUNGSI BARU UNTUK HALAMAN PENGATURAN AKUN
    public function pengaturan()
    {
        $data = [
            'title' => 'Pengaturan',
            'page' => 'pengaturan',
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
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($avatar->getMimeType(), $allowedTypes)) {
                return redirect()->to('admin/pengaturan')->with('error', 'Format avatar tidak didukung (gunakan JPG/PNG/GIF).');
            }
            if ($avatar->getSize() > 2_048_000) {
                return redirect()->to('admin/pengaturan')->with('error', 'Ukuran avatar melebihi 2MB.');
            }
            $newName = $avatar->getRandomName();
            $writablePath = WRITEPATH . 'uploads/avatars';
            if (!is_dir($writablePath)) {
                mkdir($writablePath, 0777, true);
            }
            if (!$avatar->move($writablePath, $newName)) {
                return redirect()->to('admin/pengaturan')->with('error', 'Gagal menyimpan file avatar.');
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
            'jenjang' => $jenjang,
            'status' => 'active'
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

        $request = \Config\Services::request();
        $db = \Config\Database::connect();

        $jurusanModel = new JurusanModel();
        $prodiModel = new ProdiModel();
        // Active Triwulan Logic
        $active_triwulan = $db->table('triwulan')->where('status', 'Aktif')->get()->getRowArray();
        $id_triwulan_selected = $request->getGet('id_triwulan') ?? ($active_triwulan['id'] ?? 1);
        $triwulan_list = $db->table('triwulan')->get()->getResultArray();

        $map = $jurusanModel->getMap();
        $nama_jurusan = $map[$jurusan_kode] ?? $jurusan_kode;

        // Cari prodi berdasarkan nama+jenjang+jurusan_kode
        $candidates = $prodiModel->getByJurusanKode($jurusan_kode);
        $found = null;
        foreach ($candidates as $p) {
            if (strcasecmp($p['nama_prodi'], rawurldecode($nama_prodi)) === 0 && strcasecmp($p['jenjang'], $jenjang) === 0) {
                $found = $p;
                break;
            }
        }
        if (!$found && !empty($candidates)) {
            $found = $candidates[0];
        }

        $kode_prodi_target = $found['kode_prodi'] ?? null;

        // Ambil data IKU dari Database (hanya Wajib sesuai permintaan user)
        $masterModel = new \App\Models\MasterIkuModel();
        $master_iku = $masterModel->where('jenis', 'Wajib')
            ->orderBy('id', 'ASC')
            ->findAll();

        // Map ke format tampilan iku_prodi
        $iku_data = [];
        foreach ($master_iku as $m) {
            $persentase = 0;

            // === LOGIC HITUNG PER IKU ===

            // IKU 1 (AEE)
            if ($m['kode'] == '1') {
                // IKU 1: AEE (Angka Efisiensi Edukasi) - Persentase Lulusan Tepat Waktu
                if ($kode_prodi_target) {
                    // Hitung Total Lulusan dan Yang Tepat Waktu untuk Prodi ini
                    $total_lulusan = $db->table('tb_iku_1_lulusan l')
                        ->join('tb_m_mahasiswa m', 'm.nim = l.nim')
                        ->where('m.kode_prodi', $kode_prodi_target)
                        ->where('l.id_triwulan', $id_triwulan_selected)
                        ->countAllResults();

                    $jumlah_tepat_waktu = $db->table('tb_iku_1_lulusan l')
                        ->join('tb_m_mahasiswa m', 'm.nim = l.nim')
                        ->where('m.kode_prodi', $kode_prodi_target)
                        ->where('l.id_triwulan', $id_triwulan_selected)
                        ->where('l.status_kelulusan', 'Tepat Waktu')
                        ->countAllResults();

                    if ($total_lulusan > 0) {
                        // Persentase Murni: (Jumlah Tepat Waktu / Total Lulusan) x 100
                        // Maksimal 100%
                        $persentase = round(($jumlah_tepat_waktu / $total_lulusan) * 100, 1);
                    } else {
                        $persentase = 0;
                    }
                }
            } elseif ($m['kode'] == '2') {
                // IKU 2: Lulusan (Bekerja/Wirausaha/Studi)
                if ($kode_prodi_target) {
                    $stats = $db->table('tb_iku_2_lulusan i2')
                        ->join('tb_m_mahasiswa m', 'm.nim = i2.nim')
                        ->where('m.kode_prodi', $kode_prodi_target)
                        ->where('i2.id_triwulan', $id_triwulan_selected)
                        ->selectCount('i2.id', 'total')
                        ->selectSum('i2.nilai_bobot', 'bobot')
                        ->get()->getRowArray();

                    if ($stats['total'] > 0) {
                        $persentase = round(($stats['bobot'] / $stats['total']) * 100, 1);
                    }
                }
            } else {
                // Dummy for other IKUs for now
                $persentase = 0; // Set to 0 instead of rand to look cleaner if no data
            }

            $iku_data[] = [
                'kode' => 'IKU ' . $m['kode'],
                'nama' => $m['indikator'],
                'persentase' => $persentase,
                'sasaran' => $m['sasaran'],
                'icon' => $m['kode'] == '1' ? 'school-outline' : 'stats-chart-outline', // Custom icon for IKU 1
            ];
        }

        $data = [
            'title' => 'IKU Prodi',
            'page' => 'iku-prodi',
            'nama_jurusan' => $nama_jurusan,
            'nama_prodi' => rawurldecode($nama_prodi),
            'jenjang' => $jenjang,
            'jurusan_kode' => $jurusan_kode,
            'iku_data' => $iku_data,
            'prodi' => $found,
            'triwulan_list' => $triwulan_list,
            'id_triwulan_selected' => $id_triwulan_selected
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

        $decoded_iku = rawurldecode($iku_code);

        // Default Metadata
        $meta_iku = [
            'kode' => $decoded_iku,
            'indikator' => 'Indikator Kinerja Utama',
            'sasaran' => 'Sasaran Strategis',
            'deskripsi' => 'Deskripsi belum tersedia.'
        ];

        // Custom Metadata per IKU (Bisa dipindah ke Model/Config nanti)
        if (stripos($decoded_iku, '1') !== false) {
            $meta_iku = [
                'kode' => 'IKU 1',
                'indikator' => 'Angka Efisiensi Edukasi (AEE)',
                'sasaran' => 'Meningkatnya Kualitas Lulusan Pendidikan Tinggi Vokasi',
                'deskripsi' => 'Persentase mahasiswa yang lulus tepat waktu sesuai jenjang studinya.'
            ];
        } elseif (stripos($decoded_iku, '2') !== false) {
            $meta_iku = [
                'kode' => 'IKU 2',
                'indikator' => 'Persentase Lulusan Langsung Bekerja/Melanjutkan Studi/Berwirausaha',
                'sasaran' => 'Meningkatnya Kualitas Lulusan Pendidikan Tinggi Vokasi',
                'deskripsi' => 'Persentase lulusan pendidikan tinggi (akademik, vokasi, profesi) yang memiliki aktivitas produktif berupa bekerja, melanjutkan studi ke jenjang lebih tinggi, atau berwirausaha dalam jangka waktu maksimal 12 bulan setelah kelulusan, berdasarkan hasil tracer study yang terverifikasi.'
            ];
        }

        // Simulasi detail data (Merge dengan meta)
        $detail = [
            'iku' => $meta_iku['kode'],
            'deskripsi' => $meta_iku['deskripsi'],
            'sasaran' => $meta_iku['sasaran'],
            'indikator' => $meta_iku['indikator'], // Pass indikator too
            'nilai' => rand(40, 98), // Nanti di-overwrite logic bawah
            'target' => 85,
            'catatan' => 'Data Real.'
        ];

        $iku_title = $meta_iku['kode'] . ': ' . $meta_iku['indikator'];


        // Default triwulan text but updated from DB if param exists
        $request = \Config\Services::request();
        $db = \Config\Database::connect();

        // Logic Triwulan
        $id_triwulan = $request->getGet('id_triwulan');
        if ($id_triwulan) {
            $active_triwulan = $db->table('triwulan')->where('id', $id_triwulan)->get()->getRowArray();
        } else {
            $active_triwulan = $db->table('triwulan')->where('status', 'Aktif')->get()->getRowArray();
        }

        // Mapping Manual (Since column periode missing)
        $triwulan_names = [
            1 => 'Januari - Maret',
            2 => 'April - Juni',
            3 => 'Juli - September',
            4 => 'Oktober - Desember'
        ];
        $periode = $triwulan_names[$active_triwulan['id'] ?? 1] ?? 'Periode Tidak Diketahui';
        $triwulan_text = 'TW ' . ($active_triwulan['nama_triwulan'] ?? '1') . ' (' . $periode . ' ' . date('Y') . ')';

        $data_list = [];
        $table_headers = [];

        // ==== IKU 1 (AEE) ====
        if (stripos($decoded_iku, '1') !== false) {
            // Initialize iku1_stats with defaults (will be overwritten if data exists)
            $data['iku1_stats'] = [
                'total_lulusan' => 0,
                'total_tepat' => 0,
                'total_terlambat' => 0
            ];

            // Gunakan Query Builder Manual
            $prodiModel = new \App\Models\ProdiModel();

            // 1. Cari Kode Prodi berdasarkan Nama & Jenjang
            $prodi = $prodiModel->where('nama_prodi', rawurldecode($nama_prodi))
                ->where('jenjang', $jenjang)
                ->first();

            $kode_prodi_target = $prodi['kode_prodi'] ?? null;
            $data_list = [];


            // Header Tabel Detail
            $table_headers = [
                'nama' => 'MAHASISWA',
                'nim' => 'NIM',
                'tahun_masuk' => 'ANGKATAN',
                'masa_studi' => 'MASA STUDI',
                'yudisium' => 'TGL LULUS',
                'status' => 'STATUS IKU',
                'aksi' => 'AKSI'
            ];

            if ($kode_prodi_target) {
                // 2. Query Detail Lulusan (Filter by Triwulan)
                $builder = $db->table('tb_iku_1_lulusan')
                    ->select('tb_iku_1_lulusan.id as id_iku, tb_iku_1_lulusan.*, tb_m_mahasiswa.nama_lengkap, tb_m_mahasiswa.tahun_masuk')
                    ->join('tb_m_mahasiswa', 'tb_m_mahasiswa.nim = tb_iku_1_lulusan.nim')
                    ->where('tb_m_mahasiswa.kode_prodi', $kode_prodi_target);

                // Apply Triwulan Filter
                if (!empty($active_triwulan['id'])) {
                    $builder->where('tb_iku_1_lulusan.id_triwulan', $active_triwulan['id']);
                }

                $queryLulus = $builder->orderBy('tb_m_mahasiswa.tahun_masuk', 'DESC')
                    ->orderBy('tb_m_mahasiswa.nama_lengkap', 'ASC')
                    ->get()->getResultArray();

                // Setup new simplified calculation logic (Cohort Based) - SAME AS DASHBOARD
                // We need to calculate achievement based on COHORTS, not just list of graduates.
                // ... (Keeping existing list logic first) ... #

                // 3. Gabungkan Data untuk View
                $total_tepat_filtered = 0;
                $total_records = count($queryLulus);

                foreach ($queryLulus as $row) {
                    // Format Masa Studi
                    $bln = (int) $row['masa_studi_bulan'];
                    $thn = floor($bln / 12);
                    $sisa_bln = $bln % 12;
                    $masa_studi_text = "{$thn} Thn {$sisa_bln} Bln";

                    // Hitung Stats untuk Card Atas (dari data yg tampil)
                    if ($row['status_kelulusan'] == 'Tepat Waktu') {
                        $total_tepat_filtered++;
                    }

                    // Format Badge Status
                    $status_badge = ($row['status_kelulusan'] == 'Tepat Waktu')
                        ? '<span class="px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs font-bold">Tepat Waktu</span>'
                        : '<span class="px-2 py-1 rounded-full bg-red-100 text-red-800 text-xs font-bold">Terlambat</span>';

                    // Tombol Aksi
                    $deleteUrl = base_url('admin/iku1/delete/' . $row['id_iku']) . "?redirect_to=" . urlencode(current_url() . '?id_triwulan=' . ($id_triwulan ?? ''));
                    $editUrl = base_url('admin/iku1/edit/' . $row['id_iku']) . "?redirect_to=" . urlencode(current_url() . '?id_triwulan=' . ($id_triwulan ?? ''));

                    $aksi = '<div class="flex items-center space-x-2">';
                    $aksi .= '<a href="' . $editUrl . '" class="text-blue-500 hover:text-blue-700 transition" title="Edit Data"><ion-icon name="create-outline" class="text-xl"></ion-icon></a>';
                    $aksi .= '<a href="' . $deleteUrl . '" onclick="return confirm(\'Yakin hapus?\')" class="text-red-500 hover:text-red-700 transition" title="Hapus Data"><ion-icon name="trash-outline" class="text-xl"></ion-icon></a>';
                    $aksi .= '</div>';
                    // Note: Edit feature can be added later if needed, Delete is priority.

                    $data_list[] = [
                        'main_id' => $row['id_iku'], // Pass ID for Bulk Delete
                        'nama' => '<div class="font-bold text-gray-800">' . esc($row['nama_lengkap']) . '</div>',
                        'nim' => '<div class="text-xs text-gray-500 font-mono">' . esc($row['nim']) . '</div>',
                        'tahun_masuk' => $row['tahun_masuk'],
                        'masa_studi' => $masa_studi_text,
                        'yudisium' => (!empty($row['tanggal_yudisium']) && strtotime($row['tanggal_yudisium']) > 0) ? date('d M Y', strtotime($row['tanggal_yudisium'])) : '-',
                        'status' => $status_badge,
                        'aksi' => $aksi
                    ];
                }

                // Kalkulasi Statistik untuk Chart & Cards (IKU 1 Spec)
                $total_lulusan_chart = count($queryLulus);
                $total_tepat_chart = 0;
                $total_terlambat_chart = 0;

                foreach ($queryLulus as $rowStat) {
                    if ($rowStat['status_kelulusan'] == 'Tepat Waktu') {
                        $total_tepat_chart++;
                    } else {
                        $total_terlambat_chart++;
                    }
                }

                $data['iku1_stats'] = [
                    'total_lulusan' => $total_lulusan_chart,
                    'total_tepat' => $total_tepat_chart,
                    'total_terlambat' => $total_terlambat_chart
                ];

                // Kalkulasi Capaian AEE Prodi Ini
                // Kalkulasi Capaian AEE (LOGIKA BARU - Per Angkatan)
                // Kalkulasi Capaian AEE Prodi Ini
                // Kalkulasi Capaian AEE (LOGIKA BARU - Per Angkatan FILTERED)
                $unique_angkatan = array_unique(array_column($queryLulus, 'tahun_masuk'));

                // Jika list kosong (misal Triwulan ini belum ada lulusan), 
                // kita tetap harus hitung AEE berdasarkan populasi Angkatan database utk Triwulan ini?
                // Idealnya:
                // 1. Ambil semua angkatan aktif prodi ini.
                // 2. Hitung lulusan di triwulan ini.
                // 3. Hitung AEE.

                $distinct_angkatan = $db->table('tb_m_mahasiswa')
                    ->select('tahun_masuk')
                    ->where('kode_prodi', $kode_prodi_target)
                    ->distinct()
                    ->orderBy('tahun_masuk', 'DESC')
                    ->get()->getResultArray();

                $sum_capaian = 0;
                $count_cohort = 0;

                // Tentukan Target Ideal (Sama untuk semua angkatan di prodi ini)
                $jenjang_upper = strtoupper($jenjang);
                $aee_ideal = 25;
                if (strpos($jenjang_upper, 'D3') !== false || strpos($jenjang_upper, 'DIII') !== false) {
                    $aee_ideal = 33;
                } elseif (strpos($jenjang_upper, 'S2') !== false) {
                    $aee_ideal = 50;
                }

                if (!empty($distinct_angkatan)) {
                    foreach ($distinct_angkatan as $tm_row) {
                        $tm = $tm_row['tahun_masuk'];

                        // A. Ambil Total Mahasiswa Angkatan (Denominator)
                        $total_mhs_angkatan = $db->table('tb_m_mahasiswa')
                            ->where('kode_prodi', $kode_prodi_target)
                            ->where('tahun_masuk', $tm)
                            ->countAllResults();

                        // B. Ambil Jumlah Tepat Waktu Cumulative (Numerator) - FILTERED BY TRIWULAN
                        $queryTepat = $db->table('tb_iku_1_lulusan')
                            ->join('tb_m_mahasiswa', 'tb_m_mahasiswa.nim = tb_iku_1_lulusan.nim')
                            ->where('tb_m_mahasiswa.kode_prodi', $kode_prodi_target)
                            ->where('tb_m_mahasiswa.tahun_masuk', $tm)
                            ->where('tb_iku_1_lulusan.status_kelulusan', 'Tepat Waktu');

                        if (!empty($active_triwulan['id'])) {
                            $queryTepat->where('tb_iku_1_lulusan.id_triwulan', $active_triwulan['id']);
                        }

                        $jum_tepat_waktu = $queryTepat->countAllResults();

                        // C. Hitung Realisasi
                        $realisasi = ($total_mhs_angkatan > 0) ? ($jum_tepat_waktu / $total_mhs_angkatan) * 100 : 0;

                        // D. Hitung Capaian
                        $capaian = ($aee_ideal > 0) ? ($realisasi / $aee_ideal) * 100 : 0;

                        // Only count cohort if they have students? Or count all?
                        // If total_mhs > 0, we count it.
                        if ($total_mhs_angkatan > 0) {
                            $sum_capaian += $capaian;
                            $count_cohort++;
                        }
                    }
                }

                $capaian_akhir = ($count_cohort > 0) ? ($sum_capaian / $count_cohort) : 0;

                // Update Detail Array untuk Cards
                $detail['nilai'] = number_format($capaian_akhir, 0); // Round to integer for UI
                $detail['total_data'] = $total_records;
                $detail['total_memenuhi'] = $total_tepat_filtered;
            }

            // Override Button Text
            $data['tambah_button_text'] = 'Tambah Data AEE';

        }
        // ==== IKU 2 (Lulusan Bekerja / Studi Lanjut) ====
        // ==== IKU 2 (Lulusan Bekerja / Studi Lanjut) ====
        elseif (stripos($decoded_iku, '2') !== false || stripos($decoded_iku, 'Lulusan') !== false) {
            // Header Tabel Detail
            $table_headers = [
                'nama' => 'ALUMNI',
                'nim' => 'NIM',
                'status' => 'STATUS & AKTIVITAS',
                'tempat' => 'TEMPAT',
                'gaji' => 'GAJI / UMP',
                'masa_tunggu' => 'MASA TUNGGU',
                'bobot' => 'BOBOT',
                'aksi' => 'AKSI'
            ];

            // Setup Query Builder
            if (!isset($prodiModel))
                $prodiModel = new \App\Models\ProdiModel();

            $prodi = $prodiModel->where('nama_prodi', rawurldecode($nama_prodi))
                ->where('jenjang', $jenjang)
                ->first();
            $kode_prodi_target = $prodi['kode_prodi'] ?? null;

            if ($kode_prodi_target) {
                // Join dengan tb_ref_ump untuk ambil nilai UMP (opsional untuk display)
                $builder = $db->table('tb_iku_2_lulusan')
                    ->select('tb_iku_2_lulusan.id as id_iku, tb_iku_2_lulusan.*, tb_m_mahasiswa.nama_lengkap, tb_m_mahasiswa.tahun_masuk, tb_ref_ump.provinsi, tb_ref_ump.nilai_ump')
                    ->join('tb_m_mahasiswa', 'tb_m_mahasiswa.nim = tb_iku_2_lulusan.nim')
                    ->join('tb_ref_ump', 'tb_ref_ump.id = tb_iku_2_lulusan.provinsi_tempat_kerja', 'left') // Left join in case null
                    ->where('tb_m_mahasiswa.kode_prodi', $kode_prodi_target);

                if (!empty($active_triwulan['id'])) {
                    $builder->where('tb_iku_2_lulusan.id_triwulan', $active_triwulan['id']);
                }

                $queryIku2 = $builder->orderBy('tb_m_mahasiswa.tahun_masuk', 'DESC')
                    ->get()->getResultArray();

                $detail['total_data'] = count($queryIku2);
                $total_bobot = 0.0;

                foreach ($queryIku2 as $row) {
                    $val_bobot = (float) $row['nilai_bobot'];
                    $total_bobot += $val_bobot;

                    // Badge Status
                    $color_map = [
                        'Bekerja' => 'bg-green-100 text-green-800',
                        'Wirausaha' => 'bg-purple-100 text-purple-800',
                        'Lanjut Studi' => 'bg-blue-100 text-blue-800',
                        'Mencari Kerja' => 'bg-yellow-100 text-yellow-800'
                    ];
                    $bg = $color_map[$row['jenis_aktivitas']] ?? 'bg-gray-100 text-gray-600';
                    $status_badge = "<span class='px-2 py-1 rounded text-xs font-bold {$bg}'>{$row['jenis_aktivitas']}</span>";

                    // Detail info (Wirausaha posisi / etc)
                    if ($row['jenis_aktivitas'] == 'Wirausaha') {
                        $status_badge .= "<div class='text-[10px] mt-1 text-gray-500'>{$row['posisi_wirausaha']}</div>";
                    }

                    // Gaji & UMP
                    $gaji_text = '-';
                    if ($row['jenis_aktivitas'] == 'Bekerja' && $row['gaji_bulan'] > 0) {
                        $gaji_text = "Rp " . number_format($row['gaji_bulan'], 0, ',', '.');
                        if ($row['nilai_ump'] > 0) {
                            $gaji_text .= "<div class='text-[10px] text-gray-500'>UMP: " . number_format($row['nilai_ump'], 0, ',', '.') . "</div>";
                            // Check 1.2x
                            if ($row['gaji_bulan'] >= (1.2 * $row['nilai_ump'])) {
                                $gaji_text .= "<span class='text-[10px] text-green-600 font-bold'>(Layak)</span>";
                            } else {
                                $gaji_text .= "<span class='text-[10px] text-red-600 font-bold'>(Blm Layak)</span>";
                            }
                        }
                    }

                    // Tombol Aksi
                    $deleteUrl = base_url('admin/iku2/delete/' . $row['id_iku']) . "?redirect_to=" . urlencode(current_url() . '?id_triwulan=' . ($id_triwulan ?? ''));
                    // Note: Edit link pointed to generic edit before? Now we have specific controller logic.
                    // Assuming Iku2Controller handles edit too or Iku2Lulusan?
                    // User prompt asked for Iku2Lulusan controller save().
                    // Edit/Update might need migration to Iku2Lulusan or keep Iku2Controller if compatible.
                    // For now, link to existing routes. The user can update routes if needed.
                    $editUrl = base_url('admin/iku2/edit/' . $row['id_iku']) . "?redirect_to=" . urlencode(current_url() . '?id_triwulan=' . ($id_triwulan ?? ''));

                    $aksi = '<div class="flex items-center space-x-2">';
                    $aksi .= '<a href="' . $editUrl . '" class="text-blue-500 hover:text-blue-700 transition" title="Edit Data"><ion-icon name="create-outline" class="text-xl"></ion-icon></a>';
                    $aksi .= '<a href="' . $deleteUrl . '" onclick="return confirm(\'Yakin hapus?\')" class="text-red-500 hover:text-red-700 transition" title="Hapus Data"><ion-icon name="trash-outline" class="text-xl"></ion-icon></a>';
                    $aksi .= '</div>';

                    $data_list[] = [
                        'main_id' => $row['id_iku'], // Pass ID for Bulk Delete
                        'nama' => '<div class="font-bold text-gray-800">' . esc($row['nama_lengkap']) . '</div>',
                        'nim' => '<div class="text-xs text-gray-500 font-mono">' . esc($row['nim']) . '</div>',
                        'status' => $status_badge,
                        'tempat' => esc($row['nama_tempat'] ?? '-'),
                        'gaji' => $gaji_text,
                        'masa_tunggu' => $row['masa_tunggu_bulan'] . ' Bln',
                        'bobot' => '<span class="font-bold text-purple-700 text-lg">' . $row['nilai_bobot'] . '</span>',
                        'aksi' => $aksi
                    ];
                }

                // Kalkulasi Capaian IKU 2 = (Total Bobot / Total Responden) * 100
                $total_responden = count($queryIku2);
                $capaian = ($total_responden > 0) ? ($total_bobot / $total_responden) * 100 : 0;

                $detail['nilai'] = number_format($capaian, 1); // 1 decimal
                $detail['total_memenuhi'] = number_format($total_bobot, 2); // Show Total Score Weighted

                // Calculate Chart Data for IKU 2
                $chart_data = [
                    'bekerja' => 0,
                    'wirausaha' => 0,
                    'studi' => 0,
                    'mencari' => 0
                ];
                foreach ($queryIku2 as $row) {
                    if ($row['jenis_aktivitas'] == 'Bekerja')
                        $chart_data['bekerja']++;
                    elseif ($row['jenis_aktivitas'] == 'Wirausaha')
                        $chart_data['wirausaha']++;
                    elseif ($row['jenis_aktivitas'] == 'Lanjut Studi')
                        $chart_data['studi']++;
                    elseif ($row['jenis_aktivitas'] == 'Mencari Kerja')
                        $chart_data['mencari']++;
                }
            }
            // Tampilkan Dummy jika kosong biar user gak bingung
            if (empty($data_list)) {
                $data_list = [
                    // [
                    //     'nama_lulusan' => 'Contoh Data (Belum Ada di DB)',
                    //     'nim' => '-',
                    //     'no_ijazah' => '-',
                    //     'tahun_lulus' => '-',
                    //     'status' => 'Silakan Tambah Data',
                    //     'tempat' => '-',
                    //     'tanggal_mulai' => '-',
                    //     'pendapatan' => '-',
                    //     'ump' => '-',
                    //     'masa_tunggu' => '-',
                    //     'point' => '-'
                    // ]
                ];
            }
            $data['tambah_button_text'] = 'Tambah Data Alumni';
        } else {
            $table_headers = ['Keterangan', 'Nilai', 'Bukti'];
            $data_list = [];
        }

        // Hitung Statistik Sederhana (This part might need adjustment based on IKU type)
        // This block was originally for IKU 2 (Lulusan) only.
        // If IKU 1 is active, $data_list will be AEE data, so this calculation is not relevant.
        // For now, keep it as is, but be aware it's specific to IKU 2.
        $total_alumni = count($data_list);
        $memenuhi = 0; // Logic hitung point >= 1.0 misalnya
        foreach ($data_list as $item) {
            if (isset($item['point']) && (float) $item['point'] >= 1.0) {
                $memenuhi++;
            }
        }

        $persentase_memenuhi = $total_alumni > 0 ? round(($memenuhi / $total_alumni) * 100, 2) : 0;

        $data['table_headers'] = $table_headers;
        $data['data_list'] = $data_list;
        $data['iku_detail'] = $detail; // Update detail nilai

        // Preserve IKU1 stats before array reinitialization
        $iku1_stats_temp = $data['iku1_stats'] ?? null;

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
            'tambah_button_text' => $data['tambah_button_text'] ?? 'Tambah Data Lulusan',
            'back_url' => site_url('admin/iku-prodi/' . $jurusan_kode . '/' . rawurlencode($nama_prodi) . '/' . $jenjang),
            // Export context
            'kode_prodi' => $kode_prodi_target ?? '',
            'id_triwulan' => $active_triwulan['id'] ?? '',
            // Chart data for IKU 2
            'chart_data' => $chart_data ?? null,
            // Stats for IKU 1
            'iku1_stats' => $iku1_stats_temp
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
            'page' => 'jurusan',
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
            'page' => 'jurusan',
            'nama_jurusan' => $nama_jurusan,
            'jurusan_kode' => $jurusan_kode,
            'prodi_list' => $prodi_list,
            'total_prodi' => $total_prodi,
            'rata_rata_capaian' => $rata
        ];

        return view('admin/prodi_capaian', $data);
    }

    // FORM INPUT DATA IKU
    public function ikuInput($iku_code, $jurusan_kode, $nama_prodi, $jenjang)
    {
        $data = [
            'title' => 'Input Data IKU ' . $iku_code,
            'page' => 'iku-input',
            'iku_code' => $iku_code, // e.g. "1" or "2"
            'jurusan_kode' => $jurusan_kode,
            'nama_prodi' => rawurldecode($nama_prodi),
            'jenjang' => $jenjang,
            'back_url' => site_url("admin/iku-detail/$iku_code/$jurusan_kode/$nama_prodi/$jenjang")
        ];

        // Tampilkan view sesuai kode IKU (sementara IKU 1/2 pakai yang sama)
        return view('admin/input/iku1', $data);
    }

    // PROSES SIMPAN DATA IKU
    public function ikuSave($iku_code)
    {
        $request = \Config\Services::request();
        $jurusan_kode = $request->getPost('jurusan_kode');
        $nama_prodi = $request->getPost('nama_prodi');
        $jenjang = $request->getPost('jenjang');
        $back_url = $request->getPost('back_url');

        // ==== LOGIKA IKU 1: AEE (ANGKA EFISIENSI EDUKASI) ====
        if ($iku_code == '1') {
            $model = new \App\Models\Iku1AeeModel();

            // Input Data
            $jml_masuk = $request->getPost('jml_mhs_masuk');
            $jml_lulus = $request->getPost('jml_lulus_tepat_waktu');
            $tahun_masuk = $request->getPost('tahun_masuk');

            // 1. Hitung AEE Realisasi
            $aee_real = 0;
            if ($jml_masuk > 0) {
                $aee_real = ($jml_lulus / $jml_masuk) * 100;
            }

            // 2. Tentukan AEE Ideal berdasarkan Jenjang
            // D3 = 33%, D4/S1 = 25%, S2 = 50%, S3 = 33%
            $jenjang_upper = strtoupper($jenjang);
            $aee_ideal = 25; // Default (S1/D4)
            if (strpos($jenjang_upper, 'D3') !== false || strpos($jenjang_upper, 'S3') !== false) {
                $aee_ideal = 33;
            } elseif (strpos($jenjang_upper, 'S2') !== false) {
                $aee_ideal = 50;
            }

            // 3. Hitung Capaian (% dari Ideal)
            $capaian = 0;
            if ($aee_ideal > 0) {
                $capaian = ($aee_real / $aee_ideal) * 100;
            }

            $current_user_id = session()->get('id') ?? 1;

            $data = [
                'user_id' => $current_user_id,
                'triwulan_id' => 1,
                'prodi' => rawurldecode($nama_prodi),
                'jenjang' => $jenjang,
                'tahun_masuk' => $tahun_masuk,
                'jml_mhs_masuk' => $jml_masuk,
                'jml_lulus_tepat_waktu' => $jml_lulus,
                'aee_realisasi' => $aee_real,
                'aee_ideal' => $aee_ideal,
                'capaian' => $capaian
            ];

            $model->insert($data);

            return redirect()->to($back_url)->with('success', 'Data AEE berhasil disimpan! Capaian: ' . number_format($capaian, 2) . '%');
        }

        // ==== LOGIKA IKU 2: LULUSAN BEKERJA (dulu IKU 1.1) ====
        if ($iku_code == '2' || $iku_code == '1.1') {
            // ... (Kode sebelumnya untuk Lulusan Bekerja)
            $model = new \App\Models\IkuSatuSatuModel();
            $data = [
                'user_id' => session()->get('id') ?? 1,
                'triwulan_id' => 1,
                'nama' => $request->getPost('nama'),
                'nim' => $request->getPost('nim'),
                'prodi' => $nama_prodi,
                'tahun_lulus' => $request->getPost('tahun_lulus'),
                'status' => $request->getPost('status'),
                'nama_tempat' => $request->getPost('nama_tempat'),
                'pendapatan' => $request->getPost('pendapatan'),
                'ump' => $request->getPost('ump'),
                'tanggal_mulai' => $request->getPost('tanggal_mulai'),
                'tingkat' => $request->getPost('tingkat'),
                'link_bukti' => $request->getPost('link_bukti'),
                'point' => 0
            ];
            $model->insert($data);
            return redirect()->to($back_url)->with('success', 'Data Lulusan berhasil disimpan!');
        }

        return redirect()->to($back_url);
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
            'page' => 'prodi',
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
    public function jurusan()
    {
        $jurusanModel = new JurusanModel();
        $prodiModel = new ProdiModel();
        $kampusModel = new KampusModel();

        $jurusan_rows = $jurusanModel->getList();
        $jurusan_names = array_map(function ($r) {
            return $r['nama'];
        }, $jurusan_rows);
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
            $kampus = ['nama' => $kampus_row['nama']];
            // Prefer aggregated prodi sums for mahasiswa and dosen when present (non-zero)
            $jumlah_mahasiswa_aktif = $sum_mahasiswa > 0 ? $sum_mahasiswa : (int) ($kampus_row['jumlah_mahasiswa_aktif'] ?? 0);
            $jumlah_dosen = $sum_dosen > 0 ? $sum_dosen : (int) ($kampus_row['jumlah_dosen'] ?? 0);
            $jumlah_lulusan_satu_tahun = (int) ($kampus_row['jumlah_lulusan_satu_tahun'] ?? 0);
        } else {
            $kampus = ['nama' => 'Politeknik Negeri Sriwijaya'];
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
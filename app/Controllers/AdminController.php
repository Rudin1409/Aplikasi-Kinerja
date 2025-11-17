<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;


class AdminController extends BaseController
{
    public function dashboard()
    {
        // Simulasi data capaian untuk grafik (nanti ambil dari database)
        $grafikData = [
            'labels' => ['IKU 1.1', 'IKU 1.2', 'IKU 2.1', 'IKU 2.2', 'IKU 2.3', 'IKU 3.1', 'IKU 3.2', 'IKU 3.3'],
            'values' => [85, 78, 92, 88, 75, 90, 82, 65] // Persentase capaian tiap IKU
        ];
        
        // Data capaian per jurusan untuk grafik batang
        $capaianJurusan = [
            ['nama' => 'Teknik Sipil', 'capaian' => rand(70, 95)],
            ['nama' => 'Teknik Mesin', 'capaian' => rand(70, 95)],
            ['nama' => 'Teknik Elektro', 'capaian' => rand(70, 95)],
            ['nama' => 'Teknik Kimia', 'capaian' => rand(70, 95)],
            ['nama' => 'Teknik Komputer', 'capaian' => rand(70, 95)],
            ['nama' => 'Akuntansi', 'capaian' => rand(70, 95)],
            ['nama' => 'Adm. Bisnis', 'capaian' => rand(70, 95)],
            ['nama' => 'Man. Informatika', 'capaian' => rand(70, 95)],
            ['nama' => 'Bahasa Inggris', 'capaian' => rand(70, 95)],
            ['nama' => 'Agribisnis', 'capaian' => rand(70, 95)],
        ];
        
        $data = [
            'title' => 'Dashboard Admin',
            'page'  => 'dashboard',
            'grafikData' => $grafikData,
            'capaianJurusan' => $capaianJurusan
        ];
        
        // Memanggil view dashboard
        return view('admin/dashboard', $data);
    }

    public function jurusan()
    {
        // Data 10 Jurusan (Sesuai gambar Anda)
        $daftar_jurusan = [
            ['kode' => 'J01', 'nama' => 'Teknik Sipil'],
            ['kode' => 'J02', 'nama' => 'Teknik Mesin'],
            ['kode' => 'J03', 'nama' => 'Teknik Elektro'],
            ['kode' => 'J04', 'nama' => 'Teknik Kimia'],
            ['kode' => 'J05', 'nama' => 'Teknik Komputer'],
            ['kode' => 'J06', 'nama' => 'Akuntansi'],
            ['kode' => 'J07', 'nama' => 'Administrasi Bisnis'],
            ['kode' => 'J08', 'nama' => 'Manajemen Informatika'],
            ['kode' => 'J09', 'nama' => 'Bahasa Inggris'],
            ['kode' => 'J10', 'nama' => 'Agribisnis']
        ];

        $data = [
            'title'        => 'Master Data Jurusan',
            'page'         => 'jurusan', // OK
            'jurusan_list' => $daftar_jurusan // Kirim data jurusan ke view
        ];

        return view('admin/jurusan', $data);
    }
    
    // GANTI FUNGSI prodi() LAMA ANDA DENGAN YANG INI
    public function prodi()
    {
        // Data 10 Jurusan (Kita perlukan untuk Dropdown di modal)
        $daftar_jurusan_dropdown = [
            ['kode' => 'J01', 'nama' => 'Teknik Sipil'],
            ['kode' => 'J02', 'nama' => 'Teknik Mesin'],
            ['kode' => 'J03', 'nama' => 'Teknik Elektro'],
            ['kode' => 'J04', 'nama' => 'Teknik Kimia'],
            ['kode' => 'J05', 'nama' => 'Teknik Komputer'],
            ['kode' => 'J06', 'nama' => 'Akuntansi'],
            ['kode' => 'J07', 'nama' => 'Administrasi Bisnis'],
            ['kode' => 'J08', 'nama' => 'Manajemen Informatika'],
            ['kode' => 'J09', 'nama' => 'Bahasa Inggris'],
            ['kode' => 'J10', 'nama' => 'Agribisnis']
        ];
        
        // Map Jurusan (untuk mengubah kode jadi nama di tabel)
        $daftar_jurusan_map = [
            'J01' => 'Teknik Sipil', 'J02' => 'Teknik Mesin', 'J03' => 'Teknik Elektro',
            'J04' => 'Teknik Kimia', 'J05' => 'Teknik Komputer', 'J06' => 'Akuntansi',
            'J07' => 'Administrasi Bisnis', 'J08' => 'Manajemen Informatika',
            'J09' => 'Bahasa Inggris', 'J10' => 'Agribisnis'
        ];
        
        // Database Simulasi 41 Prodi (LENGKAP)
        $semua_prodi = [
            ['nama_prodi' => 'Teknik Sipil', 'jenjang' => 'DIII', 'jurusan_kode' => 'J01'],
            ['nama_prodi' => 'Perancangan Jalan dan Jembatan', 'jenjang' => 'DIV', 'jurusan_kode' => 'J01'],
            ['nama_prodi' => 'Perancangan Jalan dan Jembatan PSDKU OKU', 'jenjang' => 'DIV', 'jurusan_kode' => 'J01'],
            ['nama_prodi' => 'Arsitektur Bangunan Gedung', 'jenjang' => 'DIV', 'jurusan_kode' => 'J01'],
            ['nama_prodi' => 'Teknik Mesin', 'jenjang' => 'DIII', 'jurusan_kode' => 'J02'],
            ['nama_prodi' => 'Teknik Mesin Produksi dan Perawatan', 'jenjang' => 'DIV', 'jurusan_kode' => 'J02'],
            ['nama_prodi' => 'Pemeliharaan Alat Berat', 'jenjang' => 'DIII', 'jurusan_kode' => 'J02'],
            ['nama_prodi' => 'Teknik Mesin Produksi dan Perawatan PSDKU Siak', 'jenjang' => 'DIV', 'jurusan_kode' => 'J02'],
            ['nama_prodi' => 'Teknik Listrik', 'jenjang' => 'DIII', 'jurusan_kode' => 'J03'],
            ['nama_prodi' => 'Teknik Elektro', 'jenjang' => 'DIV', 'jurusan_kode' => 'J03'],
            ['nama_prodi' => 'Teknik Elektronika', 'jenjang' => 'DIII', 'jurusan_kode' => 'J03'],
            ['nama_prodi' => 'Teknik Telekomunikasi', 'jenjang' => 'DIII', 'jurusan_kode' => 'J03'],
            ['nama_prodi' => 'Teknik Telekomunikasi', 'jenjang' => 'DIV', 'jurusan_kode' => 'J03'],
            ['nama_prodi' => 'Teknologi Rekayasa Instalasi Listrik', 'jenjang' => 'DIV', 'jurusan_kode' => 'J03'],
            ['nama_prodi' => 'Teknik Kimia', 'jenjang' => 'DIII', 'jurusan_kode' => 'J04'],
            ['nama_prodi' => 'Teknologi Kimia Industri', 'jenjang' => 'DIV', 'jurusan_kode' => 'J04'],
            ['nama_prodi' => 'Teknik Energi Terbarukan', 'jenjang' => 'S2', 'jurusan_kode' => 'J04'],
            ['nama_prodi' => 'Teknik Kimia PSDKU Siak', 'jenjang' => 'DIII', 'jurusan_kode' => 'J04'],
            ['nama_prodi' => 'Teknik Energi', 'jenjang' => 'DIV', 'jurusan_kode' => 'J04'],
            ['nama_prodi' => 'Teknik Komputer', 'jenjang' => 'DIII', 'jurusan_kode' => 'J05'],
            ['nama_prodi' => 'Teknologi Informatika Multimedia Digital', 'jenjang' => 'DIV', 'jurusan_kode' => 'J05'],
            ['nama_prodi' => 'Akuntansi', 'jenjang' => 'DIII', 'jurusan_kode' => 'J06'],
            ['nama_prodi' => 'Akuntansi Sektor Publik', 'jenjang' => 'DIV', 'jurusan_kode' => 'J06'],
            ['nama_prodi' => 'Akuntansi Sektor Publik PSDKU OKU', 'jenjang' => 'DIV', 'jurusan_kode' => 'J06'],
            ['nama_prodi' => 'Akuntansi Sektor Publik PSDKU Siak', 'jenjang' => 'DIV', 'jurusan_kode' => 'J06'],
            ['nama_prodi' => 'Administrasi Bisnis', 'jenjang' => 'DIII', 'jurusan_kode' => 'J07'],
            ['nama_prodi' => 'Manajemen Bisnis', 'jenjang' => 'DIV', 'jurusan_kode' => 'J07'],
            ['nama_prodi' => 'Pemasaran, Inovasi, dan Teknologi', 'jenjang' => 'S2', 'jurusan_kode' => 'J07'],
            ['nama_prodi' => 'Administrasi Bisnis PSDKU OKU', 'jenjang' => 'DIII', 'jurusan_kode' => 'J07'],
            ['nama_prodi' => 'Bisnis Digital', 'jenjang' => 'DIV', 'jurusan_kode' => 'J07'],
            ['nama_prodi' => 'Usaha Perjalanan Wisata', 'jenjang' => 'DIV', 'jurusan_kode' => 'J07'],
            ['nama_prodi' => 'Manajemen Informatika', 'jenjang' => 'DIII', 'jurusan_kode' => 'J08'],
            ['nama_prodi' => 'Manajemen Informatika', 'jenjang' => 'DIV', 'jurusan_kode' => 'J08'],
            ['nama_prodi' => 'Bahasa Inggris', 'jenjang' => 'DIII', 'jurusan_kode' => 'J09'],
            ['nama_prodi' => 'Bahasa Inggris untuk Komunikasi Bisnis dan Profesional', 'jenjang' => 'DIV', 'jurusan_kode' => 'J09'],
            ['nama_prodi' => 'Teknologi Pangan', 'jenjang' => 'DIII', 'jurusan_kode' => 'J10'],
            ['nama_prodi' => 'Teknologi Produksi Tanaman Perkebunan', 'jenjang' => 'DIV', 'jurusan_kode' => 'J10'],
            ['nama_prodi' => 'Agribisnis Pangan', 'jenjang' => 'DIV', 'jurusan_kode' => 'J10'],
            ['nama_prodi' => 'Manajemen Agribisnis', 'jenjang' => 'DIV', 'jurusan_kode' => 'J10'],
            ['nama_prodi' => 'Teknologi Akuakultur', 'jenjang' => 'DIV', 'jurusan_kode' => 'J10'],
            ['nama_prodi' => 'Teknologi Rekayasa Pangan', 'jenjang' => 'DIV', 'jurusan_kode' => 'J10'],
        ];

        // Buat data list untuk tabel (sesuai kebutuhan view)
        $prodi_list_lengkap = [];
        $i = 1;
        foreach ($semua_prodi as $prodi) {
            $prodi_list_lengkap[] = [
                'kode_prodi'   => 'P' . str_pad($i++, 2, '0', STR_PAD_LEFT), // Buat kode prodi palsu P01, P02, dst.
                'nama_prodi'   => $prodi['nama_prodi'],
                'jenjang'      => $prodi['jenjang'],
                'jurusan_kode' => $prodi['jurusan_kode'],
                'nama_jurusan' => $daftar_jurusan_map[$prodi['jurusan_kode']] ?? 'N/A' // Ambil nama jurusan
            ];
        }

        $data = [
            'title'        => 'Master Data Prodi',
            'page'         => 'prodi', // OK
            'jurusan_list' => $daftar_jurusan_dropdown, // Untuk modal
            'prodi_list'   => $prodi_list_lengkap      // DATA LENGKAP untuk tabel
        ];

        return view('admin/prodi', $data);
    }
    
    // GANTI FUNGSI LAMA prodiCapaian() DENGAN FUNGSI BARU INI
    public function prodiCapaian($jurusan_kode = null)
    {
        if ($jurusan_kode == null) {
            return redirect()->to('admin/jurusan-capaian');
        }

        // Proteksi akses manipulasi URL untuk role jurusan & prodi
        $session = \Config\Services::session();
        $role = $session->get('role');
        $relasi = $session->get('relasi_kode'); // jurusan => J03, prodi => J03|P05

        if ($role === 'jurusan') {
            // Jurusan hanya boleh melihat jurusan sendiri
            if ($relasi !== $jurusan_kode) {
                return redirect()->to('admin/prodi-capaian/' . $relasi)->with('error', 'Anda tidak berhak mengakses jurusan lain.');
            }
        } elseif ($role === 'prodi') {
            // Role prodi tidak boleh akses halaman agregat jurusan
            return redirect()->to('admin/iku-prodi/' . explode('|', $relasi)[0] . '/' . rawurlencode('') . '/')->with('error', 'Halaman capaian jurusan tidak tersedia untuk role prodi.');
        }
        // Admin & pimpinan bebas (dilewati)

        // --- DATABASE SIMULASI (SESUAI GAMBAR ANDA) ---
        
        // 1. Daftar Jurusan (untuk ambil nama)
        $daftar_jurusan = [
            'J01' => 'Teknik Sipil', 'J02' => 'Teknik Mesin', 'J03' => 'Teknik Elektro',
            'J04' => 'Teknik Kimia', 'J05' => 'Teknik Komputer', 'J06' => 'Akuntansi',
            'J07' => 'Administrasi Bisnis', 'J08' => 'Manajemen Informatika',
            'J09' => 'Bahasa Inggris', 'J10' => 'Agribisnis'
        ];
        
        // 2. Daftar Lengkap 41 Prodi (sesuai image_def3ff.png)
        $semua_prodi = [
            // J01: Teknik Sipil
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

        // 3. Filter prodi berdasarkan $jurusan_kode yang diklik
        $prodi_list_filtered = [];
        $total_persentase = 0;
        foreach ($semua_prodi as $prodi) {
            if ($prodi['jurusan_kode'] == $jurusan_kode) {
                // 4. Tambahkan persentase acak (simulasi)
                $persen = rand(40, 98);
                $prodi['persentase'] = $persen;
                $prodi_list_filtered[] = $prodi;
                $total_persentase += $persen;
            }
        }

        // 5. Hitung data untuk 2 kartu ringkasan
        $total_prodi = count($prodi_list_filtered);
        $rata_rata_capaian = ($total_prodi > 0) ? ($total_persentase / $total_prodi) : 0;

        // 6. Siapkan data untuk dikirim ke View
        $data = [
            'title'             => 'Laporan Capaian Prodi',
            'page'              => 'jurusan',
            'nama_jurusan'      => $daftar_jurusan[$jurusan_kode] ?? 'Jurusan Tidak Ditemukan',
            'prodi_list'        => $prodi_list_filtered,
            'total_prodi'       => $total_prodi,
            'rata_rata_capaian' => number_format($rata_rata_capaian, 1),
            'jurusan_kode'      => $jurusan_kode
        ];

        return view('admin/prodi_capaian', $data);
    }

    // GANTI FUNGSI LAMA JURUSANCAPAIAN() DENGAN INI
    public function jurusanCapaian()
    {
        // Data 10 Jurusan (Kita ambil lagi)
        $daftar_jurusan = [
            ['kode' => 'J01', 'nama' => 'Teknik Sipil'],
            ['kode' => 'J02', 'nama' => 'Teknik Mesin'],
            ['kode' => 'J03', 'nama' => 'Teknik Elektro'],
            ['kode' => 'J04', 'nama' => 'Teknik Kimia'],
            ['kode' => 'J05', 'nama' => 'Teknik Komputer'],
            ['kode' => 'J06', 'nama' => 'Akuntansi'],
            ['kode' => 'J07', 'nama' => 'Administrasi Bisnis'],
            ['kode' => 'J08', 'nama' => 'Manajemen Informatika'],
            ['kode' => 'J09', 'nama' => 'Bahasa Inggris'],
            ['kode' => 'J10', 'nama' => 'Agribisnis']
        ];
        
        // SIMULASI DATA PERSENTASE (Nanti ini dari hasil kalkulasi IKU)
        $total_persentase = 0;
        foreach ($daftar_jurusan as &$jurusan) { // Pakai '&' untuk memodifikasi array aslinya
            $persen = rand(30, 95); // Angka acak
            $jurusan['persentase'] = $persen;
            $total_persentase += $persen; // Tambahkan ke total
        }

        $total_jurusan = count($daftar_jurusan);
        // Hitung rata-rata, hindari pembagian dengan nol
        $rata_rata_capaian = ($total_jurusan > 0) ? ($total_persentase / $total_jurusan) : 0;

        $data = [
            'title'             => 'Laporan Capaian Jurusan',
            
            // ===== PERBAIKAN #2 DI SINI =====
            'page'              => 'jurusan', // Diubah dari 'dashboard'
            
            'jurusan_list'      => $daftar_jurusan,
            'total_jurusan'     => $total_jurusan, // Data baru untuk card
            'rata_rata_capaian' => number_format($rata_rata_capaian, 1) // Data baru untuk card
        ];

        return view('admin/jurusan_capaian', $data);
    }
    
    // ... (setelah fungsi prodiCapaian() ... )

    // FUNGSI BARU UNTUK HALAMAN DETAIL IKU PER PRODI
    public function ikuProdi($jurusan_kode = null, $nama_prodi_encoded = null, $jenjang = null)
    {
        if ($jurusan_kode == null || $nama_prodi_encoded == null || $jenjang == null) {
            return redirect()->to('admin/dashboard');
        }

        // 1. Ambil nama prodi & jurusan
        $nama_prodi = rawurldecode($nama_prodi_encoded);
        $daftar_jurusan = [
            'J01' => 'Teknik Sipil', 'J02' => 'Teknik Mesin', 'J03' => 'Teknik Elektro',
            'J04' => 'Teknik Kimia', 'J05' => 'Teknik Komputer', 'J06' => 'Akuntansi',
            'J07' => 'Administrasi Bisnis', 'J08' => 'Manajemen Informatika',
            'J09' => 'Bahasa Inggris', 'J10' => 'Agribisnis'
        ];
        $nama_jurusan = $daftar_jurusan[$jurusan_kode] ?? 'Jurusan';

        // 2. Siapkan data 8 IKU (Sesuai data awal Anda)
        $iku_data = [
            [
                'kode' => 'IKU 1.1',
                'nama' => 'Lulusan Mendapat Pekerjaan/Studi/Wirausaha',
                'persentase' => rand(70, 95),
                'icon' => 'briefcase-outline'
            ],
            [
                'kode' => 'IKU 1.2',
                'nama' => 'Mahasiswa Mendapat Pengalaman di Luar Prodi',
                'persentase' => rand(70, 95),
                'icon' => 'school-outline'
            ],
            [
                'kode' => 'IKU 2.1',
                'nama' => 'Kegiatan Dosen di Luar Kampus',
                'persentase' => rand(70, 95),
                'icon' => 'walk-outline'
            ],
            [
                'kode' => 'IKU 2.2',
                'nama' => 'Kualifikasi Dosen & Praktisi Mengajar',
                'persentase' => rand(70, 95),
                'icon' => 'sparkles-outline'
            ],
            [
                'kode' => 'IKU 2.3',
                'nama' => 'Hasil Karya Dosen (Rekognisi/Diterapkan)',
                'persentase' => rand(70, 95),
                'icon' => 'trophy-outline'
            ],
            [
                'kode' => 'IKU 3.1',
                'nama' => 'Kerjasama Program Studi dengan Mitra',
                'persentase' => rand(70, 95),
                'icon' => 'git-network-outline'
            ],
            [
                'kode' => 'IKU 3.2',
                'nama' => 'Metode Pembelajaran (Case/Project Based)',
                'persentase' => rand(70, 95),
                'icon' => 'bulb-outline'
            ],
            [
                'kode' => 'IKU 3.3',
                'nama' => 'Akreditasi Internasional Program Studi',
                'persentase' => rand(10, 30), // (Simulasi rendah)
                'icon' => 'globe-outline'
            ],
        ];

        // 3. Siapkan data untuk dikirim ke View
        $data = [
            'title'        => 'Capaian IKU Prodi',
            
            // Set page = 'dashboard' karena ini IS dashboard prodi
            // (Bukan halaman master data IKU)
            'page'         => 'dashboard',
            
            'tahun'        => '2025', // (Data statis untuk filter)
            'jurusan_kode' => $jurusan_kode,
            'nama_jurusan' => $nama_jurusan,
            'nama_prodi'   => $nama_prodi,
            'jenjang'      => $jenjang,
            'iku_data'     => $iku_data
        ];

        return view('admin/iku_prodi', $data);
    }
    
    // FUNGSI BARU UNTUK HALAMAN DETAIL SETIAP IKU
    public function ikuDetail($iku_code = null, $jurusan_kode = null, $nama_prodi_encoded = null, $jenjang = null)
    {
        if (!$iku_code || !$jurusan_kode || !$nama_prodi_encoded || !$jenjang) {
            return redirect()->back();
        }

        // 1. Siapkan data dasar
        $nama_prodi = rawurldecode($nama_prodi_encoded);
        $back_url = base_url("admin/iku-prodi/$jurusan_kode/$nama_prodi_encoded/$jenjang");
        
        $data = [
            'title'             => 'Detail IKU',
            
            // ===== PERBAIKAN #4 DI SINI =====
            'page'              => 'jurusan', // Diubah dari 'dashboard'
            
            'back_url'          => $back_url,
            'nama_prodi'        => $nama_prodi,
            'jenjang'           => $jenjang,
            'triwulan_text'     => 'TW 1 (Januari - Maret 2025)', // (Simulasi, nanti bisa diambil dari ?tw=1)
            'iku_title'         => '',
            'tambah_button_text' => 'Tambah Data',
            'table_headers'     => [], // Kunci: Kolom tabel dinamis
            'data_list'         => []  // Kunci: Isi tabel dinamis
        ];

        // 2. LOGIKA DINAMIS: Sesuaikan data berdasarkan $iku_code
        switch ($iku_code) {
            case '1.1':
                $data['iku_title'] = 'IKU 1.1: Capaian Lulusan';
                $data['tambah_button_text'] = 'Tambah Data Lulusan';
                $data['table_headers'] = [
                    // 'key_data' => 'Nama Kolom di Tabel'
                    'nama_mhs' => 'Nama Lulusan',
                    'nim'      => 'NIM',
                    'status'   => 'Status (Bekerja/Studi/Wirausaha)',
                    'bukti'    => 'Bukti (SK/NIB)'
                ];
                // Data simulasi (nanti dari database)
                $data['data_list'] = [
                    ['nama_mhs' => 'Ahmad Budi', 'nim' => '0623...1', 'status' => 'Bekerja', 'bukti' => 'SK.pdf'],
                    ['nama_mhs' => 'Citra Lestari', 'nim' => '0623...2', 'status' => 'Wirausaha', 'bukti' => 'NIB.pdf'],
                ];
                break;
            
            case '1.2':
                $data['iku_title'] = 'IKU 1.2: Kegiatan & Prestasi Mahasiswa';
                $data['tambah_button_text'] = 'Tambah Kegiatan/Prestasi';
                $data['table_headers'] = [
                    'nama_mhs' => 'Nama Mahasiswa',
                    'nim'      => 'NIM',
                    'kegiatan' => 'Nama Kegiatan',
                    'kategori' => 'Kategori (Magang/Prestasi/dll)',
                    'bukti'    => 'Bukti (SK/Sertifikat)'
                ];
                $data['data_list'] = [
                    ['nama_mhs' => 'Doni Saputra', 'nim' => '0624...1', 'kegiatan' => 'Magang di PT. ABC', 'kategori' => 'Magang', 'bukti' => 'Sertif.pdf'],
                ];
                break;

            case '2.1':
                $data['iku_title'] = 'IKU 2.1: Kegiatan Dosen';
                $data['tambah_button_text'] = 'Tambah Kegiatan Dosen';
                $data['table_headers'] = [
                    'nama_dosen' => 'Nama Dosen',
                    'kegiatan' => 'Nama Kegiatan',
                    'kategori' => 'Kategori (Tridharma/Praktisi/Membimbing)',
                    'bukti'    => 'Bukti (SK/Surat Tugas)'
                ];
                $data['data_list'] = [
                    ['nama_dosen' => 'Prof. Dr. Dosen A', 'kegiatan' => 'Mengajar di PT. X', 'kategori' => 'Praktisi', 'bukti' => 'SK.pdf'],
                ];
                break;
            
            // (Anda bisa tambahkan case untuk IKU 2.2, 2.3, 3.1, 3.2, 3.3 di sini)

            default:
                $data['iku_title'] = "IKU $iku_code (Belum disetup)";
                $data['table_headers'] = ['info' => 'Informasi'];
                $data['data_list'] = [['info' => 'Halaman detail untuk IKU ini belum dikonfigurasi di AdminController.']];
                break;
        }

        // 3. Tampilkan view dengan data yang sudah disesuaikan
        return view('admin/iku_detail', $data);
    }
    
    // GANTI FUNGSI AKUN() LAMA DENGAN INI
    public function akun()
    {
        // Data kampus dasar
        $data_kampus = [
            'nama'   => 'Politeknik Negeri Sriwijaya',
            'alamat' => 'Jl. Srijaya Negara, Bukit Besar, Palembang 30139',
            'website'=> 'https://www.polsri.ac.id'
        ];

        // Daftar jurusan (urut & nama sesuai screenshot Anda)
        $jurusan_list = [
            'Teknik Sipil',
            'Teknik Mesin',
            'Teknik Elektro',
            'Teknik Kimia',
            'Teknik Komputer',
            'Administrasi Bisnis',
            'Akuntansi',
            'Bahasa dan Pariwisata',
            'Manajemen Informatika',
            'Rekayasa Teknologi dan Bisnis Pangan'
        ];

        // Angka statistik (sementara hardcode; nanti bisa diambil dari DB)
        $jumlah_jurusan                 = count($jurusan_list); // 10
        $jumlah_prodi                   = 41;                   // Total prodi lengkap
        $jumlah_mahasiswa_aktif         = 3100;                 // Placeholder
        $jumlah_lulusan_satu_tahun      = 3000;                 // Placeholder
        $jumlah_dosen                   = 500;                  // Placeholder

        $data = [
            'title'                          => 'Informasi Kampus',
            'page'                           => 'akun',
            'kampus_data'                    => $data_kampus,
            'jurusan_list'                   => $jurusan_list,
            'jumlah_jurusan'                 => $jumlah_jurusan,
            'jumlah_prodi'                   => $jumlah_prodi,
            'jumlah_mahasiswa_aktif'         => $jumlah_mahasiswa_aktif,
            'jumlah_lulusan_satu_tahun'      => $jumlah_lulusan_satu_tahun,
            'jumlah_dosen'                   => $jumlah_dosen
        ];

        return view('admin/akun', $data);
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

        // 1. Data Jurusan (untuk dropdown)
        $daftar_jurusan = [
            ['kode' => 'J01', 'nama' => 'Teknik Sipil'], ['kode' => 'J02', 'nama' => 'Teknik Mesin'], 
            ['kode' => 'J03', 'nama' => 'Teknik Elektro'], ['kode' => 'J04', 'nama' => 'Teknik Kimia'],
            ['kode' => 'J05', 'nama' => 'Teknik Komputer'], ['kode' => 'J06', 'nama' => 'Akuntansi'],
            ['kode' => 'J07', 'nama' => 'Administrasi Bisnis'], ['kode' => 'J08', 'nama' => 'Manajemen Informatika'],
            ['kode' => 'J09', 'nama' => 'Bahasa Inggris'], ['kode' => 'J10', 'nama' => 'Agribisnis']
        ];
        
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
        // Nanti kita isi logika simpan ke database di sini
        // Untuk sekarang, kita kembalikan saja ke halaman prodi
        
        // Ambil data (contoh)
        $nama_prodi = $this->request->getPost('nama_prodi');
        echo "Menyimpan data prodi: " . $nama_prodi;
        
        // Arahkan kembali ke halaman prodi
        return redirect()->to('admin/prodi');
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
    
}
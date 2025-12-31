<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MahasiswaModel;
use App\Models\Iku1Model;
use App\Models\ProdiModel;

class Iku1Controller extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // 1. Ambil Triwulan Aktif (Default Filter)
        $active_triwulan = $db->table('triwulan')->where('status', 'Aktif')->get()->getRowArray();
        $id_triwulan = $active_triwulan['id'] ?? null;

        // Jika tidak ada triwulan aktif, mungkin ambil yang terakhir? 
        // Sementara biarkan kosong resultnya kalau tidak ada aktif.

        // 2. Query Utama (JOIN)
        $builder = $db->table('tb_iku_1_lulusan');
        $builder->select('tb_iku_1_lulusan.*, tb_m_mahasiswa.nama_lengkap, tb_m_mahasiswa.tahun_masuk, prodi.nama_prodi, prodi.jenjang');
        $builder->join('tb_m_mahasiswa', 'tb_m_mahasiswa.nim = tb_iku_1_lulusan.nim');
        $builder->join('prodi', 'tb_m_mahasiswa.kode_prodi = prodi.kode_prodi');

        if ($id_triwulan) {
            $builder->where('tb_iku_1_lulusan.id_triwulan', $id_triwulan);
        }

        $data_lulusan = $builder->get()->getResultArray();

        // 3. Hitung Statistik Cards & Chart (LOGIKA BARU SESUAI PANDUAN 2025)
        $total_lulusan = count($data_lulusan);
        $total_tepat = 0;
        $total_terlambat = 0;

        $cohort_stats = [];

        foreach ($data_lulusan as &$row) {
            // Count Tepat/Terlambat for Table Display (Pure Counter)
            if ($row['status_kelulusan'] == 'Tepat Waktu') {
                $total_tepat++;
            } else {
                $total_terlambat++;
            }

            // Format Masa Studi
            $bln = (int) $row['masa_studi_bulan'];
            $thn = floor($bln / 12);
            $sisa_bln = $bln % 12;
            $row['masa_studi_text'] = "{$thn} Tahun {$sisa_bln} Bulan";

            // Kumpulkan Data per Angkatan (Cohort) untuk Kalkulasi IKU
            $key = $row['kode_prodi'] . '-' . $row['tahun_masuk'];
            if (!isset($cohort_stats[$key])) {
                $cohort_stats[$key] = [
                    'kode_prodi' => $row['kode_prodi'],
                    'tahun_masuk' => $row['tahun_masuk'],
                    'jenjang' => $row['jenjang']
                ];
            }
        }

        // 4. Hitung Capaian Per Angkatan (Realisasi / Target * 100)
        $sum_capaian = 0;
        $count_cohort = 0;

        foreach ($cohort_stats as $cohort) {
            $tm = $cohort['tahun_masuk'];
            $kp = $cohort['kode_prodi'];
            $jenjang = strtoupper($cohort['jenjang']);

            // A. Ambil Total Mahasiswa Angkatan (Denominator)
            $total_mhs_angkatan = $db->table('tb_m_mahasiswa')
                ->where('kode_prodi', $kp)
                ->where('tahun_masuk', $tm)
                ->countAllResults();

            // B. Ambil Jumlah Tepat Waktu Cumulative (Numerator)
            // Mengambil semua lulusan tepat waktu dari angkatan tsb (tidak hanya triwulan ini) agar fair
            $jum_tepat_waktu = $db->table('tb_iku_1_lulusan')
                ->join('tb_m_mahasiswa', 'tb_m_mahasiswa.nim = tb_iku_1_lulusan.nim')
                ->where('tb_m_mahasiswa.kode_prodi', $kp)
                ->where('tb_m_mahasiswa.tahun_masuk', $tm)
                ->where('tb_iku_1_lulusan.status_kelulusan', 'Tepat Waktu')
                ->countAllResults();

            // C. Hitung Realisasi
            $realisasi = ($total_mhs_angkatan > 0) ? ($jum_tepat_waktu / $total_mhs_angkatan) * 100 : 0;

            // D. Tentukan Target Ideal
            $target_ideal = 25; // Default S1/D4
            if (strpos($jenjang, 'D3') !== false || strpos($jenjang, 'DIII') !== false) {
                $target_ideal = 33;
            } elseif (strpos($jenjang, 'S2') !== false) {
                $target_ideal = 50;
            } elseif (strpos($jenjang, 'S3') !== false) {
                $target_ideal = 33; // Asumsi S3 sama dgn D3 (Expert) atau custom
            }

            // E. Hitung Capaian
            $capaian = ($target_ideal > 0) ? ($realisasi / $target_ideal) * 100 : 0;

            $sum_capaian += $capaian;
            $count_cohort++;
        }

        // Rata-rata Capaian (Agregat PT)
        $avg_capaian = ($count_cohort > 0) ? ($sum_capaian / $count_cohort) : 0;

        $data = [
            'title' => 'Dashboard IKU 1 (Angka Efisiensi Edukasi)',
            'page' => 'iku',
            'triwulan_info' => $active_triwulan,

            // Cards Data
            'total_lulusan' => $total_lulusan,
            'total_tepat' => $total_tepat,
            'total_terlambat' => $total_terlambat,
            'avg_capaian' => number_format($avg_capaian, 2),

            // Chart Data
            'chart_label' => ['Tepat Waktu', 'Terlambat'],
            'chart_value' => [$total_tepat, $total_terlambat],

            // Table Data
            'lulusan_list' => $data_lulusan
        ];

        return view('admin/iku1/dashboard_iku1', $data);
    }


    public function create($jurusan_kode = null, $nama_prodi = null, $jenjang = null)
    {
        $db = \Config\Database::connect();

        // Ambil data referensi
        $triwulan = $db->table('triwulan')->select('*')->get()->getResultArray();
        $prodi = $db->table('prodi')->select('kode_prodi, nama_prodi, jenjang')->orderBy('nama_prodi', 'ASC')->get()->getResultArray();

        // Decode nama prodi if passed via URL
        $nama_prodi_decoded = $nama_prodi ? rawurldecode($nama_prodi) : null;

        // Find matching kode_prodi if params exist to pre-select
        $selected_kode_prodi = '';
        if ($nama_prodi_decoded && $jenjang) {
            foreach ($prodi as $p) {
                if ($p['nama_prodi'] == $nama_prodi_decoded && $p['jenjang'] == $jenjang) {
                    $selected_kode_prodi = $p['kode_prodi'];
                    break;
                }
            }
        }

        // Get Active Triwulan or Selected Context
        $request = \Config\Services::request();
        $context_id_triwulan = $request->getGet('id_triwulan');

        $active_triwulan = $db->table('triwulan')->where('status', 'Aktif')->get()->getRowArray();
        // Prioritize Context > Active > Null
        $active_triwulan_id = $context_id_triwulan ?? ($active_triwulan['id'] ?? null);

        $back_url = $jurusan_kode ? site_url("admin/iku-detail/1/$jurusan_kode/$nama_prodi/$jenjang") : base_url('admin/iku1/import');
        if ($context_id_triwulan && $jurusan_kode) {
            $back_url .= '?id_triwulan=' . $context_id_triwulan;
        }

        $data = [
            'title' => 'Input Manual IKU 1 (AEE)',
            'page' => 'iku', // Required for layout active state
            'triwulan_list' => $triwulan,
            'active_triwulan_id' => $active_triwulan_id,
            'prodi_list' => $prodi,
            // Context params for back button or pre-filling
            'selected_kode_prodi' => $selected_kode_prodi,
            'back_url' => $back_url
        ];

        return view('admin/iku1/form_tambah', $data);
    }

    // API untuk cek NIM (dipanggil via AJAX)
    public function check_nim($nim)
    {
        $model = new MahasiswaModel();
        $mahasiswa = $model->where('nim', $nim)->first();

        if ($mahasiswa) {
            // Data Ditemukan
            return $this->response->setJSON([
                'found' => true,
                'data' => $mahasiswa
            ]);
        } else {
            // Data Tidak Ditemukan (User harus input manual)
            return $this->response->setJSON([
                'found' => false
            ]);
        }
    }

    public function store()
    {
        // Validasi, exclude nama/prodi required checks IF data found? 
        // No, inputs should always be filled (either auto or manual).
        $rules = [
            'id_triwulan' => 'required',
            'nim' => 'required',
            'nama_lengkap' => 'required',
            'kode_prodi' => 'required',
            'tahun_masuk' => 'required|numeric',
            'tanggal_yudisium' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Mohon lengkapi semua field dengan benar.');
        }

        $nim = $this->request->getPost('nim');
        $nama = $this->request->getPost('nama_lengkap');
        $kode_prodi = $this->request->getPost('kode_prodi');
        $tahun_masuk = $this->request->getPost('tahun_masuk');
        $tanggal_yudisium = $this->request->getPost('tanggal_yudisium');
        $id_triwulan = $this->request->getPost('id_triwulan');

        $mahasiswaModel = new MahasiswaModel();
        $iku1Model = new Iku1Model();
        $prodiModel = new ProdiModel();

        // 1. Cek User Existing
        $existingMhs = $mahasiswaModel->where('nim', $nim)->first();

        if (!$existingMhs) {
            // DATA BARU: Insert ke Master Mahasiswa
            // Ambil data tambahan
            $nikData = $this->request->getPost('nik');
            $noHpData = $this->request->getPost('no_hp');
            $emailData = $this->request->getPost('email');
            $jkData = $this->request->getPost('jenis_kelamin');
            $smtData = $this->request->getPost('semester_masuk');

            // Validasi manual sederhana utk field wajib data baru (jika lolos frontend)
            // Bisa tambahkan $this->validate() rules lagi disini jika strict backend validation diperlukan.

            $mahasiswaModel->insert([
                'nim' => $nim,
                'nama_lengkap' => $nama,
                'kode_prodi' => $kode_prodi,
                'tahun_masuk' => $tahun_masuk,
                'status' => 'Lulus', // Default assumption
                // Field Baru
                'nik' => $nikData,
                'no_hp' => $noHpData,
                'email' => $emailData,
                'jenis_kelamin' => $jkData,
                'semester_masuk' => $smtData
            ]);
        } else {
            // DATA LAMA: Abaikan update master
        }

        // 2. Ambil Jenjang Prodi untuk hitung standar kelulusan
        $prodiData = $prodiModel->where('kode_prodi', $kode_prodi)->first();
        if (!$prodiData) {
            return redirect()->back()->withInput()->with('error', 'Kode Prodi tidak valid.');
        }
        $jenjang = $prodiData['jenjang'];

        // 3. Hitung Lama Studi (Tahun) & Masa Studi (Bulan)
        // Logika sederhana user: Tahun Yudisium - Tahun Masuk
        $tahun_yudisium = date('Y', strtotime($tanggal_yudisium));
        $lama_studi_tahun = (int) $tahun_yudisium - (int) $tahun_masuk;
        $masa_studi_bulan = $lama_studi_tahun * 12; // Konversi kasar

        // 4. Tentukan Status Kelulusan
        $max_tahun = 4; // Default S1/D4
        if ($jenjang === 'DIII') {
            $max_tahun = 3;
        }

        $status_kelulusan = ($lama_studi_tahun <= $max_tahun) ? 'Tepat Waktu' : 'Terlambat';

        // 5. Simpan Transaksi IKU 1
        $insertData = [
            'nim' => $nim,
            'id_triwulan' => $id_triwulan,
            'tanggal_yudisium' => $tanggal_yudisium,
            'masa_studi_bulan' => $masa_studi_bulan,
            'status_kelulusan' => $status_kelulusan
        ];

        if ($iku1Model->insert($insertData)) {
            return redirect()->to('admin/iku1/input')
                ->with('success', "Data berhasil disimpan for NIM: $nim. Status: $status_kelulusan");
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data ke database.');
        }
    }
    public function edit($id)
    {
        $db = \Config\Database::connect();
        $iku1Model = new Iku1Model();

        // 1. Ambil Data Transaksi join Mahasiswa
        $data_edit = $iku1Model->select('tb_iku_1_lulusan.*, tb_m_mahasiswa.nama_lengkap, tb_m_mahasiswa.kode_prodi, tb_m_mahasiswa.tahun_masuk, tb_m_mahasiswa.nik, tb_m_mahasiswa.email, tb_m_mahasiswa.no_hp, tb_m_mahasiswa.jenis_kelamin, tb_m_mahasiswa.semester_masuk')
            ->join('tb_m_mahasiswa', 'tb_m_mahasiswa.nim = tb_iku_1_lulusan.nim')
            ->where('tb_iku_1_lulusan.id', $id)
            ->first();

        if (!$data_edit) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // 2. Ambil Referensi (Copy dari create)
        $triwulan = $db->table('triwulan')->select('*')->get()->getResultArray();
        $prodi = $db->table('prodi')->select('kode_prodi, nama_prodi, jenjang')->orderBy('nama_prodi', 'ASC')->get()->getResultArray();

        $redirect_to = $this->request->getGet('redirect_to');

        $data = [
            'title' => 'Edit Data IKU 1 (AEE)',
            'page' => 'iku',
            'triwulan_list' => $triwulan,
            'prodi_list' => $prodi,
            'active_triwulan_id' => null, // Tidak perlu auto-select default jika edit
            'data_edit' => $data_edit,
            'selected_kode_prodi' => $data_edit['kode_prodi'],
            'redirect_to' => $redirect_to,
            'back_url' => $redirect_to ? urldecode($redirect_to) : base_url('admin/iku1/dashboard')
        ];

        return view('admin/iku1/form_tambah', $data);
    }

    public function update($id)
    {
        $rules = [
            'id_triwulan' => 'required',
            'nama_lengkap' => 'required',
            'kode_prodi' => 'required',
            'tahun_masuk' => 'required|numeric',
            'tanggal_yudisium' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Mohon lengkapi field wajib.');
        }

        $iku1Model = new Iku1Model();
        $mahasiswaModel = new MahasiswaModel();
        $prodiModel = new ProdiModel(); // Use \App\Models\ProdiModel if namespace issue

        // Data Existing
        $existingIku = $iku1Model->find($id);
        if (!$existingIku) {
            return redirect()->back()->with('error', 'Data IKU tidak ditemukan.');
        }

        $nim = $existingIku['nim']; // NIM tidak berubah dari tabel IKU

        // 1. Update Master Mahasiswa (Nama, Prodi, Tahun Masuk, Biodata)
        $updateMhs = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'kode_prodi' => $this->request->getPost('kode_prodi'),
            'tahun_masuk' => $this->request->getPost('tahun_masuk'),
            'nik' => $this->request->getPost('nik'),
            'no_hp' => $this->request->getPost('no_hp'),
            'email' => $this->request->getPost('email'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'semester_masuk' => $this->request->getPost('semester_masuk')
        ];
        // Filter empty values if you don't want to overwrite with null? 
        // User wants "complete data", so upgrading with value is good.
        $mahasiswaModel->update($nim, $updateMhs);

        // 2. Hitung Ulang Logika Kelulusan
        $kode_prodi = $this->request->getPost('kode_prodi');
        $prodiData = $prodiModel->where('kode_prodi', $kode_prodi)->first();
        $jenjang = $prodiData['jenjang'] ?? 'S1';

        $tahun_yudisium = date('Y', strtotime($this->request->getPost('tanggal_yudisium')));
        $tahun_masuk = $this->request->getPost('tahun_masuk');
        $lama_studi_tahun = (int) $tahun_yudisium - (int) $tahun_masuk;
        $masa_studi_bulan = $lama_studi_tahun * 12;

        $max_tahun = ($jenjang === 'DIII') ? 3 : 4; // Simplifikasi
        $status_kelulusan = ($lama_studi_tahun <= $max_tahun) ? 'Tepat Waktu' : 'Terlambat';

        // 3. Update Tabel IKU
        $updateIku = [
            'id_triwulan' => $this->request->getPost('id_triwulan'),
            'tanggal_yudisium' => $this->request->getPost('tanggal_yudisium'),
            'masa_studi_bulan' => $masa_studi_bulan,
            'status_kelulusan' => $status_kelulusan
        ];

        if ($iku1Model->update($id, $updateIku)) {
            $redirect_to = $this->request->getPost('redirect_to');
            if ($redirect_to) {
                return redirect()->to(urldecode($redirect_to))->with('success', 'Data berhasil diperbarui.');
            }
            return redirect()->to('admin/iku1/dashboard')->with('success', 'Data berhasil diperbarui.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal update data IKU.');
        }
    }

    public function delete($id)
    {
        $iku1Model = new Iku1Model();

        // Cek data exist
        $data = $iku1Model->find($id);
        if (!$data) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Proses Hapus
        if ($iku1Model->delete($id)) {
            // Cek redirect url (supaya bisa kembali ke halaman detail prodi tadi)
            $redirect_to = $this->request->getGet('redirect_to');
            if ($redirect_to) {
                return redirect()->to(urldecode($redirect_to))->with('success', 'Data berhasil dihapus.');
            }
            return redirect()->to('admin/iku1/dashboard')->with('success', 'Data berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }

}

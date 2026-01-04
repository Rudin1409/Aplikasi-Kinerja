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
        // Redirect to new Detail View (AdminController::ikuDetail via route)
        // Or redirect back to main IKU page
        return redirect()->to(base_url('admin/iku'));
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

        // 1. Validasi Duplikat (NIM + Triwulan)
        $existingIku1 = $iku1Model->where('nim', $nim)
            ->where('id_triwulan', $id_triwulan)
            ->first();
        if ($existingIku1) {
            return redirect()->back()->withInput()->with('error', "Data mahasiswa dengan NIM $nim sudah ada di triwulan ini.");
        }

        // 2. Cek/Update Master Mahasiswa
        $existingMhs = $mahasiswaModel->where('nim', $nim)->first();

        if (!$existingMhs) {
            // DATA BARU: Insert
            $mahasiswaModel->insert([
                'nim' => $nim,
                'nama_lengkap' => $nama,
                'kode_prodi' => $kode_prodi,
                'tahun_masuk' => $tahun_masuk,
                'tanggal_yudisium' => $tanggal_yudisium, // Simpan tgl yudisium
                'status' => 'Lulus',
                'nik' => $this->request->getPost('nik'),
                'no_hp' => $this->request->getPost('no_hp'),
                'email' => $this->request->getPost('email'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'semester_masuk' => $this->request->getPost('semester_masuk')
            ]);
        } else {
            // DATA LAMA: Update Tanggal Yudisium jika belum ada atau berbeda
            if (empty($existingMhs['tanggal_yudisium']) || $existingMhs['tanggal_yudisium'] != $tanggal_yudisium) {
                $mahasiswaModel->update($existingMhs['id'], [
                    'tanggal_yudisium' => $tanggal_yudisium,
                    'status' => 'Lulus'
                ]);
            }
        }

        // 3. Ambil Jenjang Prodi
        $prodiData = $prodiModel->where('kode_prodi', $kode_prodi)->first();
        if (!$prodiData) {
            return redirect()->back()->withInput()->with('error', 'Kode Prodi tidak valid.');
        }
        $jenjang = $prodiData['jenjang'];

        // 4. Hitung Masa Studi (Bulan)
        // Asumsi mulai kuliah: 1 September tahun masuk
        $masa_studi_bulan = 0;
        try {
            $tgl_masuk = new \DateTime($tahun_masuk . '-09-01');
            $tgl_lulus = new \DateTime($tanggal_yudisium);

            // Hitung selisih
            $diff = $tgl_masuk->diff($tgl_lulus);
            $masa_studi_bulan = ($diff->y * 12) + $diff->m;

            // Masa studi tidak boleh negatif
            if ($masa_studi_bulan < 0)
                $masa_studi_bulan = 0;
        } catch (\Exception $e) {
            $masa_studi_bulan = 0;
        }

        // 5. Tentukan Status Kelulusan (Standard IKU)
        // D3: Tepat waktu jika <= 3.5 tahun (42 bulan)
        // D4/S1: Tepat waktu jika <= 4.5 tahun (54 bulan)
        // S2: Tepat waktu jika <= 2.5 tahun (30 bulan) - asumsi
        $is_d3 = (strpos($jenjang, 'D3') !== false || strpos($jenjang, 'DIII') !== false);

        $threshold_bulan = $is_d3 ? 42 : 54; // Default D4/S1
        $status_kelulusan = ($masa_studi_bulan <= $threshold_bulan) ? 'Tepat Waktu' : 'Terlambat';

        // 6. Simpan Transaksi IKU 1
        $insertData = [
            'nim' => $nim,
            'id_triwulan' => $id_triwulan,
            'tanggal_yudisium' => $tanggal_yudisium,
            'masa_studi_bulan' => $masa_studi_bulan,
            'status_kelulusan' => $status_kelulusan
        ];

        if ($iku1Model->insert($insertData)) {
            $formatted_masa = floor($masa_studi_bulan / 12) . " Tahun " . ($masa_studi_bulan % 12) . " Bulan";
            return redirect()->to('admin/iku1/input')
                ->with('success', "Data berhasil disimpan. Masa Studi: $formatted_masa ($status_kelulusan)");
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
            'back_url' => $redirect_to ? urldecode($redirect_to) : base_url('admin/iku')
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
            return redirect()->to('admin/iku')->with('success', 'Data berhasil diperbarui.');
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
            return redirect()->to('admin/iku')->with('success', 'Data berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }

    public function bulk_delete()
    {
        $iku1Model = new Iku1Model();
        $json = $this->request->getJSON();
        $ids = $json->ids ?? [];

        if (empty($ids)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada data yang dipilih']);
        }

        // Use whereIn for batch delete - safer and faster
        // Also checks if ID matches primary key correctly
        $iku1Model->whereIn('id', $ids)->delete();

        $deleted = $iku1Model->db->affectedRows();

        if ($deleted > 0) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "$deleted data berhasil dihapus",
                'deleted_count' => $deleted
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false, // Changed to false if 0 deleted
                'message' => "Gagal menghapus data atau data sudah terhapus.",
                'deleted_count' => 0
            ]);
        }
    }
}

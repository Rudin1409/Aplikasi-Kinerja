<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MahasiswaModel;
use App\Models\ProdiModel;

class Iku2Controller extends BaseController
{
    // Reuse Index? Or create new dashboard?
    // User focus is on 'detail' page, but we need CRUD.

    public function create($jurusan_kode = null, $nama_prodi = null, $jenjang = null)
    {
        $db = \Config\Database::connect();

        // Referensi
        $triwulan = $db->table('triwulan')->select('*')->get()->getResultArray();
        $prodi = $db->table('prodi')->select('kode_prodi, nama_prodi, jenjang')->orderBy('nama_prodi', 'ASC')->get()->getResultArray();

        $nama_prodi_decoded = $nama_prodi ? rawurldecode($nama_prodi) : null;

        $selected_kode_prodi = '';
        if ($nama_prodi_decoded && $jenjang) {
            foreach ($prodi as $p) {
                if ($p['nama_prodi'] == $nama_prodi_decoded && $p['jenjang'] == $jenjang) {
                    $selected_kode_prodi = $p['kode_prodi'];
                    break;
                }
            }
        }

        // Context
        $request = \Config\Services::request();
        $context_id_triwulan = $request->getGet('id_triwulan');
        $active_triwulan = $db->table('triwulan')->where('status', 'Aktif')->get()->getRowArray();
        $active_triwulan_id = $context_id_triwulan ?? ($active_triwulan['id'] ?? null);

        $back_url = $jurusan_kode ? site_url("admin/iku-detail/2/$jurusan_kode/$nama_prodi/$jenjang") : base_url('admin/iku2/import');
        if ($context_id_triwulan && $jurusan_kode) {
            $back_url .= '?id_triwulan=' . $context_id_triwulan;
        }

        $data = [
            'title' => 'Input Manual IKU 2 (Lulusan Bekerja/Studi)',
            'page' => 'iku',
            'triwulan_list' => $triwulan,
            'active_triwulan_id' => $active_triwulan_id,
            'prodi_list' => $prodi,
            'selected_kode_prodi' => $selected_kode_prodi,
            'back_url' => $back_url
        ];

        return view('admin/iku2/form_tambah', $data);
    }

    public function store()
    {
        $rules = [
            'id_triwulan' => 'required',
            'nim' => 'required',
            'nama_lengkap' => 'required',
            'kode_prodi' => 'required',
            'status_aktivitas' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Mohon lengkapi field wajib.');
        }

        $nim = $this->request->getPost('nim');
        $nama = $this->request->getPost('nama_lengkap');
        $kode_prodi = $this->request->getPost('kode_prodi');
        $id_triwulan = $this->request->getPost('id_triwulan');

        $mahasiswaModel = new MahasiswaModel();
        $db = \Config\Database::connect();

        // 1. Cek User Existing & Update/Insert
        $existingMhs = $mahasiswaModel->where('nim', $nim)->first();

        $mhsData = [
            'nim' => $nim,
            'nama_lengkap' => $nama,
            'kode_prodi' => $kode_prodi,
            // Update fields if provided
            'tahun_masuk' => $this->request->getPost('tahun_masuk'),
            'nik' => $this->request->getPost('nik'),
            'no_hp' => $this->request->getPost('no_hp'),
            'email' => $this->request->getPost('email'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin')
        ];

        if (!$existingMhs) {
            $mahasiswaModel->insert($mhsData);
        } else {
            $mahasiswaModel->update($nim, $mhsData);
        }

        // 2. Simpan Transaksi IKU 2
        $insertData = [
            'nim' => $nim,
            'id_triwulan' => $id_triwulan,
            'status_aktivitas' => $this->request->getPost('status_aktivitas'),
            'nama_tempat' => $this->request->getPost('nama_tempat'),
            'pendapatan' => $this->request->getPost('pendapatan') ?? 0,
            'masa_tunggu_bulan' => $this->request->getPost('masa_tunggu_bulan') ?? 0,
            'link_bukti' => $this->request->getPost('link_bukti')
        ];

        if ($db->table('tb_iku_2_lulusan')->insert($insertData)) {
            return redirect()->to('admin/iku2/input') // Should ideally go back to specific context or list
                ->with('success', "Data berhasil disimpan for NIM: $nim.");
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data.');
        }
    }

    public function edit($id)
    {
        $db = \Config\Database::connect();

        // Join Mahasiswa
        $data_edit = $db->table('tb_iku_2_lulusan')
            ->select('tb_iku_2_lulusan.*, tb_m_mahasiswa.nama_lengkap, tb_m_mahasiswa.kode_prodi, tb_m_mahasiswa.tahun_masuk, tb_m_mahasiswa.nik, tb_m_mahasiswa.email, tb_m_mahasiswa.no_hp, tb_m_mahasiswa.jenis_kelamin')
            ->join('tb_m_mahasiswa', 'tb_m_mahasiswa.nim = tb_iku_2_lulusan.nim')
            ->where('tb_iku_2_lulusan.id', $id)
            ->get()->getRowArray();

        if (!$data_edit) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $triwulan = $db->table('triwulan')->select('*')->get()->getResultArray();
        $prodi = $db->table('prodi')->select('kode_prodi, nama_prodi, jenjang')->orderBy('nama_prodi', 'ASC')->get()->getResultArray();

        $redirect_to = $this->request->getGet('redirect_to');

        $data = [
            'title' => 'Edit Data IKU 2',
            'page' => 'iku',
            'triwulan_list' => $triwulan,
            'prodi_list' => $prodi,
            'active_triwulan_id' => null,
            'data_edit' => $data_edit,
            'selected_kode_prodi' => $data_edit['kode_prodi'],
            'redirect_to' => $redirect_to,
            'back_url' => $redirect_to ? urldecode($redirect_to) : base_url('admin/iku')
        ];

        return view('admin/iku2/form_tambah', $data);
    }

    public function update($id)
    {
        $rules = [
            'id_triwulan' => 'required',
            'nama_lengkap' => 'required',
            'kode_prodi' => 'required',
            'status_aktivitas' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Mohon lengkapi field wajib.');
        }

        $db = \Config\Database::connect();
        $mahasiswaModel = new MahasiswaModel();

        // Cek Exist
        $existing = $db->table('tb_iku_2_lulusan')->where('id', $id)->get()->getRowArray();
        if (!$existing)
            return redirect()->back()->with('error', 'Data tidak ditemukan.');

        $nim = $existing['nim'];

        // 1. Update Mahasiswa
        $mhsUpdate = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'kode_prodi' => $this->request->getPost('kode_prodi'),
            'tahun_masuk' => $this->request->getPost('tahun_masuk'),
            'nik' => $this->request->getPost('nik'),
            'no_hp' => $this->request->getPost('no_hp'),
            'email' => $this->request->getPost('email'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin')
        ];
        $mahasiswaModel->update($nim, $mhsUpdate);

        // 2. Update IKU 2
        $updateData = [
            'id_triwulan' => $this->request->getPost('id_triwulan'),
            'status_aktivitas' => $this->request->getPost('status_aktivitas'),
            'nama_tempat' => $this->request->getPost('nama_tempat'),
            'pendapatan' => $this->request->getPost('pendapatan'),
            'masa_tunggu_bulan' => $this->request->getPost('masa_tunggu_bulan'),
            'link_bukti' => $this->request->getPost('link_bukti')
        ];

        $db->table('tb_iku_2_lulusan')->where('id', $id)->update($updateData);

        $redirect_to = $this->request->getPost('redirect_to');
        if ($redirect_to) {
            return redirect()->to(urldecode($redirect_to))->with('success', 'Data berhasil diperbarui.');
        }
        return redirect()->to('admin/dashboard')->with('success', 'Data berhasil diperbarui.');
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $deleted = $db->table('tb_iku_2_lulusan')->where('id', $id)->delete();

        if ($deleted) {
            $redirect_to = $this->request->getGet('redirect_to');
            if ($redirect_to) {
                return redirect()->to(urldecode($redirect_to))->with('success', 'Data berhasil dihapus.');
            }
            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal hapus data.');
        }
    }
}

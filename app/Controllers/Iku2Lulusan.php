<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Iku2LulusanModel;
use App\Models\MahasiswaModel;
use App\Models\ProdiModel;
use App\Models\RefUmpModel;

/**
 * IKU 2 Lulusan Controller
 * 
 * Handles IKU 2: Graduate Activities Tracking (Tracer Study)
 * - Working (Bekerja)
 * - Entrepreneurship (Wirausaha) 
 * - Further Study (Lanjut Studi)
 * - Job Seeking (Mencari Kerja)
 */
class Iku2Lulusan extends BaseController
{
    protected $iku2Model;
    protected $mahasiswaModel;
    protected $prodiModel;
    protected $refUmpModel;

    public function __construct()
    {
        $this->iku2Model = new Iku2LulusanModel();
        $this->mahasiswaModel = new MahasiswaModel();
        $this->prodiModel = new ProdiModel();
        $this->refUmpModel = new RefUmpModel();
    }

    // Note: Dashboard functionality moved to AdminController::ikuDetail()
    // The standalone dashboard has been removed.

    /**
     * Create/Input Form
     */
    public function create($jurusan_kode = null, $nama_prodi = null, $jenjang = null)
    {
        $db = \Config\Database::connect();
        $id_triwulan = $this->request->getGet('id_triwulan');

        // Get Active Triwulan if not set
        if (!$id_triwulan) {
            $active = $db->table('triwulan')->where('status', 'Aktif')->get()->getRowArray();
            $id_triwulan = $active['id'] ?? null;
        }

        $triwulan_list = $db->table('triwulan')->get()->getResultArray();
        $prodi_list = $this->prodiModel->orderBy('nama_prodi', 'ASC')->findAll();
        $ump_list = $this->refUmpModel->orderBy('provinsi', 'ASC')->findAll();

        // Build Back URL
        $back_url = base_url('admin/iku2/dashboard');
        $selected_kode_prodi = '';

        if ($jurusan_kode && $nama_prodi && $jenjang) {
            $nama_prodi_decoded = rawurldecode($nama_prodi);
            $back_url = base_url("admin/iku-detail/2/$jurusan_kode/$nama_prodi/$jenjang");
            if ($id_triwulan)
                $back_url .= "?id_triwulan=$id_triwulan";

            foreach ($prodi_list as $p) {
                if (strcasecmp($p['nama_prodi'], $nama_prodi_decoded) === 0 && strcasecmp($p['jenjang'], $jenjang) === 0) {
                    $selected_kode_prodi = $p['kode_prodi'];
                    break;
                }
            }
        } elseif ($id_triwulan) {
            $back_url .= "?id_triwulan=$id_triwulan";
        }

        $data = [
            'title' => 'Input Data Tracer (IKU 2)',
            'page' => 'iku',
            'triwulan_list' => $triwulan_list,
            'prodi_list' => $prodi_list,
            'id_triwulan_selected' => $id_triwulan,
            'ump_list' => $ump_list,
            'back_url' => $back_url,
            'selected_kode_prodi' => $selected_kode_prodi
        ];

        return view('admin/iku2/form_input', $data);
    }

    /**
     * Edit Form - Load existing data and show in the same input form
     */
    public function edit($id)
    {
        $db = \Config\Database::connect();

        // Get existing IKU 2 record
        $record = $this->iku2Model->find($id);
        if (!$record) {
            return redirect()->to('admin/iku2/dashboard')->with('error', 'Data tidak ditemukan.');
        }

        // Get related mahasiswa data
        $mahasiswa = $this->mahasiswaModel->where('nim', $record['nim'])->first();

        // Get triwulan and prodi lists
        $triwulan_list = $db->table('triwulan')->get()->getResultArray();
        $prodi_list = $this->prodiModel->orderBy('nama_prodi', 'ASC')->findAll();
        $ump_list = $this->refUmpModel->orderBy('provinsi', 'ASC')->findAll();

        // Build Back URL (return to detail page)
        $back_url = base_url('admin/iku');
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $back_url = $_SERVER['HTTP_REFERER'];
        }

        // Merge record data with mahasiswa data for form population
        $existing_data = array_merge($record, [
            'nama_lengkap' => $mahasiswa['nama_lengkap'] ?? '',
            'kode_prodi' => $mahasiswa['kode_prodi'] ?? '',
            'tahun_masuk' => $mahasiswa['tahun_masuk'] ?? '',
            'tanggal_yudisium' => $mahasiswa['tanggal_yudisium'] ?? ''
        ]);

        $data = [
            'title' => 'Edit Data Tracer (IKU 2)',
            'page' => 'iku',
            'triwulan_list' => $triwulan_list,
            'prodi_list' => $prodi_list,
            'id_triwulan_selected' => $record['id_triwulan'],
            'ump_list' => $ump_list,
            'back_url' => $back_url,
            'selected_kode_prodi' => $mahasiswa['kode_prodi'] ?? '',
            'edit_mode' => true,
            'existing_data' => $existing_data,
            'record_id' => $id
        ];

        return view('admin/iku2/form_input', $data);
    }

    /**
     * AJAX: Get UMP by Province ID
     */
    public function get_ump($id)
    {
        $ump = $this->refUmpModel->find($id);
        if ($ump) {
            return $this->response->setJSON(['status' => 'success', 'data' => $ump]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'UMP not found']);
    }

    /**
     * AJAX: Check NIM
     */
    public function check_nim($nim)
    {
        $db = \Config\Database::connect();
        $mhs = $this->mahasiswaModel->where('nim', $nim)->first();

        if ($mhs) {
            // Check IKU 1 for Yudisium Date
            $iku1 = $db->table('tb_iku_1_lulusan')->where('nim', $nim)->orderBy('id', 'DESC')->get()->getRowArray();

            $tanggal_yudisium = null;
            $has_iku1 = false;

            if ($iku1 && !empty($iku1['tanggal_yudisium'])) {
                $tanggal_yudisium = $iku1['tanggal_yudisium'];
                $has_iku1 = true;
            } elseif (!empty($mhs['tanggal_yudisium'])) {
                $tanggal_yudisium = $mhs['tanggal_yudisium'];
            }

            $mhs['tanggal_yudisium'] = $tanggal_yudisium;
            $mhs['has_iku1'] = $has_iku1;

            return $this->response->setJSON(['found' => true, 'data' => $mhs]);
        }
        return $this->response->setJSON(['found' => false]);
    }

    /**
     * Store/Save IKU 2 Data
     * Implements IKU 2025 Scoring Logic
     */
    public function save()
    {
        // Validation
        $rules = [
            'nim' => 'required',
            'jenis_aktivitas' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak lengkap. NIM dan Jenis Aktivitas wajib diisi.');
        }

        $nim = $this->request->getPost('nim');
        $jenis_aktivitas = $this->request->getPost('jenis_aktivitas');
        $id_triwulan = $this->request->getPost('id_triwulan') ?: 1;
        $tanggal_mulai = $this->request->getPost('tanggal_mulai');
        $tgl_yudisium = $this->request->getPost('tanggal_yudisium');

        // =============================================
        // STEP 1: Handle New Student Registration
        // =============================================
        $mhs = $this->mahasiswaModel->where('nim', $nim)->first();
        if (!$mhs) {
            $nama_lengkap = $this->request->getPost('nama_lengkap');
            $kode_prodi = $this->request->getPost('kode_prodi');
            $tahun_masuk = $this->request->getPost('tahun_masuk');

            if ($nama_lengkap && $kode_prodi && $tahun_masuk) {
                $this->mahasiswaModel->insert([
                    'nim' => $nim,
                    'nama_lengkap' => $nama_lengkap,
                    'kode_prodi' => $kode_prodi,
                    'tahun_masuk' => $tahun_masuk,
                    'status' => 'Lulus',
                    'tanggal_yudisium' => $tgl_yudisium ?: null,
                    'nik' => $this->request->getPost('nik'),
                    'no_hp' => $this->request->getPost('no_hp'),
                    'email' => $this->request->getPost('email'),
                    'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                    'semester_masuk' => $this->request->getPost('semester_masuk')
                ]);
            } else {
                return redirect()->back()->withInput()->with('error', 'Mahasiswa baru: Nama, Prodi, dan Tahun Masuk wajib diisi.');
            }
        } else {
            // Update Yudisium date if empty
            if (empty($mhs['tanggal_yudisium']) && $tgl_yudisium) {
                $this->mahasiswaModel->update($mhs['nim'], ['tanggal_yudisium' => $tgl_yudisium]);
            }
        }

        // =============================================
        // STEP 2: Calculate Waiting Period (Masa Tunggu)
        // =============================================
        $masa_tunggu_bulan = 0;
        if ($tanggal_mulai && $tgl_yudisium) {
            $t1 = new \DateTime($tgl_yudisium);
            $t2 = new \DateTime($tanggal_mulai);
            $diff = $t1->diff($t2);
            $masa_tunggu_bulan = ($diff->y * 12) + $diff->m;
        }

        // =============================================
        // STEP 3: Get Salary & UMP Data
        // =============================================
        $gaji_raw = $this->request->getPost('gaji_bulan');
        $gaji_bulan = is_numeric(str_replace(['.', ','], '', $gaji_raw))
            ? (float) str_replace(['.', ','], ['', '.'], $gaji_raw)
            : 0;

        $provinsi_id = $this->request->getPost('provinsi_tempat_kerja');
        $is_gaji_layak = false;

        if ($provinsi_id && $gaji_bulan > 0) {
            $umpData = $this->refUmpModel->find($provinsi_id);
            if ($umpData) {
                $target_gaji = 1.2 * (float) $umpData['nilai_ump'];
                $is_gaji_layak = ($gaji_bulan >= $target_gaji);
            }
        }

        // =============================================
        // STEP 4: IKU 2025 SCORING LOGIC
        // =============================================
        $nilai_bobot = 0.00;

        switch ($jenis_aktivitas) {
            case 'Bekerja':
                /**
                 * LOGIC A: BEKERJA (Working) - Sesuai Dokumen IKU 2025
                 * - Bobot 1.0: Masa Tunggu < 6 bulan DAN Gaji >= 1.2x UMP (k = 1.0 penuh)
                 * - Bobot 0.6: Masa Tunggu 6-12 bulan DAN Gaji >= 1.2x UMP
                 * - Bobot 0.4: Masa Tunggu < 12 bulan DAN Gaji < 1.2x UMP
                 * - Bobot 0.0: Masa Tunggu > 12 bulan
                 */
                if ($masa_tunggu_bulan > 12) {
                    $nilai_bobot = 0.00;
                } elseif ($masa_tunggu_bulan < 6 && $is_gaji_layak) {
                    $nilai_bobot = 1.00; // Bobot Penuh
                } elseif ($masa_tunggu_bulan >= 6 && $masa_tunggu_bulan <= 12 && $is_gaji_layak) {
                    $nilai_bobot = 0.60; // Bobot Medium
                } elseif (!$is_gaji_layak && $masa_tunggu_bulan <= 12) {
                    $nilai_bobot = 0.40; // Bobot Rendah (Gaji tidak layak)
                }
                break;

            case 'Wirausaha':
                /**
                 * LOGIC B: WIRAUSAHA (Entrepreneurship) - Sesuai Dokumen IKU 2025
                 * - Pendiri/Co-Founder/Pemilik Usaha (0.75)
                 * - Freelance/Pekerja Lepas (0.25)
                 */
                $posisi = $this->request->getPost('posisi_wirausaha');
                if ($posisi == 'Pendiri') {
                    $nilai_bobot = 0.75;
                } elseif ($posisi == 'Freelance') {
                    $nilai_bobot = 0.25;
                }
                break;

            case 'Lanjut Studi':
                /**
                 * LOGIC C: LANJUT STUDI (Further Education) - Sesuai Dokumen IKU 2025
                 * - Diterima/Mulai Studi dalam < 12 bulan setelah lulus (1.0)
                 * - Diterima/Mulai Studi > 12 bulan setelah lulus (0.0)
                 */
                if ($masa_tunggu_bulan < 12) {
                    $nilai_bobot = 1.00;
                } else {
                    $nilai_bobot = 0.00;
                }
                break;

            case 'Mencari Kerja':
                /**
                 * LOGIC D: MENCARI KERJA (Job Seeking)
                 * Belum produktif, bobot 0
                 */
                $nilai_bobot = 0.00;
                break;
        }

        // =============================================
        // STEP 5: Handle File Upload
        // =============================================
        $file = $this->request->getFile('bukti_validasi');
        $filename = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $filename = $file->getRandomName();
            $file->move('uploads/bukti_iku2/', $filename);
        }

        // =============================================
        // STEP 6: Prepare Data Array
        // =============================================
        $data = [
            'nim' => $nim,
            'id_triwulan' => $id_triwulan,
            'jenis_aktivitas' => $jenis_aktivitas,
            'nama_tempat' => $this->request->getPost('nama_tempat') ?: null,
            'provinsi_tempat_kerja' => $provinsi_id ?: null,
            'tanggal_mulai' => $tanggal_mulai ?: null,
            'gaji_bulan' => $gaji_bulan,
            'masa_tunggu_bulan' => $masa_tunggu_bulan,
            'posisi_wirausaha' => $this->request->getPost('posisi_wirausaha') ?: null,
            'bukti_validasi' => $filename,
            'status_validasi' => 'Menunggu',
            'nilai_bobot' => $nilai_bobot
        ];

        // =============================================
        // STEP 7: Delete Existing (if any) + Insert Fresh
        // This avoids the "There is no data to update" error
        // =============================================
        $existing = $this->iku2Model
            ->where('nim', $nim)
            ->where('id_triwulan', $id_triwulan)
            ->first();

        if ($existing) {
            // DELETE old record first
            $this->iku2Model->delete($existing['id']);

            // Keep old bukti if no new file uploaded
            if (!$filename && !empty($existing['bukti_validasi'])) {
                $data['bukti_validasi'] = $existing['bukti_validasi'];
            }
        }

        // INSERT fresh data
        $this->iku2Model->insert($data);
        $msg = $existing ? "✅ Data berhasil diperbarui! Bobot: $nilai_bobot" : "✅ Data berhasil disimpan! Bobot: $nilai_bobot";

        // Check if there's a redirect URL from form (edit mode)
        $redirect_url = $this->request->getPost('redirect_url');

        if (!empty($redirect_url)) {
            // Redirect to the specified URL (e.g., detail page)
            return redirect()->to($redirect_url)->with('success', $msg);
        }

        // Default: Redirect back to input form
        $default_url = 'admin/iku2/input';
        if ($id_triwulan) {
            $default_url .= "?id_triwulan=$id_triwulan";
        }

        return redirect()->to($default_url)->with('success', $msg);
    }
    public function bulk_delete()
    {
        $json = $this->request->getJSON();
        $ids = $json->ids ?? [];

        if (empty($ids)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada data yang dipilih']);
        }

        // Use whereIn for batch delete - safer and faster
        $this->iku2Model->whereIn('id', $ids)->delete();

        $deleted = $this->iku2Model->db->affectedRows();

        if ($deleted > 0) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "$deleted data berhasil dihapus",
                'deleted_count' => $deleted
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Gagal menghapus data atau data sudah terhapus.",
                'deleted_count' => 0
            ]);
        }
    }
}

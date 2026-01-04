<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MahasiswaModel;
use App\Models\ProdiModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MahasiswaController extends BaseController
{
    protected $mahasiswaModel;
    protected $prodiModel;

    public function __construct()
    {
        $this->mahasiswaModel = new MahasiswaModel();
        $this->prodiModel = new ProdiModel();
    }

    public function index()
    {
        $keyword = $this->request->getVar('keyword');
        $jurusan_kode = $this->request->getVar('jurusan');
        $nama_prodi = $this->request->getVar('prodi');
        $jenjang = $this->request->getVar('jenjang');

        $db = \Config\Database::connect();

        // Initialize Builder
        $builder = $this->mahasiswaModel
            ->join('prodi', 'prodi.kode_prodi = tb_m_mahasiswa.kode_prodi', 'left')
            ->select('tb_m_mahasiswa.*, prodi.nama_prodi, prodi.jenjang');

        // Fetch Prodi Info if prodi and jenjang are provided
        $prodi_info = null;
        $jurusan_info = null;

        if ($nama_prodi && $jenjang) {
            // Get specific prodi by name and jenjang
            $prodi_info = $db->table('prodi')
                ->where('nama_prodi', $nama_prodi)
                ->where('jenjang', $jenjang)
                ->get()
                ->getRowArray();

            if ($prodi_info) {
                // Filter mahasiswa by this specific prodi
                $builder->where('tb_m_mahasiswa.kode_prodi', $prodi_info['kode_prodi']);

                // Also get jurusan info for context
                if ($jurusan_kode) {
                    $jurusan_info = $db->table('jurusan')->where('kode_jurusan', $jurusan_kode)->get()->getRowArray();
                }
            }
        } elseif ($jurusan_kode) {
            // Fallback: Filter by Jurusan if no prodi specified
            $jurusan_info = $db->table('jurusan')->where('kode_jurusan', $jurusan_kode)->get()->getRowArray();
            if ($jurusan_info) {
                $prodi_list = $db->table('prodi')->where('jurusan_id', $jurusan_info['id'])->get()->getResultArray();
                if (!empty($prodi_list)) {
                    $prodi_codes = array_column($prodi_list, 'kode_prodi');
                    $builder->whereIn('tb_m_mahasiswa.kode_prodi', $prodi_codes);
                }
            }
        }

        // Keyword Search
        if ($keyword) {
            $builder->groupStart()
                ->like('nama_lengkap', $keyword)
                ->orLike('nim', $keyword)
                ->groupEnd();
        }

        // Items Per Page and Ordering
        $per_page = $this->request->getVar('per_page') ?? 20;
        $builder->orderBy('created_at', 'DESC');

        // Dynamic Title Construction
        $title = 'Master Data Mahasiswa';
        if ($prodi_info) {
            // Use jenjang + nama_prodi (e.g., "D4 Arsitektur Bangunan Gedung")
            $jenjang_label = match ($prodi_info['jenjang']) {
                'DIII' => 'D3',
                'DIV' => 'D4',
                default => $prodi_info['jenjang']
            };
            $title .= ' - ' . $jenjang_label . ' ' . $prodi_info['nama_prodi'];
        } elseif ($jurusan_info) {
            $title .= ' - ' . $jurusan_info['nama_jurusan'];
        }

        // Build back URL for breadcrumb
        $back_url = base_url('admin/dashboard');
        if ($nama_prodi && $jenjang && $jurusan_kode) {
            $back_url = base_url('admin/iku-prodi/' . $jurusan_kode . '/' . urlencode($nama_prodi) . '/' . $jenjang);
        } elseif ($jurusan_kode) {
            $back_url = base_url('admin/prodi-capaian/' . $jurusan_kode);
        }

        // Sorting
        $sort_by = $this->request->getVar('sort_by') ?? 'nim';
        $sort_order = $this->request->getVar('sort_order') ?? 'asc';

        // Allowed sort columns validation to prevent SQL injection
        $allowed_sorts = ['nim', 'nama_lengkap', 'tahun_masuk', 'status', 'jenis_kelamin'];
        if (!in_array($sort_by, $allowed_sorts)) {
            $sort_by = 'nim';
        }
        $sort_order = strtolower($sort_order) === 'desc' ? 'desc' : 'asc';

        $builder->orderBy($sort_by, $sort_order);

        // Pass data to view
        $data = [
            'title' => $title,
            'page' => 'mahasiswa',
            'mahasiswa' => $builder->paginate($per_page, 'mahasiswa'),
            'pager' => $this->mahasiswaModel->pager,
            'keyword' => $keyword,
            'jurusan_kode' => $jurusan_kode,
            'jurusan_info' => $jurusan_info,
            'prodi_info' => $prodi_info,
            'nama_prodi' => $nama_prodi,
            'jenjang' => $jenjang,
            'per_page' => $per_page,
            'back_url' => $back_url,
            'sort_by' => $sort_by,
            'sort_order' => $sort_order
        ];

        return view('admin/mahasiswa/index', $data);
    }

    public function create()
    {
        $nama_prodi = $this->request->getVar('prodi');
        $jenjang = $this->request->getVar('jenjang');
        $jurusan_kode = $this->request->getVar('jurusan');

        $prodi_info = null;
        $db = \Config\Database::connect();

        // Get specific prodi if context is provided
        if ($nama_prodi && $jenjang) {
            $prodi_info = $db->table('prodi')
                ->where('nama_prodi', $nama_prodi)
                ->where('jenjang', $jenjang)
                ->get()
                ->getRowArray();
        }

        // Build back URL
        $back_url = base_url('admin/mahasiswa');
        if ($nama_prodi && $jenjang && $jurusan_kode) {
            $back_url = base_url('admin/mahasiswa') . '?' . http_build_query([
                'prodi' => $nama_prodi,
                'jenjang' => $jenjang,
                'jurusan' => $jurusan_kode
            ]);
        }

        $data = [
            'title' => 'Tambah Mahasiswa',
            'page' => 'mahasiswa',
            'prodi' => $this->prodiModel->findAll(), // All prodi for fallback
            'prodi_info' => $prodi_info, // Specific prodi context
            'nama_prodi' => $nama_prodi,
            'jenjang' => $jenjang,
            'jurusan_kode' => $jurusan_kode,
            'back_url' => $back_url,
            'validation' => \Config\Services::validation()
        ];
        return view('admin/mahasiswa/form', $data);
    }

    public function store()
    {
        if (
            !$this->validate([
                'nim' => 'required|is_unique[tb_m_mahasiswa.nim]',
                'nama_lengkap' => 'required',
                'kode_prodi' => 'required',
                'email' => 'valid_email'
            ])
        ) {
            return redirect()->to('admin/mahasiswa/create')->withInput();
        }

        $this->mahasiswaModel->save([
            'nim' => $this->request->getPost('nim'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'kode_prodi' => $this->request->getPost('kode_prodi'),
            'tahun_masuk' => $this->request->getPost('tahun_masuk'),
            'status' => $this->request->getPost('status'),
            'tanggal_yudisium' => $this->request->getPost('tanggal_yudisium'),
            'nik' => $this->request->getPost('nik'),
            'semester_masuk' => $this->request->getPost('semester_masuk'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'email' => $this->request->getPost('email'),
            'no_hp' => $this->request->getPost('no_hp'),
        ]);

        return redirect()->to('admin/mahasiswa')->with('success', 'Data Mahasiswa berhasil ditambahkan.');
    }

    public function edit($nim)
    {
        $mahasiswa = $this->mahasiswaModel->find($nim);
        if (!$mahasiswa) {
            return redirect()->to('admin/mahasiswa')->with('error', 'Data tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Mahasiswa',
            'page' => 'mahasiswa',
            'mahasiswa' => $mahasiswa,
            'prodi' => $this->prodiModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('admin/mahasiswa/form', $data);
    }

    public function update($nim)
    {
        $oldMhs = $this->mahasiswaModel->find($nim);
        if (!$oldMhs) {
            return redirect()->to('admin/mahasiswa')->with('error', 'Data tidak ditemukan.');
        }

        // Check if NIM is changed (usually PK shouldn't change, but if allowed)
        // Here assuming NIM is PK and NOT editable or checked unique if changed
        // Since NIM is PK in model ($primaryKey='nim'), save() might insert if not careful or update if ID exists.
        // But $useAutoIncrement=false.
        // If updating PK, we should use 'id' if there was one, but here PK is NIM.
        // Usually PK edit is tricky. Let's assume NIM cannot be changed, or handle it properly.
        // For simplicity, disable NIM edit in View or handle it.
        // If user creates a new NIM, it's a new record.
        // Let's assume NIM is not editable.

        $this->mahasiswaModel->update($nim, [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'kode_prodi' => $this->request->getPost('kode_prodi'),
            'tahun_masuk' => $this->request->getPost('tahun_masuk'),
            'status' => $this->request->getPost('status'),
            'tanggal_yudisium' => $this->request->getPost('tanggal_yudisium'),
            'nik' => $this->request->getPost('nik'),
            'semester_masuk' => $this->request->getPost('semester_masuk'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'email' => $this->request->getPost('email'),
            'no_hp' => $this->request->getPost('no_hp'),
        ]);

        return redirect()->to('admin/mahasiswa')->with('success', 'Data Mahasiswa berhasil diperbarui.');
    }

    public function delete($nim)
    {
        $this->mahasiswaModel->delete($nim);
        return redirect()->to('admin/mahasiswa')->with('success', 'Data berhasil dihapus.');
    }

    public function import()
    {
        $nama_prodi = $this->request->getVar('prodi');
        $jenjang = $this->request->getVar('jenjang');
        $jurusan_kode = $this->request->getVar('jurusan');

        $db = \Config\Database::connect();
        $prodi_info = null;

        if ($nama_prodi && $jenjang) {
            $prodi_info = $db->table('prodi')
                ->where('nama_prodi', $nama_prodi)
                ->where('jenjang', $jenjang)
                ->get()
                ->getRowArray();
        }

        // Build back URL
        $back_url = base_url('admin/mahasiswa');
        if ($nama_prodi && $jenjang && $jurusan_kode) {
            $back_url = base_url('admin/mahasiswa') . '?' . http_build_query([
                'prodi' => $nama_prodi,
                'jenjang' => $jenjang,
                'jurusan' => $jurusan_kode
            ]);
        }

        $data = [
            'title' => 'Import Mahasiswa',
            'page' => 'mahasiswa',
            'prodi_info' => $prodi_info,
            'nama_prodi' => $nama_prodi,
            'jenjang' => $jenjang,
            'jurusan_kode' => $jurusan_kode,
            'back_url' => $back_url,
            'preview_data' => session()->getFlashdata('preview_data') ?? []
        ];
        return view('admin/mahasiswa/import', $data);
    }

    public function downloadTemplate()
    {
        $nama_prodi = $this->request->getVar('prodi');
        $jenjang = $this->request->getVar('jenjang');

        $db = \Config\Database::connect();
        $prodi_info = null;
        $kode_prodi = '';

        if ($nama_prodi && $jenjang) {
            $prodi_info = $db->table('prodi')
                ->where('nama_prodi', $nama_prodi)
                ->where('jenjang', $jenjang)
                ->get()
                ->getRowArray();
            if ($prodi_info) {
                $kode_prodi = $prodi_info['kode_prodi'];
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Row with Styling
        $headers = ['NIM', 'Nama Lengkap', 'Kode Prodi', 'Tahun Masuk', 'Status', 'NIK', 'Jenis Kelamin (L/P)', 'Email', 'No HP', 'Tanggal Yudisium (YYYY-MM-DD)'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Bold header
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');

        // Add example row with prodi code pre-filled
        $sheet->setCellValue('A2', '062030311001');
        $sheet->setCellValue('B2', 'Nama Mahasiswa');
        $sheet->setCellValue('C2', $kode_prodi ?: '[KODE_PRODI]');
        $sheet->setCellValue('D2', date('Y'));
        $sheet->setCellValue('E2', 'Aktif');
        $sheet->setCellValue('F2', '1234567890123456');
        $sheet->setCellValue('G2', 'L');
        $sheet->setCellValue('H2', 'email@example.com');
        $sheet->setCellValue('I2', '08123456789');
        $sheet->setCellValue('J2', '');

        // Info Sheet
        $infoSheet = $spreadsheet->createSheet();
        $infoSheet->setTitle('Info');
        $infoSheet->setCellValue('A1', 'PETUNJUK PENGISIAN TEMPLATE');
        $infoSheet->setCellValue('A3', 'Kolom Wajib: NIM, Nama Lengkap, Kode Prodi');
        $infoSheet->setCellValue('A4', 'Status: Aktif, Lulus, Cuti, Keluar, Drop Out, Non-Aktif');
        $infoSheet->setCellValue('A5', 'Jenis Kelamin: L (Laki-laki), P (Perempuan)');
        if ($prodi_info) {
            $infoSheet->setCellValue('A7', 'Kode Prodi: ' . $prodi_info['kode_prodi']);
            $infoSheet->setCellValue('A8', 'Nama Prodi: ' . $prodi_info['nama_prodi'] . ' (' . $prodi_info['jenjang'] . ')');
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Mahasiswa_' . ($kode_prodi ?: 'ALL') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function previewImport()
    {
        $file = $this->request->getFile('file_excel');
        $nama_prodi = $this->request->getPost('prodi');
        $jenjang = $this->request->getPost('jenjang');
        $jurusan_kode = $this->request->getPost('jurusan');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau tidak ditemukan.');
        }

        $spreadsheet = IOFactory::load($file->getTempName());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $db = \Config\Database::connect();
        $preview_data = [];

        foreach ($rows as $key => $row) {
            if ($key == 0)
                continue; // Skip header

            $nim = trim($row[0] ?? '');
            $nama = trim($row[1] ?? '');
            $kode_prodi = trim($row[2] ?? '');

            if (empty($nim) && empty($nama))
                continue; // Skip empty rows

            $valid = true;
            $error_msg = '';
            $status_mhs = 'Baru';

            // Validate NIM
            if (empty($nim)) {
                $valid = false;
                $error_msg = 'NIM kosong';
            }

            // Validate Nama
            if (empty($nama)) {
                $valid = false;
                $error_msg = $error_msg ? $error_msg . ', Nama kosong' : 'Nama kosong';
            }

            // Check if NIM exists
            $existing = $this->mahasiswaModel->find($nim);
            if ($existing) {
                $status_mhs = 'Update';
            }

            // Get Prodi Info
            $prodi_data = $db->table('prodi')->where('kode_prodi', $kode_prodi)->get()->getRowArray();
            $prodi_nama = $prodi_data['nama_prodi'] ?? 'Tidak Ditemukan';
            $prodi_jenjang = $prodi_data['jenjang'] ?? '-';

            if (!$prodi_data) {
                $valid = false;
                $error_msg = $error_msg ? $error_msg . ', Kode Prodi tidak valid' : 'Kode Prodi tidak valid';
            }

            $preview_data[] = [
                'nim' => $nim,
                'nama' => $nama,
                'kode_prodi' => $kode_prodi,
                'nama_prodi' => $prodi_nama,
                'jenjang' => $prodi_jenjang,
                'tahun_masuk' => $row[3] ?? date('Y'),
                'status' => $row[4] ?? 'Aktif',
                'nik' => $row[5] ?? '',
                'jenis_kelamin' => $row[6] ?? '',
                'email' => $row[7] ?? '',
                'no_hp' => $row[8] ?? '',
                'tanggal_yudisium' => $row[9] ?? '',
                'valid' => $valid,
                'error_msg' => $error_msg,
                'status_mhs' => $status_mhs
            ];
        }

        // Store preview in session and redirect back
        session()->setFlashdata('preview_data', $preview_data);

        $redirect_url = base_url('admin/mahasiswa/import');
        $params = [];
        if ($nama_prodi)
            $params['prodi'] = $nama_prodi;
        if ($jenjang)
            $params['jenjang'] = $jenjang;
        if ($jurusan_kode)
            $params['jurusan'] = $jurusan_kode;
        if (!empty($params)) {
            $redirect_url .= '?' . http_build_query($params);
        }

        return redirect()->to($redirect_url);
    }

    public function saveImport()
    {
        $bulk_data = json_decode($this->request->getPost('bulk_data'), true);
        $nama_prodi = $this->request->getPost('prodi');
        $jenjang = $this->request->getPost('jenjang');
        $jurusan_kode = $this->request->getPost('jurusan');

        if (empty($bulk_data)) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diimport.');
        }

        $successCount = 0;
        $updateCount = 0;

        foreach ($bulk_data as $row) {
            if (!$row['valid'])
                continue;

            $data = [
                'nim' => $row['nim'],
                'nama_lengkap' => $row['nama'],
                'kode_prodi' => $row['kode_prodi'],
                'tahun_masuk' => $row['tahun_masuk'],
                'status' => $row['status'],
                'nik' => $row['nik'],
                'jenis_kelamin' => $row['jenis_kelamin'],
                'email' => $row['email'],
                'no_hp' => $row['no_hp'],
                'tanggal_yudisium' => $row['tanggal_yudisium'] ?: null
            ];

            if ($row['status_mhs'] == 'Update') {
                $this->mahasiswaModel->update($row['nim'], $data);
                $updateCount++;
            } else {
                $this->mahasiswaModel->insert($data);
                $successCount++;
            }
        }

        $redirect_url = base_url('admin/mahasiswa');
        $params = [];
        if ($nama_prodi)
            $params['prodi'] = $nama_prodi;
        if ($jenjang)
            $params['jenjang'] = $jenjang;
        if ($jurusan_kode)
            $params['jurusan'] = $jurusan_kode;
        if (!empty($params)) {
            $redirect_url .= '?' . http_build_query($params);
        }

        return redirect()->to($redirect_url)->with('success', "Import berhasil. $successCount data baru, $updateCount data diupdate.");
    }

    public function processImport()
    {
        // Legacy method - redirect to preview
        return $this->previewImport();
    }

    public function export()
    {
        $mahasiswa = $this->mahasiswaModel
            ->join('prodi', 'prodi.kode_prodi = tb_m_mahasiswa.kode_prodi', 'left')
            ->select('tb_m_mahasiswa.*, prodi.nama_prodi')
            ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NIM');
        $sheet->setCellValue('B1', 'Nama Lengkap');
        $sheet->setCellValue('C1', 'Prodi');
        $sheet->setCellValue('D1', 'Tahun Masuk');
        $sheet->setCellValue('E1', 'Status');

        $row = 2;
        foreach ($mahasiswa as $m) {
            $sheet->setCellValue('A' . $row, $m['nim']);
            $sheet->setCellValue('B' . $row, $m['nama_lengkap']);
            $sheet->setCellValue('C' . $row, $m['nama_prodi']);
            $sheet->setCellValue('D' . $row, $m['tahun_masuk']);
            $sheet->setCellValue('E' . $row, $m['status']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_Mahasiswa_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function bulkDelete()
    {
        $selected_nims = $this->request->getPost('selected_nims');
        $nama_prodi = $this->request->getPost('prodi');
        $jenjang = $this->request->getPost('jenjang');
        $jurusan_kode = $this->request->getPost('jurusan');

        if (empty($selected_nims)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
        }

        $deleteCount = 0;
        foreach ($selected_nims as $nim) {
            if ($this->mahasiswaModel->find($nim)) {
                $this->mahasiswaModel->delete($nim);
                $deleteCount++;
            }
        }

        $redirect_url = base_url('admin/mahasiswa');
        $params = [];
        if ($nama_prodi)
            $params['prodi'] = $nama_prodi;
        if ($jenjang)
            $params['jenjang'] = $jenjang;
        if ($jurusan_kode)
            $params['jurusan'] = $jurusan_kode;
        if (!empty($params)) {
            $redirect_url .= '?' . http_build_query($params);
        }

        return redirect()->to($redirect_url)->with('success', "$deleteCount data mahasiswa berhasil dihapus.");
    }

    public function exportSelected()
    {
        $selected_nims_json = $this->request->getPost('selected_nims');
        $selected_nims = json_decode($selected_nims_json, true);

        if (empty($selected_nims)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk diexport.');
        }

        $mahasiswa = $this->mahasiswaModel
            ->join('prodi', 'prodi.kode_prodi = tb_m_mahasiswa.kode_prodi', 'left')
            ->select('tb_m_mahasiswa.*, prodi.nama_prodi, prodi.jenjang')
            ->whereIn('tb_m_mahasiswa.nim', $selected_nims)
            ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['NIM', 'Nama Lengkap', 'Prodi', 'Jenjang', 'Tahun Masuk', 'Status', 'NIK', 'Jenis Kelamin', 'Email', 'No HP', 'Tanggal Yudisium'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');

        $row = 2;
        foreach ($mahasiswa as $m) {
            $sheet->setCellValue('A' . $row, $m['nim']);
            $sheet->setCellValue('B' . $row, $m['nama_lengkap']);
            $sheet->setCellValue('C' . $row, $m['nama_prodi']);
            $sheet->setCellValue('D' . $row, $m['jenjang']);
            $sheet->setCellValue('E' . $row, $m['tahun_masuk']);
            $sheet->setCellValue('F' . $row, $m['status']);
            $sheet->setCellValue('G' . $row, $m['nik']);
            $sheet->setCellValue('H' . $row, $m['jenis_kelamin']);
            $sheet->setCellValue('I' . $row, $m['email']);
            $sheet->setCellValue('J' . $row, $m['no_hp']);
            $sheet->setCellValue('K' . $row, $m['tanggal_yudisium']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_Mahasiswa_Selected_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}

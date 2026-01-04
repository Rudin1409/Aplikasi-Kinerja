<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DosenModel;
use App\Models\ProdiModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DosenController extends BaseController
{
    protected $dosenModel;
    protected $prodiModel;

    public function __construct()
    {
        $this->dosenModel = new DosenModel();
        $this->prodiModel = new ProdiModel();
    }

    public function index()
    {
        $keyword = $this->request->getVar('keyword');
        $jurusan_kode = $this->request->getVar('jurusan');
        $nama_prodi = $this->request->getVar('prodi');
        $jenjang = $this->request->getVar('jenjang');
        $per_page = $this->request->getVar('per_page') ?? 20;

        $db = \Config\Database::connect();
        $builder = $this->dosenModel;

        $jurusan_info = null;
        $prodi_info = null;
        $prodi_list = [];

        // Get Prodi info if provided
        if ($nama_prodi && $jenjang) {
            $prodi_info = $db->table('prodi')
                ->where('nama_prodi', $nama_prodi)
                ->where('jenjang', $jenjang)
                ->get()->getRowArray();
            if ($prodi_info) {
                $builder->where('kode_prodi', $prodi_info['kode_prodi']);
                $jurusan_info = $db->table('jurusan')->where('id', $prodi_info['jurusan_id'])->get()->getRowArray();
                $jurusan_kode = $jurusan_info['kode_jurusan'] ?? null;
            }
        } elseif ($jurusan_kode) {
            $jurusan_info = $db->table('jurusan')->where('kode_jurusan', $jurusan_kode)->get()->getRowArray();
            if ($jurusan_info) {
                $prodi_list = $db->table('prodi')->where('jurusan_id', $jurusan_info['id'])->get()->getResultArray();
                if (!empty($prodi_list)) {
                    $prodi_codes = array_column($prodi_list, 'kode_prodi');
                    $builder->whereIn('kode_prodi', $prodi_codes);
                }
            }
        }

        // Search filter
        if ($keyword) {
            $builder->groupStart()
                ->like('nama_lengkap', $keyword)
                ->orLike('nidn', $keyword)
                ->groupEnd();
        }

        // Sorting
        $sort_by = $this->request->getVar('sort_by') ?? 'nama_lengkap';
        $sort_order = $this->request->getVar('sort_order') ?? 'asc';
        $allowed_sorts = ['nidn', 'nama_lengkap', 'kode_prodi', 'homebase', 'email'];
        if (!in_array($sort_by, $allowed_sorts)) {
            $sort_by = 'nama_lengkap';
        }
        $sort_order = strtolower($sort_order) === 'desc' ? 'desc' : 'asc';
        $builder->orderBy($sort_by, $sort_order);

        // Fetch all prodi for name mapping
        $all_prodi = $this->prodiModel->findAll();
        $prodi_map = [];
        foreach ($all_prodi as $p) {
            $prodi_map[$p['kode_prodi']] = $p['nama_prodi'];
        }

        // Dynamic title
        $title = 'Master Data Dosen';
        if ($prodi_info) {
            $jenjang_label = match ($prodi_info['jenjang']) {
                'DIII' => 'D3', 'DIV' => 'D4', default => $prodi_info['jenjang']
            };
            $title .= ' - ' . $jenjang_label . ' ' . $prodi_info['nama_prodi'];
        } elseif ($jurusan_info) {
            $title .= ' - ' . $jurusan_info['nama_jurusan'];
        }

        // Back URL
        $back_url = base_url('admin/dashboard');
        if ($nama_prodi && $jenjang && $jurusan_kode) {
            $back_url = base_url('admin/iku-prodi/' . $jurusan_kode . '/' . urlencode($nama_prodi) . '/' . $jenjang);
        } elseif ($jurusan_kode) {
            $back_url = base_url('admin/prodi-capaian/' . $jurusan_kode);
        }

        $data = [
            'title' => $title,
            'page' => 'dosen',
            'dosen' => $builder->paginate($per_page, 'dosen'),
            'pager' => $this->dosenModel->pager,
            'keyword' => $keyword,
            'jurusan_kode' => $jurusan_kode,
            'jurusan_info' => $jurusan_info,
            'prodi_info' => $prodi_info,
            'prodi_map' => $prodi_map,
            'nama_prodi' => $nama_prodi,
            'jenjang' => $jenjang,
            'per_page' => $per_page,
            'back_url' => $back_url,
            'sort_by' => $sort_by,
            'sort_order' => $sort_order
        ];

        return view('admin/dosen/index', $data);
    }

    public function create()
    {
        $nama_prodi = $this->request->getVar('prodi');
        $jenjang = $this->request->getVar('jenjang');
        $jurusan_kode = $this->request->getVar('jurusan');

        $prodi_info = null;
        $db = \Config\Database::connect();

        if ($nama_prodi && $jenjang) {
            $prodi_info = $db->table('prodi')
                ->where('nama_prodi', $nama_prodi)
                ->where('jenjang', $jenjang)
                ->get()->getRowArray();
        }

        $query_params = [];
        if ($nama_prodi)
            $query_params['prodi'] = $nama_prodi;
        if ($jenjang)
            $query_params['jenjang'] = $jenjang;
        if ($jurusan_kode)
            $query_params['jurusan'] = $jurusan_kode;
        $back_url = base_url('admin/dosen') . (!empty($query_params) ? '?' . http_build_query($query_params) : '');

        $data = [
            'title' => 'Tambah Dosen',
            'page' => 'dosen',
            'prodi' => $this->prodiModel->findAll(),
            'prodi_info' => $prodi_info,
            'nama_prodi' => $nama_prodi,
            'jenjang' => $jenjang,
            'jurusan_kode' => $jurusan_kode,
            'back_url' => $back_url,
            'validation' => \Config\Services::validation()
        ];
        return view('admin/dosen/form', $data);
    }

    public function store()
    {
        if (
            !$this->validate([
                'nidn' => 'required|is_unique[tb_m_dosen.nidn]',
                'nama_lengkap' => 'required',
                'kode_prodi' => 'required'
            ])
        ) {
            return redirect()->to('admin/dosen/create')->withInput();
        }

        $this->dosenModel->save([
            'nidn' => $this->request->getPost('nidn'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'kode_prodi' => $this->request->getPost('kode_prodi'),
            'homebase' => $this->request->getPost('homebase'),
            'gelar_depan' => $this->request->getPost('gelar_depan'),
            'gelar_belakang' => $this->request->getPost('gelar_belakang'),
            'email' => $this->request->getPost('email'),
            'no_hp' => $this->request->getPost('no_hp'),
        ]);

        $nama_prodi = $this->request->getPost('redirect_prodi');
        $jenjang = $this->request->getPost('redirect_jenjang');
        $jurusan_kode = $this->request->getPost('redirect_jurusan');

        $redirect_url = base_url('admin/dosen');
        $params = [];
        if ($nama_prodi)
            $params['prodi'] = $nama_prodi;
        if ($jenjang)
            $params['jenjang'] = $jenjang;
        if ($jurusan_kode)
            $params['jurusan'] = $jurusan_kode;
        if (!empty($params))
            $redirect_url .= '?' . http_build_query($params);

        return redirect()->to($redirect_url)->with('success', 'Data Dosen berhasil ditambahkan.');
    }

    public function edit($nidn)
    {
        $dosen = $this->dosenModel->find($nidn);
        if (!$dosen) {
            return redirect()->to('admin/dosen')->with('error', 'Data tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Dosen',
            'page' => 'dosen',
            'dosen' => $dosen,
            'prodi' => $this->prodiModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('admin/dosen/form', $data);
    }

    public function update($nidn)
    {
        $oldData = $this->dosenModel->find($nidn);
        if (!$oldData) {
            return redirect()->to('admin/dosen')->with('error', 'Data tidak ditemukan.');
        }

        $this->dosenModel->update($nidn, [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'kode_prodi' => $this->request->getPost('kode_prodi'),
            'homebase' => $this->request->getPost('homebase'),
            'gelar_depan' => $this->request->getPost('gelar_depan'),
            'gelar_belakang' => $this->request->getPost('gelar_belakang'),
            'email' => $this->request->getPost('email'),
            'no_hp' => $this->request->getPost('no_hp'),
        ]);

        return redirect()->to('admin/dosen')->with('success', 'Data Dosen berhasil diperbarui.');
    }

    public function delete($nidn)
    {
        $this->dosenModel->delete($nidn);
        return redirect()->to('admin/dosen')->with('success', 'Data berhasil dihapus.');
    }

    public function bulkDelete()
    {
        $selected_nidns = $this->request->getPost('selected_nidns');
        $nama_prodi = $this->request->getPost('prodi');
        $jenjang = $this->request->getPost('jenjang');
        $jurusan_kode = $this->request->getPost('jurusan');

        if (empty($selected_nidns)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
        }

        $deleteCount = 0;
        foreach ($selected_nidns as $nidn) {
            if ($this->dosenModel->find($nidn)) {
                $this->dosenModel->delete($nidn);
                $deleteCount++;
            }
        }

        $redirect_url = base_url('admin/dosen');
        $params = [];
        if ($nama_prodi)
            $params['prodi'] = $nama_prodi;
        if ($jenjang)
            $params['jenjang'] = $jenjang;
        if ($jurusan_kode)
            $params['jurusan'] = $jurusan_kode;
        if (!empty($params))
            $redirect_url .= '?' . http_build_query($params);

        return redirect()->to($redirect_url)->with('success', "$deleteCount data dosen berhasil dihapus.");
    }

    public function import()
    {
        $nama_prodi = $this->request->getVar('prodi');
        $jenjang = $this->request->getVar('jenjang');
        $jurusan_kode = $this->request->getVar('jurusan');

        $prodi_info = null;
        $db = \Config\Database::connect();
        if ($nama_prodi && $jenjang) {
            $prodi_info = $db->table('prodi')->where('nama_prodi', $nama_prodi)->where('jenjang', $jenjang)->get()->getRowArray();
        }

        $query_params = [];
        if ($nama_prodi)
            $query_params['prodi'] = $nama_prodi;
        if ($jenjang)
            $query_params['jenjang'] = $jenjang;
        if ($jurusan_kode)
            $query_params['jurusan'] = $jurusan_kode;
        $back_url = base_url('admin/dosen') . (!empty($query_params) ? '?' . http_build_query($query_params) : '');

        $data = [
            'title' => 'Import Dosen',
            'page' => 'dosen',
            'prodi_info' => $prodi_info,
            'nama_prodi' => $nama_prodi,
            'jenjang' => $jenjang,
            'jurusan_kode' => $jurusan_kode,
            'back_url' => $back_url,
            'preview_data' => session()->getFlashdata('preview_data')
        ];
        return view('admin/dosen/import', $data);
    }

    public function downloadTemplate()
    {
        $nama_prodi = $this->request->getVar('prodi');
        $jenjang = $this->request->getVar('jenjang');

        $kode_prodi_example = 'KODE_PRODI';
        if ($nama_prodi && $jenjang) {
            $db = \Config\Database::connect();
            $prodi = $db->table('prodi')->where('nama_prodi', $nama_prodi)->where('jenjang', $jenjang)->get()->getRowArray();
            if ($prodi)
                $kode_prodi_example = $prodi['kode_prodi'];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Dosen');

        $headers = ['NIDN', 'Nama Lengkap', 'Kode Prodi', 'Homebase', 'Gelar Depan', 'Gelar Belakang', 'Email', 'No HP'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');

        // Example row
        $sheet->setCellValue('A2', '0123456789');
        $sheet->setCellValue('B2', 'Nama Dosen Contoh');
        $sheet->setCellValue('C2', $kode_prodi_example);
        $sheet->setCellValue('D2', 'Polsri');
        $sheet->setCellValue('E2', 'Dr.');
        $sheet->setCellValue('F2', 'M.T.');
        $sheet->setCellValue('G2', 'dosen@example.com');
        $sheet->setCellValue('H2', '08123456789');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Dosen.xlsx';

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
            return redirect()->back()->with('error', 'File tidak valid.');
        }

        $spreadsheet = IOFactory::load($file->getTempName());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $db = \Config\Database::connect();
        $preview_data = [];

        foreach ($rows as $key => $row) {
            if ($key == 0)
                continue;
            if (empty(array_filter($row)))
                continue;

            $nidn = trim($row[0] ?? '');
            $nama = trim($row[1] ?? '');
            $kode_prodi = trim($row[2] ?? '');

            $valid = true;
            $error_msg = '';

            if (empty($nidn)) {
                $valid = false;
                $error_msg = 'NIDN kosong';
            } elseif (empty($nama)) {
                $valid = false;
                $error_msg = 'Nama kosong';
            } elseif (empty($kode_prodi)) {
                $valid = false;
                $error_msg = 'Kode Prodi kosong';
            }

            $prodi = $db->table('prodi')->where('kode_prodi', $kode_prodi)->get()->getRowArray();
            if (!$prodi && $valid) {
                $valid = false;
                $error_msg = 'Prodi tidak ditemukan';
            }

            $existing = $this->dosenModel->find($nidn);
            $status_dosen = $existing ? 'Update' : 'Baru';

            $preview_data[] = [
                'nidn' => $nidn,
                'nama' => $nama,
                'kode_prodi' => $kode_prodi,
                'nama_prodi' => $prodi['nama_prodi'] ?? '-',
                'homebase' => trim($row[3] ?? ''),
                'gelar_depan' => trim($row[4] ?? ''),
                'gelar_belakang' => trim($row[5] ?? ''),
                'email' => trim($row[6] ?? ''),
                'no_hp' => trim($row[7] ?? ''),
                'valid' => $valid,
                'error_msg' => $error_msg,
                'status_dosen' => $status_dosen
            ];
        }

        $redirect_url = base_url('admin/dosen/import');
        $params = [];
        if ($nama_prodi)
            $params['prodi'] = $nama_prodi;
        if ($jenjang)
            $params['jenjang'] = $jenjang;
        if ($jurusan_kode)
            $params['jurusan'] = $jurusan_kode;
        if (!empty($params))
            $redirect_url .= '?' . http_build_query($params);

        return redirect()->to($redirect_url)->with('preview_data', $preview_data);
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
                'nidn' => $row['nidn'],
                'nama_lengkap' => $row['nama'],
                'kode_prodi' => $row['kode_prodi'],
                'homebase' => $row['homebase'],
                'gelar_depan' => $row['gelar_depan'],
                'gelar_belakang' => $row['gelar_belakang'],
                'email' => $row['email'],
                'no_hp' => $row['no_hp']
            ];

            if ($row['status_dosen'] == 'Update') {
                $this->dosenModel->update($row['nidn'], $data);
                $updateCount++;
            } else {
                $this->dosenModel->insert($data);
                $successCount++;
            }
        }

        $redirect_url = base_url('admin/dosen');
        $params = [];
        if ($nama_prodi)
            $params['prodi'] = $nama_prodi;
        if ($jenjang)
            $params['jenjang'] = $jenjang;
        if ($jurusan_kode)
            $params['jurusan'] = $jurusan_kode;
        if (!empty($params))
            $redirect_url .= '?' . http_build_query($params);

        return redirect()->to($redirect_url)->with('success', "Import berhasil. $successCount data baru, $updateCount data diupdate.");
    }

    public function processImport()
    {
        return $this->previewImport();
    }

    public function export()
    {
        $dosen = $this->dosenModel
            ->join('prodi', 'prodi.kode_prodi = tb_m_dosen.kode_prodi', 'left')
            ->select('tb_m_dosen.*, prodi.nama_prodi')
            ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['NIDN', 'Nama Lengkap', 'Prodi', 'Homebase', 'Gelar Depan', 'Gelar Belakang', 'Email', 'No HP'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $row = 2;
        foreach ($dosen as $d) {
            $sheet->setCellValue('A' . $row, $d['nidn']);
            $sheet->setCellValue('B' . $row, $d['nama_lengkap']);
            $sheet->setCellValue('C' . $row, $d['nama_prodi']);
            $sheet->setCellValue('D' . $row, $d['homebase']);
            $sheet->setCellValue('E' . $row, $d['gelar_depan']);
            $sheet->setCellValue('F' . $row, $d['gelar_belakang']);
            $sheet->setCellValue('G' . $row, $d['email']);
            $sheet->setCellValue('H' . $row, $d['no_hp']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_Dosen_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function exportSelected()
    {
        $selected_nidns_json = $this->request->getPost('selected_nidns');
        $selected_nidns = json_decode($selected_nidns_json, true);

        if (empty($selected_nidns)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk diexport.');
        }

        $dosen = $this->dosenModel
            ->join('prodi', 'prodi.kode_prodi = tb_m_dosen.kode_prodi', 'left')
            ->select('tb_m_dosen.*, prodi.nama_prodi')
            ->whereIn('tb_m_dosen.nidn', $selected_nidns)
            ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['NIDN', 'Nama Lengkap', 'Prodi', 'Homebase', 'Gelar Depan', 'Gelar Belakang', 'Email', 'No HP'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');

        $row = 2;
        foreach ($dosen as $d) {
            $sheet->setCellValue('A' . $row, $d['nidn']);
            $sheet->setCellValue('B' . $row, $d['nama_lengkap']);
            $sheet->setCellValue('C' . $row, $d['nama_prodi']);
            $sheet->setCellValue('D' . $row, $d['homebase']);
            $sheet->setCellValue('E' . $row, $d['gelar_depan']);
            $sheet->setCellValue('F' . $row, $d['gelar_belakang']);
            $sheet->setCellValue('G' . $row, $d['email']);
            $sheet->setCellValue('H' . $row, $d['no_hp']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_Dosen_Selected_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}

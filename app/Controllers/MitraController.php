<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MitraModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MitraController extends BaseController
{
    protected $mitraModel;

    public function __construct()
    {
        $this->mitraModel = new MitraModel();
    }

    public function index()
    {
        $keyword = $this->request->getVar('keyword');
        $jurusan_kode = $this->request->getVar('jurusan');
        $nama_prodi = $this->request->getVar('prodi');
        $jenjang = $this->request->getVar('jenjang');
        $per_page = $this->request->getVar('per_page') ?? 20;

        $db = \Config\Database::connect();

        // Get jurusan info if provided
        $jurusan_info = null;
        $prodi_info = null;

        if ($nama_prodi && $jenjang) {
            $prodi_info = $db->table('prodi')
                ->where('nama_prodi', $nama_prodi)
                ->where('jenjang', $jenjang)
                ->get()->getRowArray();
            if ($prodi_info) {
                $jurusan_info = $db->table('jurusan')->where('id', $prodi_info['jurusan_id'])->get()->getRowArray();
                $jurusan_kode = $jurusan_info['kode_jurusan'] ?? null;
            }
        } elseif ($jurusan_kode) {
            $jurusan_info = $db->table('jurusan')->where('kode_jurusan', $jurusan_kode)->get()->getRowArray();
        }

        // Build query
        $builder = $this->mitraModel;

        // Search filter
        if ($keyword) {
            $builder = $builder->groupStart()
                ->like('nama_mitra', $keyword)
                ->orLike('bidang_usaha', $keyword)
                ->groupEnd();
        }

        // Sorting
        $sort_by = $this->request->getVar('sort_by') ?? 'nama_mitra';
        $sort_order = $this->request->getVar('sort_order') ?? 'asc';
        $allowed_sorts = ['nama_mitra', 'bidang_usaha', 'kota', 'provinsi', 'jenis_kerjasama'];
        if (!in_array($sort_by, $allowed_sorts)) {
            $sort_by = 'nama_mitra';
        }
        $sort_order = strtolower($sort_order) === 'desc' ? 'desc' : 'asc';
        $builder->orderBy($sort_by, $sort_order);

        // Dynamic title
        $title = 'Master Data Mitra';
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
            'page' => 'mitra',
            'mitra' => $builder->paginate($per_page, 'mitra'),
            'pager' => $this->mitraModel->pager,
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

        return view('admin/mitra/index', $data);
    }

    public function create()
    {
        $nama_prodi = $this->request->getVar('prodi');
        $jenjang = $this->request->getVar('jenjang');
        $jurusan_kode = $this->request->getVar('jurusan');

        $query_params = [];
        if ($nama_prodi)
            $query_params['prodi'] = $nama_prodi;
        if ($jenjang)
            $query_params['jenjang'] = $jenjang;
        if ($jurusan_kode)
            $query_params['jurusan'] = $jurusan_kode;
        $back_url = base_url('admin/mitra') . (!empty($query_params) ? '?' . http_build_query($query_params) : '');

        $data = [
            'title' => 'Tambah Mitra',
            'page' => 'mitra',
            'nama_prodi' => $nama_prodi,
            'jenjang' => $jenjang,
            'jurusan_kode' => $jurusan_kode,
            'back_url' => $back_url,
            'validation' => \Config\Services::validation()
        ];
        return view('admin/mitra/form', $data);
    }

    public function store()
    {
        if (
            !$this->validate([
                'nama_mitra' => 'required',
                'bidang_usaha' => 'required'
            ])
        ) {
            return redirect()->to('admin/mitra/create')->withInput();
        }

        $this->mitraModel->save([
            'nama_mitra' => $this->request->getPost('nama_mitra'),
            'bidang_usaha' => $this->request->getPost('bidang_usaha'),
            'alamat' => $this->request->getPost('alamat'),
            'kota' => $this->request->getPost('kota'),
            'provinsi' => $this->request->getPost('provinsi'),
            'penanggung_jawab' => $this->request->getPost('penanggung_jawab'),
            'jabatan_penanggung_jawab' => $this->request->getPost('jabatan_penanggung_jawab'),
            'no_telp' => $this->request->getPost('no_telp'),
            'email' => $this->request->getPost('email'),
            'jenis_kerjasama' => $this->request->getPost('jenis_kerjasama'),
        ]);

        $nama_prodi = $this->request->getPost('redirect_prodi');
        $jenjang = $this->request->getPost('redirect_jenjang');
        $jurusan_kode = $this->request->getPost('redirect_jurusan');

        $redirect_url = base_url('admin/mitra');
        $params = [];
        if ($nama_prodi)
            $params['prodi'] = $nama_prodi;
        if ($jenjang)
            $params['jenjang'] = $jenjang;
        if ($jurusan_kode)
            $params['jurusan'] = $jurusan_kode;
        if (!empty($params))
            $redirect_url .= '?' . http_build_query($params);

        return redirect()->to($redirect_url)->with('success', 'Data Mitra berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $mitra = $this->mitraModel->find($id);
        if (!$mitra) {
            return redirect()->to('admin/mitra')->with('error', 'Data tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Mitra',
            'page' => 'mitra',
            'mitra' => $mitra,
            'validation' => \Config\Services::validation()
        ];
        return view('admin/mitra/form', $data);
    }

    public function update($id)
    {
        $old = $this->mitraModel->find($id);
        if (!$old) {
            return redirect()->to('admin/mitra')->with('error', 'Data tidak ditemukan.');
        }

        $this->mitraModel->update($id, [
            'nama_mitra' => $this->request->getPost('nama_mitra'),
            'bidang_usaha' => $this->request->getPost('bidang_usaha'),
            'alamat' => $this->request->getPost('alamat'),
            'kota' => $this->request->getPost('kota'),
            'provinsi' => $this->request->getPost('provinsi'),
            'penanggung_jawab' => $this->request->getPost('penanggung_jawab'),
            'jabatan_penanggung_jawab' => $this->request->getPost('jabatan_penanggung_jawab'),
            'no_telp' => $this->request->getPost('no_telp'),
            'email' => $this->request->getPost('email'),
            'jenis_kerjasama' => $this->request->getPost('jenis_kerjasama'),
        ]);

        return redirect()->to('admin/mitra')->with('success', 'Data Mitra berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->mitraModel->delete($id);
        return redirect()->to('admin/mitra')->with('success', 'Data berhasil dihapus.');
    }

    public function bulkDelete()
    {
        $selected_ids = $this->request->getPost('selected_ids');
        $nama_prodi = $this->request->getPost('prodi');
        $jenjang = $this->request->getPost('jenjang');
        $jurusan_kode = $this->request->getPost('jurusan');

        if (empty($selected_ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
        }

        $deleteCount = 0;
        foreach ($selected_ids as $id) {
            if ($this->mitraModel->find($id)) {
                $this->mitraModel->delete($id);
                $deleteCount++;
            }
        }

        $redirect_url = base_url('admin/mitra');
        $params = [];
        if ($nama_prodi)
            $params['prodi'] = $nama_prodi;
        if ($jenjang)
            $params['jenjang'] = $jenjang;
        if ($jurusan_kode)
            $params['jurusan'] = $jurusan_kode;
        if (!empty($params))
            $redirect_url .= '?' . http_build_query($params);

        return redirect()->to($redirect_url)->with('success', "$deleteCount data mitra berhasil dihapus.");
    }

    public function import()
    {
        $nama_prodi = $this->request->getVar('prodi');
        $jenjang = $this->request->getVar('jenjang');
        $jurusan_kode = $this->request->getVar('jurusan');

        $query_params = [];
        if ($nama_prodi)
            $query_params['prodi'] = $nama_prodi;
        if ($jenjang)
            $query_params['jenjang'] = $jenjang;
        if ($jurusan_kode)
            $query_params['jurusan'] = $jurusan_kode;
        $back_url = base_url('admin/mitra') . (!empty($query_params) ? '?' . http_build_query($query_params) : '');

        $data = [
            'title' => 'Import Mitra',
            'page' => 'mitra',
            'nama_prodi' => $nama_prodi,
            'jenjang' => $jenjang,
            'jurusan_kode' => $jurusan_kode,
            'back_url' => $back_url,
            'preview_data' => session()->getFlashdata('preview_data')
        ];
        return view('admin/mitra/import', $data);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Mitra');

        $headers = ['Nama Mitra', 'Bidang Usaha', 'Alamat', 'Kota', 'Provinsi', 'Penanggung Jawab', 'Jabatan PJ', 'No Telp', 'Email', 'Jenis Kerjasama'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');

        // Example row
        $sheet->setCellValue('A2', 'PT Contoh Industries');
        $sheet->setCellValue('B2', 'Manufaktur');
        $sheet->setCellValue('C2', 'Jl. Industri No. 1');
        $sheet->setCellValue('D2', 'Palembang');
        $sheet->setCellValue('E2', 'Sumatera Selatan');
        $sheet->setCellValue('F2', 'Budi Santoso');
        $sheet->setCellValue('G2', 'Direktur');
        $sheet->setCellValue('H2', '0711-123456');
        $sheet->setCellValue('I2', 'info@contoh.com');
        $sheet->setCellValue('J2', 'Kerjasama Industri');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Mitra.xlsx';

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

        $preview_data = [];

        foreach ($rows as $key => $row) {
            if ($key == 0)
                continue;
            if (empty(array_filter($row)))
                continue;

            $nama = trim($row[0] ?? '');
            $bidang = trim($row[1] ?? '');

            $valid = true;
            $error_msg = '';

            if (empty($nama)) {
                $valid = false;
                $error_msg = 'Nama kosong';
            } elseif (empty($bidang)) {
                $valid = false;
                $error_msg = 'Bidang usaha kosong';
            }

            $preview_data[] = [
                'nama_mitra' => $nama,
                'bidang_usaha' => $bidang,
                'alamat' => trim($row[2] ?? ''),
                'kota' => trim($row[3] ?? ''),
                'provinsi' => trim($row[4] ?? ''),
                'penanggung_jawab' => trim($row[5] ?? ''),
                'jabatan_penanggung_jawab' => trim($row[6] ?? ''),
                'no_telp' => trim($row[7] ?? ''),
                'email' => trim($row[8] ?? ''),
                'jenis_kerjasama' => trim($row[9] ?? ''),
                'valid' => $valid,
                'error_msg' => $error_msg
            ];
        }

        $redirect_url = base_url('admin/mitra/import');
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

        foreach ($bulk_data as $row) {
            if (!$row['valid'])
                continue;

            $this->mitraModel->insert([
                'nama_mitra' => $row['nama_mitra'],
                'bidang_usaha' => $row['bidang_usaha'],
                'alamat' => $row['alamat'],
                'kota' => $row['kota'],
                'provinsi' => $row['provinsi'],
                'penanggung_jawab' => $row['penanggung_jawab'],
                'jabatan_penanggung_jawab' => $row['jabatan_penanggung_jawab'],
                'no_telp' => $row['no_telp'],
                'email' => $row['email'],
                'jenis_kerjasama' => $row['jenis_kerjasama']
            ]);
            $successCount++;
        }

        $redirect_url = base_url('admin/mitra');
        $params = [];
        if ($nama_prodi)
            $params['prodi'] = $nama_prodi;
        if ($jenjang)
            $params['jenjang'] = $jenjang;
        if ($jurusan_kode)
            $params['jurusan'] = $jurusan_kode;
        if (!empty($params))
            $redirect_url .= '?' . http_build_query($params);

        return redirect()->to($redirect_url)->with('success', "Import berhasil. $successCount data ditambahkan.");
    }

    public function processImport()
    {
        return $this->previewImport();
    }

    public function export()
    {
        $mitra = $this->mitraModel->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Nama Mitra', 'Bidang Usaha', 'Alamat', 'Kota', 'Provinsi', 'Penanggung Jawab', 'Jabatan PJ', 'No Telp', 'Email', 'Jenis Kerjasama'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        $row = 2;
        foreach ($mitra as $m) {
            $sheet->setCellValue('A' . $row, $m['nama_mitra']);
            $sheet->setCellValue('B' . $row, $m['bidang_usaha']);
            $sheet->setCellValue('C' . $row, $m['alamat']);
            $sheet->setCellValue('D' . $row, $m['kota']);
            $sheet->setCellValue('E' . $row, $m['provinsi']);
            $sheet->setCellValue('F' . $row, $m['penanggung_jawab']);
            $sheet->setCellValue('G' . $row, $m['jabatan_penanggung_jawab']);
            $sheet->setCellValue('H' . $row, $m['no_telp']);
            $sheet->setCellValue('I' . $row, $m['email']);
            $sheet->setCellValue('J' . $row, $m['jenis_kerjasama']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_Mitra_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function exportSelected()
    {
        $selected_ids_json = $this->request->getPost('selected_ids');
        $selected_ids = json_decode($selected_ids_json, true);

        if (empty($selected_ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk diexport.');
        }

        $mitra = $this->mitraModel->whereIn('id', $selected_ids)->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Nama Mitra', 'Bidang Usaha', 'Alamat', 'Kota', 'Provinsi', 'Penanggung Jawab', 'Jabatan PJ', 'No Telp', 'Email', 'Jenis Kerjasama'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');

        $row = 2;
        foreach ($mitra as $m) {
            $sheet->setCellValue('A' . $row, $m['nama_mitra']);
            $sheet->setCellValue('B' . $row, $m['bidang_usaha']);
            $sheet->setCellValue('C' . $row, $m['alamat']);
            $sheet->setCellValue('D' . $row, $m['kota']);
            $sheet->setCellValue('E' . $row, $m['provinsi']);
            $sheet->setCellValue('F' . $row, $m['penanggung_jawab']);
            $sheet->setCellValue('G' . $row, $m['jabatan_penanggung_jawab']);
            $sheet->setCellValue('H' . $row, $m['no_telp']);
            $sheet->setCellValue('I' . $row, $m['email']);
            $sheet->setCellValue('J' . $row, $m['jenis_kerjasama']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_Mitra_Selected_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}

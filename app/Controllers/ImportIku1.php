<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MahasiswaModel;
use App\Models\Iku1Model;
use App\Models\ProdiModel;
use App\Models\TriwulanModel; // Assuming this model exists or we use QB, but better create/use it. 
// If TriwulanModel doesn't exist, I'll use direct DB builder or create it. The user didn't ask for it but the table exists.
// I'll stick to using builder if needed or just assume existence if safe. 
// Actually, to be safe, I will define a basic usage or use `db->table`.
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportIku1 extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        // Get Active Triwulan
        $active_triwulan = $db->table('triwulan')->where('status', 'Aktif')->get()->getRowArray();
        $active_triwulan_id = $active_triwulan['id'] ?? null;

        // Get Triwulan list
        $triwulan = $db->table('triwulan')->select('*')->get()->getResultArray();

        // Capture return_url from query string
        $return_url = $this->request->getGet('return_url');

        $data = [
            'title' => 'Import IKU 1 (Angka Efisiensi Edukasi)',
            'page' => 'iku',
            'triwulan_list' => $triwulan,
            'active_triwulan_id' => $active_triwulan_id,
            'return_url' => $return_url
        ];

        return view('admin/iku1/form_import', $data);
    }

    public function preview()
    {
        $id_triwulan = $this->request->getPost('id_triwulan');
        $file = $this->request->getFile('file_excel');
        $return_url = $this->request->getPost('return_url');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', $file->getErrorString());
        }

        if (!$id_triwulan) {
            return redirect()->back()->with('error', 'Triwulan harus dipilih.');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file Excel. Pastikan format benar.');
        }

        $mahasiswaModel = new MahasiswaModel();
        $prodiModel = new ProdiModel();
        $db = \Config\Database::connect();

        $preview_data = [];

        // AUTH & SECURITY DATA
        $role = session('role');
        $relasi_kode = session('relasi_kode');
        $jurusan_id_user = null;

        if ($role == 'jurusan') {
            $j = $db->table('jurusan')->where('kode_jurusan', $relasi_kode)->get()->getRowArray();
            $jurusan_id_user = $j['id'] ?? null;
        }

        foreach ($rows as $key => $row) {
            if ($key === 0)
                continue; // Header

            $nim = trim($row[0] ?? '');
            $nama = trim($row[1] ?? '');
            $kode_prodi = trim($row[2] ?? '');
            $tahun_masuk = trim($row[3] ?? '');
            $tanggal_yudisium = trim($row[4] ?? '');

            if (empty($nim) || empty($nama))
                continue;

            // INIT VALIDATION VARIABLES
            $is_valid = true;
            $error_msg = '';

            // SECURITY CEK
            if ($role == 'jurusan') {
                $prodi_check = $prodiModel->where('kode_prodi', $kode_prodi)->first();
                if (!$prodi_check || $prodi_check['jurusan_id'] != $jurusan_id_user) {
                    $is_valid = false;
                    $error_msg = 'Akses Ditolak: Prodi ini bukan di bawah Jurusan Anda.';
                }
            } elseif ($role == 'prodi') {
                if ($kode_prodi != $relasi_kode) {
                    $is_valid = false;
                    $error_msg = 'Akses Ditolak: Anda hanya boleh import data Prodi Anda sendiri.';
                }
            }

            // 1. Cek User Status
            $existing = $mahasiswaModel->where('nim', $nim)->first();
            $status_mhs = $existing ? 'Lama' : 'Baru';

            // 2. Cek Prodi & Jenjang
            $prodi = $prodiModel->where('kode_prodi', $kode_prodi)->first();
            $jenjang = $prodi ? $prodi['jenjang'] : 'Unknown';
            $nama_prodi = $prodi ? $prodi['nama_prodi'] : 'Unknown';

            if (!$prodi && $is_valid) {
                $is_valid = false;
                $error_msg = 'Kode Prodi tidak ditemukan di Database.';
            }

            // 3. Logic IKU 1 (AEE)
            $tgl_yudisium_time = strtotime($tanggal_yudisium);
            if (!$tgl_yudisium_time) {
                $masa_studi_text = 'Invalid Date';
                $masa_studi_bulan = 0;
                if ($is_valid) {
                    $is_valid = false;
                    $error_msg = 'Format Tanggal Yudisium Salah.';
                }
                $status_kelulusan = 'Error';
            } else {
                $thn_yudisium = date('Y', $tgl_yudisium_time);
                $lama_studi_tahun = (int) $thn_yudisium - (int) $tahun_masuk;
                $masa_studi_bulan = $lama_studi_tahun * 12;
                $masa_studi_text = "$lama_studi_tahun Tahun";

                $max_tahun = (stripos($jenjang, 'D3') !== false || stripos($jenjang, 'DIII') !== false) ? 3 : 4;
                $status_kelulusan = ($lama_studi_tahun <= $max_tahun) ? 'Tepat Waktu' : 'Terlambat';
            }

            $preview_data[] = [
                'nim' => $nim,
                'nama' => $nama,
                'kode_prodi' => $kode_prodi,
                'nama_prodi' => $nama_prodi,
                'status_mhs' => $status_mhs,
                'tahun_masuk' => $tahun_masuk,
                'tanggal_yudisium' => $tanggal_yudisium,
                'masa_studi_text' => $masa_studi_text,
                'masa_studi_bulan' => $masa_studi_bulan,
                'status_kelulusan' => $status_kelulusan,
                'jenjang' => $jenjang,
                'valid' => $is_valid,
                'error_msg' => $error_msg
            ];
        }

        // Return View with Preview Data
        $triwulan = $db->table('triwulan')->select('*')->get()->getResultArray();

        $data = [
            'title' => 'Preview Import IKU 1',
            'page' => 'iku',
            'triwulan_list' => $triwulan,
            'active_triwulan_id' => $id_triwulan,
            'preview_data' => $preview_data,
            'id_triwulan_selected' => $id_triwulan,
            'return_url' => $return_url
        ];

        return view('admin/iku1/form_import', $data);
    }

    public function save_data()
    {
        $json_data = $this->request->getPost('bulk_data');
        $id_triwulan = $this->request->getPost('id_triwulan');
        $return_url = $this->request->getPost('return_url'); // Perlu tambahkan input hidden di form_import step 2

        if (!$json_data || !$id_triwulan) {
            return redirect()->back()->with('error', 'Data tidak valid.');
        }

        $rows = json_decode($json_data, true);
        if (empty($rows)) {
            return redirect()->back()->with('error', 'Data kosong.');
        }

        $mahasiswaModel = new MahasiswaModel();
        $iku1Model = new Iku1Model();

        $count = 0;
        $new_mhs = 0;

        foreach ($rows as $row) {
            if (!$row['valid'])
                continue;

            if ($row['status_mhs'] == 'Baru') {
                if (!$mahasiswaModel->where('nim', $row['nim'])->first()) {
                    $mahasiswaModel->insert([
                        'nim' => $row['nim'],
                        'nama_lengkap' => $row['nama'],
                        'kode_prodi' => $row['kode_prodi'],
                        'tahun_masuk' => $row['tahun_masuk'],
                        'status' => 'Lulus'
                    ]);
                    $new_mhs++;
                }
            }

            $iku1Model->insert([
                'nim' => $row['nim'],
                'id_triwulan' => $id_triwulan,
                'tanggal_yudisium' => $row['tanggal_yudisium'],
                'masa_studi_bulan' => $row['masa_studi_bulan'],
                'status_kelulusan' => $row['status_kelulusan']
            ]);
            $count++;
        }

        $msg = "Berhasil disave! $count Data Transaksi, $new_mhs Mahasiswa Baru ditambahkan.";

        if (!empty($return_url)) {
            return redirect()->to(urldecode($return_url))->with('success', $msg);
        }

        return redirect()->to('admin/iku1/dashboard')->with('success', $msg);
    }
    public function download_template()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Set Headers
        $headers = [
            'A1' => 'NO',
            'B1' => 'NIM',
            'C1' => 'NAMA MAHASISWA',
            'D1' => 'KODE PRODI',
            'E1' => 'TAHUN MASUK',
            'F1' => 'TANGGAL YUDISIUM',
            'G1' => 'STATUS KELULUSAN'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
        }

        // 2. Sample Data (Optional, for easy copy-paste)
        $sheet->setCellValue('A2', 1);
        $sheet->setCellValue('B2', '061930320658'); // String to keep leading zero
        $sheet->setCellValue('C2', 'CONTOH NAMA');
        $sheet->setCellValue('D2', 'A01');
        $sheet->setCellValue('E2', '2019');
        $sheet->setCellValue('F2', '2023-08-25'); // Format YYYY-MM-DD
        $sheet->setCellValue('G2', 'Lulus'); // Just description

        // 3. Auto Size Columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 4. Output as Download
        $filename = 'template_import_iku_1.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Data IKU 1 (Lulusan) ke Excel
     */
    public function export()
    {
        $db = \Config\Database::connect();
        $kode_prodi = $this->request->getGet('kode_prodi');
        $id_triwulan = $this->request->getGet('id_triwulan');

        // Get Prodi Info
        $prodi_info = $db->table('prodi')->where('kode_prodi', $kode_prodi)->get()->getRowArray();
        $nama_prodi = $prodi_info['nama_prodi'] ?? 'Semua Prodi';
        $jenjang = $prodi_info['jenjang'] ?? '-';

        // Get Triwulan Info
        $triwulan_info = $db->table('triwulan')->where('id', $id_triwulan)->get()->getRowArray();
        $nama_triwulan = $triwulan_info['nama_triwulan'] ?? 'Semua Triwulan';

        // Query Data Lulusan
        $builder = $db->table('tb_iku_1_lulusan l')
            ->select('l.nim, l.tanggal_yudisium, l.status_kelulusan, m.nama_lengkap, m.tahun_masuk, m.kode_prodi')
            ->join('tb_m_mahasiswa m', 'm.nim = l.nim', 'left');

        if (!empty($kode_prodi)) {
            $builder->where('m.kode_prodi', $kode_prodi);
        }
        if (!empty($id_triwulan)) {
            $builder->where('l.id_triwulan', $id_triwulan);
        }

        $data = $builder->orderBy('m.tahun_masuk', 'ASC')->orderBy('m.nama_lengkap', 'ASC')->get()->getResultArray();

        // Create Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data IKU 1');

        // 1. Header Meta Info
        $sheet->setCellValue('A1', 'EXPORT DATA IKU 1 (AEE) - LULUSAN TEPAT WAKTU');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A2', 'Program Studi: ' . $nama_prodi . ' (' . $jenjang . ')');
        $sheet->setCellValue('A3', 'Triwulan: ' . $nama_triwulan);
        $sheet->setCellValue('A4', 'Tanggal Export: ' . date('d-m-Y H:i'));

        // 2. Table Headers
        $headers = ['NO', 'NIM', 'NAMA MAHASISWA', 'KODE PRODI', 'TAHUN MASUK', 'TANGGAL YUDISIUM', 'STATUS KELULUSAN'];
        $col = 'A';
        $row = 6; // Start from row 6
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
            $col++;
        }

        // 3. Table Data
        $row = 7;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValueExplicit('B' . $row, $item['nim'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row, $item['nama_lengkap'] ?? '-');
            $sheet->setCellValue('D' . $row, $item['kode_prodi']);
            $sheet->setCellValue('E' . $row, $item['tahun_masuk']);
            $sheet->setCellValue('F' . $row, $item['tanggal_yudisium']);
            $sheet->setCellValue('G' . $row, $item['status_kelulusan']);
            $row++;
            $no++;
        }

        // 4. Footer Notes (Keterangan)
        $row += 2; // Add 2 empty rows
        $sheet->setCellValue('A' . $row, 'KETERANGAN:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, '1. Status Kelulusan: "Tepat Waktu" jika lulus dalam durasi normal (D3: ≤3 tahun, S1/D4: ≤4 tahun, S2: ≤2 tahun).');
        $row++;
        $sheet->setCellValue('A' . $row, '2. Data ini diambil dari database berdasarkan filter Prodi dan Triwulan yang aktif.');
        $row++;
        $sheet->setCellValue('A' . $row, '3. Dokumen ini di-generate otomatis oleh Sistem Aplikasi Kinerja Polsri.');

        // Auto-size columns
        foreach (range('A', 'G') as $colLetter) {
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // 5. Output as Download
        $filename = 'Export_IKU1_' . str_replace(' ', '_', $nama_prodi) . '_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}

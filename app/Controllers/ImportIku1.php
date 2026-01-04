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
            $rows = $sheet->toArray(null, true, true, false);
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
            $raw_tgl_yudisium = trim($row[4] ?? '');
            $tanggal_yudisium = $this->parseDate($raw_tgl_yudisium);

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
            if (empty($tanggal_yudisium)) {
                $masa_studi_text = 'Invalid Date';
                $masa_studi_bulan = 0;
                if ($is_valid) {
                    $is_valid = false;
                    $error_msg = 'Format Tanggal Yudisium Salah (Gunakan YYYY-MM-DD atau DD/MM/YYYY).';
                }
                $status_kelulusan = 'Error';
            } else {
                // NEW LOGIC: Calculate precise months
                $masa_studi_bulan = 0;
                try {
                    // Asumsi masuk 1 September
                    $tgl_masuk = new \DateTime($tahun_masuk . '-09-01');
                    $tgl_lulus = new \DateTime($tanggal_yudisium);
                    $diff = $tgl_masuk->diff($tgl_lulus);
                    $masa_studi_bulan = ($diff->y * 12) + $diff->m;
                    if ($masa_studi_bulan < 0)
                        $masa_studi_bulan = 0;
                } catch (\Exception $e) {
                    $masa_studi_bulan = 0;
                }

                $thn_studi = floor($masa_studi_bulan / 12);
                $bln_studi = $masa_studi_bulan % 12;
                $masa_studi_text = "$thn_studi Tahun $bln_studi Bulan";

                // Threshold: D3=42 bln (3.5th), D4/S1=54 bln (4.5th)
                $max_bulan = (stripos($jenjang, 'D3') !== false || stripos($jenjang, 'DIII') !== false) ? 42 : 54;
                $status_kelulusan = ($masa_studi_bulan <= $max_bulan) ? 'Tepat Waktu' : 'Terlambat';
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
        $return_url = $this->request->getPost('return_url');

        if (!$json_data || !$id_triwulan) {
            return redirect()->back()->with('error', 'Data tidak valid.');
        }

        $rows = json_decode($json_data, true);
        if (empty($rows)) {
            return redirect()->back()->with('error', 'Data kosong.');
        }

        $mahasiswaModel = new MahasiswaModel();
        $iku1Model = new Iku1Model();
        $db = \Config\Database::connect();

        $count = 0;
        $new_mhs = 0;

        foreach ($rows as $row) {
            if (!$row['valid'])
                continue;

            // 1. Cek / Upsert Mahasiswa
            $existingMhs = $mahasiswaModel->where('nim', $row['nim'])->first();

            if (!$existingMhs) {
                // Insert Baru
                $mahasiswaModel->insert([
                    'nim' => $row['nim'],
                    'nama_lengkap' => $row['nama'],
                    'kode_prodi' => $row['kode_prodi'],
                    'tahun_masuk' => $row['tahun_masuk'],
                    'tanggal_yudisium' => $row['tanggal_yudisium'],
                    'status' => 'Lulus'
                ]);
                $new_mhs++;
            } else {
                // Update Tanggal Yudisium Data Lama
                if (empty($existingMhs['tanggal_yudisium']) || $existingMhs['tanggal_yudisium'] != $row['tanggal_yudisium']) {
                    $mahasiswaModel->update($existingMhs['id'], [
                        'tanggal_yudisium' => $row['tanggal_yudisium'],
                        'status' => 'Lulus'
                    ]);
                }
            }

            // 2. Insert ke IKU 1 (Hapus dulu jika ada duplikat di triwulan sama utk hindari error/double count)
            $existingIku1 = $iku1Model->where('nim', $row['nim'])
                ->where('id_triwulan', $id_triwulan)
                ->first();
            if ($existingIku1) {
                // Option: Skip atau Delete-Insert. Kita delete-insert agar data terbaru masuk.
                $iku1Model->delete($existingIku1['id']);
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

        return redirect()->to('admin/iku')->with('success', $msg);
    }
    public function download_template()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Import');

        // 1. Set Headers
        $headers = [
            'A1' => 'NIM',
            'B1' => 'NAMA MAHASISWA',
            'C1' => 'KODE PRODI',
            'D1' => 'TAHUN MASUK',
            'E1' => 'TANGGAL YUDISIUM',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle($cell)->getFill()->getStartColor()->setARGB('FF4472C4');
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FFFFFFFF');
        }

        // 2. Sample Data
        $sheet->setCellValueExplicit('A2', '061930320658', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('B2', 'CONTOH MAHASISWA');
        $sheet->setCellValue('C2', 'A01');
        $sheet->setCellValue('D2', '2019');
        $sheet->setCellValue('E2', '25/08/2023'); // Format DD/MM/YYYY

        // 3. Column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(20);

        // 4. Add instruction sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Panduan Pengisian');

        $instructionSheet->setCellValue('A1', 'PANDUAN PENGISIAN TEMPLATE IKU 1');
        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $instructions = [
            ['A3' => 'KOLOM', 'B3' => 'KETERANGAN', 'C3' => 'CONTOH'],
            ['A4' => 'NIM', 'B4' => 'Nomor Induk Mahasiswa (10-15 digit)', 'C4' => '061930320658'],
            ['A5' => 'NAMA MAHASISWA', 'B5' => 'Nama lengkap mahasiswa', 'C5' => 'JOHN DOE'],
            ['A6' => 'KODE PRODI', 'B6' => 'Kode prodi dari sistem (lihat master prodi)', 'C6' => 'A01'],
            ['A7' => 'TAHUN MASUK', 'B7' => 'Tahun masuk kuliah (4 digit)', 'C7' => '2019'],
            ['A8' => 'TANGGAL YUDISIUM', 'B8' => 'Format: DD/MM/YYYY atau YYYY-MM-DD (keduanya diterima)', 'C8' => '25/08/2023 atau 2023-08-25'],
        ];

        foreach ($instructions as $row) {
            foreach ($row as $cell => $value) {
                $instructionSheet->setCellValue($cell, $value);
            }
        }
        $instructionSheet->getStyle('A3:C3')->getFont()->setBold(true);
        $instructionSheet->getColumnDimension('A')->setWidth(25);
        $instructionSheet->getColumnDimension('B')->setWidth(55);
        $instructionSheet->getColumnDimension('C')->setWidth(30);

        // Note row
        $instructionSheet->setCellValue('A10', 'CATATAN:');
        $instructionSheet->setCellValue('A11', '1. Kolom NIM harus diisi sebagai TEXT, bukan angka (untuk menjaga leading zero)');
        $instructionSheet->setCellValue('A12', '2. Status Kelulusan (Tepat Waktu/Terlambat) akan dihitung otomatis oleh sistem');
        $instructionSheet->setCellValue('A13', '3. Kriteria: D3 <= 42 bulan = Tepat Waktu, D4/S1 <= 54 bulan = Tepat Waktu');
        $instructionSheet->getStyle('A10')->getFont()->setBold(true);

        // Set active back to main sheet
        $spreadsheet->setActiveSheetIndex(0);

        // 5. Output as Download
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
            ->select('l.nim, l.tanggal_yudisium, l.masa_studi_bulan, l.status_kelulusan, m.nama_lengkap, m.tahun_masuk, m.kode_prodi, p.nama_prodi, p.jenjang')
            ->join('tb_m_mahasiswa m', 'm.nim = l.nim', 'left')
            ->join('prodi p', 'm.kode_prodi = p.kode_prodi', 'left');

        if (!empty($kode_prodi)) {
            $builder->where('m.kode_prodi', $kode_prodi);
        }
        if (!empty($id_triwulan)) {
            $builder->where('l.id_triwulan', $id_triwulan);
        }

        // Filter by Selected IDs (if any)
        $ids = $this->request->getGet('ids');
        if (!empty($ids)) {
            $id_array = explode(',', $ids);
            $builder->whereIn('l.id', $id_array);
        }

        $data = $builder->orderBy('m.tahun_masuk', 'ASC')->orderBy('m.nama_lengkap', 'ASC')->get()->getResultArray();

        // Create Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data IKU 1');

        // ==========================================
        // TITLE SECTION
        // ==========================================
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'LAPORAN DATA IKU 1 (AEE) - KELULUSAN TEPAT WAKTU');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF4472C4'); // Blue

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', "Program Studi: {$nama_prodi} ({$jenjang}) | Triwulan: {$nama_triwulan} | Tanggal Export: " . date('d/m/Y H:i'));
        $sheet->getStyle('A2')->getFont()->setSize(11)->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD6DCE5'); // Light Gray

        // ==========================================
        // HEADER ROW
        // ==========================================
        $headers = ['NO', 'NIM', 'NAMA MAHASISWA', 'KODE PRODI', 'NAMA PRODI', 'TAHUN MASUK', 'TANGGAL YUDISIUM', 'MASA STUDI (Bulan)', 'MASA STUDI (Teks)', 'STATUS KELULUSAN'];
        $col = 'A';
        $row = 4; // Start from row 4

        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle($col . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF2F5597'); // Dark Blue
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        // ==========================================
        // DATA ROWS
        // ==========================================
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            $bln = (int) $item['masa_studi_bulan'];
            $thn = floor($bln / 12);
            $sisa_bln = $bln % 12;
            $masa_text = "{$thn} Tahun {$sisa_bln} Bulan";

            // Conditional Color for Status
            $statusColor = ($item['status_kelulusan'] == 'Tepat Waktu') ? 'FF00B050' : 'FFFF0000'; // Green or Red

            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValueExplicit('B' . $row, $item['nim'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row, $item['nama_lengkap'] ?? '-');
            $sheet->setCellValue('D' . $row, $item['kode_prodi']);
            $sheet->setCellValue('E' . $row, $item['nama_prodi']);
            $sheet->setCellValue('F' . $row, $item['tahun_masuk']);
            $sheet->setCellValue('G' . $row, $item['tanggal_yudisium']);
            $sheet->setCellValue('H' . $row, $item['masa_studi_bulan']);
            $sheet->setCellValue('I' . $row, $masa_text);
            $sheet->setCellValue('J' . $row, $item['status_kelulusan']);

            // Styling Status Cell
            $sheet->getStyle('J' . $row)->getFont()->setBold(true)->getColor()->setARGB($statusColor);

            // Alignment Center
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $row++;
            $no++;
        }

        // ==========================================
        // FINAL STYLING
        // ==========================================
        // Borders for all cells
        $lastRow = $row - 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle('A4:J' . $lastRow)->applyFromArray($styleArray);

        // Auto Size Columns
        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
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

    /**
     * Parse Date from various formats
     */
    private function parseDate($dateValue)
    {
        if (empty($dateValue))
            return null;

        $dateValue = trim($dateValue);
        if ($dateValue === '' || $dateValue === '0')
            return null;

        // 1. If numeric (Excel serial date) - use PhpSpreadsheet's utility
        if (is_numeric($dateValue) && $dateValue > 0) {
            try {
                $dateObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue);
                $result = $dateObj->format('Y-m-d');
                $parts = explode('-', $result);
                if (count($parts) === 3 && checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0])) {
                    return $result;
                }
            } catch (\Exception $e) {
                // Ignore
            }
        }

        // 2. If string format X/X/XXXX - try both DD/MM/YYYY and MM/DD/YYYY
        if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/', $dateValue, $matches)) {
            $a = (int) $matches[1];
            $b = (int) $matches[2];
            $year = (int) $matches[3];

            // Try DD/MM/YYYY first (Indonesia format)
            if (checkdate($b, $a, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $b, $a);
            }
            // Try MM/DD/YYYY (US format)
            if (checkdate($a, $b, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $a, $b);
            }
            return null;
        }

        // 3. If XXXX/X/X format - try YYYY-MM-DD and YYYY-DD-MM
        if (preg_match('/^(\d{4})[\/\-\.](\d{1,2})[\/\-\.](\d{1,2})$/', $dateValue, $matches)) {
            $year = (int) $matches[1];
            $b = (int) $matches[2];
            $c = (int) $matches[3];

            if (checkdate($b, $c, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $b, $c);
            }
            if (checkdate($c, $b, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $c, $b);
            }
            return null;
        }

        // 4. Try DateTime parsing
        try {
            $d = \DateTime::createFromFormat('d/m/Y', $dateValue);
            if ($d !== false)
                return $d->format('Y-m-d');

            $d = \DateTime::createFromFormat('m/d/Y', $dateValue);
            if ($d !== false)
                return $d->format('Y-m-d');

            $d = \DateTime::createFromFormat('Y-m-d', $dateValue);
            if ($d !== false)
                return $d->format('Y-m-d');
        } catch (\Exception $e) {
            // Ignore
        }

        return null;
    }
}

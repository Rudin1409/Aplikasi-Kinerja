<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MahasiswaModel;
use App\Models\ProdiModel;
use App\Models\RefUmpModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Import IKU 2 Controller
 * Updated to match new database schema
 */
class ImportIku2 extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $active_triwulan = $db->table('triwulan')->where('status', 'Aktif')->get()->getRowArray();
        $active_triwulan_id = $active_triwulan['id'] ?? null;
        $triwulan = $db->table('triwulan')->select('*')->get()->getResultArray();
        $return_url = $this->request->getGet('return_url');

        $data = [
            'title' => 'Import IKU 2 (Tracer Study)',
            'page' => 'iku',
            'triwulan_list' => $triwulan,
            'active_triwulan_id' => $active_triwulan_id,
            'return_url' => $return_url
        ];

        return view('admin/iku2/form_import', $data);
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
        $refUmpModel = new RefUmpModel();
        $db = \Config\Database::connect();

        $preview_data = [];

        foreach ($rows as $key => $row) {
            if ($key === 0)
                continue; // Skip Header

            // Column mapping (sesuai template baru):
            // A=NIM, B=NAMA, C=KODE_PRODI, D=TAHUN_MASUK, E=TANGGAL_YUDISIUM, 
            // F=JENIS_AKTIVITAS, G=NAMA_TEMPAT, H=TANGGAL_MULAI, I=PROVINSI_TEMPAT_KERJA,
            // J=GAJI_BULAN, K=POSISI_WIRAUSAHA

            $nim = trim($row[0] ?? '');
            $nama = trim($row[1] ?? '');
            $kode_prodi = trim($row[2] ?? '');
            $tahun_masuk = trim($row[3] ?? '');

            // Fix Date Format (Excel numeric, d/m/Y, etc -> Y-m-d)
            $raw_tgl_yudisium = trim($row[4] ?? '');
            $tanggal_yudisium = $this->parseDate($raw_tgl_yudisium);

            $jenis_aktivitas = trim($row[5] ?? '');
            $nama_tempat = trim($row[6] ?? '');

            $raw_tgl_mulai = trim($row[7] ?? '');
            $tanggal_mulai = $this->parseDate($raw_tgl_mulai);

            $provinsi_tempat_kerja = trim($row[8] ?? '');
            $gaji_bulan = trim($row[9] ?? '');
            $posisi_wirausaha = trim($row[10] ?? '');

            if (empty($nim) || empty($nama))
                continue;

            $is_valid = true;
            $error_msg = '';

            // 1. Cek Mahasiswa Status
            $existing = $mahasiswaModel->where('nim', $nim)->first();
            $status_mhs = $existing ? 'Lama' : 'Baru';

            // 2. Validate Jenis Aktivitas
            $valid_statuses = ['Bekerja', 'Wirausaha', 'Lanjut Studi', 'Mencari Kerja'];
            if (!in_array($jenis_aktivitas, $valid_statuses)) {
                $is_valid = false;
                $error_msg .= "Jenis aktivitas tidak valid (harus: Bekerja/Wirausaha/Lanjut Studi/Mencari Kerja). ";
            }

            // 3. Validate Prodi
            $prodi = $prodiModel->where('kode_prodi', $kode_prodi)->first();
            $jenjang = $prodi ? $prodi['jenjang'] : '';
            if (!$prodi) {
                $is_valid = false;
                $error_msg .= "Kode Prodi tidak ditemukan. ";
            }

            // EXTRA: Validate Tanggal Yudisium format
            if (!empty($raw_tgl_yudisium) && empty($tanggal_yudisium)) {
                $is_valid = false;
                $error_msg .= "Format Tanggal Yudisium salah. Raw value: '{$raw_tgl_yudisium}' (Gunakan YYYY-MM-DD atau DD/MM/YYYY). ";
            }

            // 4. Validate Provinsi (optional for non-bekerja)
            $provinsi_id = null;
            $nilai_ump = 0;
            if ($jenis_aktivitas == 'Bekerja' && !empty($provinsi_tempat_kerja)) {
                // Try to find by name or ID
                $ump_data = $refUmpModel->where('provinsi', $provinsi_tempat_kerja)->first();
                if (!$ump_data) {
                    $ump_data = $refUmpModel->find($provinsi_tempat_kerja);
                }
                if ($ump_data) {
                    $provinsi_id = $ump_data['id'];
                    $nilai_ump = $ump_data['nilai_ump'];
                } else {
                    $error_msg .= "(Peringatan: Provinsi tidak ditemukan, UMP tidak tersedia) ";
                }
            }

            // 5. Calculate Nilai Bobot
            $nilai_bobot = $this->calculateBobot(
                $jenis_aktivitas,
                $tanggal_yudisium,
                $tanggal_mulai,
                $gaji_bulan,
                $nilai_ump,
                $posisi_wirausaha
            );

            // 6. Check IKU 1 Sync Status (apakah perlu ditambahkan ke IKU 1)
            $sync_iku1 = false;
            $iku1_status = '';
            if (!empty($tanggal_yudisium)) {
                // Cek apakah sudah ada di IKU 1
                $existing_iku1 = $db->table('tb_iku_1_lulusan')
                    ->where('nim', $nim)
                    ->where('id_triwulan', $id_triwulan)
                    ->get()->getRowArray();

                if (!$existing_iku1) {
                    $sync_iku1 = true;
                    $iku1_status = 'BARU (akan ditambahkan ke IKU 1)';
                } else {
                    // Check if yudisium date needs update
                    if (empty($existing_iku1['tanggal_yudisium']) && !empty($tanggal_yudisium)) {
                        $sync_iku1 = true;
                        $iku1_status = 'UPDATE (tanggal yudisium akan diupdate di IKU 1)';
                    } else {
                        $iku1_status = 'OK (sudah ada di IKU 1)';
                    }
                }
            }

            $preview_data[] = [
                'nim' => $nim,
                'nama' => $nama,
                'kode_prodi' => $kode_prodi,
                'tahun_masuk' => $tahun_masuk,
                'tanggal_yudisium' => $tanggal_yudisium,
                'raw_tgl_yudisium' => $raw_tgl_yudisium, // DEBUG
                'jenis_aktivitas' => $jenis_aktivitas,
                'nama_tempat' => $nama_tempat,
                'tanggal_mulai' => $tanggal_mulai,
                'raw_tgl_mulai' => $raw_tgl_mulai, // DEBUG
                'provinsi_tempat_kerja' => $provinsi_id,
                'provinsi_nama' => $provinsi_tempat_kerja,
                'gaji_bulan' => $gaji_bulan,
                'posisi_wirausaha' => $posisi_wirausaha,
                'nilai_bobot' => $nilai_bobot,
                'status_mhs' => $status_mhs,
                'jenjang' => $jenjang,
                'sync_iku1' => $sync_iku1,
                'iku1_status' => $iku1_status,
                'valid' => $is_valid,
                'error_msg' => $error_msg
            ];
        }

        $triwulan = $db->table('triwulan')->select('*')->get()->getResultArray();

        $data = [
            'title' => 'Preview Import IKU 2',
            'page' => 'iku',
            'triwulan_list' => $triwulan,
            'active_triwulan_id' => $id_triwulan,
            'preview_data' => $preview_data,
            'id_triwulan_selected' => $id_triwulan,
            'return_url' => $return_url
        ];

        return view('admin/iku2/form_import', $data);
    }

    /**
     * Calculate Bobot IKU 2025
     */
    private function calculateBobot($jenis_aktivitas, $tanggal_yudisium, $tanggal_mulai, $gaji, $nilai_ump, $posisi_wirausaha)
    {
        $nilai_bobot = 0.00;

        // Calculate masa tunggu
        $masa_tunggu_bulan = 0;
        if (!empty($tanggal_yudisium) && !empty($tanggal_mulai)) {
            try {
                $d1 = new \DateTime($tanggal_yudisium);
                $d2 = new \DateTime($tanggal_mulai);
                $diff = $d1->diff($d2);
                $masa_tunggu_bulan = ($diff->y * 12) + $diff->m;
            } catch (\Exception $e) {
                $masa_tunggu_bulan = 0;
            }
        }

        // Check gaji layak
        $gaji_num = (float) str_replace(['.', ','], ['', '.'], $gaji);
        $is_gaji_layak = ($nilai_ump > 0) ? ($gaji_num >= (1.2 * $nilai_ump)) : false;

        switch ($jenis_aktivitas) {
            case 'Bekerja':
                if ($masa_tunggu_bulan > 12) {
                    $nilai_bobot = 0.00;
                } elseif ($masa_tunggu_bulan < 6 && $is_gaji_layak) {
                    $nilai_bobot = 1.00;
                } elseif ($masa_tunggu_bulan >= 6 && $masa_tunggu_bulan <= 12 && $is_gaji_layak) {
                    $nilai_bobot = 0.60;
                } elseif (!$is_gaji_layak && $masa_tunggu_bulan <= 12) {
                    $nilai_bobot = 0.40;
                }
                break;

            case 'Wirausaha':
                if ($posisi_wirausaha == 'Pendiri') {
                    $nilai_bobot = 0.75;
                } elseif ($posisi_wirausaha == 'Freelance') {
                    $nilai_bobot = 0.25;
                }
                break;

            case 'Lanjut Studi':
                if ($masa_tunggu_bulan < 12) {
                    $nilai_bobot = 1.00;
                } else {
                    $nilai_bobot = 0.00;
                }
                break;

            case 'Mencari Kerja':
                $nilai_bobot = 0.00;
                break;
        }

        return $nilai_bobot;
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
        $db = \Config\Database::connect();

        $count = 0;
        $new_mhs = 0;
        $iku1_new = 0;
        $iku1_updated = 0;

        foreach ($rows as $row) {
            if (!$row['valid'])
                continue;

            // Upsert Mahasiswa
            $existing_mhs = $mahasiswaModel->where('nim', $row['nim'])->first();
            if (!$existing_mhs) {
                $mahasiswaModel->insert([
                    'nim' => $row['nim'],
                    'nama_lengkap' => $row['nama'],
                    'kode_prodi' => $row['kode_prodi'],
                    'tahun_masuk' => $row['tahun_masuk'],
                    'tanggal_yudisium' => $row['tanggal_yudisium'] ?: null,
                    'status' => 'Lulus'
                ]);
                $new_mhs++;
            } else {
                // Update tanggal_yudisium if provided
                if (!empty($row['tanggal_yudisium'])) {
                    $mahasiswaModel->update($existing_mhs['nim'], [
                        'tanggal_yudisium' => $row['tanggal_yudisium']
                    ]);
                }
            }

            // Calculate masa tunggu
            $masa_tunggu_bulan = 0;
            if (!empty($row['tanggal_yudisium']) && !empty($row['tanggal_mulai'])) {
                try {
                    $d1 = new \DateTime($row['tanggal_yudisium']);
                    $d2 = new \DateTime($row['tanggal_mulai']);
                    $diff = $d1->diff($d2);
                    $masa_tunggu_bulan = ($diff->y * 12) + $diff->m;
                } catch (\Exception $e) {
                    $masa_tunggu_bulan = 0;
                }
            }

            // Check if record exists
            $existing = $db->table('tb_iku_2_lulusan')
                ->where('nim', $row['nim'])
                ->where('id_triwulan', $id_triwulan)
                ->get()->getRowArray();

            if ($existing) {
                // Delete old record
                $db->table('tb_iku_2_lulusan')->delete(['id' => $existing['id']]);
            }

            // Insert IKU 2 with new schema
            $db->table('tb_iku_2_lulusan')->insert([
                'nim' => $row['nim'],
                'id_triwulan' => $id_triwulan,
                'jenis_aktivitas' => $row['jenis_aktivitas'],
                'nama_tempat' => $row['nama_tempat'],
                'tanggal_mulai' => $row['tanggal_mulai'] ?: null,
                'provinsi_tempat_kerja' => $row['provinsi_tempat_kerja'] ?: null,
                'gaji_bulan' => is_numeric(str_replace(['.', ','], '', $row['gaji_bulan'])) ? (float) str_replace(['.', ','], ['', '.'], $row['gaji_bulan']) : 0,
                'masa_tunggu_bulan' => $masa_tunggu_bulan,
                'posisi_wirausaha' => $row['posisi_wirausaha'] ?: null,
                'nilai_bobot' => $row['nilai_bobot'],
                'status_validasi' => 'Pending'
            ]);
            $count++;

            // === SYNC TO IKU 1 ===
            // Jika ada tanggal_yudisium, sync ke IKU 1 (tb_iku_1_lulusan)
            if (!empty($row['tanggal_yudisium']) && !empty($row['sync_iku1']) && $row['sync_iku1']) {
                // Cek apakah sudah ada record di IKU 1
                $existingIku1 = $db->table('tb_iku_1_lulusan')
                    ->where('nim', $row['nim'])
                    ->where('id_triwulan', $id_triwulan)
                    ->get()->getRowArray();

                // Hitung masa studi (dari tahun masuk ke tanggal yudisium)
                $masa_studi_bulan = 0;
                if (!empty($row['tahun_masuk']) && !empty($row['tanggal_yudisium'])) {
                    try {
                        // Asumsi mulai kuliah bulan September tahun masuk
                        $mulai = new \DateTime($row['tahun_masuk'] . '-09-01');
                        $lulus = new \DateTime($row['tanggal_yudisium']);
                        $diff = $mulai->diff($lulus);
                        $masa_studi_bulan = ($diff->y * 12) + $diff->m;
                    } catch (\Exception $e) {
                        $masa_studi_bulan = 0;
                    }
                }

                // Tentukan status kelulusan (contoh: jika D3 max 42 bulan, D4/S1 max 54 bulan)
                $jenjang = $row['jenjang'] ?? '';
                $tepat_waktu = false;
                if (strpos($jenjang, 'DIII') !== false || $jenjang == 'D3') {
                    $tepat_waktu = ($masa_studi_bulan <= 42); // 3.5 tahun
                } else {
                    $tepat_waktu = ($masa_studi_bulan <= 54); // 4.5 tahun
                }
                $status_kelulusan = $tepat_waktu ? 'Tepat Waktu' : 'Terlambat';

                if ($existingIku1) {
                    // Update record
                    $db->table('tb_iku_1_lulusan')->update([
                        'tanggal_yudisium' => $row['tanggal_yudisium'],
                        'masa_studi_bulan' => $masa_studi_bulan,
                        'status_kelulusan' => $status_kelulusan
                    ], ['id' => $existingIku1['id']]);
                    $iku1_updated++;
                } else {
                    // Insert new record
                    $db->table('tb_iku_1_lulusan')->insert([
                        'nim' => $row['nim'],
                        'id_triwulan' => $id_triwulan,
                        'tanggal_yudisium' => $row['tanggal_yudisium'],
                        'masa_studi_bulan' => $masa_studi_bulan,
                        'status_kelulusan' => $status_kelulusan
                    ]);
                    $iku1_new++;
                }
            }
        }

        // Build success message
        $msg = "✅ Berhasil import! $count Data IKU 2";
        if ($new_mhs > 0)
            $msg .= ", $new_mhs Mahasiswa Baru";
        if ($iku1_new > 0)
            $msg .= ", $iku1_new Data Baru di IKU 1";
        if ($iku1_updated > 0)
            $msg .= ", $iku1_updated Data IKU 1 diupdate";
        $msg .= ".";

        if (!empty($return_url)) {
            return redirect()->to(urldecode($return_url))->with('success', $msg);
        }

        return redirect()->to('admin/dashboard')->with('success', $msg);
    }

    public function download_template()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template IKU 2');

        // Headers (sesuai form input terbaru)
        $headers = [
            'A1' => 'NIM',
            'B1' => 'NAMA MAHASISWA',
            'C1' => 'KODE PRODI',
            'D1' => 'TAHUN MASUK',
            'E1' => 'TANGGAL YUDISIUM',
            'F1' => 'JENIS AKTIVITAS',
            'G1' => 'NAMA TEMPAT',
            'H1' => 'TANGGAL MULAI',
            'I1' => 'PROVINSI TEMPAT KERJA',
            'J1' => 'GAJI BULAN (Angka)',
            'K1' => 'POSISI WIRAUSAHA'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle($cell)->getFill()->getStartColor()->setARGB('FF4472C4');
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FFFFFFFF');
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(25);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(20);

        // Add instruction sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Panduan Pengisian');

        $instructionSheet->setCellValue('A1', 'PANDUAN PENGISIAN TEMPLATE IKU 2');
        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $instructions = [
            ['A3' => 'KOLOM', 'B3' => 'KETERANGAN', 'C3' => 'CONTOH'],
            ['A4' => 'NIM', 'B4' => 'Nomor Induk Mahasiswa (10-15 digit)', 'C4' => '061930320658'],
            ['A5' => 'NAMA MAHASISWA', 'B5' => 'Nama lengkap mahasiswa', 'C5' => 'JOHN DOE'],
            ['A6' => 'KODE PRODI', 'B6' => 'Kode prodi dari sistem (lihat master prodi)', 'C6' => 'A01'],
            ['A7' => 'TAHUN MASUK', 'B7' => 'Tahun masuk kuliah (4 digit)', 'C7' => '2019'],
            ['A8' => 'TANGGAL YUDISIUM', 'B8' => 'Format: DD/MM/YYYY atau YYYY-MM-DD (keduanya diterima)', 'C8' => '25/08/2023 atau 2023-08-25'],
            ['A9' => 'JENIS AKTIVITAS', 'B9' => 'Pilih: Bekerja / Wirausaha / Lanjut Studi / Mencari Kerja', 'C9' => 'Bekerja'],
            ['A10' => 'NAMA TEMPAT', 'B10' => 'Nama perusahaan/kampus/usaha', 'C10' => 'PT Maju Jaya'],
            ['A11' => 'TANGGAL MULAI', 'B11' => 'Format: DD/MM/YYYY atau YYYY-MM-DD', 'C11' => '01/10/2023 atau 2023-10-01'],
            ['A12' => 'PROVINSI TEMPAT KERJA', 'B12' => 'Nama provinsi tempat kerja (hanya untuk Bekerja)', 'C12' => 'Sumatera Selatan'],
            ['A13' => 'GAJI BULAN', 'B13' => 'Gaji per bulan dalam angka (hanya untuk Bekerja)', 'C13' => '5000000'],
            ['A14' => 'POSISI WIRAUSAHA', 'B14' => 'Pilih: Pendiri / Freelance (hanya untuk Wirausaha)', 'C14' => 'Pendiri'],
        ];

        foreach ($instructions as $row) {
            foreach ($row as $cell => $value) {
                $instructionSheet->setCellValue($cell, $value);
            }
        }
        $instructionSheet->getStyle('A3:C3')->getFont()->setBold(true);
        $instructionSheet->getColumnDimension('A')->setWidth(25);
        $instructionSheet->getColumnDimension('B')->setWidth(50);
        $instructionSheet->getColumnDimension('C')->setWidth(25);

        // Sample Data (back to main sheet)
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        // Sample Row 1: Bekerja
        $sheet->setCellValueExplicit('A2', '061930320658', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('B2', 'CONTOH MAHASISWA BEKERJA');
        $sheet->setCellValue('C2', 'A01');
        $sheet->setCellValue('D2', '2019');
        $sheet->setCellValue('E2', '2023-08-15');
        $sheet->setCellValue('F2', 'Bekerja');
        $sheet->setCellValue('G2', 'PT Maju Jaya Indonesia');
        $sheet->setCellValue('H2', '2023-10-01');
        $sheet->setCellValue('I2', 'Sumatera Selatan');
        $sheet->setCellValue('J2', '5000000');
        $sheet->setCellValue('K2', '');

        // Sample Row 2: Wirausaha
        $sheet->setCellValueExplicit('A3', '061930320659', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('B3', 'CONTOH MAHASISWA WIRAUSAHA');
        $sheet->setCellValue('C3', 'A01');
        $sheet->setCellValue('D3', '2019');
        $sheet->setCellValue('E3', '2023-08-15');
        $sheet->setCellValue('F3', 'Wirausaha');
        $sheet->setCellValue('G3', 'Toko Online ABC');
        $sheet->setCellValue('H3', '2023-09-01');
        $sheet->setCellValue('I3', '');
        $sheet->setCellValue('J3', '');
        $sheet->setCellValue('K3', 'Pendiri');

        // Sample Row 3: Lanjut Studi
        $sheet->setCellValueExplicit('A4', '061930320660', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('B4', 'CONTOH MAHASISWA STUDI');
        $sheet->setCellValue('C4', 'A01');
        $sheet->setCellValue('D4', '2019');
        $sheet->setCellValue('E4', '2023-08-15');
        $sheet->setCellValue('F4', 'Lanjut Studi');
        $sheet->setCellValue('G4', 'Universitas Indonesia');
        $sheet->setCellValue('H4', '2023-09-15');
        $sheet->setCellValue('I4', '');
        $sheet->setCellValue('J4', '');
        $sheet->setCellValue('K4', '');

        $filename = 'Template_Import_IKU2_TracerStudy.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function export()
    {
        $db = \Config\Database::connect();
        $kode_prodi = $this->request->getGet('kode_prodi');
        $id_triwulan = $this->request->getGet('id_triwulan');

        // Get Info
        $prodi = $db->table('prodi')->where('kode_prodi', $kode_prodi)->get()->getRowArray();
        $triwulan = $db->table('triwulan')->where('id', $id_triwulan)->get()->getRowArray();

        $builder = $db->table('tb_iku_2_lulusan l')
            ->select('l.*, m.nama_lengkap, m.tahun_masuk, m.kode_prodi, m.tanggal_yudisium, u.provinsi, u.nilai_ump')
            ->join('tb_m_mahasiswa m', 'm.nim = l.nim', 'left')
            ->join('tb_ref_ump u', 'u.id = l.provinsi_tempat_kerja', 'left');

        if (!empty($kode_prodi))
            $builder->where('m.kode_prodi', $kode_prodi);
        if (!empty($id_triwulan))
            $builder->where('l.id_triwulan', $id_triwulan);

        // Filter by Selected IDs (if any)
        $ids = $this->request->getGet('ids');
        if (!empty($ids)) {
            $id_array = explode(',', $ids);
            $builder->whereIn('l.id', $id_array);
        }

        $data = $builder->orderBy('m.tahun_masuk', 'DESC')->get()->getResultArray();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data IKU 2');

        // ==========================================
        // TITLE SECTION (Merged Header)
        // ==========================================
        $prodi_name = $prodi['nama_prodi'] ?? 'Semua Prodi';
        $prodi_jenjang = $prodi['jenjang'] ?? '';
        $tw_name = $triwulan['nama_triwulan'] ?? 'Semua Triwulan';

        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1', 'LAPORAN DATA IKU 2 - TRACER STUDY');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A1')->getFill()->getStartColor()->setARGB('FF4472C4');
        $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFFFFFF');

        $sheet->mergeCells('A2:N2');
        $sheet->setCellValue('A2', "Program Studi: {$prodi_name} ({$prodi_jenjang}) | Triwulan: {$tw_name} | Tanggal Export: " . date('d/m/Y H:i'));
        $sheet->getStyle('A2')->getFont()->setSize(11)->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A2')->getFill()->getStartColor()->setARGB('FFD6DCE5');

        // ==========================================
        // HEADER ROW
        // ==========================================
        $headers = ['NO', 'NIM', 'NAMA LENGKAP', 'PRODI', 'ANGKATAN', 'TGL YUDISIUM', 'AKTIVITAS', 'NAMA TEMPAT', 'TGL MULAI', 'PROVINSI', 'GAJI (Rp)', 'MASA TUNGGU', 'POSISI', 'BOBOT'];
        $col = 'A';
        $headerRow = 4;
        foreach ($headers as $h) {
            $sheet->setCellValue($col . $headerRow, $h);
            $col++;
        }

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2F5496']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]]
        ];
        $sheet->getStyle("A{$headerRow}:N{$headerRow}")->applyFromArray($headerStyle);
        $sheet->getRowDimension($headerRow)->setRowHeight(25);

        // ==========================================
        // DATA ROWS
        // ==========================================
        $row = 5;
        $no = 1;
        $totalBobot = 0;
        $countBekerja = 0;
        $countWirausaha = 0;
        $countStudi = 0;
        $countMencari = 0;

        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValueExplicit('B' . $row, $item['nim'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row, $item['nama_lengkap']);
            $sheet->setCellValue('D' . $row, $item['kode_prodi']);
            $sheet->setCellValue('E' . $row, $item['tahun_masuk']);
            $sheet->setCellValue('F' . $row, $item['tanggal_yudisium']);
            $sheet->setCellValue('G' . $row, $item['jenis_aktivitas']);
            $sheet->setCellValue('H' . $row, $item['nama_tempat']);
            $sheet->setCellValue('I' . $row, $item['tanggal_mulai']);
            $sheet->setCellValue('J' . $row, $item['provinsi']);
            $sheet->setCellValue('K' . $row, $item['gaji_bulan']);
            $sheet->setCellValue('L' . $row, ($item['masa_tunggu_bulan'] ?? 0) . ' Bln');
            $sheet->setCellValue('M' . $row, $item['posisi_wirausaha']);
            $sheet->setCellValue('N' . $row, $item['nilai_bobot']);

            // Format gaji as currency
            $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('#,##0');

            // Color code by aktivitas
            $aktivitasColors = [
                'Bekerja' => 'FFE2EFDA',
                'Wirausaha' => 'FFFCE4D6',
                'Lanjut Studi' => 'FFDDEBF7',
                'Mencari Kerja' => 'FFFFF2CC'
            ];
            $aktivitasColor = $aktivitasColors[$item['jenis_aktivitas']] ?? 'FFFFFFFF';
            $sheet->getStyle('G' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle('G' . $row)->getFill()->getStartColor()->setARGB($aktivitasColor);

            // Bold bobot and color based on value
            $bobot = (float) $item['nilai_bobot'];
            $totalBobot += $bobot;
            $sheet->getStyle('N' . $row)->getFont()->setBold(true);
            if ($bobot >= 1.0) {
                $sheet->getStyle('N' . $row)->getFont()->getColor()->setARGB('FF00B050');
            } elseif ($bobot >= 0.5) {
                $sheet->getStyle('N' . $row)->getFont()->getColor()->setARGB('FF0070C0');
            } elseif ($bobot > 0) {
                $sheet->getStyle('N' . $row)->getFont()->getColor()->setARGB('FFED7D31');
            } else {
                $sheet->getStyle('N' . $row)->getFont()->getColor()->setARGB('FFC00000');
            }

            // Count by aktivitas
            switch ($item['jenis_aktivitas']) {
                case 'Bekerja':
                    $countBekerja++;
                    break;
                case 'Wirausaha':
                    $countWirausaha++;
                    break;
                case 'Lanjut Studi':
                    $countStudi++;
                    break;
                case 'Mencari Kerja':
                    $countMencari++;
                    break;
            }

            $row++;
        }

        // Data borders
        $lastDataRow = $row - 1;
        if ($lastDataRow >= 5) {
            $sheet->getStyle("A5:N{$lastDataRow}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            // Alternate row colors
            for ($r = 5; $r <= $lastDataRow; $r++) {
                if ($r % 2 == 0) {
                    $sheet->getStyle("A{$r}:N{$r}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                    $sheet->getStyle("A{$r}:N{$r}")->getFill()->getStartColor()->setARGB('FFF2F2F2');
                }
            }
        }

        // ==========================================
        // SUMMARY SECTION
        // ==========================================
        $summaryRow = $row + 1;
        $sheet->mergeCells("A{$summaryRow}:B{$summaryRow}");
        $sheet->setCellValue("A{$summaryRow}", 'RINGKASAN:');
        $sheet->getStyle("A{$summaryRow}")->getFont()->setBold(true)->setSize(11);

        $totalData = count($data);
        $avgBobot = $totalData > 0 ? round($totalBobot / $totalData, 2) : 0;
        $capaian = $totalData > 0 ? round(($totalBobot / $totalData) * 100, 1) : 0;

        $summaryData = [
            "C{$summaryRow}" => "Total: {$totalData}",
            "E{$summaryRow}" => "Bekerja: {$countBekerja}",
            "F{$summaryRow}" => "Wirausaha: {$countWirausaha}",
            "G{$summaryRow}" => "Studi: {$countStudi}",
            "H{$summaryRow}" => "Mencari: {$countMencari}",
            "L{$summaryRow}" => "Avg Bobot: {$avgBobot}",
            "N{$summaryRow}" => "Capaian: {$capaian}%",
        ];
        foreach ($summaryData as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }
        $sheet->getStyle("A{$summaryRow}:N{$summaryRow}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle("A{$summaryRow}:N{$summaryRow}")->getFill()->getStartColor()->setARGB('FFDCE6F1');

        // ==========================================
        // COLUMN WIDTHS
        // ==========================================
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(18);
        $sheet->getColumnDimension('K')->setWidth(15);
        $sheet->getColumnDimension('L')->setWidth(12);
        $sheet->getColumnDimension('M')->setWidth(12);
        $sheet->getColumnDimension('N')->setWidth(8);

        // Freeze header
        $sheet->freezePane('A5');

        $filename = 'Export_IKU2_' . str_replace(' ', '_', $prodi_name) . '_TW' . ($triwulan['nama_triwulan'] ?? '') . '_' . date('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

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
                // Validate the result
                $parts = explode('-', $result);
                if (count($parts) === 3 && checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0])) {
                    return $result;
                }
            } catch (\Exception $e) {
                // Ignore and try other methods
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
            // Both failed - date is invalid
            return null;
        }

        // 3. If XXXX/X/X format - try YYYY-MM-DD and YYYY-DD-MM
        if (preg_match('/^(\d{4})[\/\-\.](\d{1,2})[\/\-\.](\d{1,2})$/', $dateValue, $matches)) {
            $year = (int) $matches[1];
            $b = (int) $matches[2];
            $c = (int) $matches[3];

            // Try YYYY-MM-DD first
            if (checkdate($b, $c, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $b, $c);
            }
            // Try YYYY-DD-MM
            if (checkdate($c, $b, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $c, $b);
            }
            return null;
        }

        // 4. Try DateTime parsing as last resort
        try {
            $d = \DateTime::createFromFormat('d/m/Y', $dateValue);
            if ($d !== false) {
                return $d->format('Y-m-d');
            }

            $d = \DateTime::createFromFormat('m/d/Y', $dateValue);
            if ($d !== false) {
                return $d->format('Y-m-d');
            }

            $d = \DateTime::createFromFormat('Y-m-d', $dateValue);
            if ($d !== false) {
                return $d->format('Y-m-d');
            }
        } catch (\Exception $e) {
            // Ignore
        }

        return null;
    }
}

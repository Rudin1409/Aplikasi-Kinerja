<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RefUmpModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controller untuk CRUD Data UMP (Upah Minimum Provinsi)
 * Menggunakan AJAX/JSON Response
 */
class RefUmp extends BaseController
{
    protected $umpModel;

    public function __construct()
    {
        $this->umpModel = new RefUmpModel();
    }

    /**
     * GET: Ambil semua data UMP (JSON Response)
     */
    public function index()
    {
        $data = $this->umpModel->getAllOrderByProvinsi();

        // Format nilai_ump untuk display
        foreach ($data as &$row) {
            $row['nilai_ump_formatted'] = RefUmpModel::formatRupiah($row['nilai_ump']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data,
            'total' => count($data)
        ]);
    }

    /**
     * GET: Ambil single data UMP by ID
     */
    public function show($id)
    {
        $data = $this->umpModel->find($id);

        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * POST: Simpan data UMP baru atau Update existing
     */
    public function store()
    {
        $id = $this->request->getPost('id');
        $provinsi = trim($this->request->getPost('provinsi'));
        $nilai_ump = $this->request->getPost('nilai_ump');
        $tahun = $this->request->getPost('tahun') ?: date('Y');

        // Validasi input
        if (empty($provinsi)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nama provinsi wajib diisi'
            ])->setStatusCode(400);
        }

        // Clean nilai_ump (remove formatting)
        $nilai_ump = (float) preg_replace('/[^0-9]/', '', $nilai_ump);

        if ($nilai_ump <= 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nilai UMP harus berupa angka lebih dari 0'
            ])->setStatusCode(400);
        }

        $data = [
            'provinsi' => $provinsi,
            'nilai_ump' => $nilai_ump,
            'tahun' => $tahun
        ];

        try {
            if ($id) {
                // Update existing
                $existing = $this->umpModel->find($id);
                if (!$existing) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Data tidak ditemukan'
                    ])->setStatusCode(404);
                }

                $this->umpModel->update($id, $data);
                $message = 'Data UMP berhasil diperbarui';
            } else {
                // Check duplicate provinsi for same year
                $duplicate = $this->umpModel
                    ->where('provinsi', $provinsi)
                    ->where('tahun', $tahun)
                    ->first();

                if ($duplicate) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => "Data UMP untuk {$provinsi} tahun {$tahun} sudah ada"
                    ])->setStatusCode(400);
                }

                $this->umpModel->insert($data);
                $id = $this->umpModel->getInsertID();
                $message = 'Data UMP berhasil ditambahkan';
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $message,
                'data' => $this->umpModel->find($id)
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * DELETE: Hapus data UMP
     */
    public function delete($id)
    {
        $existing = $this->umpModel->find($id);

        if (!$existing) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        }

        try {
            $this->umpModel->delete($id);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data UMP ' . $existing['provinsi'] . ' berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * POST: Bulk Update UMP (untuk update massal)
     */
    public function bulkUpdate()
    {
        $updates = $this->request->getPost('updates');

        if (empty($updates) || !is_array($updates)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data update tidak valid'
            ])->setStatusCode(400);
        }

        $success = 0;
        $failed = 0;

        foreach ($updates as $update) {
            if (!empty($update['id']) && !empty($update['nilai_ump'])) {
                try {
                    $this->umpModel->update($update['id'], [
                        'nilai_ump' => (float) preg_replace('/[^0-9]/', '', $update['nilai_ump'])
                    ]);
                    $success++;
                } catch (\Exception $e) {
                    $failed++;
                }
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => "Berhasil update {$success} data, gagal {$failed} data"
        ]);
    }

    /**
     * GET: Search UMP by provinsi name
     */
    public function search()
    {
        $keyword = $this->request->getGet('q');

        if (empty($keyword)) {
            return $this->index();
        }

        $data = $this->umpModel->searchByProvinsi($keyword);

        foreach ($data as &$row) {
            $row['nilai_ump_formatted'] = RefUmpModel::formatRupiah($row['nilai_ump']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data,
            'total' => count($data)
        ]);
    }
}

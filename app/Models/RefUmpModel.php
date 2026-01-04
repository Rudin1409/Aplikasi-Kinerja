<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk mengelola data UMP (Upah Minimum Provinsi)
 * Digunakan sebagai referensi perhitungan gaji layak di IKU 2
 */
class RefUmpModel extends Model
{
    protected $table = 'tb_ref_ump';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['provinsi', 'nilai_ump', 'tahun'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    // Validation Rules
    protected $validationRules = [
        'provinsi' => 'required|min_length[3]|max_length[100]',
        'nilai_ump' => 'required|numeric|greater_than[0]',
        'tahun' => 'required|numeric|exact_length[4]'
    ];

    protected $validationMessages = [
        'provinsi' => [
            'required' => 'Nama provinsi wajib diisi',
            'min_length' => 'Nama provinsi minimal 3 karakter'
        ],
        'nilai_ump' => [
            'required' => 'Nilai UMP wajib diisi',
            'numeric' => 'Nilai UMP harus berupa angka',
            'greater_than' => 'Nilai UMP harus lebih dari 0'
        ],
        'tahun' => [
            'required' => 'Tahun wajib diisi',
            'exact_length' => 'Tahun harus 4 digit'
        ]
    ];

    /**
     * Ambil semua data UMP urut berdasarkan nama provinsi
     */
    public function getAllOrderByProvinsi()
    {
        return $this->orderBy('provinsi', 'ASC')->findAll();
    }

    /**
     * Ambil data UMP berdasarkan tahun tertentu
     */
    public function getByTahun($tahun)
    {
        return $this->where('tahun', $tahun)
            ->orderBy('provinsi', 'ASC')
            ->findAll();
    }

    /**
     * Cari UMP berdasarkan nama provinsi (partial match)
     */
    public function searchByProvinsi($keyword)
    {
        return $this->like('provinsi', $keyword)
            ->orderBy('provinsi', 'ASC')
            ->findAll();
    }

    /**
     * Ambil UMP terbaru per provinsi (distinct provinsi, max tahun)
     */
    public function getLatestUmpPerProvinsi()
    {
        $db = \Config\Database::connect();
        $subquery = $db->table($this->table)
            ->select('provinsi, MAX(tahun) as max_tahun')
            ->groupBy('provinsi');

        return $db->table($this->table . ' u')
            ->select('u.*')
            ->join("({$subquery->getCompiledSelect()}) sub", 'u.provinsi = sub.provinsi AND u.tahun = sub.max_tahun')
            ->orderBy('u.provinsi', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Format nilai UMP ke format Rupiah
     */
    public static function formatRupiah($nilai)
    {
        return 'Rp ' . number_format($nilai, 0, ',', '.');
    }
}

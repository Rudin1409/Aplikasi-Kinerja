<?php

namespace App\Models;

use CodeIgniter\Model;

class MahasiswaModel extends Model
{
    protected $table = 'tb_m_mahasiswa';
    protected $primaryKey = 'nim';
    protected $useAutoIncrement = false; // NIM is not auto-increment
    protected $returnType = 'array';
    protected $allowedFields = [
        'nim',
        'nama_lengkap',
        'kode_prodi',
        'tahun_masuk',
        'status',
        'tanggal_yudisium', // <-- CRITICAL FIX: Required for IKU 2 update
        'nik',
        'semester_masuk',
        'jenis_kelamin',
        'email',
        'no_hp'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Helper to check if exists
    public function nimExists($nim)
    {
        return $this->where('nim', $nim)->countAllResults() > 0;
    }
}

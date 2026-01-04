<?php

namespace App\Models;

use CodeIgniter\Model;

class DosenModel extends Model
{
    protected $table = 'tb_m_dosen';
    protected $primaryKey = 'nidn';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = [
        'nidn',
        'nama_lengkap',
        'kode_prodi',
        'homebase', // Assuming homebase/prodi relation
        'gelar_depan',
        'gelar_belakang',
        'email',
        'no_hp'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}

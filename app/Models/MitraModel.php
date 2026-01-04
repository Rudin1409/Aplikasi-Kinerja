<?php

namespace App\Models;

use CodeIgniter\Model;

class MitraModel extends Model
{
    protected $table = 'tb_m_mitra';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'nama_mitra',
        'bidang_usaha', // or 'bidang'
        'alamat',
        'kota',
        'provinsi',
        'penanggung_jawab',
        'jabatan_penanggung_jawab',
        'no_telp',
        'email',
        'jenis_kerjasama'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}

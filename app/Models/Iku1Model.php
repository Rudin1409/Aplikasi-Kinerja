<?php

namespace App\Models;

use CodeIgniter\Model;

class Iku1Model extends Model
{
    protected $table = 'tb_iku_1_lulusan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'nim',
        'id_triwulan',
        'tanggal_yudisium',
        'masa_studi_bulan',
        'status_kelulusan'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = ''; // No updated_at in schema for this table
}

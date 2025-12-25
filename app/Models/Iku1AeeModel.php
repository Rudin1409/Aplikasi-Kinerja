<?php

namespace App\Models;

use CodeIgniter\Model;

class Iku1AeeModel extends Model
{
    protected $table = 'iku_1_aee';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'triwulan_id',
        'prodi',
        'jenjang',
        'tahun_masuk',
        'jml_mhs_masuk',
        'jml_lulus_tepat_waktu',
        'aee_realisasi',
        'aee_ideal',
        'capaian'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}

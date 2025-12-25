<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterIkuModel extends Model
{
    protected $table = 'master_iku';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['kode', 'sasaran', 'indikator', 'jenis', 'tabel_tujuan'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}

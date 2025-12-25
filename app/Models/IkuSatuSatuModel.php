<?php

namespace App\Models;

use CodeIgniter\Model;

class IkuSatuSatuModel extends Model
{
    protected $table = 'iku_satu_satu';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'triwulan_id',
        'nama',
        'nim',
        'prodi',
        'no_ijazah',
        'tanggal_ijazah',
        'tahun_lulus',
        'nik',
        'no_telp',
        'email',
        'status',
        'nama_tempat',
        'pendapatan',
        'ump',
        'tanggal_mulai',
        'masa_tunggu',
        'tingkat',
        'link_bukti',
        'point'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}

<?php
namespace App\Models;

use CodeIgniter\Model;

class KampusModel extends Model
{
    protected $table = 'kampus';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'jumlah_mahasiswa_aktif', 'jumlah_lulusan_satu_tahun', 'jumlah_dosen'];
    protected $useTimestamps = false;

    public function getInfo()
    {
        return $this->asArray()->first();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * IKU 2 Lulusan Model
 * 
 * Handles data for IKU 2: Mahasiswa Berkegiatan / Tracer Study
 * Tracks graduate activities: Working, Entrepreneurship, Further Study, Job Seeking
 */
class Iku2LulusanModel extends Model
{
    protected $table = 'tb_iku_2_lulusan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    /**
     * IMPORTANT: This list MUST match the database columns exactly
     * The DataException error occurs when fields are not in this list
     */
    protected $allowedFields = [
        'nim',
        'id_triwulan',
        'jenis_aktivitas',
        'nama_tempat',
        'provinsi_tempat_kerja',
        'tanggal_mulai',
        'gaji_bulan',
        'masa_tunggu_bulan',
        'posisi_wirausaha',
        'bukti_validasi',
        'status_validasi',
        'nilai_bobot',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'nim' => 'required|max_length[20]',
        'jenis_aktivitas' => 'required|in_list[Bekerja,Wirausaha,Lanjut Studi,Mencari Kerja]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get IKU 2 data with related information
     */
    public function getWithDetails($id_triwulan = null)
    {
        $builder = $this->db->table($this->table)
            ->select('tb_iku_2_lulusan.id as main_id, tb_iku_2_lulusan.*, tb_m_mahasiswa.nama_lengkap, tb_m_mahasiswa.tahun_masuk, prodi.nama_prodi, prodi.jenjang, tb_ref_ump.provinsi, tb_ref_ump.nilai_ump')
            ->join('tb_m_mahasiswa', 'tb_m_mahasiswa.nim = tb_iku_2_lulusan.nim', 'left')
            ->join('prodi', 'tb_m_mahasiswa.kode_prodi = prodi.kode_prodi', 'left')
            ->join('tb_ref_ump', 'tb_ref_ump.id = tb_iku_2_lulusan.provinsi_tempat_kerja', 'left');

        if ($id_triwulan) {
            $builder->where('tb_iku_2_lulusan.id_triwulan', $id_triwulan);
        }

        return $builder->orderBy('tb_iku_2_lulusan.id', 'DESC')->get()->getResultArray();
    }
}

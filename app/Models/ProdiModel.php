<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdiModel extends Model
{
    protected $table = 'prodi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['jurusan_id', 'kode_prodi', 'nama_prodi', 'jenjang', 'lokasi', 'jumlah_mahasiswa_aktif', 'jumlah_dosen', 'status', 'created_at', 'updated_at', 'deleted_at'];
    protected $useTimestamps = true;

    /**
     * Ambil semua prodi dan sertakan kode/nama jurusan (join ke tabel jurusan)
     * Mengembalikan array of [kode_prodi, nama_prodi, jenjang, jurusan_kode, nama_jurusan, jurusan_id]
     */
    public function getAllWithJurusan()
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, j.kode_jurusan AS jurusan_kode, j.nama_jurusan AS nama_jurusan');
        $builder->join('jurusan j', 'j.id = p.jurusan_id', 'left');
        $builder->orderBy('j.kode_jurusan', 'ASC')->orderBy('p.nama_prodi', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Ambil prodi berdasarkan kode jurusan (kode_jurusan)
     */
    public function getByJurusanKode($kode_jurusan)
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, j.kode_jurusan AS jurusan_kode, j.nama_jurusan AS nama_jurusan');
        $builder->join('jurusan j', 'j.id = p.jurusan_id', 'left');
        $builder->where('j.kode_jurusan', $kode_jurusan);
        $builder->orderBy('p.nama_prodi', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Cari prodi berdasarkan nilai `kode_prodi` persis
     */
    public function findByKodeProdi($kode_prodi)
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, j.kode_jurusan AS jurusan_kode, j.nama_jurusan AS nama_jurusan');
        $builder->join('jurusan j', 'j.id = p.jurusan_id', 'left');
        $builder->where('p.kode_prodi', $kode_prodi);
        return $builder->get()->getRowArray();
    }
}

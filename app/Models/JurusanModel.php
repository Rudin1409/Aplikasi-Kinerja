<?php

namespace App\Models;

use CodeIgniter\Model;

class JurusanModel extends Model
{
    protected $table = 'jurusan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode_jurusan', 'nama_jurusan', 'lokasi', 'status', 'created_at', 'updated_at', 'deleted_at'];
    protected $useTimestamps = true;

    /**
     * Kembalikan daftar jurusan sebagai array ['kode' => 'J01', 'nama' => 'Teknik Sipil']
     */
    public function getList()
    {
        $rows = $this->orderBy('kode_jurusan', 'ASC')->findAll();
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id' => $r['id'],
                'kode' => $r['kode_jurusan'],
                'nama' => $r['nama_jurusan']
            ];
        }
        return $out;
    }

    /**
     * Kembalikan peta kode => nama, contohnya ['J01' => 'Teknik Sipil']
     */
    public function getMap()
    {
        $rows = $this->orderBy('kode_jurusan', 'ASC')->findAll();
        $map = [];
        foreach ($rows as $r) {
            $map[$r['kode_jurusan']] = $r['nama_jurusan'];
        }
        return $map;
    }

    /**
     * Cari jurusan berdasarkan kode_jurusan, kembalikan satu baris atau null
     */
    public function findByKode($kode)
    {
        return $this->where('kode_jurusan', $kode)->first();
    }
}

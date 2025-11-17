<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    // Sesuaikan dengan tabel baru kita
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    
    // Kolom yang boleh diisi
    protected $allowedFields = ['nama_lengkap', 'email', 'password', 'role', 'status', 'relasi_kode', 'avatar'];

    // Aktifkan timestamp
    protected $useTimestamps    = true;

    // Sembunyikan password saat data diambil
    protected $hidden           = ['password'];

    // Callback untuk HASHING PASSWORD secara otomatis
    protected $beforeInsert     = ['hashPassword'];
    protected $beforeUpdate     = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            // Hash password hanya jika ada password baru dan tidak kosong
            if (!empty($data['data']['password']) && strlen($data['data']['password']) > 0) {
                 $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            } else {
                // Jika password kosong (misal saat edit profil tanpa ubah password), hapus dari data
                unset($data['data']['password']);
            }
        }
        return $data;
    }
}
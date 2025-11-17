<?php

namespace App\Controllers;

use App\Controllers\BaseController; 
use App\Models\UserModel; // Panggil Model

class Auth extends BaseController 
{
    
    // Fungsi untuk menampilkan halaman pilih role
    public function selectRole()
    {
        return view('auth/role_selection');
    }

    // ===== INI ADALAH PERBAIKAN UTAMA =====
    // Fungsi ini harus memuat VIEW, bukan ECHO TEXT
    public function login($role = null)
    {
        if (empty($role)) { 
            return redirect()->to('/'); 
        }
        $validRoles = ['admin', 'prodi', 'jurusan', 'pimpinan'];
        if (!in_array($role, $validRoles)) { 
            return redirect()->to('/'); 
        }
        
        $data = ['role' => $role];
        
        // Memuat file 'auth/login_form.php' dan mengirim data 'role'
        return view('auth/login_form', $data); 
    }
    // ===== AKHIR PERBAIKAN =====

    // Fungsi untuk memproses data login
    // GANTI TOTAL FUNGSI processLogin() DENGAN INI
    public function processLogin()
    {
        $email = $this->request->getPost('email'); 
        $password = $this->request->getPost('password');
        $role_form = $this->request->getPost('role'); // Role dari form (hidden input)

        $model = new UserModel();
        $user = $model->where('email', $email)->first();

        // Cek User, Password, dan Status
        if ($user && $user['status'] == 'aktif' && password_verify($password, $user['password'])) {
            
            // VALIDASI BARU: Cek apakah role user sesuai dengan form login yang digunakan
            if ($user['role'] !== $role_form) {
                return redirect()->back()->with('error', 'Anda tidak dapat login di form ini. Silakan gunakan form login sesuai role akun Anda: ' . strtoupper($user['role']) . '.');
            }
            
            // 1. Set session DARI DATABASE (TERMASUK relasi_kode)
            $session = \Config\Services::session();
            $session->set([
                'isLoggedIn'   => true,
                'role'         => $user['role'],
                'nama_lengkap' => $user['nama_lengkap'],
                'email'        => $user['email'],
                'user_id'      => $user['id'],
                'relasi_kode'  => $user['relasi_kode'],
                'avatar'       => $user['avatar'] ?? null // Simpan avatar path
            ]);

            // 2. Arahkan ke dashboard yang sesuai (Logika Baru)
            return $this->_handleLoginRedirect($user);

        } else {
            // 3. Jika login GAGAL
            return redirect()->back()->with('error', 'Email, Password, atau Status akun salah.');
        }
    }
    // FUNGSI BARU: Logika Pengarahan Dashboard
    private function _handleLoginRedirect($user)
    {
        $role = $user['role'];
        
        if ($role == 'admin' || $role == 'pimpinan') {
            // Admin dan Pimpinan ke Dashboard Utama
            return redirect()->to('admin/dashboard');

        } elseif ($role == 'jurusan') {
            // Jurusan diarahkan langsung ke Laporan Capaian Prodi yang difilter
            if (empty($user['relasi_kode'])) {
                return redirect()->back()->with('error', 'Akun Jurusan tidak terhubung ke kode Jurusan.');
            }
            // Arahkan ke /admin/prodi-capaian/J03 (atau kode jurusan lainnya)
            return redirect()->to('admin/prodi-capaian/' . $user['relasi_kode']);

        } elseif ($role == 'prodi') {
            // Prodi diarahkan ke fungsi perantara yang akan menerima 3 parameter
            if (empty($user['relasi_kode'])) {
                return redirect()->back()->with('error', 'Akun Prodi tidak terhubung ke relasi Prodi.');
            }
            
            // SOLUSI: Arahkan ke fungsi 'admin/prodi-dashboard-redirect' yang akan memecah kode relasi
            return redirect()->to('admin/prodi-dashboard-redirect');
            
        } else {
            return redirect()->to('/');
        }
    }

    // FUNGSI BARU UNTUK LOGOUT (Perbaikan Session)
    public function logout()
    {
        // Panggil service session
        $session = \Config\Services::session();
        
        // Hapus variabel-variabel kunci terlebih dahulu
        $session->remove(['isLoggedIn', 'role', 'email', 'user_id', 'nama_lengkap']); 
        
        // Hancurkan seluruh data session
        $session->destroy();
        
        // Arahkan kembali ke halaman awal (pilih role)
        return redirect()->to('/');
    }
}
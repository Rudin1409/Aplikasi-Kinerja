<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// == RUTE APLIKASI KINERJA (YANG KITA BUAT) ==

// 1. Rute Autentikasi (Auth)
$routes->get('/', 'Auth::selectRole'); // Halaman pemilihan role
$routes->get('auth/login/(:segment)', 'Auth::login/$1'); // Halaman form login
$routes->post('auth/processLogin', 'Auth::processLogin'); // Rute untuk proses login
$routes->get('logout', 'Auth::logout');

// 1a. Rute Avatar (Streaming dari writable/uploads/avatars)
$routes->get('avatar/(:segment)', 'AdminController::avatar/$1');


/*
 * 2. Rute Khusus Admin
 * --------------------------------------------------------------------
 */
// Menambahkan filter 'admin-auth' yang sudah kita buat
$routes->group('admin', ['filter' => 'admin-auth'], static function ($routes) {
    
    // == HALAMAN DASHBOARD ==
    // URL: /admin/dashboard
    $routes->get('dashboard', 'AdminController::dashboard');
    
    // == HALAMAN LAPORAN CAPAIAN (DRILL-DOWN) ==
    // URL: /admin/jurusan-capaian (INI RUTE BARU)
    $routes->get('jurusan-capaian', 'AdminController::jurusanCapaian');
    
    // URL: /admin/prodi-capaian/J01 (INI RUTE BARU)
    $routes->get('prodi-capaian/(:segment)', 'AdminController::prodiCapaian/$1');
    
    // RUTE BARU: Landing Page untuk Prodi setelah login
    $routes->get('prodi-dashboard-redirect', 'AdminController::prodiDashboardRedirect');
    
    
    // == HALAMAN MASTER DATA ==
    // URL: /admin/jurusan
    $routes->get('jurusan', 'AdminController::jurusan');
    
    // URL: /admin/prodi
    $routes->get('prodi', 'AdminController::prodi');
    $routes->post('prodi/save', 'AdminController::saveProdi');
    
    $routes->get('iku-prodi/(:segment)/(:segment)/(:segment)', 'AdminController::ikuProdi/$1/$2/$3');
    // --- (Rute Admin Lainnya (Placeholder)) ---
    $routes->get('iku-detail/(:segment)/(:segment)/(:segment)/(:segment)', 'AdminController::ikuDetail/$1/$2/$3/$4');
    // URL: /admin/user
    // $routes->get('user', 'AdminController::user');
    // URL: /admin/akun (Menggantikan IKU)
    $routes->get('akun', 'AdminController::akun');

    $routes->get('iku', 'AdminController::iku');

    // URL: /admin/panduan (Menggantikan Tahun)
    $routes->get('panduan', 'AdminController::panduan');

    $routes->get('laporan', 'AdminController::laporan');

    // URL: /admin/user
    $routes->get('user', 'AdminController::user');
    $routes->post('user/save', 'AdminController::saveUser');

    // URL: /admin/pengaturan (Pengaturan dengan 3 tab)
    $routes->get('pengaturan', 'AdminController::pengaturan');
    $routes->post('pengaturan/update-profil', 'AdminController::updateProfil');
    $routes->post('pengaturan/update-password', 'AdminController::updatePassword');
    $routes->post('pengaturan/update-tampilan', 'AdminController::updateTampilan');
    $routes->post('pengaturan/update', 'AdminController::updatePengaturan'); // Legacy (deprecated)

    
    // URL: /admin/iku
    // $routes->get('iku', 'AdminController::iku');
});


/*
 * --------------------------------------------------------------------
 * Additional Routing (Bagian standar CI4)
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
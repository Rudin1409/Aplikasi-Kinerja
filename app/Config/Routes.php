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

    // Rute Setup DB (Sementara)
    $routes->get('setup_db', 'AdminController::setup_db');

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
    // URL: /admin/jurusan/J01 (detail jurusan)
    $routes->get('jurusan/(:segment)', 'AdminController::jurusanDetail/$1');

    // URL: /admin/prodi
    $routes->get('prodi', 'AdminController::prodi');
    $routes->post('prodi/save', 'AdminController::saveProdi');
    $routes->get('prodi-edit/(:num)', 'AdminController::prodiEdit/$1');
    $routes->post('prodi-update/(:num)', 'AdminController::prodiUpdate/$1');
    $routes->post('prodi-delete/(:num)', 'AdminController::prodiDelete/$1');

    $routes->get('iku-prodi/(:segment)/(:segment)/(:segment)', 'AdminController::ikuProdi/$1/$2/$3');
    // --- (Rute Admin Lainnya (Placeholder)) ---
    // URL: /admin/iku-input/1/J01/...
    $routes->get('iku-input/(:segment)/(:segment)/(:segment)/(:segment)', 'AdminController::ikuInput/$1/$2/$3/$4');
    $routes->post('iku-save/(:segment)', 'AdminController::ikuSave/$1');

    $routes->get('iku-detail/(:segment)/(:segment)/(:segment)/(:segment)', 'AdminController::ikuDetail/$1/$2/$3/$4');
    // URL: /admin/user
    // $routes->get('user', 'AdminController::user');
    // URL: /admin/akun (Menggantikan IKU)
    $routes->get('akun', 'AdminController::akun');
    $routes->get('akun-edit', 'AdminController::akunEdit');
    $routes->post('akun-save', 'AdminController::akunSave');

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

    // == IMPORT IKU 1 ==
    $routes->get('iku1/import', 'ImportIku1::index');
    $routes->post('iku1/preview', 'ImportIku1::preview');
    $routes->post('iku1/save_data', 'ImportIku1::save_data');
    $routes->get('iku1/download_template', 'ImportIku1::download_template');
    $routes->get('iku1/export', 'ImportIku1::export');
    $routes->get('setup-iku2-db', 'AdminController::setup_iku2_db');

    // == INPUT MANUAL IKU 1 ==
    // Use (:any) to catch optional parameters (Jurusan/Prodi/Jenjang)
    $routes->get('iku1/input', 'Iku1Controller::create');
    $routes->get('iku1/input/(:any)', 'Iku1Controller::create/$1');
    $routes->post('iku1/store', 'Iku1Controller::store');
    $routes->get('iku1/edit/(:num)', 'Iku1Controller::edit/$1');
    $routes->post('iku1/update/(:num)', 'Iku1Controller::update/$1');
    $routes->get('iku1/check_nim/(:any)', 'Iku1Controller::check_nim/$1');
    $routes->get('iku1/delete/(:num)', 'Iku1Controller::delete/$1');
    $routes->get('iku1/dashboard', 'Iku1Controller::index');
});


/*
 * --------------------------------------------------------------------
 * Additional Routing (Bagian standar CI4)
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
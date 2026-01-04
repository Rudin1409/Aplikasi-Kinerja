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
$routes->get('setup-iku2-final', 'AdminController::setup_iku2_final_db');



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

    // URL: /admin/mahasiswa
    $routes->get('mahasiswa', 'MahasiswaController::index');
    $routes->get('mahasiswa/create', 'MahasiswaController::create');
    $routes->post('mahasiswa/store', 'MahasiswaController::store');
    $routes->get('mahasiswa/edit/(:segment)', 'MahasiswaController::edit/$1');
    $routes->post('mahasiswa/update/(:segment)', 'MahasiswaController::update/$1');
    $routes->get('mahasiswa/delete/(:segment)', 'MahasiswaController::delete/$1');
    $routes->get('mahasiswa/import', 'MahasiswaController::import');
    $routes->get('mahasiswa/download-template', 'MahasiswaController::downloadTemplate');
    $routes->post('mahasiswa/preview-import', 'MahasiswaController::previewImport');
    $routes->post('mahasiswa/save-import', 'MahasiswaController::saveImport');
    $routes->post('mahasiswa/bulk-delete', 'MahasiswaController::bulkDelete');
    $routes->post('mahasiswa/export-selected', 'MahasiswaController::exportSelected');
    $routes->post('mahasiswa/process-import', 'MahasiswaController::processImport');
    $routes->get('mahasiswa/export', 'MahasiswaController::export');

    // URL: /admin/dosen
    $routes->get('dosen', 'DosenController::index');
    $routes->get('dosen/create', 'DosenController::create');
    $routes->post('dosen/store', 'DosenController::store');
    $routes->get('dosen/edit/(:segment)', 'DosenController::edit/$1');
    $routes->post('dosen/update/(:segment)', 'DosenController::update/$1');
    $routes->get('dosen/delete/(:segment)', 'DosenController::delete/$1');
    $routes->get('dosen/import', 'DosenController::import');
    $routes->get('dosen/download-template', 'DosenController::downloadTemplate');
    $routes->post('dosen/preview-import', 'DosenController::previewImport');
    $routes->post('dosen/save-import', 'DosenController::saveImport');
    $routes->post('dosen/bulk-delete', 'DosenController::bulkDelete');
    $routes->post('dosen/export-selected', 'DosenController::exportSelected');
    $routes->post('dosen/process-import', 'DosenController::processImport');
    $routes->get('dosen/export', 'DosenController::export');

    // URL: /admin/mitra
    $routes->get('mitra', 'MitraController::index');
    $routes->get('mitra/create', 'MitraController::create');
    $routes->post('mitra/store', 'MitraController::store');
    $routes->get('mitra/edit/(:segment)', 'MitraController::edit/$1');
    $routes->post('mitra/update/(:segment)', 'MitraController::update/$1');
    $routes->get('mitra/delete/(:segment)', 'MitraController::delete/$1');
    $routes->get('mitra/import', 'MitraController::import');
    $routes->get('mitra/download-template', 'MitraController::downloadTemplate');
    $routes->post('mitra/preview-import', 'MitraController::previewImport');
    $routes->post('mitra/save-import', 'MitraController::saveImport');
    $routes->post('mitra/bulk-delete', 'MitraController::bulkDelete');
    $routes->post('mitra/export-selected', 'MitraController::exportSelected');
    $routes->post('mitra/process-import', 'MitraController::processImport');
    $routes->get('mitra/export', 'MitraController::export');


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


    // == INPUT MANUAL IKU 1 ==
    // Use (:any) to catch optional parameters (Jurusan/Prodi/Jenjang)
    $routes->get('iku1/input', 'Iku1Controller::create');
    $routes->get('iku1/input/(:any)', 'Iku1Controller::create/$1');
    $routes->post('iku1/store', 'Iku1Controller::store');
    $routes->get('iku1/edit/(:num)', 'Iku1Controller::edit/$1');
    $routes->post('iku1/update/(:num)', 'Iku1Controller::update/$1');
    $routes->get('iku1/check_nim/(:any)', 'Iku1Controller::check_nim/$1');
    $routes->get('iku1/delete/(:num)', 'Iku1Controller::delete/$1');
    $routes->post('iku1/bulk_delete', 'Iku1Controller::bulk_delete');
    // $routes->get('iku1/dashboard', 'Iku1Controller::index'); // REMOVED: Legacy Dashboard

    // == INPUT MANUAL IKU 2 (Lulusan Bekerja) ==
    // == INPUT MANUAL IKU 2 (Lulusan Bekerja) - FINAL ==
    $routes->get('iku2/dashboard', 'Iku2Lulusan::index'); // Dashboard IKU 2
    $routes->get('iku2/input', 'Iku2Lulusan::create');
    $routes->get('iku2/input/(:segment)/(:segment)/(:segment)', 'Iku2Lulusan::create/$1/$2/$3'); // Context Route
    $routes->get('iku2/input/(:any)', 'Iku2Lulusan::create/$1'); // Fallback
    $routes->post('iku2/store-final', 'Iku2Lulusan::save'); // Renamed store to match form action
    $routes->get('iku2/get_ump/(:num)', 'Iku2Lulusan::get_ump/$1');
    $routes->get('iku2/check_nim/(:any)', 'Iku2Lulusan::check_nim/$1');

    // Legacy or Other Actions if needed
    $routes->get('iku2/edit/(:num)', 'Iku2Lulusan::edit/$1'); // Updated to use Iku2Lulusan controller
    $routes->post('iku2/update/(:num)', 'Iku2Lulusan::save'); // Use same save method
    $routes->get('iku2/delete/(:num)', 'Iku2Controller::delete/$1');
    $routes->post('iku2/bulk_delete', 'Iku2Lulusan::bulk_delete');

    // == IMPORT IKU 2 ==
    $routes->get('iku2/import', 'ImportIku2::index');
    $routes->post('iku2/preview', 'ImportIku2::preview');
    $routes->post('iku2/save_data', 'ImportIku2::save_data');
    $routes->get('iku2/download_template', 'ImportIku2::download_template');
    $routes->get('iku2/export', 'ImportIku2::export');

    // == MANAJEMEN DATA UMP (Upah Minimum Provinsi) ==
    $routes->get('ump', 'RefUmp::index');              // GET: List all UMP (JSON)
    $routes->get('ump/(:num)', 'RefUmp::show/$1');     // GET: Single UMP by ID
    $routes->post('ump/store', 'RefUmp::store');       // POST: Create/Update UMP
    $routes->get('ump/delete/(:num)', 'RefUmp::delete/$1');
    $routes->delete('ump/delete/(:num)', 'RefUmp::delete/$1'); // DELETE: Remove UMP
    $routes->get('ump/search', 'RefUmp::search');      // GET: Search UMP
    $routes->post('ump/bulk-update', 'RefUmp::bulkUpdate'); // POST: Bulk Update UMP
});


/*
 * --------------------------------------------------------------------
 * Additional Routing (Bagian standar CI4)
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
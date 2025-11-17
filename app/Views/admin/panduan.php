<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Panduan Pengguna Sistem</h1>
            <p class="text-gray-600">Unduh dokumen panduan penggunaan Aplikasi Pengukuran Kinerja (APKP).</p>
        </div>
    </div>

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg">
        <div class="flex items-center space-x-6">
            <div>
                <ion-icon name="book-outline" class="text-7xl text-purple-600"></ion-icon>
            </div>
            
            <div class="flex-1">
                <h2 class="text-xl font-semibold text-gray-800">Panduan Lengkap APKP</h2>
                <p class="text-gray-600 mb-4">Dokumen ini berisi petunjuk lengkap untuk semua role pengguna, mulai dari admin, jurusan, prodi, hingga pimpinan.</p>
                
                <a href="<?= base_url('assets/dokumen/panduan.pdf') ?>" download
                   class="inline-flex items-center space-x-2 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    <ion-icon name="download-outline" class="text-xl"></ion-icon>
                    <span>Unduh Panduan (.pdf)</span>
                </a>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>
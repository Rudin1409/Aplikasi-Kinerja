<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Unduh Laporan</h1>
            <p class="text-gray-600">Unduh rekapitulasi laporan capaian kinerja per triwulan.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-blue-500">
            <ion-icon name="document-text-outline" class="text-5xl text-blue-500 mb-3"></ion-icon>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Laporan Triwulan 1</h2>
            <p class="text-sm text-gray-600 mb-4">Mencakup data dari Januari - Maret 2025.</p>
            <a href="#" download
               class="inline-flex items-center space-x-2 w-full justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                <ion-icon name="download-outline" class="text-xl"></ion-icon>
                <span>Unduh (.pdf)</span>
            </a>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-green-500">
            <ion-icon name="document-text-outline" class="text-5xl text-green-500 mb-3"></ion-icon>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Laporan Triwulan 2</h2>
            <p class="text-sm text-gray-600 mb-4">Mencakup data dari April - Juni 2025.</p>
            <a href="#" download
               class="inline-flex items-center space-x-2 w-full justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                <ion-icon name="download-outline" class="text-xl"></ion-icon>
                <span>Unduh (.pdf)</span>
            </a>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-yellow-500">
            <ion-icon name="document-text-outline" class="text-5xl text-yellow-500 mb-3"></ion-icon>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Laporan Triwulan 3</h2>
            <p class="text-sm text-gray-600 mb-4">Mencakup data dari Juli - September 2025.</p>
            <a href="#" download
               class="inline-flex items-center space-x-2 w-full justify-center bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                <ion-icon name="download-outline" class="text-xl"></ion-icon>
                <span>Unduh (.pdf)</span>
            </a>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-red-500">
            <ion-icon name="document-text-outline" class="text-5xl text-red-500 mb-3"></ion-icon>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Laporan Triwulan 4</h2>
            <p class="text-sm text-gray-600 mb-4">Mencakup data dari Oktober - Desember 2025.</p>
            <a href="#" download
               class="inline-flex items-center space-x-2 w-full justify-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                <ion-icon name="download-outline" class="text-xl"></ion-icon>
                <span>Unduh (.pdf)</span>
            </a>
        </div>
    </div>

<?= $this->endSection() ?>
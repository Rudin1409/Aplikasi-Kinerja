<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Master Data IKU</h1>
            <p class="text-gray-600">Daftar Indikator Kinerja Utama (IKU) yang digunakan.</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md w-full">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Daftar 8 IKU</h2>

        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">Kode IKU</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">Nama Indikator</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">Sasaran</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600 uppercase border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    
                    <?php foreach ($iku_list as $iku): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border-b font-medium"><?= $iku['kode'] ?></td>
                        <td class="px-4 py-3 border-b"><?= $iku['nama'] ?></td>
                        <td class="px-4 py-3 border-b text-sm text-gray-600"><?= $iku['sasaran'] ?></td>
                        <td class="px-4 py-3 border-b text-center">
                            <button class="text-blue-500 hover:text-blue-700 p-1" title="Edit Deskripsi">
                                <ion-icon name="create-outline" class="text-xl"></ion-icon>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                </tbody>
            </table>
        </div>
    </div>

<?= $this->endSection() ?>

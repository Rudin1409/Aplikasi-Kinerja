<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Master Data Jurusan</h1>
            <p class="text-gray-600">Kelola daftar jurusan di Politeknik Negeri Sriwijaya.</p>
        </div>
        <button class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition duration-300">
            <ion-icon name="add-outline" class="text-xl"></ion-icon>
            <span>Tambah Jurusan</span>
        </button>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md w-full">
        
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Daftar Jurusan</h2>

        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center space-x-2">
                <label for="showEntries" class="text-sm text-gray-600">Tampilkan</label>
                <select id="showEntries" class="border border-gray-300 rounded-md px-2 py-1 text-sm">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
                <span class="text-sm text-gray-600">entri</span>
            </div>
            <div class="relative">
                <input type="search" placeholder="Cari jurusan..." class="border border-gray-300 rounded-md pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <ion-icon name="search-outline"></ion-icon>
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">No</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">Kode Jurusan</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">Nama Jurusan</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600 uppercase border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    
                    <?php $no = 1; ?>
                    <?php foreach ($jurusan_list as $jurusan): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border-b"><?= $no++ ?></td>
                        <td class="px-4 py-3 border-b font-medium"><?= $jurusan['kode'] ?></td>
                        <td class="px-4 py-3 border-b"><?= $jurusan['nama'] ?></td>
                        <td class="px-4 py-3 border-b text-center">
                            <button class="text-blue-500 hover:text-blue-700 p-1" title="Edit">
                                <ion-icon name="create-outline" class="text-xl"></ion-icon>
                            </button>
                            <button class="text-red-500 hover:text-red-700 p-1" title="Hapus">
                                <ion-icon name="trash-outline" class="text-xl"></ion-icon>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center mt-4">
            <span class="text-sm text-gray-600">Menampilkan 1 dari 10 dari 10 entri</span>
            <div class="flex space-x-1">
                <button class="px-3 py-1 rounded-md border border-gray-300 text-sm text-gray-600 hover:bg-gray-100 disabled:opacity-50" disabled>Previous</button>
                <button class="px-3 py-1 rounded-md bg-purple-600 text-white border border-purple-600 text-sm">1</button>
                <button class="px-3 py-1 rounded-md border border-gray-300 text-sm text-gray-600 hover:bg-gray-100">Next</button>
            </div>
        </div>

    </div>

<?= $this->endSection() ?>
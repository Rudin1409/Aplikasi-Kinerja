<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="<?= base_url('admin/dashboard') ?>" 
               class="inline-flex items-center space-x-2 text-gray-600 hover:text-purple-700 transition duration-300 mb-2">
                <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
                <span>Kembali ke Dashboard</span>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">LAPORAN CAPAIAN KINERJA JURUSAN</h1>
        </div>
        
        <div>
            <label for="tahun" class="text-sm font-medium text-gray-700">TAHUN:</label>
            <select id="tahun" name="tahun" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option>2025</option>
                <option>2024</option>
                <option>2023</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">TOTAL JURUSAN</p>
                    <span class="text-3xl font-bold text-gray-800"><?= $total_jurusan ?> Jurusan</span>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <ion-icon name="business-outline" class="text-3xl text-purple-600"></ion-icon>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-teal-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">RATA-RATA CAPAIAN</p>
                    <span class="text-3xl font-bold text-gray-800"><?= $rata_rata_capaian ?>%</span>
                </div>
                <div class="p-3 bg-teal-100 rounded-full">
                    <ion-icon name="stats-chart-outline" class="text-3xl text-teal-600"></ion-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md w-full">
        
        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">No</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">Nama Jurusan</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b w-1/2">Persentase Capaian</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    
                    <?php $no = 1; ?>
                    <?php foreach ($jurusan_list as $jurusan): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border-b"><?= $no++ ?></td>
                        
                        <td class="px-4 py-3 border-b">
                            <a href="<?= base_url('admin/prodi-capaian/' . $jurusan['kode']) ?>" 
                               class="font-medium text-purple-700 hover:text-purple-900 transition duration-300">
                                <?= $jurusan['nama'] ?>
                            </a>
                        </td>

                        <td class="px-4 py-3 border-b">
                            <div class="flex items-center space-x-4">
                                <span class="text-base font-bold text-purple-700 w-16 text-right"><?= $jurusan['persentase'] ?>%</span>
                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div class="bg-purple-600 h-4 rounded-full" 
                                         style="width: <?= $jurusan['persentase'] ?>%">
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
            </table>
        </div>
    </div>

<?= $this->endSection() ?>
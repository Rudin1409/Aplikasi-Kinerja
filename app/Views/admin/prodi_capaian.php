<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

    <div class="flex justify-between items-center mb-6">
        <div>
            <?php if (session()->get('role') == 'admin'): ?>
            <a href="<?= base_url('admin/jurusan-capaian') ?>" 
               class="inline-flex items-center space-x-2 text-gray-600 hover:text-purple-700 transition duration-300 mb-2">
                <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
                <span>Kembali ke Laporan Jurusan</span>
            </a>
            <?php endif; ?>
            <h1 class="text-3xl font-bold text-gray-800">LAPORAN CAPAIAN KINERJA PROGRAM STUDI</h1>
            <h2 class="text-2xl font-semibold text-gray-700">JURUSAN <?= strtoupper($nama_jurusan) ?></h2>
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
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">TOTAL PROGRAM STUDI</p>
                    <span class="text-3xl font-bold text-gray-800"><?= $total_prodi ?> Prodi</span>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <ion-icon name="school-outline" class="text-3xl text-blue-600"></ion-icon>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">RATA-RATA CAPAIAN</p>
                    <span class="text-3xl font-bold text-gray-800"><?= $rata_rata_capaian ?>%</span>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <ion-icon name="stats-chart-outline" class="text-3xl text-green-600"></ion-icon>
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
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">Nama Program Studi</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b w-1/2">Persentase Capaian</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    
                    <?php if (empty($prodi_list)): ?>
                        <tr>
                            <td colspan="3" class="px-4 py-3 border-b text-center text-gray-500">Belum ada data program studi untuk jurusan ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($prodi_list as $prodi): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 border-b"><?= $no++ ?></td>
                            
                            <td class="px-4 py-3 border-b">
    <a href="<?= base_url('admin/iku-prodi/' . $jurusan_kode . '/' . rawurlencode($prodi['nama_prodi']) . '/' . $prodi['jenjang']) ?>"
       class="font-medium text-purple-700 hover:text-purple-900 transition duration-300">
        <?= $prodi['nama_prodi'] ?> (<?= $prodi['jenjang'] ?>)
    </a>
</td>

                            <td class="px-4 py-3 border-b">
                                <div class="flex items-center space-x-4">
                                    <span class="text-base font-bold text-blue-700 w-16 text-right"><?= $prodi['persentase'] ?>%</span>
                                    <div class="w-full bg-gray-200 rounded-full h-4">
                                        <div class="bg-blue-600 h-4 rounded-full" 
                                             style="width: <?= $prodi['persentase'] ?>%">
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
            </table>
        </div>
    </div>

<?= $this->endSection() ?>
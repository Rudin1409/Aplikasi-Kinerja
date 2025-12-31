<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard IKU 1 (Angka Efisiensi Edukasi)</h1>
            <p class="text-sm text-gray-600 mt-1">
                Data Capaian Kelulusan Tepat Waktu.
                <?php if ($triwulan_info): ?>
                    Periode Aktif: <span class="font-bold text-purple-600"><?= $triwulan_info['nama_triwulan'] ?></span>
                <?php else: ?>
                    <span class="text-red-500">(Tidak ada periode aktif)</span>
                <?php endif; ?>
            </p>
        </div>
        <div>
            <!-- Button Actions if needed -->
            <a href="<?= base_url('admin/iku1/import') ?>"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow-sm text-sm font-medium mr-2">
                <ion-icon name="document-text-outline" class="mr-1 inline-block align-middle"></ion-icon> Import Excel
            </a>
            <a href="<?= base_url('admin/iku1/input') ?>"
                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded shadow-sm text-sm font-medium">
                <ion-icon name="add-circle-outline" class="mr-1 inline-block align-middle"></ion-icon> Input Manual
            </a>
        </div>
    </div>

    <!-- SECTION 1: Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1: Total Lulusan -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-blue-500 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                    <ion-icon name="people-outline" class="text-2xl"></ion-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Lulusan</p>
                    <h3 class="text-2xl font-bold text-gray-800"><?= number_format($total_lulusan) ?></h3>
                </div>
            </div>
        </div>

        <!-- Card 2: Tepat Waktu -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-500 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                    <ion-icon name="checkmark-circle-outline" class="text-2xl"></ion-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Lulus Tepat Waktu</p>
                    <h3 class="text-2xl font-bold text-gray-800"><?= number_format($total_tepat) ?></h3>
                </div>
            </div>
        </div>

        <!-- Card 3: Terlambat -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-red-500 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500 mr-4">
                    <ion-icon name="alert-circle-outline" class="text-2xl"></ion-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Lulus Terlambat</p>
                    <h3 class="text-2xl font-bold text-gray-800"><?= number_format($total_terlambat) ?></h3>
                </div>
            </div>
        </div>

        <!-- Card 4: Avg Capaian -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-yellow-500 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                    <ion-icon name="trending-up-outline" class="text-2xl"></ion-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Rata-rata Capaian AEE</p>
                    <h3 class="text-2xl font-bold text-gray-800"><?= $avg_capaian ?>%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: Chart Visual -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm col-span-1">
            <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Komposisi Kelulusan</h3>
            <div class="relative h-64">
                <canvas id="gradStatusChart"></canvas>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm col-span-1 lg:col-span-2">
            <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Informasi Penilaian IKU 1</h3>
            <div class="space-y-4">
                <div class="flex items-start">
                    <div
                        class="flex-shrink-0 h-6 w-6 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 mt-1">
                        1</div>
                    <div class="ml-3">
                        <p class="font-bold text-gray-800">Sasaran Strategis</p>
                        <p class="text-sm text-gray-600">Meningkatnya kualitas lulusan pendidikan tinggi vokasi.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div
                        class="flex-shrink-0 h-6 w-6 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 mt-1">
                        2</div>
                    <div class="ml-3">
                        <p class="font-bold text-gray-800">Definisi Angka Efisiensi Edukasi (AEE)</p>
                        <p class="text-sm text-gray-600">
                            Persentase mahasiswa yang lulus tepat waktu sesuai jenjang studinya.
                        </p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div
                        class="flex-shrink-0 h-6 w-6 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 mt-1">
                        3</div>
                    <div class="ml-3">
                        <p class="font-bold text-gray-800">Target Ideal</p>
                        <ul class="list-disc list-inside text-sm text-gray-600 ml-1">
                            <li>Jenjang D3: <span class="font-semibold text-gray-800">33%</span></li>
                            <li>Jenjang D4/S1: <span class="font-semibold text-gray-800">25%</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 3: Drill Down Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-700">Detail Data Lulusan</h3>
            <!-- Optional Search/Filter placeholder -->
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mahasiswa</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prodi
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masa
                            Studi</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Periode</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($lulusan_list)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data lulusan untuk periode ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($lulusan_list as $index => $row): ?>
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $index + 1 ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?= esc($row['nama_lengkap']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">NIM: <?= esc($row['nim']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?= esc($row['jenjang']) ?>
                                    </span>
                                    <div class="text-sm text-gray-500 mt-1"><?= esc($row['nama_prodi']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <div class="font-medium"><?= $row['masa_studi_text'] ?></div>
                                    <div class="text-xs text-gray-500"><?= $row['masa_studi_bulan'] ?> Bulan</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>Masuk: <?= $row['tahun_masuk'] ?></div>
                                    <div>Lulus: <?= date('d M Y', strtotime($row['tanggal_yudisium'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($row['status_kelulusan'] == 'Tepat Waktu'): ?>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Tepat Waktu
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Terlambat
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('gradStatusChart').getContext('2d');
    const gradStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($chart_label) ?>,
            datasets: [{
                label: 'Jumlah Mahasiswa',
                data: <?= json_encode($chart_value) ?>,
                backgroundColor: [
                    '#10B981', // Emerald 500 (Green) for Tepat Waktu
                    '#EF4444'  // Red 500 for Terlambat
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
</script>

<?= $this->endSection() ?>
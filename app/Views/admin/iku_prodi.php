<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<div class="bg-white p-6 rounded-lg shadow-md mb-6">

    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">CAPAIAN IKU</h1>
            <h2 class="text-xl font-semibold text-gray-700">JURUSAN <?= strtoupper($nama_jurusan) ?></h2>
            <h3 class="text-lg font-medium text-gray-600">PRODI <?= strtoupper($nama_prodi) ?> (<?= $jenjang ?>)</h3>
        </div>
        <div>
            <label for="tahun" class="text-sm font-medium text-gray-700">TAHUN:</label>
            <select id="tahun" name="tahun"
                class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option value="2025">2025</option>
                <option value="2024">2024</option>
            </select>
        </div>
    </div>

    <hr class="my-4 border-gray-200">

    <div class="flex justify-between items-end">

        <div>
            <?php
            // Find selected triwulan name
            $selected_triwulan_name = 'Kinerja Triwulan';
            foreach ($triwulan_list as $tw) {
                if ($tw['id'] == $id_triwulan_selected) {
                    $triwulan_names = [
                        1 => 'Januari - Maret',
                        2 => 'April - Juni',
                        3 => 'Juli - September',
                        4 => 'Oktober - Desember'
                    ];
                    $periode = $triwulan_names[$tw['id']] ?? 'Periode Tidak Diketahui';
                    $selected_triwulan_name = 'Kinerja ' . $tw['nama_triwulan'] . ' (' . $periode . ' ' . (date('Y')) . ')';
                    break;
                }
            }
            ?>
            <h4 id="judul-triwulan" class="text-lg font-semibold text-gray-700 mb-3">
                <?= esc($selected_triwulan_name) ?>
            </h4>

            <div id="triwulan-buttons" class="flex rounded-md shadow-sm">
                <?php foreach ($triwulan_list as $index => $tw): ?>
                    <?php
                    $isActive = ($tw['id'] == $id_triwulan_selected);
                    $baseClass = "px-4 py-2 text-sm font-semibold border focus:z-10 focus:ring-2 focus:ring-purple-500 focus:text-purple-700";
                    $activeClass = "bg-purple-600 text-white border-purple-700 z-10 hover:bg-purple-700";
                    $inactiveClass = "bg-white text-gray-700 border-gray-300 hover:bg-gray-50 -ml-px";

                    // Rounded corners logic
                    if ($index === 0)
                        $baseClass .= " rounded-l-md";
                    if ($index === count($triwulan_list) - 1)
                        $baseClass .= " rounded-r-md";

                    $finalClass = $baseClass . ' ' . ($isActive ? $activeClass : $inactiveClass);
                    ?>
                    <a href="<?= current_url() . '?id_triwulan=' . $tw['id'] ?>" class="<?= $finalClass ?>">
                        <?= esc(str_replace('Triwulan ', 'TW ', $tw['nama_triwulan'])) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (session()->get('role') == 'admin'): ?>
            <div>
                <button
                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition duration-300">
                    <ion-icon name="lock-closed-outline" class="text-xl"></ion-icon>
                    <span>Kunci Data</span>
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

    <?php foreach ($iku_data as $iku): ?>
        <div
            class="relative group bg-white p-6 rounded-2xl shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
            <!-- Decorative Background Blob -->
            <div
                class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-gradient-to-br from-purple-100 to-transparent opacity-50 group-hover:scale-150 transition-transform duration-500">
            </div>

            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span
                            class="inline-block px-3 py-1 bg-purple-50 text-purple-700 text-xs font-bold rounded-full mb-2 border border-purple-100">
                            <?= $iku['kode'] ?>
                        </span>
                        <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                            <?= isset($iku['sasaran']) ? $iku['sasaran'] : '-' ?>
                        </h5>
                    </div>
                    <div
                        class="p-3 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl shadow-lg shadow-purple-200 text-white">
                        <ion-icon name="<?= $iku['icon'] ?>" class="text-2xl"></ion-icon>
                    </div>
                </div>

                <p class="text-sm font-medium text-gray-700 mb-6 line-clamp-2 h-10" title="<?= $iku['nama'] ?>">
                    <?= $iku['nama'] ?>
                </p>

                <div class="flex items-end space-x-2 mb-6">
                    <span class="text-5xl font-extrabold text-gray-800 tracking-tight">
                        <?= $iku['persentase'] ?><span class="text-2xl text-gray-400">%</span>
                    </span>
                    <!-- Dummy Trend (Bisa diganti logic real nanti) -->
                    <?php if ($iku['persentase'] >= 80): ?>
                        <span class="flex items-center text-sm text-green-500 font-bold bg-green-50 px-2 py-1 rounded-md mb-2">
                            <ion-icon name="trending-up-outline" class="mr-1"></ion-icon> +5%
                        </span>
                    <?php else: ?>
                        <span
                            class="flex items-center text-sm text-yellow-500 font-bold bg-yellow-50 px-2 py-1 rounded-md mb-2">
                            <ion-icon name="remove-outline" class="mr-1"></ion-icon> 0%
                        </span>
                    <?php endif; ?>
                </div>

                <?php
                // Mengambil angka IKU, misal "IKU 1.1" -> "1.1"
                $iku_code = str_replace('IKU ', '', $iku['kode']);
                ?>
                <a href="<?= base_url('admin/iku-detail/' . $iku_code . '/' . $jurusan_kode . '/' . rawurlencode($nama_prodi) . '/' . $jenjang) . '?id_triwulan=' . ($id_triwulan_selected ?? '') ?>"
                    class="group/btn flex items-center justify-between w-full bg-gray-50 hover:bg-gradient-to-r hover:from-purple-600 hover:to-indigo-600 hover:text-white text-gray-600 font-semibold py-3 px-5 rounded-xl transition-all duration-300 border border-gray-100">
                    <span>Lihat Detail</span>
                    <ion-icon name="arrow-forward-outline"
                        class="group-hover/btn:translate-x-1 transition-transform"></ion-icon>
                </a>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<?= $this->endSection() ?>
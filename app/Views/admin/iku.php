<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Master Data IKU</h1>
        <p class="text-gray-600">Daftar Indikator Kinerja Utama (IKU) yang digunakan.</p>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-md w-full">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Daftar Indikator Kinerja Utama</h2>

    <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse border border-gray-300">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold border border-gray-300 w-16">No.</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold border border-gray-300">Indikator Kinerja Utama
                    </th>
                    <th class="px-4 py-3 text-center text-sm font-semibold border border-gray-300 w-24">Wajib</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold border border-gray-300 w-24">Pilihan</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if (empty($iku_list)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            Data belum tersedia. Silakan jalankan <a href="<?= base_url('admin/setup_db') ?>"
                                class="text-blue-500 underline">Setup Database</a>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php
                    $currentSasaran = null;
                    foreach ($iku_list as $iku):
                        if ($iku['sasaran'] != $currentSasaran):
                            $currentSasaran = $iku['sasaran'];
                            ?>
                            <tr class="bg-blue-50">
                                <td colspan="4" class="px-4 py-3 border border-gray-300 font-bold text-blue-900">
                                    Sasaran: <?= $currentSasaran ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr class="hover:bg-gray-50 border border-gray-300">
                            <td class="px-4 py-3 border border-gray-300 font-bold align-top w-16 text-center">
                                <?= $iku['kode'] ?>
                            </td>
                            <td class="px-4 py-3 border border-gray-300 align-top">
                                <?= $iku['indikator'] ?>
                            </td>
                            <td class="px-4 py-3 border border-gray-300 text-center align-middle">
                                <?php if ($iku['jenis'] == 'Wajib'): ?>
                                    <ion-icon name="checkmark-sharp" class="text-2xl text-black font-bold"></ion-icon>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 border border-gray-300 text-center align-middle">
                                <?php if ($iku['jenis'] == 'Pilihan'): ?>
                                    <div class="w-4 h-4 bg-black rounded-full mx-auto"></div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
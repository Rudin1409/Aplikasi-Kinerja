<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<?php
// Defensive defaults for variables that may not be provided by controller
$iku_detail = $iku_detail ?? [];
$iku_title = $iku_title ?? ($iku_detail['iku'] ?? ($title ?? 'IKU Detail'));
$triwulan_text = $triwulan_text ?? '';
$tambah_button_text = $tambah_button_text ?? 'Tambah';
$table_headers = $table_headers ?? [];
$data_list = $data_list ?? [];
$back_url = $back_url ?? site_url('admin/iku-prodi/' . ($jurusan_kode ?? '') . '/' . rawurlencode($nama_prodi ?? '') . '/' . ($jenjang ?? ''));
?>

    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="<?= esc($back_url) ?>" class="inline-flex items-center space-x-2 text-gray-600 hover:text-purple-700 transition duration-300 mb-2">
                <ion-icon name="arrow-back-outline" class="text-xl"></ion-icon>
                <span>Kembali ke Capaian IKU</span>
            </a>

            <h1 class="text-3xl font-bold text-gray-800">DETAIL <?= esc($iku_title) ?></h1>
            <h2 class="text-lg font-medium text-gray-600">PRODI <?= esc(strtoupper($nama_prodi ?? '')) ?> (<?= esc($jenjang ?? '') ?>) - <?= esc($triwulan_text) ?></h2>
        </div>

        <?php if (session()->get('role') == 'admin'): ?>
        <button class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition duration-300">
            <ion-icon name="add-outline" class="text-xl"></ion-icon>
            <span><?= esc($tambah_button_text) ?></span>
        </button>
        <?php endif; ?>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md w-full">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Detail IKU</h2>

        <div class="mb-6">
            <div class="flex items-start justify-between gap-6">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold mb-1"><?= esc($iku_detail['iku'] ?? $iku_title) ?></h3>
                    <p class="text-sm text-gray-600"><?= nl2br(esc($iku_detail['deskripsi'] ?? '')) ?></p>
                </div>

                <div class="flex-shrink-0 grid grid-cols-2 gap-4">
                    <div class="bg-white border rounded-lg p-4 text-center shadow-sm">
                        <div class="text-sm text-gray-500">Nilai</div>
                        <div class="text-2xl font-bold text-gray-800 mt-1"><?= esc($iku_detail['nilai'] ?? '-') ?>%</div>
                    </div>
                    <div class="bg-white border rounded-lg p-4 text-center shadow-sm">
                        <div class="text-sm text-gray-500">Target</div>
                        <div class="text-2xl font-bold text-gray-800 mt-1"><?= esc($iku_detail['target'] ?? '-') ?>%</div>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="text-xl font-semibold text-gray-700 mb-4">Daftar Data</h2>
        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">No</th>
                        <?php foreach ($table_headers as $header): ?>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b"><?= esc($header) ?></th>
                        <?php endforeach; ?>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600 uppercase border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php $no = 1; ?>
                    <?php foreach ($data_list as $item): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border-b"><?= $no++ ?></td>
                        <?php foreach ($table_headers as $key => $header): ?>
                            <td class="px-4 py-3 border-b"><?= esc($item[$key] ?? 'N/A') ?></td>
                        <?php endforeach; ?>
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
                    <?php if (empty($data_list)): ?>
                        <tr>
                            <td colspan="<?= count($table_headers) + 2 ?>" class="text-center py-4 text-gray-500">Belum ada data untuk IKU ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <a class="btn btn-secondary" href="<?= esc($back_url) ?>">&larr; Kembali</a>
        </div>
    </div>

<?= $this->endSection() ?>
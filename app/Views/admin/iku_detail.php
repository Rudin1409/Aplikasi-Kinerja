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

<!-- Custom Style for this page -->
<style>
    .card-stat {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        color: white;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-stat .icon-bg {
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        height: 50px;
        width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-stat h3 {
        font-size: 0.85rem;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 5px;
        opacity: 0.9;
    }

    .card-stat .value {
        font-size: 2.5rem;
        font-weight: bold;
        line-height: 1;
    }

    .card-stat .desc {
        font-size: 0.75rem;
        opacity: 0.8;
        margin-top: 5px;
    }
</style>

<!-- Breadcrumb & Header -->
<div class="mb-6">
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <h1 class="text-xl font-bold text-gray-800 mb-1"><?= esc($iku_title) ?></h1>
        <nav class="text-sm text-gray-500">
            <a href="<?= base_url('admin/dashboard') ?>" class="hover:text-purple-600">Dashboard</a> /
            <a href="<?= esc($back_url) ?>" class="hover:text-purple-600">Capaian IKU</a> /
            <span class="text-gray-400"><?= esc($iku_detail['iku'] ?? 'Detail') ?></span>
        </nav>
    </div>
</div>

<!-- Statistik Cards (Top Row) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Card 1: Total Alumni -->
    <div class="card-stat bg-blue-600">
        <div>
            <h3>TOTAL ALUMNI</h3>
            <div class="value">1</div>
            <div class="desc">Data terinput triwulan ini</div>
        </div>
        <div class="icon-bg">
            <ion-icon name="people" class="text-2xl"></ion-icon>
        </div>
    </div>

    <!-- Card 2: Memenuhi IKU -->
    <div class="card-stat bg-green-500">
        <div>
            <h3>MEMENUHI IKU</h3>
            <div class="value">0</div>
            <div class="desc">Point capaian 1.0</div>
        </div>
        <div class="icon-bg">
            <ion-icon name="checkmark-circle" class="text-2xl"></ion-icon>
        </div>
    </div>

    <!-- Card 3: Capaian -->
    <div class="card-stat bg-blue-500">
        <div>
            <h3>CAPAIAN</h3>
            <div class="value"><?= esc($iku_detail['nilai'] ?? '0') ?>%</div>
            <div class="desc">Persentase keberhasilan</div>
        </div>
        <div class="icon-bg">
            <ion-icon name="pie-chart" class="text-2xl"></ion-icon>
        </div>
    </div>
</div>

<!-- Section Daftar Lulusan / Table -->
<div class="bg-white p-6 rounded-lg shadow-md w-full">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-bold text-gray-700">Daftar Lulusan</h2>

        <?php if (session()->get('role') == 'admin'): ?>
            <?php
            // Ambil kode angka bersih, misal IKU 1.1 -> 1.1 (ada di controller logic iku_code)
            // Kita extract dari iku_title atau parameter URL yang ada
            // Asumsi $iku_detail['iku'] berisi string kode (e.g., '1', '2')
            $current_iku_code = str_replace('IKU ', '', $iku_detail['iku'] ?? '1');
            ?>
            <a href="<?= site_url('admin/iku-input/' . $current_iku_code . '/' . ($jurusan_kode ?? '') . '/' . rawurlencode($nama_prodi ?? '') . '/' . ($jenjang ?? '')) ?>"
                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition duration-300">
                <ion-icon name="add-outline" class="text-xl"></ion-icon>
                <span><?= esc($tambah_button_text) ?></span>
            </a>
        <?php endif; ?>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-xs text-gray-500 uppercase border-b border-gray-100">
                    <th class="px-4 py-3 font-semibold">ALUMNI</th>
                    <th class="px-4 py-3 font-semibold">KELULUSAN</th>
                    <th class="px-4 py-3 font-semibold">STATUS</th>
                    <th class="px-4 py-3 font-semibold">TEMPAT</th>
                    <th class="px-4 py-3 font-semibold">PENDAPATAN</th>
                    <th class="px-4 py-3 font-semibold">MASA TUNGGU</th>
                    <th class="px-4 py-3 font-semibold text-center">POINT</th>
                    <th class="px-4 py-3 font-semibold text-center">BUKTI</th>
                    <th class="px-4 py-3 font-semibold text-center">AKSI</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                <?php if (empty($data_list)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-6 text-gray-400">Belum ada data tersedia.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data_list as $item): ?>
                        <tr class="hover:bg-gray-50 border-b border-gray-50 transition duration-150">
                            <!-- Kolom Alumni -->
                            <td class="px-4 py-4 align-top">
                                <div class="font-bold text-gray-800"><?= esc($item['nama_lulusan'] ?? '-') ?></div>
                                <div class="text-xs text-gray-500 mt-1"><?= esc($item['nim'] ?? '') ?></div>
                            </td>
                            <!-- Kolom Kelulusan -->
                            <td class="px-4 py-4 align-top">
                                <div class="font-semibold text-gray-700">0624012023000739</div>
                                <div class="text-xs text-gray-500 mt-1">2025</div>
                            </td>
                            <!-- Kolom Status -->
                            <td class="px-4 py-4 align-top">
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">
                                    <?= esc($item['status'] ?? 'Bekerja') ?>
                                </span>
                            </td>
                            <!-- Kolom Tempat -->
                            <td class="px-4 py-4 align-top">
                                <div class="font-medium text-gray-800">Polres Muratara</div>
                                <div class="text-xs text-gray-500 mt-1">23 Nov 2025</div>
                            </td>
                            <!-- Kolom Pendapatan -->
                            <td class="px-4 py-4 align-top">
                                <div class="font-bold text-gray-800">Rp 1.000.000</div>
                                <div class="text-xs text-gray-500 mt-1">UMP: Rp 3.627.622</div>
                            </td>
                            <!-- Kolom Masa Tunggu -->
                            <td class="px-4 py-4 align-top font-semibold text-green-600">
                                2 Bulan
                            </td>
                            <!-- Kolom Point -->
                            <td class="px-4 py-4 align-top text-center">
                                <span class="bg-red-500 text-white px-2 py-1 rounded text-xs font-bold">0.0</span>
                            </td>
                            <!-- Kolom Bukti -->
                            <td class="px-4 py-4 align-top text-center text-gray-400">
                                -
                            </td>
                            <!-- Kolom Aksi -->
                            <td class="px-4 py-4 align-top text-center">
                                <div class="inline-flex border border-gray-200 rounded-md overflow-hidden">
                                    <button class="bg-gray-50 hover:bg-gray-100 text-gray-600 p-1.5 transition">
                                        <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                    </button>
                                    <button
                                        class="bg-gray-50 hover:bg-red-50 text-red-500 p-1.5 border-l border-gray-200 transition">
                                        <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                                    </button>
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
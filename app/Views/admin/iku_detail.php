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
    <!-- Styled Breadcrumb -->
    <nav class="flex items-center text-sm text-gray-500 mb-4">
        <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center hover:text-purple-600 transition-colors">
            <ion-icon name="home-outline" class="mr-1.5 text-lg"></ion-icon>
            <span class="font-medium">Dashboard</span>
        </a>
        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>

        <a href="<?= esc($back_url) ?>" class="flex items-center hover:text-purple-600 transition-colors font-medium">
            <ion-icon name="stats-chart-outline" class="mr-1.5 text-lg"></ion-icon>
            <span>Capaian IKU</span>
        </a>
        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>

        <div
            class="flex items-center text-purple-700 bg-purple-50 px-3 py-1 rounded-full border border-purple-100 shadow-sm">
            <span class="font-bold text-xs mr-1">Active:</span>
            <span class="font-bold"><?= esc($iku_detail['iku'] ?? 'Detail') ?></span>
        </div>
    </nav>

    <div
        class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6 relative overflow-hidden group hover:shadow-md transition-all duration-300">
        <!-- Decorative bg blob -->
        <div
            class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-purple-50 opacity-50 blur-3xl group-hover:bg-purple-100 transition-all duration-500">
        </div>

        <div class="flex flex-col md:flex-row md:items-start md:justify-between relative z-10">
            <div>
                <div class="flex items-center space-x-2 mb-3">
                    <span
                        class="px-3 py-1 bg-gradient-to-r from-purple-500 to-indigo-600 text-white text-xs font-bold rounded-lg shadow-sm tracking-wide uppercase">
                        <?= esc($iku_detail['iku'] ?? 'IKU') ?> Detail
                    </span>
                    <?php if (!empty($breadcrumbs['jurusan']) && !empty($breadcrumbs['prodi'])): ?>
                        <div
                            class="flex items-center text-xs text-gray-500 font-medium bg-gray-50 px-2 py-1 rounded-md border border-gray-100">
                            <?= esc($breadcrumbs['jurusan']) ?>
                            <ion-icon name="chevron-forward-outline" class="mx-1 text-gray-300"></ion-icon>
                            <?= esc($breadcrumbs['prodi']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <h1 class="text-3xl font-extrabold text-gray-800 mb-3 tracking-tight leading-tight">
                    <?= esc($iku_detail['indikator'] ?? $iku_title) ?>
                </h1>

                <?php if (!empty($iku_detail['sasaran'])): ?>
                    <div class="mb-2 text-sm text-gray-600 flex items-start">
                        <ion-icon name="target-outline" class="mr-2 text-purple-500 mt-0.5"></ion-icon>
                        <span><span class="font-bold text-gray-700">Sasaran:</span>
                            <?= esc($iku_detail['sasaran']) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($iku_detail['deskripsi'])): ?>
                    <div
                        class="text-sm text-gray-500 italic bg-gray-50 p-3 rounded-lg border border-gray-100 inline-block max-w-3xl">
                        <span class="font-bold text-gray-600 not-italic mr-1">Ket:</span>
                        <?= esc($iku_detail['deskripsi']) ?>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Optional: Action button or filter could go here -->
        </div>
    </div>
    <!-- Old Navigation Removed -->
    </nav>
</div>

<!-- Statistik Cards (Top Row) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Card 1: Total Alumni -->
    <div class="card-stat bg-blue-600">
        <div>
            <h3>TOTAL ALUMNI</h3>
            <div class="value"><?= esc($iku_detail['total_data'] ?? '0') ?></div>
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
            <div class="value"><?= esc($iku_detail['total_memenuhi'] ?? '0') ?></div>
            <div class="desc">Point capaian 1.0</div>
        </div>
        <div class="icon-bg">
            <ion-icon name="checkmark-circle" class="text-2xl"></ion-icon>
        </div>
    </div>

    <!-- Card 3: Capaian -->
    <div class="card-stat bg-blue-500">
        <div>
            <h3 class="flex items-center">
                CAPAIAN
                <button type="button" onclick="openCalcModal()"
                    class="ml-2 text-white/70 hover:text-white transition focus:outline-none"
                    title="Lihat Detail Perhitungan">
                    <ion-icon name="information-circle" class="text-lg"></ion-icon>
                </button>
            </h3>
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

            // Tentukan Link Tambah Data
            // Jika IKU 1 (AEE), arahkan ke Input Manual IKU 1
            if (strpos($current_iku_code, '1') !== false) {
                $link_tambah = site_url('admin/iku1/input/' . ($jurusan_kode ?? '') . '/' . rawurlencode($nama_prodi ?? '') . '/' . ($jenjang ?? '')) . '?id_triwulan=' . ($id_triwulan ?? '');
            } else {
                // Default (IKU 2 dll) - Arahkan ke form lama atau generic
                $link_tambah = site_url('admin/iku-input/' . $current_iku_code . '/' . ($jurusan_kode ?? '') . '/' . rawurlencode($nama_prodi ?? '') . '/' . ($jenjang ?? ''));
            }
            ?>

            <div class="flex space-x-2">
                <!-- Tombol Import Excel (Khusus IKU 1) -->
                <?php if (strpos($current_iku_code, '1') !== false): ?>
                    <a href="<?= site_url('admin/iku1/import') . '?return_url=' . rawurlencode(base_url(uri_string()) . '?id_triwulan=' . ($id_triwulan ?? '')) . '&id_triwulan=' . ($id_triwulan ?? '') ?>"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition duration-300">
                        <ion-icon name="document-text-outline" class="text-xl"></ion-icon>
                        <span>Import Excel</span>
                    </a>
                <?php endif; ?>

                <a href="<?= $link_tambah ?>"
                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition duration-300">
                    <ion-icon name="add-outline" class="text-xl"></ion-icon>
                    <span><?= esc($tambah_button_text) ?></span>
                </a>

                <!-- Tombol Export Excel -->
                <?php if (strpos($current_iku_code, '1') !== false): ?>
                    <a href="<?= site_url('admin/iku1/export') . '?' . http_build_query(['kode_prodi' => $kode_prodi ?? '', 'id_triwulan' => $id_triwulan ?? '']) ?>"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition duration-300">
                        <ion-icon name="download-outline" class="text-xl"></ion-icon>
                        <span>Export Excel</span>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="overflow-x-auto">
        <table id="ikuDetailTable" class="w-full text-left border-collapse">
            <thead>
                <tr class="text-xs text-gray-500 uppercase border-b border-gray-100">
                    <?php if (!empty($table_headers)): ?>
                        <?php foreach ($table_headers as $key => $label): ?>
                            <th class="px-4 py-3 font-semibold text-left"><?= esc($label) ?></th>
                        <?php endforeach; ?>
                        <!-- Aksi column logic handled separately or included in headers? Assuming Aksi only for IKU 2 currently or handled manually -->
                        <?php if (isset($table_headers['alumni'])): ?>
                            <th class="px-4 py-3 font-semibold text-center">AKSI</th>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Fallback Default -->
                        <th class="px-4 py-3 font-semibold">Data</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                <?php if (empty($data_list)): ?>
                    <!-- Empty state handled by Datatables normally, but good to keep -->
                <?php else: ?>
                    <?php foreach ($data_list as $item): ?>
                        <tr class="hover:bg-gray-50 border-b border-gray-50 transition duration-150">

                            <?php if (isset($table_headers['alumni'])): ?>
                                <!-- LAYOUT KHUSUS IKU 2 (LULUSAN) -->
                                <td class="px-4 py-4 align-top">
                                    <div class="font-bold text-gray-800"><?= esc($item['nama_lulusan'] ?? '-') ?></div>
                                    <div class="text-xs text-gray-500 mt-1"><?= esc($item['nim'] ?? '') ?></div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="font-semibold text-gray-700"><?= esc($item['no_ijazah'] ?? '-') ?></div>
                                    <div class="text-xs text-gray-500 mt-1"><?= esc($item['tahun_lulus'] ?? '') ?></div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">
                                        <?= esc($item['status'] ?? '-') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="font-medium text-gray-800"><?= esc($item['tempat'] ?? '-') ?></div>
                                    <div class="text-xs text-gray-500 mt-1"><?= esc($item['tanggal_mulai'] ?? '') ?></div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="font-bold text-gray-800"><?= esc($item['pendapatan'] ?? '-') ?></div>
                                    <div class="text-xs text-gray-500 mt-1">UMP: <?= esc($item['ump'] ?? '-') ?></div>
                                </td>
                                <td class="px-4 py-4 align-top font-semibold text-green-600">
                                    <?= esc($item['masa_tunggu'] ?? '-') ?>
                                </td>
                                <td class="px-4 py-4 align-top text-center">
                                    <span
                                        class="bg-red-500 text-white px-2 py-1 rounded text-xs font-bold"><?= esc($item['point'] ?? '-') ?></span>
                                </td>
                                <td class="px-4 py-4 align-top text-center text-gray-400">
                                    <?= esc($item['bukti'] ?? '-') ?>
                                </td>
                                <!-- Aksi -->
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

                            <?php else: ?>
                                <!-- LAYOUT GENERIC (UNTUK IKU 1 DLL) -->
                                <?php foreach ($table_headers as $key => $label): ?>
                                    <td class="px-4 py-4 align-top">
                                        <!-- Allow HTML for 'capaian' key (badges etc needed) -->
                                        <!-- Allow HTML for specific keys (badges, formatted text) -->
                                        <?php if (in_array($key, ['capaian', 'nama', 'nim', 'status', 'aksi'])): ?>
                                            <?= $item[$key] ?? '-' ?>
                                        <?php else: ?>
                                            <?= esc($item[$key] ?? '-') ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- DataTables Scripts -->
<!-- CDN jQuery & DataTables (if not already in layout) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        $('#ikuDetailTable').DataTable({
            responsive: true,
            language: {
                search: "Cari Data:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 s/d 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    });
</script>

<!-- MODAL: Penjelasan Perhitungan IKU 1 (AEE) -->
<div id="calcModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeCalcModal()">
    </div>

    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <div
            class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full border border-gray-100">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <ion-icon name="calculator-outline" class="mr-2 text-2xl"></ion-icon>
                    Penjelasan Perhitungan IKU 1 (AEE)
                </h3>
                <button type="button" onclick="closeCalcModal()"
                    class="text-white hover:text-gray-200 focus:outline-none transition">
                    <ion-icon name="close-circle-outline" class="text-2xl"></ion-icon>
                </button>
            </div>

            <!-- Content -->
            <div class="px-6 py-6 space-y-6">
                <!-- 1. Realisasi -->
                <div class="bg-blue-50 rounded-xl p-5 border border-blue-100">
                    <h4 class="font-bold text-blue-800 mb-2 flex items-center text-sm uppercase tracking-wide">
                        <span
                            class="w-6 h-6 rounded-full bg-blue-200 text-blue-700 flex items-center justify-center text-xs mr-2 font-bold">1</span>
                        Realisasi (Persentase Lulusan)
                    </h4>
                    <div
                        class="text-center font-mono text-sm bg-white p-3 rounded border border-blue-100 mb-2 text-gray-700">
                        (Jumlah Lulusan Tepat Waktu ÷ Total Mahasiswa Angkatan) × 100%
                    </div>
                    <p class="text-xs text-gray-500">
                        *Dihitung per angkatan untuk setiap prodi berdasarkan data mahasiswa yang terdaftar pada tahun
                        masuk tersebut.
                    </p>
                </div>

                <!-- 2. Target Ideal -->
                <div class="bg-purple-50 rounded-xl p-5 border border-purple-100">
                    <h4 class="font-bold text-purple-800 mb-2 flex items-center text-sm uppercase tracking-wide">
                        <span
                            class="w-6 h-6 rounded-full bg-purple-200 text-purple-700 flex items-center justify-center text-xs mr-2 font-bold">2</span>
                        Target Ideal (Sesuai Jenjang)
                    </h4>
                    <div class="grid grid-cols-4 gap-2 text-center text-sm">
                        <div class="bg-white p-2 rounded border border-purple-100">
                            <div class="font-bold text-gray-800">D3</div>
                            <div class="text-purple-600 font-bold">33%</div>
                        </div>
                        <div class="bg-white p-2 rounded border border-purple-100">
                            <div class="font-bold text-gray-800">D4 / S1</div>
                            <div class="text-purple-600 font-bold">25%</div>
                        </div>
                        <div class="bg-white p-2 rounded border border-purple-100">
                            <div class="font-bold text-gray-800">S2</div>
                            <div class="text-purple-600 font-bold">50%</div>
                        </div>
                        <div class="bg-white p-2 rounded border border-purple-100">
                            <div class="font-bold text-gray-800">S3</div>
                            <div class="text-purple-600 font-bold">33%</div>
                        </div>
                    </div>
                </div>

                <!-- 3. Capaian Akhir -->
                <div class="bg-green-50 rounded-xl p-5 border border-green-100">
                    <h4 class="font-bold text-green-800 mb-2 flex items-center text-sm uppercase tracking-wide">
                        <span
                            class="w-6 h-6 rounded-full bg-green-200 text-green-700 flex items-center justify-center text-xs mr-2 font-bold">3</span>
                        Capaian Akhir (Kinerja)
                    </h4>
                    <div
                        class="text-center font-mono text-lg bg-white p-4 rounded border border-green-200 mb-2 text-green-700 font-bold shadow-sm">
                        (Realisasi ÷ Target Ideal) × 100%
                    </div>
                    <p class="text-xs text-center text-gray-500">
                        Nilai Capaian = Seberapa dekat realisasi Anda dengan target ideal yang ditetapkan.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                <button type="button" onclick="closeCalcModal()"
                    class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openCalcModal() {
        document.getElementById('calcModal').classList.remove('hidden');
    }

    function closeCalcModal() {
        document.getElementById('calcModal').classList.add('hidden');
    }

    // Close on Escape key
    document.addEventListener('keydown', function (event) {
        if (event.key === "Escape") {
            closeCalcModal();
        }
    });

</script>

<?= $this->endSection() ?>
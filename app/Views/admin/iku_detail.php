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

<!-- Flash Message Success Popup -->
<?php if (session()->getFlashdata('success')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Show popup notification
            var successMsg = "<?= esc(session()->getFlashdata('success')) ?>";

            // Create floating toast notification
            var toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 z-[9999] bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center space-x-3 animate-bounce';
            toast.innerHTML = '<ion-icon name="checkmark-circle" class="text-2xl"></ion-icon><span class="font-bold">' + successMsg + '</span>';
            document.body.appendChild(toast);

            // Remove after 4 seconds
            setTimeout(function () {
                toast.style.transition = 'opacity 0.5s';
                toast.style.opacity = '0';
                setTimeout(function () { toast.remove(); }, 500);
            }, 4000);
        });
    </script>
<?php endif; ?>

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
            <!-- Action Buttons (IKU 2 Only: Kelola UMP) -->
            <?php if (stripos($iku_detail['iku'] ?? '', '2') !== false): ?>
                <div class="mt-4 md:mt-0">
                    <button type="button"
                        class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg shadow-md transition transform hover:scale-105"
                        onclick="openUmpModal()">
                        <ion-icon name="cash-outline" class="mr-2 text-xl"></ion-icon>
                        💰 Kelola Data UMP
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Old Navigation Removed -->
    </nav>
</div>

<!-- Statistik Cards & Charts -->
<?php if (stripos($iku_detail['iku'] ?? '', '1') !== false && isset($iku1_stats)): ?>
    <!-- IKU 1 SPECIFIC DASHBOARD - Redesigned like IKU 2 -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Card 1: Total Lulusan -->
        <div class="card-stat bg-blue-600">
            <div>
                <h3>TOTAL LULUSAN</h3>
                <div class="value"><?= number_format($iku1_stats['total_lulusan']) ?></div>
                <div class="desc">Data terinput triwulan ini</div>
            </div>
            <div class="icon-bg">
                <ion-icon name="people" class="text-2xl"></ion-icon>
            </div>
        </div>

        <!-- Card 2: Lulus Tepat Waktu -->
        <div class="card-stat bg-green-500">
            <div>
                <h3>LULUS TEPAT WAKTU</h3>
                <div class="value"><?= number_format($iku1_stats['total_tepat']) ?></div>
                <div class="desc">Masa studi sesuai jenjang</div>
            </div>
            <div class="icon-bg">
                <ion-icon name="checkmark-circle" class="text-2xl"></ion-icon>
            </div>
        </div>

        <!-- Card 3: Capaian (like IKU 2) -->
        <div class="card-stat bg-blue-500">
            <div>
                <h3 class="flex items-center">
                    CAPAIAN
                    <button type="button" onclick="openCalcModalIku1()"
                        class="ml-2 text-white/70 hover:text-white transition focus:outline-none"
                        title="Lihat Detail Perhitungan">
                        <ion-icon name="information-circle" class="text-lg"></ion-icon>
                    </button>
                </h3>
                <?php
                // Tentukan Target Ideal berdasarkan Jenjang
                $jenjang_upper = strtoupper($jenjang ?? 'D4');
                $target_aee = 25; // Default D4/S1
                if (strpos($jenjang_upper, 'D3') !== false || strpos($jenjang_upper, 'DIII') !== false) {
                    $target_aee = 33;
                } elseif (strpos($jenjang_upper, 'S2') !== false) {
                    $target_aee = 50;
                } elseif (strpos($jenjang_upper, 'S3') !== false) {
                    $target_aee = 33;
                }

                // Hitung Realisasi dan Capaian Akhir
                $realisasi_aee = ($iku1_stats['total_lulusan'] > 0)
                    ? round(($iku1_stats['total_tepat'] / $iku1_stats['total_lulusan']) * 100, 1)
                    : 0;
                $capaian_iku1 = ($target_aee > 0)
                    ? round(($realisasi_aee / $target_aee) * 100, 1)
                    : 0;
                ?>
                <div class="value"><?= $capaian_iku1 ?>%</div>
                <div class="desc">Capaian Kinerja (Target <?= $target_aee ?>%)</div>
            </div>
            <div class="icon-bg">
                <ion-icon name="trending-up" class="text-2xl"></ion-icon>
            </div>
        </div>
    </div>

    <!-- IKU 1: Komposisi Kelulusan & Ringkasan Statistik -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Donut Chart with Percentages -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 col-span-1">
            <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Komposisi Kelulusan</h3>
            <div class="relative h-64">
                <canvas id="gradStatusChart"></canvas>
            </div>
        </div>

        <!-- Ringkasan Statistik Cards (like IKU 2) -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 col-span-1 lg:col-span-2">
            <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Ringkasan Statistik</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Total Lulusan -->
                <div class="text-center p-6 bg-blue-50 rounded-lg">
                    <div class="text-4xl font-bold text-blue-600"><?= number_format($iku1_stats['total_lulusan']) ?></div>
                    <div class="text-sm text-gray-600 mt-2 font-medium">Total Lulusan</div>
                </div>
                <!-- Tepat Waktu -->
                <div class="text-center p-6 bg-green-50 rounded-lg">
                    <div class="text-4xl font-bold text-green-600"><?= number_format($iku1_stats['total_tepat']) ?></div>
                    <div class="text-sm text-gray-600 mt-2 font-medium">Lulus Tepat Waktu</div>
                    <?php
                    $persen_tepat = ($iku1_stats['total_lulusan'] > 0)
                        ? round(($iku1_stats['total_tepat'] / $iku1_stats['total_lulusan']) * 100, 1)
                        : 0;
                    ?>
                    <div class="text-xs text-green-500 mt-1">(<?= $persen_tepat ?>%)</div>
                </div>
                <!-- Terlambat -->
                <div class="text-center p-6 bg-red-50 rounded-lg">
                    <div class="text-4xl font-bold text-red-600"><?= number_format($iku1_stats['total_terlambat']) ?></div>
                    <div class="text-sm text-gray-600 mt-2 font-medium">Lulus Terlambat</div>
                    <?php
                    $persen_terlambat = ($iku1_stats['total_lulusan'] > 0)
                        ? round(($iku1_stats['total_terlambat'] / $iku1_stats['total_lulusan']) * 100, 1)
                        : 0;
                    ?>
                    <div class="text-xs text-red-500 mt-1">(<?= $persen_terlambat ?>%)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart JS for IKU 1 with Percentage Labels -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('gradStatusChart');
            if (ctx) {
                var tepat = <?= $iku1_stats['total_tepat'] ?>;
                var terlambat = <?= $iku1_stats['total_terlambat'] ?>;
                var total = tepat + terlambat;

                new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    plugins: [ChartDataLabels],
                    data: {
                        labels: ['Tepat Waktu', 'Terlambat'],
                        datasets: [{
                            data: [tepat, terlambat],
                            backgroundColor: ['#10B981', '#EF4444'],
                            hoverOffset: 8,
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    font: { size: 11, weight: 'bold' }
                                }
                            },
                            datalabels: {
                                color: '#fff',
                                font: { weight: 'bold', size: 14 },
                                formatter: function (value, context) {
                                    if (total === 0 || value === 0) return '';
                                    var percentage = ((value / total) * 100).toFixed(1);
                                    return percentage + '%';
                                },
                                anchor: 'center',
                                align: 'center'
                            }
                        }
                    }
                });
            }
        });
    </script>

<?php else: ?>
    <!-- IKU 2 / DEFAULT DASHBOARD -->
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
                <div class="desc">Weighted Score</div>
            </div>
            <div class="icon-bg">
                <ion-icon name="trending-up" class="text-2xl"></ion-icon>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
// Check if this is IKU 2 to show the chart
$is_iku2 = (strpos($iku_detail['iku'] ?? '', '2') !== false);
?>

<?php if ($is_iku2 && !empty($chart_data)): ?>
    <!-- IKU 2: Komposisi Aktivitas Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 col-span-1">
            <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Komposisi Aktivitas</h3>
            <div class="relative h-64">
                <canvas id="iku2ActivityChart"></canvas>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 col-span-1 lg:col-span-2">
            <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Ringkasan Statistik</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-3xl font-bold text-green-600"><?= $chart_data['bekerja'] ?? 0 ?></div>
                    <div class="text-sm text-gray-600 mt-1">Bekerja</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-3xl font-bold text-purple-600"><?= $chart_data['wirausaha'] ?? 0 ?></div>
                    <div class="text-sm text-gray-600 mt-1">Wirausaha</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-3xl font-bold text-blue-600"><?= $chart_data['studi'] ?? 0 ?></div>
                    <div class="text-sm text-gray-600 mt-1">Lanjut Studi</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-3xl font-bold text-yellow-600"><?= $chart_data['mencari'] ?? 0 ?></div>
                    <div class="text-sm text-gray-600 mt-1">Mencari Kerja</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js with Datalabels Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('iku2ActivityChart');
            if (ctx) {
                // Calculate total for percentage
                var bekerja = <?= $chart_data['bekerja'] ?? 0 ?>;
                var wirausaha = <?= $chart_data['wirausaha'] ?? 0 ?>;
                var studi = <?= $chart_data['studi'] ?? 0 ?>;
                var mencari = <?= $chart_data['mencari'] ?? 0 ?>;
                var total = bekerja + wirausaha + studi + mencari;

                new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    plugins: [ChartDataLabels],
                    data: {
                        labels: ['Bekerja', 'Wirausaha', 'Lanjut Studi', 'Mencari Kerja'],
                        datasets: [{
                            data: [bekerja, wirausaha, studi, mencari],
                            backgroundColor: ['#10B981', '#8B5CF6', '#3B82F6', '#F59E0B'],
                            hoverOffset: 8,
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    font: { size: 11, weight: 'bold' }
                                }
                            },
                            datalabels: {
                                color: '#fff',
                                font: { weight: 'bold', size: 12 },
                                formatter: function (value, context) {
                                    if (total === 0 || value === 0) return '';
                                    var percentage = ((value / total) * 100).toFixed(1);
                                    return percentage + '%';
                                },
                                anchor: 'center',
                                align: 'center'
                            }
                        }
                    }
                });
            }
        });
    </script>
<?php endif; ?>

<!-- Section Daftar Lulusan / Table -->
<div class="bg-white p-6 rounded-lg shadow-md w-full">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <h2 class="text-lg font-bold text-gray-700">Daftar Lulusan</h2>
            <button id="btnBulkDelete" onclick="deleteSelected()"
                class="hidden bg-red-600 hover:bg-red-700 text-white text-sm font-bold py-1.5 px-3 rounded-lg transition duration-300">
                <ion-icon name="trash-outline" class="mr-1"></ion-icon> Hapus (<span id="countSelected">0</span>)
            </button>
        </div>

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
            } elseif (strpos($current_iku_code, '2') !== false) {
                // IKU 2 (Lulusan Bekerja)
                $link_tambah = site_url('admin/iku2/input/' . ($jurusan_kode ?? '') . '/' . rawurlencode($nama_prodi ?? '') . '/' . ($jenjang ?? '')) . '?id_triwulan=' . ($id_triwulan ?? '');
            } else {
                // Default (IKU Lain)
                $link_tambah = site_url('admin/iku-input/' . $current_iku_code . '/' . ($jurusan_kode ?? '') . '/' . rawurlencode($nama_prodi ?? '') . '/' . ($jenjang ?? ''));
            }
            ?>

            <div class="flex space-x-2">
                <!-- Tombol Import Excel (Khusus IKU 1 & 2) -->
                <?php if (strpos($current_iku_code, '1') !== false): ?>
                    <a href="<?= site_url('admin/iku1/import') . '?return_url=' . rawurlencode(base_url(uri_string()) . '?id_triwulan=' . ($id_triwulan ?? '')) . '&id_triwulan=' . ($id_triwulan ?? '') ?>"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition duration-300">
                        <ion-icon name="document-text-outline" class="text-xl"></ion-icon>
                        <span>Import Excel</span>
                    </a>
                <?php elseif (strpos($current_iku_code, '2') !== false): ?>
                    <a href="<?= site_url('admin/iku2/import') . '?return_url=' . rawurlencode(base_url(uri_string()) . '?id_triwulan=' . ($id_triwulan ?? '')) . '&id_triwulan=' . ($id_triwulan ?? '') ?>"
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
                    <a href="javascript:void(0)"
                        onclick="confirmDetailExport('<?= site_url('admin/iku1/export') ?>', '<?= http_build_query(['kode_prodi' => $kode_prodi ?? '', 'id_triwulan' => $id_triwulan ?? '']) ?>')"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition duration-300">
                        <ion-icon name="download-outline" class="text-xl"></ion-icon>
                        <span>Export Excel</span>
                    </a>
                <?php elseif (strpos($current_iku_code, '2') !== false): ?>
                    <a href="javascript:void(0)"
                        onclick="confirmDetailExport('<?= site_url('admin/iku2/export') ?>', '<?= http_build_query(['kode_prodi' => $kode_prodi ?? '', 'id_triwulan' => $id_triwulan ?? '']) ?>')"
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
                    <th class="px-4 py-3 font-semibold text-left w-10">
                        <input type="checkbox" id="selectAll"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </th>
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
                            <td class="px-4 py-4 align-top">
                                <input type="checkbox" name="ids[]" value="<?= $item['main_id'] ?? $item['id'] ?? '' ?>"
                                    class="select-item rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>

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
                                        <!-- Allow HTML for specific keys (badges, formatted text) -->
                                        <?php if (in_array($key, ['capaian', 'nama', 'nim', 'status', 'aksi', 'gaji', 'bobot', 'tempat', 'masa_tunggu'])): ?>
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

<!-- Scripts for Bulk Delete -->
<script>
    // Existing Datatables setup... logic for bulk delete uses same selectors
    const selectAllInfo = document.getElementById('selectAll');
    const checkboxesInfo = document.querySelectorAll('.select-item');
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    const countSelectedSpan = document.getElementById('countSelected');

    function updateBulkDeleteUI() {
        const selectedCount = document.querySelectorAll('.select-item:checked').length;
        if (countSelectedSpan) countSelectedSpan.innerText = selectedCount;
        if (btnBulkDelete) {
            if (selectedCount > 0) {
                btnBulkDelete.classList.remove('hidden');
            } else {
                btnBulkDelete.classList.add('hidden');
            }
        }
    }

    if (selectAllInfo) {
        selectAllInfo.addEventListener('change', function () {
            checkboxesInfo.forEach(cb => cb.checked = this.checked);
            updateBulkDeleteUI();
        });
    }

    checkboxesInfo.forEach(cb => {
        cb.addEventListener('change', updateBulkDeleteUI);
    });

    function deleteSelected() {
        // Determine endpoint based on URL or current IKU code (passed from php preferably)
        // Hardcoding checks based on URL for now as it's cleaner than injecting more PHP var
        let deleteUrl = '';
        if (window.location.href.includes('iku1')) {
            deleteUrl = '<?= site_url('admin/iku1/bulk_delete') ?>';
        } else if (window.location.href.includes('iku2')) {
            deleteUrl = '<?= site_url('admin/iku2/bulk_delete') ?>';
        } else {
            // Fallback detection via PHP variable if available
            <?php if (strpos($current_iku_code, '1') !== false): ?>
                deleteUrl = '<?= site_url('admin/iku1/bulk_delete') ?>';
            <?php elseif (strpos($current_iku_code, '2') !== false): ?>
                deleteUrl = '<?= site_url('admin/iku2/bulk_delete') ?>';
            <?php endif; ?>
        }

        if (!deleteUrl) {
            alert('Fitur hapus massal belum tersedia untuk halaman ini.');
            return;
        }

        const selected = Array.from(document.querySelectorAll('.select-item:checked')).map(cb => cb.value);
        if (selected.length === 0) return;

        if (confirm(`Yakin ingin menghapus ${selected.length} data terpilih? Data tidak bisa dikembalikan.`)) {
            fetch(deleteUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ ids: selected })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal menghapus data.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan sistem.');
                });
        }
    }
</script>

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

<!-- MODAL: Pilihan Export -->
<div id="exportModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"
        onclick="closeExportModal()">
    </div>

    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <div
            class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full border border-gray-100">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <ion-icon name="cloud-download-outline" class="text-blue-600 text-2xl"></ion-icon>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Pilih Metode Export
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-4">
                                Anda telah memilih <span id="exportSelectedCount"
                                    class="font-bold text-blue-600">0</span>
                                data. Bagaimana Anda ingin melakukan export?
                            </p>

                            <div class="space-y-3">
                                <!-- Option 1: Export Selected -->
                                <button id="btnExportSelected" onclick="executeDetailExport('selected')"
                                    class="w-full flex items-center justify-between p-3 border rounded-lg hover:bg-blue-50 hover:border-blue-300 transition group">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-blue-100 rounded group-hover:bg-blue-200 text-blue-600">
                                            <ion-icon name="checkbox-outline" class="text-xl"></ion-icon>
                                        </div>
                                        <div class="ml-3 text-left">
                                            <p class="text-sm font-medium text-gray-900">Export Terpilih Saja</p>
                                            <p class="text-xs text-gray-500">Hanya data yang Anda centang</p>
                                        </div>
                                    </div>
                                    <ion-icon name="chevron-forward-outline" class="text-gray-400"></ion-icon>
                                </button>

                                <!-- Option 2: Export All -->
                                <button id="btnExportAll" onclick="executeDetailExport('all')"
                                    class="w-full flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 hover:border-gray-300 transition group">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-gray-100 rounded group-hover:bg-gray-200 text-gray-600">
                                            <ion-icon name="documents-outline" class="text-xl"></ion-icon>
                                        </div>
                                        <div class="ml-3 text-left">
                                            <p class="text-sm font-medium text-gray-900">Export Semua Data</p>
                                            <p class="text-xs text-gray-500">Seluruh data hasil filter saat ini</p>
                                        </div>
                                    </div>
                                    <ion-icon name="chevron-forward-outline" class="text-gray-400"></ion-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeExportModal()"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Penjelasan Perhitungan -->
<div id="calcModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeCalcModal()">
    </div>

    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <div
            class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full border border-gray-100">

            <?php
            $is_iku2 = (strpos($iku_detail['iku'] ?? '', '2') !== false);
            ?>

            <?php if ($is_iku2): ?>


                <!-- HEADER IKU 2 -->
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <ion-icon name="calculator-outline" class="mr-2 text-2xl"></ion-icon>
                        Penjelasan Perhitungan IKU 2 (Weighted)
                    </h3>
                    <button type="button" onclick="closeCalcModal()"
                        class="text-white hover:text-gray-200 focus:outline-none transition">
                        <ion-icon name="close-circle-outline" class="text-2xl"></ion-icon>
                    </button>
                </div>

                <!-- CONTENT IKU 2 -->
                <div class="px-6 py-6 space-y-6 max-h-[70vh] overflow-y-auto">
                    <!-- 1. Rumus Utama -->
                    <div class="bg-purple-50 rounded-xl p-5 border border-purple-100">
                        <h4 class="font-bold text-purple-800 mb-2 flex items-center text-sm uppercase tracking-wide">
                            <span
                                class="w-6 h-6 rounded-full bg-purple-200 text-purple-700 flex items-center justify-center text-xs mr-2 font-bold">1</span>
                            Rumus Perhitungan
                        </h4>
                        <div
                            class="text-center font-mono text-sm bg-white p-3 rounded border border-purple-200 mb-2 text-gray-800 font-bold">
                            (Total Bobot Nilai Lulusan ÷ Total Responden) × 100
                        </div>
                        <p class="text-xs text-gray-500">
                            *Setiap lulusan memiliki bobot nilai berbeda tergantung kualitas pekerjaan, masa tunggu, dan
                            pendapatan.
                        </p>
                    </div>

                    <!-- 2. Tabel Bobot -->
                    <div class="bg-blue-50 rounded-xl p-5 border border-blue-100">
                        <h4 class="font-bold text-blue-800 mb-2 flex items-center text-sm uppercase tracking-wide">
                            <span
                                class="w-6 h-6 rounded-full bg-blue-200 text-blue-700 flex items-center justify-center text-xs mr-2 font-bold">2</span>
                            Bobot Nilai (Kriteria)
                        </h4>
                        <div class="overflow-hidden rounded-lg border border-blue-200">
                            <table class="min-w-full text-xs text-left">
                                <thead class="bg-blue-100 text-blue-800 uppercase font-bold">
                                    <tr>
                                        <th class="px-3 py-2">Kriteria</th>
                                        <th class="px-3 py-2">Syarat</th>
                                        <th class="px-3 py-2 text-center">Bobot</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <tr>
                                        <td class="px-3 py-2 font-bold text-gray-700">Bekerja (Max)</td>
                                        <td class="px-3 py-2 text-gray-600">Masa Tunggu < 6 bln & Gaji ≥ 1.2x UMP</td>
                                        <td class="px-3 py-1 text-center font-bold text-green-600 bg-green-50">1.0</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 font-bold text-gray-700">Bekerja (Med)</td>
                                        <td class="px-3 py-2 text-gray-600">Masa Tunggu 6-12 bln & Gaji ≥ 1.2x UMP</td>
                                        <td class="px-3 py-2 text-center font-bold text-blue-600 bg-blue-50">0.6</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 font-bold text-gray-700">Bekerja (Low)</td>
                                        <td class="px-3 py-2 text-gray-600">Bekerja (Gaji < 1.2x UMP)</td>
                                        <td class="px-3 py-2 text-center font-bold text-orange-600 bg-orange-50">0.4</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 font-bold text-gray-700">Wirausaha (Pendiri)</td>
                                        <td class="px-3 py-2 text-gray-600">Pendiri / Co-Founder / Pemilik</td>
                                        <td class="px-3 py-2 text-center font-bold text-purple-600 bg-purple-50">0.75</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 font-bold text-gray-700">Wirausaha (Freelance)</td>
                                        <td class="px-3 py-2 text-gray-600">Pekerja Lepas / Freelance</td>
                                        <td class="px-3 py-2 text-center font-bold text-purple-600 bg-purple-50">0.25</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 font-bold text-gray-700">Studi Lanjut</td>
                                        <td class="px-3 py-2 text-gray-600">Lanjut Studi < 12 bln setelah lulus</td>
                                        <td class="px-3 py-2 text-center font-bold text-indigo-600 bg-indigo-50">1.0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- HEADER IKU 1 (Legacy / Default) -->
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

                <!-- CONTENT IKU 1 -->
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
            <?php endif; ?>

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

    function confirmDetailExport_OLD(baseUrl, existingQuery) {
        const selected = Array.from(document.querySelectorAll('.select-item:checked')).map(cb => cb.value);

        if (selected.length > 0) {
            // Logic: Selected Export
            if (confirm("Anda memilih " + selected.length + " data.\nKlik OK untuk meng-export HANYA data yang dipilih.\nKlik CANCEL untuk opsi lainnya.")) {
                const ids = selected.join(',');
                const separator = existingQuery ? '&' : '?';
                window.open(baseUrl + '?' + existingQuery + separator + 'ids=' + ids, '_blank');
            } else {
                // Logic: All Export
                if (confirm("Apakah Anda ingin mengekspor SEMUA data dari halaman ini?")) {
                    window.open(baseUrl + '?' + existingQuery, '_blank');
                }
            }
        } else {
            // No selection, direct to all
            window.open(baseUrl + '?' + existingQuery, '_blank');
        }
    }

    // NEW EXPORT LOGIC FOR DETAIL
    let currentExportBaseUrl = '';
    let currentExportQuery = '';

    function confirmDetailExport(baseUrl, existingQuery) {
        const selected = Array.from(document.querySelectorAll('.select-item:checked')).map(cb => cb.value);

        // Store for execution
        currentExportBaseUrl = baseUrl;
        currentExportQuery = existingQuery;

        if (selected.length > 0) {
            document.getElementById('exportSelectedCount').innerText = selected.length;
            document.getElementById('exportModal').classList.remove('hidden');
        } else {
            // Direct export all
            window.open(baseUrl + '?' + existingQuery, '_blank');
        }
    }

    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }

    function executeDetailExport(type) {
        const selected = Array.from(document.querySelectorAll('.select-item:checked')).map(cb => cb.value);

        if (type === 'selected') {
            const ids = selected.join(',');
            const separator = currentExportQuery ? '&' : '?';
            window.open(currentExportBaseUrl + '?' + currentExportQuery + separator + 'ids=' + ids, '_blank');
        } else {
            window.open(currentExportBaseUrl + '?' + currentExportQuery, '_blank');
        }
        closeExportModal();
    }

</script>

<!-- Modal Rumus IKU 1 -->
<?php if (stripos($iku_detail['iku'] ?? '', '1') !== false && isset($iku1_stats)): ?>
    <?php
    // Tentukan Target Ideal berdasarkan Jenjang
    $jenjang_upper = strtoupper($jenjang ?? 'D4');
    $target_ideal = 25; // Default D4/S1
    if (strpos($jenjang_upper, 'D3') !== false || strpos($jenjang_upper, 'DIII') !== false) {
        $target_ideal = 33;
    } elseif (strpos($jenjang_upper, 'S2') !== false) {
        $target_ideal = 50;
    } elseif (strpos($jenjang_upper, 'S3') !== false) {
        $target_ideal = 33;
    }

    // Hitung Realisasi dan Capaian
    $realisasi_iku1 = ($iku1_stats['total_lulusan'] > 0)
        ? round(($iku1_stats['total_tepat'] / $iku1_stats['total_lulusan']) * 100, 2)
        : 0;
    $capaian_akhir_iku1 = ($target_ideal > 0)
        ? round(($realisasi_iku1 / $target_ideal) * 100, 2)
        : 0;
    ?>
    <div id="calcModalIku1" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500/75" onclick="closeCalcModalIku1()"></div>
            <div class="relative z-10 w-full max-w-lg p-6 mx-auto bg-white rounded-2xl shadow-2xl">
                <div class="flex flex-col items-center justify-between mb-4">
                    <div class="flex w-full justify-between items-start">
                        <h3 class="text-xl font-bold text-gray-800 text-left">📊 Rumus Perhitungan IKU 1 (AEE)</h3>
                        <button onclick="closeCalcModalIku1()" class="text-gray-400 hover:text-gray-600 transition">
                            <ion-icon name="close-circle" class="text-2xl"></ion-icon>
                        </button>
                    </div>
                    <div class="w-full text-left mt-1">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Jenjang Terdeteksi: <?= $jenjang_upper ?> (Target <?= $target_ideal ?>%)
                        </span>
                    </div>
                </div>

                <div class="text-left space-y-4">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <div class="flex items-center mb-2">
                            <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full mr-2">1</span>
                            <p class="font-bold text-blue-800">REALISASI (PERSENTASE LULUSAN)</p>
                        </div>
                        <div class="bg-white p-3 rounded-md font-mono text-sm text-center border mb-2">
                            (Jumlah Lulusan Tepat Waktu ÷ Total Mahasiswa Angkatan) × 100%
                        </div>
                        <p class="text-xs text-gray-500 italic">*Dihitung per angkatan untuk setiap prodi.</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <div class="flex items-center mb-2">
                            <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full mr-2">2</span>
                            <p class="font-bold text-gray-700">TARGET IDEAL (SESUAI JENJANG)</p>
                        </div>
                        <div class="grid grid-cols-4 gap-2 text-center text-sm">
                            <div class="p-2 bg-white rounded-lg border">
                                <div class="font-bold text-gray-700">D3</div>
                                <div class="text-lg font-bold text-green-600">33%</div>
                            </div>
                            <div class="p-2 bg-white rounded-lg border">
                                <div class="font-bold text-gray-700">D4/S1</div>
                                <div class="text-lg font-bold text-green-600">25%</div>
                            </div>
                            <div class="p-2 bg-white rounded-lg border">
                                <div class="font-bold text-gray-700">S2</div>
                                <div class="text-lg font-bold text-green-600">50%</div>
                            </div>
                            <div class="p-2 bg-white rounded-lg border">
                                <div class="font-bold text-gray-700">S3</div>
                                <div class="text-lg font-bold text-green-600">33%</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                        <div class="flex items-center mb-2">
                            <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full mr-2">3</span>
                            <p class="font-bold text-green-800">CAPAIAN AKHIR (KINERJA)</p>
                        </div>
                        <div class="bg-white p-3 rounded-md font-mono text-sm text-center border border-green-200 mb-2">
                            (Realisasi ÷ Target Ideal) × 100%
                        </div>
                        <p class="text-xs text-gray-500 italic text-center">Nilai Capaian = Seberapa dekat realisasi Anda
                            dengan target ideal yang ditetapkan.</p>
                    </div>
                </div>

                <!-- Hasil Perhitungan -->
                <div class="mt-4 bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-100">
                    <p class="font-bold text-gray-800 mb-2 border-b border-blue-200 pb-1">📈 Perhitungan Prodi Anda:</p>
                    <div class="grid grid-cols-3 gap-2 text-center text-sm mb-2">
                        <div class="bg-white rounded-lg p-2 shadow-sm">
                            <div class="text-xs text-gray-500">Realisasi</div>
                            <div class="text-base font-bold text-blue-600"><?= $realisasi_iku1 ?>%</div>
                        </div>
                        <div class="bg-white rounded-lg p-2 shadow-sm">
                            <div class="text-xs text-gray-500">Target (<?= $jenjang_upper ?>)</div>
                            <div class="text-base font-bold text-gray-600"><?= $target_ideal ?>%</div>
                        </div>
                        <div class="bg-white rounded-lg p-2 shadow-sm ring-1 ring-green-400">
                            <div class="text-xs text-gray-500">Capaian</div>
                            <div class="text-base font-bold text-green-600"><?= $capaian_akhir_iku1 ?>%</div>
                        </div>
                    </div>
                    <p class="text-xs text-center text-gray-600 font-mono bg-white/50 py-1 rounded">
                        (<?= $realisasi_iku1 ?>% ÷ <?= $target_ideal ?>%) × 100 =
                        <strong><?= $capaian_akhir_iku1 ?>%</strong>
                    </p>
                </div>

                <button onclick="closeCalcModalIku1()"
                    class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg transition">
                    Mengerti
                </button>
            </div>
        </div>
    </div>

    <script>
        function openCalcModalIku1() {
            document.getElementById('calcModalIku1').classList.remove('hidden');
        }

        function closeCalcModalIku1() {
            document.getElementById('calcModalIku1').classList.add('hidden');
        }

        // Close on Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === "Escape") {
                closeCalcModalIku1();
            }
        });
    </script>
<?php endif; ?>

<!-- Include Modal Kelola UMP (IKU 2 Only) -->
<?php if (stripos($iku_detail['iku'] ?? '', '2') !== false): ?>
    <?= $this->include('admin/ump/modal_kelola') ?>
<?php endif; ?>

<?= $this->endSection() ?>
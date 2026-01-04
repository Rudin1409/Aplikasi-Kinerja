<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>
<!-- LOGIC TAMPILAN: STEP 1 (UPLOAD) VS STEP 2 (PREVIEW) -->
<!-- STEP 1: FORM UPLOAD (ALWAYS VISIBLE) -->
<!-- Styled Breadcrumb & Header -->
<div class="mb-6">
    <nav class="flex items-center text-sm text-gray-500 mb-4">
        <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center hover:text-purple-600 transition-colors">
            <ion-icon name="home-outline" class="mr-1.5 text-lg"></ion-icon>
            <span class="font-medium">Dashboard</span>
        </a>
        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>

        <a href="<?= !empty($return_url) ? urldecode($return_url) : base_url('admin/dashboard') ?>"
            class="flex items-center hover:text-purple-600 transition-colors font-medium">
            <ion-icon name="briefcase-outline" class="mr-1.5 text-lg"></ion-icon>
            <span>IKU 2 (Lulusan)</span>
        </a>
        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>

        <div
            class="flex items-center text-purple-700 bg-purple-50 px-3 py-1 rounded-full border border-purple-100 shadow-sm">
            <span class="font-bold text-xs mr-1">Action:</span>
            <span class="font-bold">Import Excel</span>
        </div>
    </nav>
    <div class="mb-2">
        <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight leading-tight">
            Import Data Tracer (IKU 2)
        </h1>
        <p class="text-gray-500 mt-2 text-sm">Upload data lulusan yang bekerja / melanjutkan studi.</p>
    </div>
</div>

<form action="<?= base_url('admin/iku2/preview') ?>" method="post" enctype="multipart/form-data" id="form-upload"
    class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8">

    <!-- Form Header Deco -->
    <div class="h-1.5 bg-gradient-to-r from-green-500 to-teal-500"></div>

    <div class="p-8">
        <?= csrf_field() ?>
        <?php if (!empty($return_url)): ?>
            <input type="hidden" name="return_url" value="<?= esc($return_url) ?>">
        <?php endif; ?>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <p class="font-bold text-blue-700">Panduan Import:</p>
            <ul class="list-disc list-inside text-sm text-blue-600">
                <li>Gunakan template Excel yang sesuai (download di bawah).</li>
                <ul class="list-disc list-inside text-sm mt-2 space-y-1 ml-4">
                    <li>Kolom Wajib: <strong>NIM, Nama, Kode Prodi, Tahun Masuk, Tanggal Yudisium, Jenis
                            Aktivitas</strong></li>
                    <li>Jenis Aktivitas: <strong>Bekerja / Wirausaha / Lanjut Studi / Mencari Kerja</strong></li>
                    <li>Untuk <strong>Bekerja</strong>: Isi Provinsi Tempat Kerja dan Gaji Bulan</li>
                    <li>Untuk <strong>Wirausaha</strong>: Isi Posisi (Pendiri/Freelance)</li>
                    <li>Template sudah include sheet <strong>Panduan Pengisian</strong></li>
                </ul>
                <div class="mt-4">
                    <a href="<?= base_url('admin/iku2/download_template') ?>"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-md hover:bg-green-700 transition">
                        <ion-icon name="download-outline" class="mr-2 text-lg"></ion-icon>
                        Download Template Excel (IKU 2)
                    </a>
                </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Triwulan -->
            <div class="mb-4">
                <label for="id_triwulan" class="block text-gray-700 font-bold mb-2">Triwulan (Otomatis Aktif)</label>
                <?php if (isset($active_triwulan_id)): ?>
                    <select name="id_triwulan" id="id_triwulan"
                        class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed focus:outline-none"
                        readonly tabindex="-1" onmousedown="return false;">
                        <?php foreach ($triwulan_list as $t): ?>
                            <?php if ($t['id'] == $active_triwulan_id): ?>
                                <option value="<?= $t['id'] ?>" selected>
                                    <?= $t['nama_triwulan'] ?> (Aktif)
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <select name="id_triwulan" id="id_triwulan"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-purple-500 transition"
                        required onchange="checkAndSubmit()">
                        <option value="">-- Pilih Triwulan --</option>
                        <?php foreach ($triwulan_list as $t): ?>
                            <option value="<?= $t['id'] ?>">
                                <?= $t['nama_triwulan'] ?>         <?= $t['status'] == 'Aktif' ? '(Aktif)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <!-- File Input -->
            <div class="mb-4">
                <label for="file_excel" class="block text-gray-700 font-bold mb-2">File Excel (.xlsx / .xls)</label>
                <input type="file" name="file_excel" id="file_excel"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-purple-500 transition"
                    accept=".xlsx, .xls" required onchange="checkAndSubmit()">
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="loading-indicator" class="hidden flex items-center text-purple-600 font-bold mt-2">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            Memproses File... Mohon tunggu.
        </div>

        <button type="submit" id="btn-preview" class="hidden">Preview</button>

    </div>
</form>

<script>
    function checkAndSubmit() {
        var triwulan = document.getElementById('id_triwulan').value;
        var file = document.getElementById('file_excel').value;
        if (triwulan && file) {
            document.getElementById('loading-indicator').classList.remove('hidden');
            document.getElementById('form-upload').submit();
        }
    }
</script>

<?php if (!empty($preview_data)): ?>
    <!-- STEP 2: TABLE PREVIEW -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="mb-4 flex justify-between items-center border-b border-gray-100 pb-4">
            <h3 class="text-lg font-bold text-gray-700">Preview Data Hasil Import</h3>
        </div>

        <?php
        $has_error = false;
        foreach ($preview_data as $row) {
            if (!$row['valid']) {
                $has_error = true;
                break;
            }
        }
        ?>

        <?php if ($has_error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p class="font-bold">Perhatian!</p>
                <p>Terdapat data yang tidak valid. Silakan perbaiki file Excel dan upload ulang.</p>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto bg-gray-50 rounded-lg border border-gray-200 mb-6 max-h-[500px]">
            <table class="w-full text-left border-collapse relative">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-gray-100 text-xs uppercase text-gray-600 border-b border-gray-200 shadow-sm">
                        <th class="px-4 py-3 font-semibold bg-gray-100">NIM / Nama</th>
                        <th class="px-4 py-3 font-semibold bg-gray-100">Prodi</th>
                        <th class="px-4 py-3 font-semibold bg-gray-100">Aktivitas</th>
                        <th class="px-4 py-3 font-semibold bg-gray-100">Detail</th>
                        <th class="px-4 py-3 font-semibold bg-gray-100 text-center">Bobot</th>
                        <th class="px-4 py-3 font-semibold bg-purple-100 text-purple-800 text-center">Sync IKU 1</th>
                        <th class="px-4 py-3 font-semibold bg-gray-100 text-center">Valid</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach ($preview_data as $row): ?>
                        <tr class="border-b border-gray-200 hover:bg-white transition <?= !$row['valid'] ? 'bg-red-50' : '' ?>">
                            <td class="px-4 py-3 align-top">
                                <div class="font-bold text-gray-800"><?= esc($row['nama']) ?></div>
                                <div class="text-xs text-gray-500">
                                    <?= esc($row['nim']) ?>
                                    <?php if ($row['status_mhs'] == 'Baru'): ?>
                                        <span
                                            class="ml-2 bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded text-[10px] font-bold">BARU</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($row['tanggal_yudisium'])): ?>
                                    <div class="text-xs text-green-600 mt-1">✅ Yudisium: <?= esc($row['tanggal_yudisium']) ?></div>
                                <?php elseif (!empty($row['raw_tgl_yudisium'] ?? '')): ?>
                                    <div class="text-xs text-red-500 mt-1">❌ Raw: <?= esc($row['raw_tgl_yudisium']) ?> (GAGAL PARSE)
                                    </div>
                                <?php endif; ?>
                                <?php if (!$row['valid']): ?>
                                    <div class="text-xs text-red-600 font-bold mt-1">
                                        Error: <?= esc($row['error_msg']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <?= esc($row['kode_prodi']) ?>
                                <span class="text-xs text-gray-500 block">Masuk: <?= $row['tahun_masuk'] ?></span>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <?php
                                $aktivitasColors = [
                                    'Bekerja' => 'bg-green-100 text-green-800',
                                    'Wirausaha' => 'bg-orange-100 text-orange-800',
                                    'Lanjut Studi' => 'bg-blue-100 text-blue-800',
                                    'Mencari Kerja' => 'bg-yellow-100 text-yellow-800'
                                ];
                                $color = $aktivitasColors[$row['jenis_aktivitas']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span
                                    class="px-2 py-1 rounded text-xs font-bold <?= $color ?>"><?= esc($row['jenis_aktivitas']) ?></span>
                                <?php if (!empty($row['posisi_wirausaha'])): ?>
                                    <div class="text-xs text-gray-500 mt-1"><?= esc($row['posisi_wirausaha']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <span class="block text-xs font-medium"><?= esc($row['nama_tempat']) ?></span>
                                <?php if (!empty($row['gaji_bulan'])): ?>
                                    <span class="block text-xs text-gray-500">Gaji: Rp
                                        <?= number_format((float) str_replace(['.', ','], ['', '.'], $row['gaji_bulan']), 0, ',', '.') ?></span>
                                <?php endif; ?>
                                <?php if (!empty($row['provinsi_nama'])): ?>
                                    <span class="block text-xs text-gray-400"><?= esc($row['provinsi_nama']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($row['tanggal_mulai'])): ?>
                                    <span class="block text-xs text-green-600 mt-1">✅ Mulai:
                                        <?= esc($row['tanggal_mulai']) ?></span>
                                <?php elseif (!empty($row['raw_tgl_mulai'] ?? '')): ?>
                                    <span class="block text-xs text-red-500 mt-1">❌ Raw Mulai:
                                        <?= esc($row['raw_tgl_mulai']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center align-top">
                                <?php
                                $bobot = (float) ($row['nilai_bobot'] ?? 0);
                                $bobotColor = 'text-gray-600';
                                if ($bobot >= 1.0)
                                    $bobotColor = 'text-green-600';
                                elseif ($bobot >= 0.5)
                                    $bobotColor = 'text-blue-600';
                                elseif ($bobot > 0)
                                    $bobotColor = 'text-orange-600';
                                else
                                    $bobotColor = 'text-red-600';
                                ?>
                                <span class="font-bold <?= $bobotColor ?>"><?= $bobot ?></span>
                            </td>
                            <!-- IKU 1 Sync Status Column -->
                            <td class="px-4 py-3 text-center align-top">
                                <?php if (!empty($row['sync_iku1']) && $row['sync_iku1']): ?>
                                    <?php if (strpos($row['iku1_status'], 'BARU') !== false): ?>
                                        <span
                                            class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 rounded text-[10px] font-bold">
                                            <ion-icon name="add-circle" class="mr-1"></ion-icon>
                                            + IKU 1
                                        </span>
                                        <div class="text-[9px] text-purple-600 mt-0.5">Data baru akan<br>ditambahkan</div>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center px-2 py-1 bg-amber-100 text-amber-800 rounded text-[10px] font-bold">
                                            <ion-icon name="sync" class="mr-1"></ion-icon>
                                            Update
                                        </span>
                                        <div class="text-[9px] text-amber-600 mt-0.5">Akan diupdate</div>
                                    <?php endif; ?>
                                <?php elseif (!empty($row['iku1_status'])): ?>
                                    <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-500 rounded text-[10px]">
                                        <ion-icon name="checkmark" class="mr-1"></ion-icon>
                                        OK
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-300 text-xs">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center align-top">
                                <?php if ($row['valid']): ?>
                                    <ion-icon name="checkmark-circle" class="text-green-500 text-xl"></ion-icon>
                                <?php else: ?>
                                    <ion-icon name="alert-circle" class="text-red-500 text-xl"
                                        title="<?= esc($row['error_msg']) ?>"></ion-icon>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <form action="<?= base_url('admin/iku2/save_data') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="id_triwulan" value="<?= $id_triwulan_selected ?>">
            <input type="hidden" name="bulk_data" value='<?= json_encode($preview_data) ?>'>
            <?php if (!empty($return_url)): ?>
                <input type="hidden" name="return_url" value="<?= esc($return_url) ?>">
            <?php endif; ?>

            <div class="flex justify-end pt-4 border-t border-gray-100">
                <div class="text-right">
                    <button type="submit"
                        class="<?= $has_error ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 hover:scale-105 shadow-lg' ?> text-white font-bold py-2 px-8 rounded-lg transition duration-200 flex items-center ml-auto"
                        <?= $has_error ? 'disabled' : '' ?>>
                        <ion-icon name="save" class="mr-2 text-xl"></ion-icon>
                        Proses & Simpan Data IKU 2
                    </button>
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
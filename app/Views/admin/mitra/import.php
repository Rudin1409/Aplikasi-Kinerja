<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<?php
$query_params = [];
if (isset($nama_prodi) && $nama_prodi)
    $query_params['prodi'] = $nama_prodi;
if (isset($jenjang) && $jenjang)
    $query_params['jenjang'] = $jenjang;
if (isset($jurusan_kode) && $jurusan_kode)
    $query_params['jurusan'] = $jurusan_kode;
$query_string = http_build_query($query_params);
$back_url = base_url('admin/mitra') . ($query_string ? '?' . $query_string : '');
?>

<!-- Header Section -->
<div class="mb-6">
    <nav
        class="flex items-center text-sm font-medium text-gray-500 mb-4 bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-100">
        <a href="<?= base_url('admin/dashboard') ?>" class="hover:text-amber-600 transition-colors flex items-center">
            <ion-icon name="home-outline" class="mr-2 text-lg"></ion-icon> Dashboard
        </a>
        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>
        <a href="<?= $back_url ?>" class="hover:text-amber-600 transition-colors flex items-center">
            <ion-icon name="briefcase-outline" class="mr-2 text-lg"></ion-icon> Master Mitra
        </a>
        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>
        <span class="text-amber-600 flex items-center bg-amber-50 px-3 py-1 rounded-full border border-amber-100">
            <ion-icon name="cloud-upload-outline" class="mr-2"></ion-icon> Import Data
        </span>
    </nav>

    <div class="mb-2">
        <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight leading-tight">Import Data Mitra</h1>
        <p class="text-gray-500 mt-2 text-sm">Upload data mitra menggunakan template Excel yang disediakan.</p>
    </div>
</div>

<!-- Upload Form -->
<form action="<?= base_url('admin/mitra/preview-import') ?>" method="post" enctype="multipart/form-data"
    id="form-upload" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8">

    <div class="h-1.5 bg-gradient-to-r from-amber-500 to-orange-500"></div>

    <div class="p-8">
        <?= csrf_field() ?>
        <?php if ($nama_prodi): ?><input type="hidden" name="prodi" value="<?= esc($nama_prodi) ?>"><?php endif; ?>
        <?php if ($jenjang): ?><input type="hidden" name="jenjang" value="<?= esc($jenjang) ?>"><?php endif; ?>
        <?php if ($jurusan_kode): ?><input type="hidden" name="jurusan"
                value="<?= esc($jurusan_kode) ?>"><?php endif; ?>

        <!-- Panduan Import -->
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6 rounded-r-lg">
            <p class="font-bold text-amber-700 mb-2">Panduan Import:</p>
            <ul class="list-disc list-inside text-sm text-amber-600 space-y-1">
                <li>Gunakan template Excel yang disediakan.</li>
                <li>Kolom Wajib: <strong>Nama Mitra, Bidang Usaha</strong>.</li>
                <li>Kolom Opsional: Alamat, Kota, Provinsi, Penanggung Jawab, Jabatan PJ, No Telp, Email, Jenis
                    Kerjasama.</li>
            </ul>

            <?php $template_url = base_url('admin/mitra/download-template'); ?>
            <div class="mt-4">
                <a href="<?= $template_url ?>"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition shadow-md hover:shadow-lg">
                    <ion-icon name="download-outline" class="mr-2 text-lg"></ion-icon>
                    Download Template Excel
                </a>
            </div>
        </div>

        <!-- File Input -->
        <div class="mb-6">
            <label for="file_excel" class="block text-gray-700 font-bold mb-2">File Excel (.xlsx / .xls)</label>
            <div class="flex items-center gap-4">
                <input type="file" name="file_excel" id="file_excel"
                    class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition"
                    accept=".xlsx, .xls" required onchange="autoPreview()">
                <button type="submit" id="btn-preview"
                    class="px-6 py-3 bg-amber-600 text-white font-bold rounded-lg hover:bg-amber-700 transition shadow-md flex items-center">
                    <ion-icon name="eye-outline" class="mr-2 text-xl"></ion-icon>
                    Preview Data
                </button>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="loading-indicator" class="hidden flex items-center text-amber-600 font-bold mt-2">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            Memproses File... Mohon tunggu.
        </div>
    </div>
</form>

<script>
    function autoPreview() {
        var file = document.getElementById('file_excel').value;
        if (file) {
            document.getElementById('loading-indicator').classList.remove('hidden');
            document.getElementById('form-upload').submit();
        }
    }
</script>

<script>
    document.getElementById('form-upload')?.addEventListener('submit', function () {
        document.getElementById('loading-indicator').classList.remove('hidden');
    });
</script>

<?php if (!empty($preview_data)): ?>
    <!-- Preview Table -->
    <div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-100">
        <div class="mb-4 flex justify-between items-center border-b border-gray-100 pb-4">
            <h3 class="text-lg font-bold text-gray-700 flex items-center">
                <ion-icon name="list-outline" class="mr-2 text-amber-600"></ion-icon>
                Preview Data Hasil Import
            </h3>
            <span class="text-sm text-gray-500"><?= count($preview_data) ?> baris data</span>
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
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r-lg" role="alert">
                <p class="font-bold">Perhatian!</p>
                <p>Terdapat data yang tidak valid. Silakan perbaiki file Excel dan upload ulang.</p>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto bg-gray-50 rounded-lg border border-gray-200 mb-6 max-h-[500px]">
            <table class="w-full text-left border-collapse relative">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-gray-100 text-xs uppercase text-gray-600 border-b border-gray-200 shadow-sm">
                        <th class="px-4 py-3 font-semibold bg-gray-100">Nama Mitra</th>
                        <th class="px-4 py-3 font-semibold bg-gray-100">Bidang Usaha</th>
                        <th class="px-4 py-3 font-semibold bg-gray-100">Lokasi</th>
                        <th class="px-4 py-3 font-semibold bg-gray-100">Kontak</th>
                        <th class="px-4 py-3 font-semibold bg-gray-100 text-center">Validasi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach ($preview_data as $row): ?>
                        <tr class="border-b border-gray-200 hover:bg-white transition <?= !$row['valid'] ? 'bg-red-50' : '' ?>">
                            <td class="px-4 py-3 align-top">
                                <div class="font-bold text-gray-800"><?= esc($row['nama_mitra']) ?></div>
                                <?php if (!empty($row['penanggung_jawab'])): ?>
                                    <div class="text-xs text-gray-500">PJ: <?= esc($row['penanggung_jawab']) ?></div>
                                <?php endif; ?>
                                <?php if (!$row['valid']): ?>
                                    <div class="text-xs text-red-600 font-bold mt-1">
                                        <ion-icon name="alert-circle-outline" class="mr-1"></ion-icon>
                                        <?= esc($row['error_msg']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                    <?= esc($row['bidang_usaha']) ?>
                                </span>
                                <?php if (!empty($row['jenis_kerjasama'])): ?>
                                    <span class="text-xs text-gray-500 block mt-1"><?= esc($row['jenis_kerjasama']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 align-top text-xs text-gray-600">
                                <?php
                                $lokasi = implode(', ', array_filter([$row['kota'] ?? '', $row['provinsi'] ?? '']));
                                echo $lokasi ?: '<span class="text-gray-400">-</span>';
                                ?>
                                <?php if (!empty($row['alamat'])): ?>
                                    <div class="text-gray-400 truncate max-w-[150px]" title="<?= esc($row['alamat']) ?>">
                                        <?= esc($row['alamat']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 align-top text-xs text-gray-600">
                                <?php if (!empty($row['email'])): ?>
                                    <div class="flex items-center"><ion-icon name="mail-outline" class="mr-1"></ion-icon>
                                        <?= esc($row['email']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($row['no_telp'])): ?>
                                    <div class="flex items-center"><ion-icon name="call-outline" class="mr-1"></ion-icon>
                                        <?= esc($row['no_telp']) ?></div>
                                <?php endif; ?>
                                <?php if (empty($row['email']) && empty($row['no_telp'])): ?>
                                    <span class="text-gray-400">-</span>
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

        <!-- Save Form -->
        <form action="<?= base_url('admin/mitra/save-import') ?>" method="post">
            <?= csrf_field() ?>
            <?php if ($nama_prodi): ?><input type="hidden" name="prodi" value="<?= esc($nama_prodi) ?>"><?php endif; ?>
            <?php if ($jenjang): ?><input type="hidden" name="jenjang" value="<?= esc($jenjang) ?>"><?php endif; ?>
            <?php if ($jurusan_kode): ?><input type="hidden" name="jurusan"
                    value="<?= esc($jurusan_kode) ?>"><?php endif; ?>
            <input type="hidden" name="bulk_data" value='<?= json_encode($preview_data) ?>'>

            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                <div>
                    <?php if ($has_error): ?>
                        <p class="text-sm text-red-500 font-bold">
                            <ion-icon name="warning-outline" class="mr-1"></ion-icon>
                            Tombol simpan dinonaktifkan karena ada data error.
                        </p>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">Pastikan data di atas sudah benar sebelum menyimpan.</p>
                    <?php endif; ?>
                </div>

                <div class="flex gap-3">
                    <a href="<?= $back_url ?>"
                        class="px-6 py-3 bg-gray-100 text-gray-600 font-semibold rounded-lg hover:bg-gray-200 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="<?= $has_error ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 shadow-lg hover:shadow-xl' ?> text-white font-bold py-3 px-8 rounded-lg transition duration-200 flex items-center"
                        <?= $has_error ? 'disabled' : '' ?>>
                        <ion-icon name="save-outline" class="mr-2 text-xl"></ion-icon>
                        Proses & Simpan Data
                    </button>
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
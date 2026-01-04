<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<?php
// Edit mode detection
$edit_mode = $edit_mode ?? false;
$existing = $existing_data ?? [];
$record_id = $record_id ?? null;
?>

<div class="w-full px-4 py-6">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 w-full">
        <!-- Form Header Deco -->
        <div class="h-2 bg-gradient-to-r from-purple-600 to-indigo-600"></div>

        <div class="p-8 md:p-10">
            <!-- Breadcrumb -->
            <nav class="flex items-center text-sm text-gray-500 mb-6">
                <a href="<?= base_url('admin/dashboard') ?>"
                    class="flex items-center hover:text-purple-600 transition-colors">
                    <ion-icon name="home-outline" class="mr-1.5 text-lg"></ion-icon>
                    <span class="font-medium">Dashboard</span>
                </a>
                <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>
                <a href="<?= $back_url ?>"
                    class="flex items-center hover:text-purple-600 transition-colors font-medium">
                    <ion-icon name="briefcase-outline" class="mr-1.5 text-lg"></ion-icon>
                    <span>IKU 2 (Tracer)</span>
                </a>
                <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>
                <div
                    class="flex items-center text-purple-700 bg-purple-50 px-3 py-1 rounded-full border border-purple-100 shadow-sm">
                    <span class="font-bold text-xs mr-1"><?= $edit_mode ? 'Edit:' : 'Input:' ?></span>
                    <span class="font-bold">Data Lulusan</span>
                </div>
            </nav>

            <!-- Title Header -->
            <div
                class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 pb-6 border-b border-gray-100">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight leading-tight">
                        <?= $edit_mode ? 'Edit Data Tracer Study (IKU 2)' : 'Input Tracer Study (IKU 2)' ?>
                    </h1>
                    <p class="text-gray-500 mt-2 text-sm">
                        <?= $edit_mode ? 'Edit data aktivitas lulusan yang sudah tersimpan.' : 'Validasi data mahasiswa dan input aktivitas lulusan (Bekerja, Wirausaha, Studi).' ?>
                    </p>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
                    role="alert">
                    <span class="block sm:inline"><?= session()->getFlashdata('success') ?></span>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline"><?= session()->getFlashdata('error') ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/iku2/store-final') ?>" method="post" enctype="multipart/form-data"
                id="formIku2">
                <?= csrf_field() ?>
                
                <!-- Hidden field for redirect on save (edit mode) -->
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="redirect_url" value="<?= esc($back_url) ?>">
                <?php endif; ?>

                <!-- SECTION 1: INFORMASI MAHASISWA -->
                <div class="mb-8">
                    <h3 class="flex items-center text-lg font-bold text-gray-800 mb-6 pb-2 border-b border-gray-100">
                        <div
                            class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mr-3">
                            <ion-icon name="person-outline"></ion-icon>
                        </div>
                        1. Informasi Mahasiswa
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <!-- Triwulan (Locked) -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Triwulan (Periode
                                Input)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <ion-icon name="calendar-outline" class="text-gray-400"></ion-icon>
                                </div>
                                <select name="id_triwulan"
                                    class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2.5 bg-gray-100 text-gray-500 pointer-events-none focus:outline-none"
                                    required tabindex="-1">
                                    <?php foreach ($triwulan_list as $t): ?>
                                        <option value="<?= $t['id'] ?>" <?= ($id_triwulan_selected == $t['id']) ? 'selected' : '' ?>>
                                            <?= $t['nama_triwulan'] ?>     <?= $t['status'] == 'Aktif' ? '(Aktif)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <ion-icon name="lock-closed-outline" class="text-gray-400"></ion-icon>
                                </div>
                            </div>
                        </div>

                        <!-- NIM -->
                        <div>
                            <label for="nim" class="block text-sm font-semibold text-gray-700 mb-2">NIM
                                Mahasiswa</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <ion-icon name="card-outline" class="text-gray-400"></ion-icon>
                                </div>
                                <input type="text" name="nim" id="nim"
                                    class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-500 transition <?= $edit_mode ? 'bg-gray-100' : '' ?>"
                                    placeholder="Masukkan NIM..." required
                                    value="<?= esc($existing['nim'] ?? '') ?>"
                                    <?= $edit_mode ? 'readonly' : '' ?>>
                            </div>
                            <p class="text-xs text-blue-600 mt-2 ml-1" id="nim-msg">
                                <ion-icon name="information-circle-outline" class="align-middle"></ion-icon>
                                Ketik NIM lalu tekan enter/klik luar.
                            </p>
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label for="nama_lengkap" class="block text-sm font-semibold text-gray-700 mb-2">Nama
                                Lengkap</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <ion-icon name="text-outline" class="text-gray-400"></ion-icon>
                                </div>
                                <input type="text" name="nama_lengkap" id="nama_lengkap"
                                    class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-200 <?= $edit_mode ? 'bg-gray-100' : '' ?>"
                                    placeholder="Nama akan terisi otomatis..." required
                                    value="<?= esc($existing['nama_lengkap'] ?? '') ?>"
                                    <?= $edit_mode ? 'readonly' : '' ?>>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: BIODATA DETAIL (HIDDEN INITIALLY) -->
                <div id="form-lengkap-mhs" class="hidden mb-8">
                    <div class="bg-blue-50/50 rounded-xl p-6 border border-blue-100">
                        <h3 class="flex items-center text-sm font-bold text-blue-800 mb-4 uppercase tracking-wider">
                            <ion-icon name="create-outline" class="mr-2 text-lg"></ion-icon>
                            Biodata Detail (Lengkapi untuk Mahasiswa Baru)
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- NIK -->
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase">NIK</label>
                                <input type="text" name="nik" id="nik"
                                    class="w-full border-gray-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    placeholder="16 Digit NIK">
                            </div>
                            <!-- No HP -->
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase">No.
                                    WhatsApp</label>
                                <input type="text" name="no_hp" id="no_hp"
                                    class="w-full border-gray-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    placeholder="08xxx">
                            </div>
                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase">Email
                                    Aktif</label>
                                <input type="email" name="email" id="email"
                                    class="w-full border-gray-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    placeholder="email@contoh.com">
                            </div>
                            <!-- JK -->
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-2 uppercase">Jenis
                                    Kelamin</label>
                                <div class="flex space-x-4">
                                    <label
                                        class="flex items-center bg-white px-3 py-2 rounded border border-gray-200 cursor-pointer hover:border-blue-300">
                                        <input type="radio" name="jenis_kelamin" value="L" class="text-blue-600">
                                        <span class="ml-2 text-xs font-medium">Laki-laki</span>
                                    </label>
                                    <label
                                        class="flex items-center bg-white px-3 py-2 rounded border border-gray-200 cursor-pointer hover:border-blue-300">
                                        <input type="radio" name="jenis_kelamin" value="P" class="text-blue-600">
                                        <span class="ml-2 text-xs font-medium">Perempuan</span>
                                    </label>
                                </div>
                            </div>
                            <!-- Semester -->
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-2 uppercase">Semester
                                    Masuk</label>
                                <div class="flex space-x-4">
                                    <label
                                        class="flex items-center bg-white px-3 py-2 rounded border border-gray-200 cursor-pointer hover:border-blue-300">
                                        <input type="radio" name="semester_masuk" value="Ganjil" class="text-blue-600">
                                        <span class="ml-2 text-xs font-medium">Ganjil</span>
                                    </label>
                                    <label
                                        class="flex items-center bg-white px-3 py-2 rounded border border-gray-200 cursor-pointer hover:border-blue-300">
                                        <input type="radio" name="semester_masuk" value="Genap" class="text-blue-600">
                                        <span class="ml-2 text-xs font-medium">Genap</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: DATA AKADEMIK -->
                <div class="mb-8">
                    <h3 class="flex items-center text-lg font-bold text-gray-800 mb-6 pb-2 border-b border-gray-100">
                        <div
                            class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3">
                            <ion-icon name="school-outline"></ion-icon>
                        </div>
                        2. Data Akademik
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <!-- Prodi -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Program Studi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <ion-icon name="business-outline" class="text-gray-400"></ion-icon>
                                </div>
                                <select name="kode_prodi" id="kode_prodi"
                                    class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-200 transition"
                                    required>
                                    <option value="">-- Pilih Program Studi --</option>
                                    <?php foreach ($prodi_list as $p): ?>
                                        <option value="<?= $p['kode_prodi'] ?>" <?= (isset($selected_kode_prodi) && $selected_kode_prodi == $p['kode_prodi']) ? 'selected' : '' ?>>
                                            <?= $p['nama_prodi'] ?> (<?= $p['jenjang'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!-- Tahun Masuk -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Masuk (Angkatan)</label>
                            <input type="number" name="tahun_masuk" id="tahun_masuk"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-200"
                                placeholder="YYYY" required>
                        </div>
                        <!-- Tanggal Yudisium -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Yudisium</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <ion-icon name="calendar-clear-outline" class="text-gray-400"></ion-icon>
                                </div>
                                <input type="date" name="tanggal_yudisium" id="tanggal_yudisium"
                                    class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-200"
                                    required>
                            </div>
                            <p class="text-xs text-gray-500 mt-1" id="yudisium-msg">Penting untuk perhitungan Masa
                                Tunggu.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: DATA AKTIVITAS -->
                <div class="mb-8">
                    <h3 class="flex items-center text-lg font-bold text-gray-800 mb-6 pb-2 border-b border-gray-100">
                        <div
                            class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3">
                            <ion-icon name="briefcase-outline"></ion-icon>
                        </div>
                        3. Data Aktivitas
                    </h3>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Aktivitas Saat Ini</label>
                        <select name="jenis_aktivitas" id="jenis_aktivitas"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-200"
                            required>
                            <option value="">-- Pilih Aktivitas --</option>
                            <option value="Bekerja" <?= ($existing['jenis_aktivitas'] ?? '') == 'Bekerja' ? 'selected' : '' ?>>Bekerja</option>
                            <option value="Wirausaha" <?= ($existing['jenis_aktivitas'] ?? '') == 'Wirausaha' ? 'selected' : '' ?>>Wirausaha</option>
                            <option value="Lanjut Studi" <?= ($existing['jenis_aktivitas'] ?? '') == 'Lanjut Studi' ? 'selected' : '' ?>>Melanjutkan Pendidikan (Lanjut Studi)</option>
                            <option value="Mencari Kerja" <?= ($existing['jenis_aktivitas'] ?? '') == 'Mencari Kerja' ? 'selected' : '' ?>>Sedang Mencari Kerja</option>
                        </select>
                    </div>

                    <!-- UMUM (Tanggal Mulai & Nama Tempat) -->
                    <div id="section_umum" class="hidden grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5"
                                value="<?= esc($existing['tanggal_mulai'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Tempat</label>
                            <input type="text" name="nama_tempat" id="nama_tempat"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5"
                                placeholder="Nama PT / Kampus / Usaha"
                                value="<?= esc($existing['nama_tempat'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- BEKERJA -->
                    <div id="section_bekerja" class="hidden mb-6 border border-blue-200 p-5 rounded-xl bg-blue-50/50">
                        <h4 class="font-bold text-blue-800 mb-4 flex items-center"><ion-icon name="cash-outline"
                                class="mr-2"></ion-icon> Detail Gaji & UMP</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Provinsi Tempat
                                    Kerja</label>
                                <select name="provinsi_tempat_kerja" id="provinsi_tempat_kerja"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5">
                                    <option value="">-- Pilih Provinsi --</option>
                                    <?php foreach ($ump_list as $ump): ?>
                                        <option value="<?= $ump['id'] ?>" data-ump="<?= $ump['nilai_ump'] ?>" <?= ($existing['provinsi_tempat_kerja'] ?? '') == $ump['id'] ? 'selected' : '' ?>>
                                            <?= $ump['provinsi'] ?> (<?= number_format($ump['nilai_ump'], 0, ',', '.') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Gaji Per Bulan
                                    (Rp)</label>
                                <input type="text" name="gaji_bulan" id="gaji_bulan"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5"
                                    placeholder="Contoh: 5.000.000"
                                    value="<?= !empty($existing['gaji_bulan']) ? number_format($existing['gaji_bulan'], 0, ',', '.') : '' ?>">
                            </div>
                        </div>
                        <!-- Smart Alert -->
                        <div id="alert_ump" class="mt-4 hidden p-4 rounded-lg text-sm bg-white border shadow-sm"></div>
                    </div>

                    <!-- WIRAUSAHA -->
                    <div id="section_wirausaha"
                        class="hidden mb-6 border border-orange-200 p-5 rounded-xl bg-orange-50/50">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Posisi dalam Usaha</label>
                        <select name="posisi_wirausaha" id="posisi_wirausaha"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5">
                            <option value="">-- Pilih Posisi --</option>
                            <option value="Pendiri" <?= ($existing['posisi_wirausaha'] ?? '') == 'Pendiri' ? 'selected' : '' ?>>Pendiri / Pemilik (Bobot: 0.75)</option>
                            <option value="Freelance" <?= ($existing['posisi_wirausaha'] ?? '') == 'Freelance' ? 'selected' : '' ?>>Freelance (Bobot: 0.25)</option>
                        </select>
                    </div>
                </div>

                <!-- SECTION 5: BUKTI -->
                <div class="mb-8">
                    <h3 class="flex items-center text-lg font-bold text-gray-800 mb-6 pb-2 border-b border-gray-100">
                        <div
                            class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center mr-3">
                            <ion-icon name="document-attach-outline"></ion-icon>
                        </div>
                        4. Bukti Pendukung
                    </h3>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 border-dashed">
                        <input type="file" name="bukti_validasi"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100"
                            accept=".pdf,.jpg,.png">
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                    <a href="<?= $back_url ?>"
                        class="mr-4 text-gray-500 hover:text-gray-700 font-medium transition">Batal</a>
                    <button type="submit"
                        class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition transform hover:-translate-y-1">
                        <ion-icon name="paper-plane-outline" class="mr-2"></ion-icon> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {

        // --- 1. CHECK NIM & AUTO FILL ---
        $('#nim').on('change blur', function () {
            var nim = $(this).val();
            var $msg = $('#nim-msg');
            var $extraForm = $('#form-lengkap-mhs');

            var $nama = $('#nama_lengkap');
            var $prodi = $('#kode_prodi');
            var $tahun = $('#tahun_masuk');
            var $yudisium = $('#tanggal_yudisium');
            var $yudisiumMsg = $('#yudisium-msg');

            if (nim) {
                $msg.html('<span class="text-yellow-600 animate-pulse">Checking...</span>');

                $.ajax({
                    url: "<?= base_url('admin/iku2/check_nim') ?>/" + nim,
                    type: "GET",
                    dataType: "JSON",
                    success: function (res) {
                        if (res.found) {
                            var m = res.data;
                            $msg.html('<span class="text-green-600 font-bold">✅ Data Mahasiswa Ditemukan</span>');

                            $nama.val(m.nama_lengkap).prop('readonly', true).addClass('bg-gray-100');
                            $prodi.val(m.kode_prodi).addClass('pointer-events-none bg-gray-100');
                            $tahun.val(m.tahun_masuk).prop('readonly', true).addClass('bg-gray-100');

                            $extraForm.slideUp();

                            if (m.tanggal_yudisium) {
                                $yudisium.val(m.tanggal_yudisium).addClass('bg-green-50');
                                $yudisiumMsg.html('<span class="text-green-600 font-bold">✅ Data Yudisium Ditemukan</span>');
                            } else {
                                $yudisium.val('').removeClass('bg-green-50');
                                $yudisiumMsg.html('<span class="text-orange-600 font-bold">⚠️ Belum Ada Data Yudisium</span> (Input Manual)');
                            }

                        } else {
                            $msg.html('<span class="inline-flex items-center text-blue-600 font-bold px-2 py-1 bg-blue-100 rounded text-xs">🆕 Data Baru</span> <span class="text-xs text-gray-500 ml-2">Silakan lengkapi biodata detail.</span>');

                            $nama.val('').prop('readonly', false).removeClass('bg-gray-100').focus();
                            $prodi.val('').removeClass('pointer-events-none bg-gray-100');
                            $tahun.val('').prop('readonly', false).removeClass('bg-gray-100');
                            $yudisium.val('').removeClass('bg-green-50');
                            $yudisiumMsg.text('Penting untuk perhitungan Masa Tunggu.');

                            $extraForm.slideDown();
                        }
                    },
                    error: function () {
                        $msg.html('<span class="text-red-500">Error Connection.</span>');
                    }
                });
            }
        });

        // --- 2. CONDITIONAL SECTIONS ---
        $('#jenis_aktivitas').change(function () {
            var val = $(this).val();
            $('#section_umum, #section_bekerja, #section_wirausaha').addClass('hidden');
            $('#alert_ump').addClass('hidden');

            if (val === 'Bekerja') {
                $('#section_umum, #section_bekerja').removeClass('hidden');
            } else if (val === 'Wirausaha') {
                $('#section_umum, #section_wirausaha').removeClass('hidden');
            } else if (val === 'Lanjut Studi') {
                $('#section_umum').removeClass('hidden');
            }
        });

        // --- 3. FORMAT CURRENCY ---
        $('#gaji_bulan').on('keyup', function () {
            var n = parseInt($(this).val().replace(/\D/g, ''), 10);
            if (!isNaN(n)) $(this).val(n.toLocaleString('id-ID'));
            checkUmp();
        });

        $('#provinsi_tempat_kerja').change(function () { checkUmp(); });

        function checkUmp() {
            var $sel = $('#provinsi_tempat_kerja option:selected');
            var ump = parseFloat($sel.data('ump')) || 0;
            var gaji = parseFloat($('#gaji_bulan').val().replace(/\./g, '')) || 0;

            if (ump > 0 && gaji > 0) {
                var target = ump * 1.2;
                var isLayak = gaji >= target;
                var html = `
                <div class="flex justify-between text-sm">
                   <div>UMP: <b>Rp ${ump.toLocaleString('id-ID')}</b></div>
                   <div>Target (1.2x): <b>Rp ${target.toLocaleString('id-ID')}</b></div>
                </div>
                <div class="mt-2 font-bold ${isLayak ? 'text-green-600' : 'text-red-600'}">
                   Status: ${isLayak ? '✅ LAYAK (Bobot Tinggi)' : '❌ TIDAK LAYAK'}
                </div>
            `;
                $('#alert_ump').html(html).removeClass('hidden border-red-500 border-green-500').addClass(isLayak ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50');
            }
        }

        // =============================================
        // EDIT MODE: Trigger visibility on page load
        // =============================================
        <?php if ($edit_mode): ?>
        // Trigger change event to show correct sections
        $('#jenis_aktivitas').trigger('change');
        
        // If existing data has values, make sure form section is visible
        if ($('#jenis_aktivitas').val()) {
            $('#section_umum').removeClass('hidden').addClass('grid');
        }
        <?php endif; ?>

    });
</script>

<?= $this->endSection() ?>
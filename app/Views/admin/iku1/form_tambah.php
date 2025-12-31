<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<div class="w-full px-4 py-6">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 w-full">
        <!-- Form Header Deco -->
        <div class="h-2 bg-gradient-to-r from-purple-600 to-indigo-600"></div>

        <div class="p-8 md:p-10">
            <!-- Styled Breadcrumb (Inside Card) -->
            <nav class="flex items-center text-sm text-gray-500 mb-6">
                <a href="<?= base_url('admin/dashboard') ?>"
                    class="flex items-center hover:text-purple-600 transition-colors">
                    <ion-icon name="home-outline" class="mr-1.5 text-lg"></ion-icon>
                    <span class="font-medium">Dashboard</span>
                </a>
                <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>

                <a href="<?= $back_url ?? base_url('admin/iku1/dashboard') ?>"
                    class="flex items-center hover:text-purple-600 transition-colors font-medium">
                    <ion-icon name="stats-chart-outline" class="mr-1.5 text-lg"></ion-icon>
                    <span>IKU 1 (AEE)</span>
                </a>
                <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>

                <div
                    class="flex items-center text-purple-700 bg-purple-50 px-3 py-1 rounded-full border border-purple-100 shadow-sm">
                    <span class="font-bold text-xs mr-1"><?= isset($data_edit) ? 'Edit' : 'Input' ?>:</span>
                    <span class="font-bold">Manual Data</span>
                </div>
            </nav>

            <!-- Title Header (Inside Card) -->
            <div
                class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 pb-6 border-b border-gray-100">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight leading-tight">
                        <?= $title ?>
                    </h1>
                    <p class="text-gray-500 mt-2 text-sm">Silakan lengkapi form di bawah ini dengan data yang valid.</p>
                </div>
            </div>

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

            <?php
            $is_edit = isset($data_edit);
            $form_action = $is_edit ? base_url('admin/iku1/update/' . $data_edit['id']) : base_url('admin/iku1/store');
            ?>
            <form action="<?= $form_action ?>" method="post">
                <?= csrf_field() ?>

                <?php if ($is_edit): ?>
                    <input type="hidden" name="redirect_to" value="<?= $redirect_to ?? '' ?>">
                <?php endif; ?>

                <!-- Section: Informasi Utama -->
                <div class="mb-8">
                    <h3 class="flex items-center text-lg font-bold text-gray-800 mb-6 pb-2 border-b border-gray-100">
                        <div
                            class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mr-3">
                            <ion-icon name="person-outline"></ion-icon>
                        </div>
                        Informasi Mahasiswa
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <!-- Triwulan (Full Width) -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Triwulan (Periode
                                Aktif)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <ion-icon name="calendar-outline" class="text-gray-400"></ion-icon>
                                </div>
                                <select name="id_triwulan" id="id_triwulan"
                                    class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-200 transition <?= isset($active_triwulan_id) ? 'pointer-events-none bg-gray-100 text-gray-500' : '' ?>"
                                    required>
                                    <option value="">-- Pilih Triwulan --</option>
                                    <?php foreach ($triwulan_list as $t): ?>
                                        <?php
                                        $selected = '';
                                        if (old('id_triwulan') == $t['id'])
                                            $selected = 'selected';
                                        elseif ($is_edit && $data_edit['id_triwulan'] == $t['id'])
                                            $selected = 'selected';
                                        elseif (isset($active_triwulan_id) && $active_triwulan_id == $t['id'])
                                            $selected = 'selected';
                                        ?>
                                        <option value="<?= $t['id'] ?>" <?= $selected ?>>
                                            <?= $t['nama_triwulan'] ?>     <?= $t['status'] == 'Aktif' ? '(Aktif)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($active_triwulan_id)): ?>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <ion-icon name="lock-closed-outline" class="text-gray-400"></ion-icon>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- NIM -->
                        <div>
                            <label for="nim" class="block text-sm font-semibold text-gray-700 mb-2">NIM</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <ion-icon name="card-outline" class="text-gray-400"></ion-icon>
                                </div>
                                <input type="text" name="nim" id="nim"
                                    class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-500 transition <?= $is_edit ? 'bg-gray-100' : '' ?>"
                                    placeholder="Masukkan NIM..." value="<?= old('nim', $data_edit['nim'] ?? '') ?>"
                                    <?= $is_edit ? 'readonly' : 'required' ?>>
                            </div>
                            <?php if (!$is_edit): ?>
                                <p class="text-xs text-blue-600 mt-2 ml-1" id="nim-msg">
                                    <ion-icon name="information-circle-outline" class="align-middle"></ion-icon>
                                    Ketik NIM lalu tekan enter/klik luar.
                                </p>
                            <?php endif; ?>
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
                                    class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-500 transition"
                                    placeholder="Nama Mahasiswa"
                                    value="<?= old('nama_lengkap', $data_edit['nama_lengkap'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Biodata Tambahan (Hidden by default) -->
                <div id="form-lengkap-mhs" class="<?= $is_edit ? '' : 'hidden' ?> mb-8">
                    <div class="bg-blue-50/50 rounded-xl p-6 border border-blue-100">
                        <h3 class="flex items-center text-sm font-bold text-blue-800 mb-4 uppercase tracking-wider">
                            <ion-icon name="create-outline" class="mr-2 text-lg"></ion-icon>
                            Biodata Detail (Lengkapi)
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- NIK -->
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase">NIK</label>
                                <input type="number" name="nik" id="nik"
                                    class="w-full border-gray-200 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                    placeholder="16 Digit NIK" value="<?= old('nik', $data_edit['nik'] ?? '') ?>">
                            </div>
                            <!-- No HP -->
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase">No.
                                    WhatsApp</label>
                                <input type="number" name="no_hp" id="no_hp"
                                    class="w-full border-gray-200 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                    placeholder="08xxx" value="<?= old('no_hp', $data_edit['no_hp'] ?? '') ?>">
                            </div>
                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase">Email
                                    Aktif</label>
                                <input type="email" name="email" id="email"
                                    class="w-full border-gray-200 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                    placeholder="email@contoh.com"
                                    value="<?= old('email', $data_edit['email'] ?? '') ?>">
                            </div>
                            <!-- JK & Semester -->
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-2 uppercase">Jenis
                                    Kelamin</label>
                                <div class="flex space-x-4">
                                    <?php $jk = old('jenis_kelamin', $data_edit['jenis_kelamin'] ?? ''); ?>
                                    <label
                                        class="flex items-center bg-white px-3 py-2 rounded border border-gray-200 cursor-pointer hover:border-blue-300">
                                        <input type="radio" name="jenis_kelamin" value="L" class="text-blue-600"
                                            <?= $jk == 'L' ? 'checked' : '' ?>>
                                        <span class="ml-2 text-xs font-medium">Laki-laki</span>
                                    </label>
                                    <label
                                        class="flex items-center bg-white px-3 py-2 rounded border border-gray-200 cursor-pointer hover:border-blue-300">
                                        <input type="radio" name="jenis_kelamin" value="P" class="text-blue-600"
                                            <?= $jk == 'P' ? 'checked' : '' ?>>
                                        <span class="ml-2 text-xs font-medium">Perempuan</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-2 uppercase">Semester
                                    Masuk</label>
                                <div class="flex space-x-4">
                                    <?php $smt = old('semester_masuk', $data_edit['semester_masuk'] ?? ''); ?>
                                    <label
                                        class="flex items-center bg-white px-3 py-2 rounded border border-gray-200 cursor-pointer hover:border-blue-300">
                                        <input type="radio" name="semester_masuk" value="Ganjil" class="text-blue-600"
                                            <?= $smt == 'Ganjil' ? 'checked' : '' ?>>
                                        <span class="ml-2 text-xs font-medium">Ganjil</span>
                                    </label>
                                    <label
                                        class="flex items-center bg-white px-3 py-2 rounded border border-gray-200 cursor-pointer hover:border-blue-300">
                                        <input type="radio" name="semester_masuk" value="Genap" class="text-blue-600"
                                            <?= $smt == 'Genap' ? 'checked' : '' ?>>
                                        <span class="ml-2 text-xs font-medium">Genap</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Data Akademik -->
                <div class="mb-8">
                    <h3 class="flex items-center text-lg font-bold text-gray-800 mb-6 pb-2 border-b border-gray-100">
                        <div
                            class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3">
                            <ion-icon name="school-outline"></ion-icon>
                        </div>
                        Data Akademik IKU
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
                                    class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-purple-200 transition <?= !empty($selected_kode_prodi) ? 'pointer-events-none bg-gray-100' : '' ?>"
                                    required>
                                    <option value="">-- Pilih Program Studi --</option>
                                    <?php foreach ($prodi_list as $p): ?>
                                        <?php
                                        $selProdi = '';
                                        if (old('kode_prodi') == $p['kode_prodi'])
                                            $selProdi = 'selected';
                                        elseif ($is_edit && $data_edit['kode_prodi'] == $p['kode_prodi'])
                                            $selProdi = 'selected';
                                        elseif (isset($selected_kode_prodi) && $selected_kode_prodi == $p['kode_prodi'])
                                            $selProdi = 'selected';
                                        ?>
                                        <option value="<?= $p['kode_prodi'] ?>" <?= $selProdi ?>>
                                            <?= $p['nama_prodi'] ?> (<?= $p['jenjang'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Tahun Masuk -->
                        <div>
                            <label for="tahun_masuk" class="block text-sm font-semibold text-gray-700 mb-2">Tahun Masuk
                                (Angkatan)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <ion-icon name="calendar-number-outline" class="text-gray-400"></ion-icon>
                                </div>
                                <input type="number" name="tahun_masuk" id="tahun_masuk"
                                    class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-200 transition"
                                    placeholder="Contoh: 2021"
                                    value="<?= old('tahun_masuk', $data_edit['tahun_masuk'] ?? '') ?>" required>
                            </div>
                        </div>

                        <!-- Tanggal Yudisium -->
                        <div>
                            <label for="tanggal_yudisium" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal
                                Yudisium</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <ion-icon name="calendar-clear-outline" class="text-gray-400"></ion-icon>
                                </div>
                                <input type="date" name="tanggal_yudisium" id="tanggal_yudisium"
                                    class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-200 transition cursor-pointer"
                                    value="<?= old('tanggal_yudisium', $data_edit['tanggal_yudisium'] ?? '') ?>"
                                    required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                    <a href="<?= $back_url ?? base_url('admin/iku1/import') ?>"
                        class="mr-4 text-gray-500 hover:text-gray-700 font-medium transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-purple-200 transition-all duration-300 transform hover:-translate-y-1">
                        <ion-icon name="<?= $is_edit ? 'save-outline' : 'paper-plane-outline' ?>"
                            class="inline-block mr-2 text-xl align-bottom"></ion-icon>
                        <?= $is_edit ? 'Simpan Perubahan' : 'Simpan Data' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // --- 1. LOGIKA AUTO-UNLOCK & CEK NIM ---
        $('#nim').on('change blur', function () {
            var nim = $(this).val();
            var $msg = $('#nim-msg');
            var $extraForm = $('#form-lengkap-mhs');

            if (nim) {
                // Show Spinner & Reset State
                $msg.html('<span class="inline-flex items-center text-yellow-600"><svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memeriksa data...</span>');

                $.ajax({
                    url: "<?= base_url('admin/iku1/check_nim') ?>/" + nim,
                    type: "GET",
                    dataType: "JSON",
                    success: function (response) {
                        if (response.found) {
                            var mhs = response.data;

                            // A. DATA DITEMUKAN (MAHASISWA LAMA)
                            // Isi & Kunci
                            $('#nama_lengkap').val(mhs.nama_lengkap).prop('readonly', true).addClass('bg-gray-100');
                            $('#kode_prodi').val(mhs.kode_prodi).prop('readonly', true).addClass('pointer-events-none bg-gray-100');
                            $('#tahun_masuk').val(mhs.tahun_masuk).prop('readonly', true).addClass('bg-gray-100');

                            // Sembunyikan Form Tambahan & Hapus Required
                            $extraForm.slideUp();
                            $extraForm.find('input').prop('required', false);

                            $msg.html('<span class="inline-flex items-center text-green-600 font-bold px-2 py-1 bg-green-100 rounded text-xs">✅ Data Terdaftar</span> <span class="text-xs text-gray-500 ml-2">Data Mahasiswa diambil dari database.</span>');
                        } else {
                            // B. DATA TIDAK DITEMUKAN (MAHASISWA BARU)
                            // Kosongkan & Buka Kunci
                            if ($('#nama_lengkap').prop('readonly')) {
                                $('#nama_lengkap').val('').prop('readonly', false).removeClass('bg-gray-100');
                                $('#kode_prodi').val('').prop('readonly', false).removeClass('pointer-events-none bg-gray-100');
                                $('#tahun_masuk').val('').prop('readonly', false).removeClass('bg-gray-100');
                            } else {
                                $('#nama_lengkap').prop('readonly', false).removeClass('bg-gray-100');
                                $('#kode_prodi').prop('readonly', false).removeClass('pointer-events-none bg-gray-100');
                                $('#tahun_masuk').prop('readonly', false).removeClass('bg-gray-100');
                            }

                            // Tampilkan Form Tambahan & Set Required
                            $extraForm.slideDown();
                            $('#nik, #no_hp, #email').prop('required', true);
                            $('input[name="jenis_kelamin"], input[name="semester_masuk"]').prop('required', true);

                            $msg.html('<span class="inline-flex items-center text-blue-600 font-bold px-2 py-1 bg-blue-100 rounded text-xs">🆕 Data Baru</span> <span class="text-xs text-gray-500 ml-2">Silakan lengkapi biodata detail.</span>');
                            $('#nama_lengkap').focus();
                        }

                        // Trigger calculation in case data changes
                        calculateDuration();
                    },
                    error: function () {
                        $msg.html('<span class="text-red-500">Gagal menghubungi server.</span>');
                    }
                });
            } else {
                // Jika input NIM kosong, sembunyikan form tambahan dan hapus required
                $extraForm.slideUp();
                $extraForm.find('input').prop('required', false);
            }
        });

        // --- 2. LOGIKA LIVE CALCULATION ---
        $('#tanggal_yudisium, #tahun_masuk, #kode_prodi').on('change keyup input', function () {
            calculateDuration();
        });

        function calculateDuration() {
            var thn_masuk = parseInt($('#tahun_masuk').val());
            var tgl_yudisium = $('#tanggal_yudisium').val();
            var kode_prodi = $('#kode_prodi').val();

            if (thn_masuk && tgl_yudisium && kode_prodi) {
                var d_yudisium = new Date(tgl_yudisium);
                var thn_yudisium = d_yudisium.getFullYear();

                // Hitung Durasi (Sederhana: Tahun ke Tahun)
                var durasi_tahun = thn_yudisium - thn_masuk;

                // Ambil Text Prodi untuk tebak jenjang
                var prodi_text = $("#kode_prodi option:selected").text();
                var jenjang = 'S1'; // Default
                if (prodi_text.includes('DIII') || prodi_text.includes('D3')) {
                    jenjang = 'D3';
                }

                // Tentukan Batas
                var max_tahun = (jenjang === 'D3') ? 3 : 4;
                var status_text = (durasi_tahun <= max_tahun) ? 'TEPAT WAKTU' : 'TERLAMBAT';
                var color_class = (durasi_tahun <= max_tahun) ? 'text-green-600' : 'text-red-600';

                // Buat Elemen Alert jika belum ada
                if ($('#calc-info').length === 0) {
                    $('<div id="calc-info" class="mt-4 p-3 rounded bg-gray-50 border border-gray-200 text-sm font-semibold"></div>').insertAfter('#tanggal_yudisium');
                }

                // Tampilkan Info
                $('#calc-info').html(
                    `Durasi Studi: ~${durasi_tahun} Tahun. <br>Status: <span class="${color_class} text-base uppercase">${status_text}</span>`
                );
            } else {
                $('#calc-info').remove();
            }
        }
    });
</script>

<?= $this->endSection() ?>
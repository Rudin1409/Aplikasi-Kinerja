<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<div class="w-full px-4 py-6">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 w-full">
        <!-- Form Header Deco -->
        <div class="h-2 bg-gradient-to-r from-blue-600 to-cyan-600"></div>

        <div class="p-8 md:p-10">
            <!-- Styled Breadcrumb (Inside Card) -->
            <nav class="flex items-center text-sm text-gray-500 mb-6">
                <a href="<?= base_url('admin/dashboard') ?>"
                    class="flex items-center hover:text-blue-600 transition-colors">
                    <ion-icon name="home-outline" class="mr-1.5 text-lg"></ion-icon>
                    <span class="font-medium">Dashboard</span>
                </a>
                <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>

                <div
                    class="flex items-center text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-100 shadow-sm">
                    <span class="font-bold text-xs mr-1"><?= isset($data_edit) ? 'Edit' : 'Input' ?>:</span>
                    <span class="font-bold">IKU 2 (Lulusan)</span>
                </div>
            </nav>

            <!-- Title Header (Inside Card) -->
            <div
                class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 pb-6 border-b border-gray-100">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight leading-tight">
                        <?= $title ?>
                    </h1>
                    <p class="text-gray-500 mt-2 text-sm">Lengkapi data tracer study lulusan (Bekerja/Studi/Wirausaha).
                    </p>
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
            $form_action = $is_edit ? base_url('admin/iku2/update/' . $data_edit['id']) : base_url('admin/iku2/store');
            ?>
            <form action="<?= $form_action ?>" method="post">
                <?= csrf_field() ?>

                <input type="hidden" name="redirect_to" value="<?= $redirect_to ?? '' ?>">

                <!-- Section: Informasi Mahasiswa -->
                <div class="mb-8">
                    <h3 class="flex items-center text-lg font-bold text-gray-800 mb-6 pb-2 border-b border-gray-100">
                        <div
                            class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
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
                                    class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-200 transition <?= isset($active_triwulan_id) ? 'pointer-events-none bg-gray-100 text-gray-500' : '' ?>"
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
                                    class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition <?= $is_edit ? 'bg-gray-100' : '' ?>"
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
                                    class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition"
                                    placeholder="Nama Mahasiswa"
                                    value="<?= old('nama_lengkap', $data_edit['nama_lengkap'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Biodata Tambahan (Hidden by default / Same as IKU 1) -->
                <div id="form-lengkap-mhs" class="<?= $is_edit ? '' : 'hidden' ?> mb-8">
                    <!-- Reuse same biodata fields (NIK, Email, Prodi, etc) -->
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Prodi -->
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Program Studi</label>
                                <select name="kode_prodi" id="kode_prodi"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5" required>
                                    <option value="">-- Pilih Prodi --</option>
                                    <?php foreach ($prodi_list as $p): ?>
                                        <?php
                                        $sel = '';
                                        if (old('kode_prodi', $selected_kode_prodi) == $p['kode_prodi'])
                                            $sel = 'selected';
                                        ?>
                                        <option value="<?= $p['kode_prodi'] ?>" <?= $sel ?>><?= $p['nama_prodi'] ?>
                                            (<?= $p['jenjang'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Tahun Masuk -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Masuk</label>
                                <input type="number" name="tahun_masuk" id="tahun_masuk"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5"
                                    value="<?= old('tahun_masuk', $data_edit['tahun_masuk'] ?? '') ?>" required>
                            </div>
                            <!-- NIK/Email Optional (Hidden to save space or reuse logic) -->
                            <!-- Adding Hidden inputs for required logic bypass if needed -->
                        </div>
                    </div>
                </div>

                <!-- Section: Data Tracer IKU 2 -->
                <div class="mb-8">
                    <h3 class="flex items-center text-lg font-bold text-gray-800 mb-6 pb-2 border-b border-gray-100">
                        <div
                            class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3">
                            <ion-icon name="briefcase-outline"></ion-icon>
                        </div>
                        Data Pekerjaan / Studi
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                        <!-- Status Aktivitas -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status Saat Ini</label>
                            <select name="status_aktivitas" id="status_aktivitas"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-200"
                                required>
                                <option value="">-- Pilih Status --</option>
                                <?php
                                $opts = ['Bekerja', 'Melanjutkan Pendidikan', 'Wirausaha', 'Mencari Kerja', 'Belum Memungkinkan Bekerja'];
                                $val = old('status_aktivitas', $data_edit['status_aktivitas'] ?? '');
                                foreach ($opts as $o):
                                    ?>
                                    <option value="<?= $o ?>" <?= $val == $o ? 'selected' : '' ?>><?= $o ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Nama Tempat (Conditional) -->
                        <div id="field-nama-tempat" class="w-full">
                            <label class="block text-sm font-semibold text-gray-700 mb-2" id="label-tempat">Nama Tempat
                                Kerja / Kampus</label>
                            <input type="text" name="nama_tempat" id="nama_tempat"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5"
                                value="<?= old('nama_tempat', $data_edit['nama_tempat'] ?? '') ?>"
                                placeholder="Nama PT / Univ">
                        </div>

                        <!-- Pendapatan (Conditional) -->
                        <div id="field-pendapatan" class="w-full">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pendapatan Bulanan
                                (Rp)</label>
                            <input type="number" name="pendapatan" id="pendapatan"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5"
                                value="<?= old('pendapatan', $data_edit['pendapatan'] ?? 0) ?>"
                                placeholder="Contoh: 5000000">
                        </div>

                        <!-- Masa Tunggu -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Masa Tunggu (Bulan)</label>
                            <input type="number" name="masa_tunggu_bulan" id="masa_tunggu_bulan"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5"
                                value="<?= old('masa_tunggu_bulan', $data_edit['masa_tunggu_bulan'] ?? 0) ?>" required>
                            <p class="text-xs text-gray-400 mt-1">Estimasi bulan sebelum mendapat pekerjaan pertama.</p>
                        </div>

                        <!-- Link Bukti -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Link Bukti
                                (Dokumen/SK)</label>
                            <input type="text" name="link_bukti" id="link_bukti"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5"
                                value="<?= old('link_bukti', $data_edit['link_bukti'] ?? '') ?>"
                                placeholder="https://drive.google.com/...">
                        </div>

                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                    <a href="<?= $back_url ?? base_url('admin/dashboard') ?>"
                        class="mr-4 text-gray-500 hover:text-gray-700 font-medium transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-blue-200 transition-all duration-300 transform hover:-translate-y-1">
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
        // --- 1. LOGIKA AUTO-UNLOCK & CEK NIM (Same as IKU 1) ---
        $('#nim').on('change blur', function () {
            var nim = $(this).val();
            // Reuse route check_nim from IKU 1 (It checks MahasiswaModel, generic)
            if (nim) {
                $.ajax({
                    url: "<?= base_url('admin/iku1/check_nim') ?>/" + nim,
                    type: "GET",
                    dataType: "JSON",
                    success: function (response) {
                        if (response.found) {
                            var mhs = response.data;
                            $('#nama_lengkap').val(mhs.nama_lengkap).prop('readonly', true).addClass('bg-gray-100');
                            $('#kode_prodi').val(mhs.kode_prodi).prop('readonly', true).addClass('pointer-events-none bg-gray-100');
                            $('#tahun_masuk').val(mhs.tahun_masuk).prop('readonly', true).addClass('bg-gray-100');
                            $('#form-lengkap-mhs').slideUp();
                        } else {
                            // New Data
                            $('#nama_lengkap').prop('readonly', false).removeClass('bg-gray-100');
                            $('#kode_prodi').prop('readonly', false).removeClass('pointer-events-none bg-gray-100');
                            $('#tahun_masuk').prop('readonly', false).removeClass('bg-gray-100');
                            $('#form-lengkap-mhs').slideDown();
                        }
                    }
                });
            }
        });

        // --- 2. LOGIKA CONDITIONAL FIELDS ---
        function checkStatus() {
            var status = $('#status_aktivitas').val();
            var $tempat = $('#field-nama-tempat');
            var $duit = $('#field-pendapatan');
            var $label = $('#label-tempat');

            if (status === 'Bekerja') {
                $tempat.show();
                $duit.show();
                $label.text('Nama Perusahaan / Instansi');
            } else if (status === 'Melanjutkan Pendidikan') {
                $tempat.show();
                $duit.hide();
                $label.text('Nama Universitas / Kampus');
            } else if (status === 'Wirausaha') {
                $tempat.show();
                $duit.show();
                $label.text('Nama Usaha');
            } else {
                $tempat.hide();
                $duit.hide();
            }
        }

        $('#status_aktivitas').on('change', checkStatus);
        checkStatus(); // Run on load
    });
</script>

<?= $this->endSection() ?>
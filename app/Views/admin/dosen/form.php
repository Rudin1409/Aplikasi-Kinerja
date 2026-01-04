<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<?php
// Determine if we have Prodi context (from URL params)
$prodi_locked = isset($prodi_info) && $prodi_info;
$selected_kode_prodi = $prodi_locked ? $prodi_info['kode_prodi'] : old('kode_prodi', $dosen['kode_prodi'] ?? '');

// Build query params for back URL
$query_params = [];
if (isset($nama_prodi) && $nama_prodi)
    $query_params['prodi'] = $nama_prodi;
if (isset($jenjang) && $jenjang)
    $query_params['jenjang'] = $jenjang;
if (isset($jurusan_kode) && $jurusan_kode)
    $query_params['jurusan'] = $jurusan_kode;
$query_string = http_build_query($query_params);
$back_url = base_url('admin/dosen') . ($query_string ? '?' . $query_string : '');
?>

<div class="min-h-screen bg-gray-50/50 p-6 md:p-8">
    <!-- Sophisticated Breadcrumb -->
    <nav
        class="flex items-center text-sm font-medium text-gray-500 mb-8 bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-100 max-w-7xl mx-auto">
        <a href="<?= base_url('admin/dashboard') ?>" class="hover:text-emerald-600 transition-colors flex items-center">
            <ion-icon name="home-outline" class="mr-2 text-lg"></ion-icon> Dashboard
        </a>
        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>

        <a href="<?= $back_url ?>" class="hover:text-emerald-600 transition-colors flex items-center">
            <ion-icon name="school-outline" class="mr-2 text-lg"></ion-icon> Master Dosen
        </a>

        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>

        <span class="text-emerald-600 flex items-center bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
            <ion-icon name="<?= isset($dosen) ? 'create-outline' : 'add-circle-outline' ?>" class="mr-2"></ion-icon>
            <?= isset($dosen) ? 'Edit Data' : 'Tambah Data' ?>
        </span>
    </nav>

    <!-- Main Card with Increased Width -->
    <div
        class="bg-white rounded-[2rem] shadow-xl border border-emerald-50/50 overflow-hidden max-w-7xl mx-auto relative">
        <!-- Decorative Top Border -->
        <div class="h-2 bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-500"></div>

        <!-- Header -->
        <div
            class="px-8 py-8 md:px-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-gray-100 bg-white">
            <div class="flex items-start md:items-center space-x-5">
                <div class="bg-emerald-600 p-4 rounded-2xl shadow-lg shadow-emerald-200 text-white transform -rotate-3">
                    <ion-icon name="<?= isset($dosen) ? 'create-outline' : 'person-add-outline' ?>"
                        class="text-3xl"></ion-icon>
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">
                        <?= isset($dosen) ? 'Edit Data Dosen' : 'Tambah Dosen Baru' ?>
                    </h1>
                    <?php if ($prodi_locked): ?>
                        <div class="flex items-center mt-2 text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg w-fit">
                            <ion-icon name="school-outline" class="mr-2"></ion-icon>
                            <span class="font-semibold text-sm">Prodi: <?= esc($prodi_info['jenjang']) ?>
                                <?= esc($prodi_info['nama_prodi']) ?></span>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm mt-1">Isi formulir berikut dengan data yang valid dan lengkap.</p>
                    <?php endif; ?>
                </div>
            </div>

            <a href="<?= $back_url ?>"
                class="group flex items-center px-5 py-2.5 bg-gray-50 text-gray-600 rounded-xl hover:bg-gray-100 font-medium transition-all duration-300 border border-gray-200 shadow-sm hover:shadow hover:-translate-y-0.5">
                <ion-icon name="arrow-back-outline"
                    class="mr-2 group-hover:-translate-x-1 transition-transform"></ion-icon>
                Kembali
            </a>
        </div>

        <form
            action="<?= isset($dosen) ? base_url('admin/dosen/update/' . $dosen['nidn']) : base_url('admin/dosen/store') ?>"
            method="POST" class="p-8 md:p-10">
            <?= csrf_field() ?>

            <!-- Hidden fields for redirect context -->
            <?php if (isset($nama_prodi) && $nama_prodi): ?><input type="hidden" name="redirect_prodi"
                    value="<?= esc($nama_prodi) ?>"><?php endif; ?>
            <?php if (isset($jenjang) && $jenjang): ?><input type="hidden" name="redirect_jenjang"
                    value="<?= esc($jenjang) ?>"><?php endif; ?>
            <?php if (isset($jurusan_kode) && $jurusan_kode): ?><input type="hidden" name="redirect_jurusan"
                    value="<?= esc($jurusan_kode) ?>"><?php endif; ?>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">

                <!-- Section 1: Identitas & Akademik -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 text-emerald-600 mb-2 border-b border-emerald-50 pb-2">
                        <ion-icon name="school-outline" class="text-xl"></ion-icon>
                        <h3 class="font-bold text-lg">Informasi Akademik</h3>
                    </div>

                    <!-- NIDN -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nomor Induk Dosen Nasional (NIDN) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nidn" value="<?= old('nidn', $dosen['nidn'] ?? '') ?>"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all font-mono text-gray-800 disabled:bg-gray-50 disabled:text-gray-500"
                            <?= isset($dosen) ? 'readonly' : 'required' ?> placeholder="Contoh: 0011223344">
                        <?php if ($validation->hasError('nidn')): ?>
                            <p class="text-red-500 text-xs mt-1 flex items-center">
                                <ion-icon name="alert-circle" class="mr-1"></ion-icon><?= $validation->getError('nidn') ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- NIDK & NUP -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">NIDK</label>
                            <input type="text" name="nidk" value="<?= old('nidk', $dosen['nidk'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all font-mono"
                                placeholder="Nomor Induk Dosen Khusus">
                        </div>
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">NUP</label>
                            <input type="text" name="nup" value="<?= old('nup', $dosen['nup'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all font-mono"
                                placeholder="Nomor Urut Pendidik">
                        </div>
                    </div>

                    <!-- Prodi Field - Locked or Dropdown -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Program Studi <span class="text-red-500">*</span>
                        </label>

                        <?php if ($prodi_locked): ?>
                            <!-- Locked Prodi Display -->
                            <input type="hidden" name="kode_prodi" value="<?= esc($prodi_info['kode_prodi']) ?>">
                            <div
                                class="w-full px-4 py-3 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 font-semibold flex items-center">
                                <ion-icon name="lock-closed-outline" class="mr-2 text-emerald-500"></ion-icon>
                                <?= esc($prodi_info['nama_prodi']) ?> (<?= esc($prodi_info['jenjang']) ?>)
                            </div>
                            <p class="text-xs text-gray-500 mt-1 flex items-center">
                                <ion-icon name="information-circle-outline" class="mr-1"></ion-icon>
                                Prodi terkunci karena Anda mengakses dari halaman prodi tertentu.
                            </p>
                        <?php else: ?>
                            <!-- Regular Prodi Dropdown -->
                            <div class="relative">
                                <select name="kode_prodi" required
                                    class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all appearance-none bg-white">
                                    <option value="">-- Pilih Prodi --</option>
                                    <?php foreach ($prodi as $p): ?>
                                        <option value="<?= $p['kode_prodi'] ?>" <?= ($selected_kode_prodi == $p['kode_prodi']) ? 'selected' : '' ?>>
                                            <?= $p['nama_prodi'] ?> (<?= $p['jenjang'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <ion-icon name="chevron-down-outline"
                                    class="absolute right-4 top-3.5 text-gray-400 pointer-events-none"></ion-icon>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Homebase -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi Homebase (Unit
                            Kerja)</label>
                        <input type="text" name="homebase" value="<?= old('homebase', $dosen['homebase'] ?? '') ?>"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                            placeholder="Contoh: Jurusan Teknik Sipil">
                    </div>

                    <!-- Status Kepegawaian & Jabatan Fungsional -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status Kepegawaian</label>
                            <div class="relative">
                                <select name="status_kepegawaian"
                                    class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all appearance-none bg-white">
                                    <option value="">-- Pilih --</option>
                                    <option value="Tetap" <?= (old('status_kepegawaian', $dosen['status_kepegawaian'] ?? '') == 'Tetap') ? 'selected' : '' ?>>Tetap</option>
                                    <option value="Tidak Tetap" <?= (old('status_kepegawaian', $dosen['status_kepegawaian'] ?? '') == 'Tidak Tetap') ? 'selected' : '' ?>>Tidak
                                        Tetap</option>
                                </select>
                                <ion-icon name="chevron-down-outline"
                                    class="absolute right-4 top-3.5 text-gray-400 pointer-events-none"></ion-icon>
                            </div>
                        </div>
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jabatan Fungsional</label>
                            <div class="relative">
                                <select name="jabatan_fungsional"
                                    class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all appearance-none bg-white">
                                    <option value="">-- Pilih --</option>
                                    <?php
                                    $jabfungs = ['Tenaga Pengajar', 'Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'];
                                    foreach ($jabfungs as $jf): ?>
                                        <option value="<?= $jf ?>" <?= (old('jabatan_fungsional', $dosen['jabatan_fungsional'] ?? '') == $jf) ? 'selected' : '' ?>><?= $jf ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <ion-icon name="chevron-down-outline"
                                    class="absolute right-4 top-3.5 text-gray-400 pointer-events-none"></ion-icon>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Data Diri & Kontak -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 text-emerald-600 mb-2 border-b border-emerald-50 pb-2">
                        <ion-icon name="person-outline" class="text-xl"></ion-icon>
                        <h3 class="font-bold text-lg">Informasi Pribadi & Kontak</h3>
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap"
                            value="<?= old('nama_lengkap', $dosen['nama_lengkap'] ?? '') ?>"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all font-medium"
                            required placeholder="Nama tanpa gelar">
                    </div>

                    <!-- Gelar Depan & Belakang -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Gelar Depan</label>
                            <input type="text" name="gelar_depan"
                                value="<?= old('gelar_depan', $dosen['gelar_depan'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                                placeholder="Dr., Ir., Prof.">
                        </div>
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Gelar Belakang</label>
                            <input type="text" name="gelar_belakang"
                                value="<?= old('gelar_belakang', $dosen['gelar_belakang'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                                placeholder="S.T., M.T., Ph.D.">
                        </div>
                    </div>

                    <!-- Email & No HP -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="<?= old('email', $dosen['email'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                                placeholder="dosen@polsri.ac.id">
                        </div>
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">No. HP / WA</label>
                            <input type="text" name="no_hp" value="<?= old('no_hp', $dosen['no_hp'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                                placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100">
                        <p class="text-sm text-emerald-700 flex items-start">
                            <ion-icon name="information-circle" class="mr-2 mt-0.5 text-lg"></ion-icon>
                            <span>Data dosen akan digunakan untuk keperluan pelaporan IKU dan terintegrasi dengan sistem
                                kinerja.</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-between">
                <p class="text-sm text-gray-500 italic"><span class="text-red-500">*</span> Field wajib diisi</p>
                <div class="flex space-x-4">
                    <a href="<?= $back_url ?>"
                        class="px-6 py-3 rounded-xl bg-gray-100 text-gray-600 font-semibold hover:bg-gray-200 transition">Batal</a>
                    <button type="submit"
                        class="px-8 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-lg hover:shadow-emerald-500/30 hover:scale-[1.02] transition-all duration-300 flex items-center">
                        <ion-icon name="save-outline" class="mr-2 text-xl"></ion-icon> Simpan Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
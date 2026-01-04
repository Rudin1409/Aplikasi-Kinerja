<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<div class="min-h-screen bg-gray-50/50 p-6 md:p-8">
    <!-- Sophisticated Breadcrumb -->
    <nav class="flex items-center text-sm font-medium text-gray-500 mb-8 bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-100 max-w-7xl mx-auto">
        <a href="<?= base_url('admin/dashboard') ?>" class="hover:text-indigo-600 transition-colors flex items-center">
             <ion-icon name="home-outline" class="mr-2 text-lg"></ion-icon> Dashboard
        </a>
        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>
        
        <a href="<?= $back_url ?? base_url('admin/mahasiswa') ?>" class="hover:text-indigo-600 transition-colors flex items-center">
             <ion-icon name="people-outline" class="mr-2 text-lg"></ion-icon> Master Mahasiswa
        </a>
        
        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>
        
        <span class="text-indigo-600 flex items-center bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100">
             <ion-icon name="<?= isset($mahasiswa) ? 'create-outline' : 'add-circle-outline' ?>" class="mr-2"></ion-icon>
             <?= isset($mahasiswa) ? 'Edit Data' : 'Tambah Data' ?>
        </span>
    </nav>

    <!-- Main Card with Increased Width -->
    <div class="bg-white rounded-[2rem] shadow-xl border border-indigo-50/50 overflow-hidden max-w-7xl mx-auto relative">
        <!-- Decorative Top Border -->
        <div class="h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-500"></div>

        <!-- Header -->
        <div class="px-8 py-8 md:px-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-gray-100 bg-white">
            <div class="flex items-start md:items-center space-x-5">
                <div class="bg-indigo-600 p-4 rounded-2xl shadow-lg shadow-indigo-200 text-white transform -rotate-3">
                    <ion-icon name="<?= isset($mahasiswa) ? 'create-outline' : 'person-add-outline' ?>" class="text-3xl"></ion-icon>
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">
                        <?= isset($mahasiswa) ? 'Edit Mahasiswa' : 'Tambah Mahasiswa' ?>
                    </h1>
                    <?php if(isset($prodi_info) && $prodi_info): ?>
                        <?php 
                            $jenjang_label = match ($prodi_info['jenjang']) {
                                'DIII' => 'D3',
                                'DIV' => 'D4',
                                default => $prodi_info['jenjang']
                            };
                        ?>
                        <div class="flex items-center mt-2 text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg w-fit">
                             <ion-icon name="school-outline" class="mr-2"></ion-icon>
                             <span class="font-semibold text-sm">Prodi: <?= $jenjang_label ?> <?= $prodi_info['nama_prodi'] ?></span>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm mt-1">Isi formulir berikut dengan data yang valid dan lengkap.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <a href="<?= $back_url ?? base_url('admin/mahasiswa') ?>"
                class="group flex items-center px-5 py-2.5 bg-gray-50 text-gray-600 rounded-xl hover:bg-gray-100 font-medium transition-all duration-300 border border-gray-200 shadow-sm hover:shadow hover:-translate-y-0.5">
                <ion-icon name="arrow-back-outline" class="mr-2 group-hover:-translate-x-1 transition-transform"></ion-icon>
                Kembali
            </a>
        </div>

        <form action="<?= isset($mahasiswa) ? base_url('admin/mahasiswa/update/' . $mahasiswa['nim']) : base_url('admin/mahasiswa/store') ?>" method="POST" class="p-8 md:p-10">
            <?= csrf_field() ?>

            <!-- Hidden fields for prodi context -->
            <?php if (isset($nama_prodi) && $nama_prodi): ?><input type="hidden" name="context_prodi"
                    value="<?= esc($nama_prodi) ?>"><?php endif; ?>
            <?php if (isset($jenjang) && $jenjang): ?><input type="hidden" name="context_jenjang"
                    value="<?= esc($jenjang) ?>"><?php endif; ?>
            <?php if (isset($jurusan_kode) && $jurusan_kode): ?><input type="hidden" name="context_jurusan"
                    value="<?= esc($jurusan_kode) ?>"><?php endif; ?>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">

                <!-- Section 1: Akademik -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 text-indigo-600 mb-2 border-b border-indigo-50 pb-2">
                        <ion-icon name="school-outline" class="text-xl"></ion-icon>
                        <h3 class="font-bold text-lg">Informasi Akademik</h3>
                    </div>

                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Induk Mahasiswa (NIM) <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nim" value="<?= old('nim', $mahasiswa['nim'] ?? '') ?>"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-mono text-gray-800 disabled:bg-gray-50 disabled:text-gray-500"
                            <?= isset($mahasiswa) ? 'readonly' : 'required' ?> placeholder="Contoh: 062030311234">
                        <?php if ($validation->hasError('nim')): ?>
                            <p class="text-red-500 text-xs mt-1 flex items-center"><ion-icon name="alert-circle"
                                    class="mr-1"></ion-icon><?= $validation->getError('nim') ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Prodi Field: Locked if context is provided -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Program Studi <span
                                class="text-red-500">*</span></label>
                        <?php if (isset($prodi_info) && $prodi_info): ?>
                            <!-- Locked Prodi Display -->
                            <?php
                            $jenjang_label = match ($prodi_info['jenjang']) {
                                'DIII' => 'D3',
                                'DIV' => 'D4',
                                default => $prodi_info['jenjang']
                            };
                            ?>
                            <input type="hidden" name="kode_prodi" value="<?= $prodi_info['kode_prodi'] ?>">
                            <div
                                class="w-full px-4 py-3 rounded-xl border border-indigo-200 bg-indigo-50 text-indigo-700 font-semibold flex items-center">
                                <ion-icon name="lock-closed-outline" class="mr-2 text-indigo-500"></ion-icon>
                                <?= $jenjang_label ?>     <?= $prodi_info['nama_prodi'] ?>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 flex items-center">
                                <ion-icon name="information-circle-outline" class="mr-1"></ion-icon>
                                Prodi otomatis sesuai konteks halaman.
                            </p>
                        <?php else: ?>
                            <!-- Dropdown for selecting Prodi -->
                            <div class="relative">
                                <select name="kode_prodi" required
                                    class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none bg-white">
                                    <option value="">-- Pilih Program Studi --</option>
                                    <?php foreach ($prodi as $p): ?>
                                        <option value="<?= $p['kode_prodi'] ?>" <?= (old('kode_prodi', $mahasiswa['kode_prodi'] ?? '') == $p['kode_prodi']) ? 'selected' : '' ?>>
                                            <?= $p['nama_prodi'] ?> (<?= $p['jenjang'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <ion-icon name="chevron-down-outline"
                                    class="absolute right-4 top-3.5 text-gray-400 pointer-events-none"></ion-icon>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Masuk</label>
                            <input type="number" name="tahun_masuk"
                                value="<?= old('tahun_masuk', $mahasiswa['tahun_masuk'] ?? date('Y')) ?>"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        </div>
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status Mahasiswa</label>
                            <div class="relative">
                                <select name="status"
                                    class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none bg-white">
                                    <?php $stats = ['Aktif', 'Lulus', 'Cuti', 'Keluar', 'Drop Out', 'Non-Aktif']; ?>
                                    <?php foreach ($stats as $s): ?>
                                        <option value="<?= $s ?>" <?= (old('status', $mahasiswa['status'] ?? 'Aktif') == $s) ? 'selected' : '' ?>><?= $s ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <ion-icon name="chevron-down-outline"
                                    class="absolute right-4 top-3.5 text-gray-400 pointer-events-none"></ion-icon>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Pribadi -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 text-indigo-600 mb-2 border-b border-indigo-50 pb-2">
                        <ion-icon name="person-outline" class="text-xl"></ion-icon>
                        <h3 class="font-bold text-lg">Informasi Pribadi</h3>
                    </div>

                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap"
                            value="<?= old('nama_lengkap', $mahasiswa['nama_lengkap'] ?? '') ?>"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium"
                            required placeholder="Nama Sesuai KTM">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">NIK</label>
                            <input type="text" name="nik" value="<?= old('nik', $mahasiswa['nik'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        </div>
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kelamin</label>
                            <div class="relative">
                                <select name="jenis_kelamin"
                                    class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none bg-white">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" <?= (old('jenis_kelamin', $mahasiswa['jenis_kelamin'] ?? '') == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= (old('jenis_kelamin', $mahasiswa['jenis_kelamin'] ?? '') == 'P') ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                                <ion-icon name="chevron-down-outline"
                                    class="absolute right-4 top-3.5 text-gray-400 pointer-events-none"></ion-icon>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="<?= old('email', $mahasiswa['email'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        </div>
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">No. HP / WA</label>
                            <input type="text" name="no_hp" value="<?= old('no_hp', $mahasiswa['no_hp'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Yudisium</label>
                        <input type="date" name="tanggal_yudisium"
                            value="<?= old('tanggal_yudisium', $mahasiswa['tanggal_yudisium'] ?? '') ?>"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        <p class="text-xs text-gray-500 mt-2 flex items-center"><ion-icon
                                name="information-circle-outline" class="mr-1"></ion-icon> Isi hanya jika mahasiswa
                            telah dinyatakan lulus.</p>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-between">
                <p class="text-sm text-gray-500 italic"><span class="text-red-500">*</span> Field wajib diisi</p>
                <div class="flex space-x-4">
                    <a href="<?= $back_url ?? base_url('admin/mahasiswa') ?>"
                        class="px-6 py-3 rounded-xl bg-gray-100 text-gray-600 font-semibold hover:bg-gray-200 transition">Batal</a>
                    <button type="submit"
                        class="px-8 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold shadow-lg hover:shadow-indigo-500/30 hover:scale-[1.02] transition-all duration-300 flex items-center">
                        <ion-icon name="save-outline" class="mr-2 text-xl"></ion-icon> Simpan Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
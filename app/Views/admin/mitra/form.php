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

<div class="min-h-screen bg-gray-50/50 p-6 md:p-8">
    <!-- Sophisticated Breadcrumb -->
    <nav
        class="flex items-center text-sm font-medium text-gray-500 mb-8 bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-100 max-w-7xl mx-auto">
        <a href="<?= base_url('admin/dashboard') ?>" class="hover:text-amber-600 transition-colors flex items-center">
            <ion-icon name="home-outline" class="mr-2 text-lg"></ion-icon> Dashboard
        </a>
        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>

        <a href="<?= $back_url ?>" class="hover:text-amber-600 transition-colors flex items-center">
            <ion-icon name="briefcase-outline" class="mr-2 text-lg"></ion-icon> Master Mitra
        </a>

        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>

        <span class="text-amber-600 flex items-center bg-amber-50 px-3 py-1 rounded-full border border-amber-100">
            <ion-icon name="<?= isset($mitra) ? 'create-outline' : 'add-circle-outline' ?>" class="mr-2"></ion-icon>
            <?= isset($mitra) ? 'Edit Data' : 'Tambah Data' ?>
        </span>
    </nav>

    <!-- Main Card with Increased Width -->
    <div class="bg-white rounded-[2rem] shadow-xl border border-amber-50/50 overflow-hidden max-w-7xl mx-auto relative">
        <!-- Decorative Top Border -->
        <div class="h-2 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-500"></div>

        <!-- Header -->
        <div
            class="px-8 py-8 md:px-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-gray-100 bg-white">
            <div class="flex items-start md:items-center space-x-5">
                <div class="bg-amber-600 p-4 rounded-2xl shadow-lg shadow-amber-200 text-white transform -rotate-3">
                    <ion-icon name="<?= isset($mitra) ? 'create-outline' : 'business-outline' ?>"
                        class="text-3xl"></ion-icon>
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">
                        <?= isset($mitra) ? 'Edit Data Mitra' : 'Tambah Mitra Baru' ?>
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">Lengkapi formulir di bawah ini dengan informasi kemitraan
                        valid.</p>
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
            action="<?= isset($mitra) ? base_url('admin/mitra/update/' . $mitra['id']) : base_url('admin/mitra/store') ?>"
            method="POST" class="p-8 md:p-10">
            <?= csrf_field() ?>

            <?php if (isset($nama_prodi) && $nama_prodi): ?><input type="hidden" name="redirect_prodi"
                    value="<?= esc($nama_prodi) ?>"><?php endif; ?>
            <?php if (isset($jenjang) && $jenjang): ?><input type="hidden" name="redirect_jenjang"
                    value="<?= esc($jenjang) ?>"><?php endif; ?>
            <?php if (isset($jurusan_kode) && $jurusan_kode): ?><input type="hidden" name="redirect_jurusan"
                    value="<?= esc($jurusan_kode) ?>"><?php endif; ?>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">

                <!-- Section 1: Profil Perusahaan -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 text-amber-600 mb-2 border-b border-amber-50 pb-2">
                        <ion-icon name="briefcase-outline" class="text-xl"></ion-icon>
                        <h3 class="font-bold text-lg">Profil Mitra / Industri</h3>
                    </div>

                    <!-- Nama Mitra -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Mitra / Perusahaan <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_mitra"
                            value="<?= old('nama_mitra', $mitra['nama_mitra'] ?? '') ?>"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all font-medium text-gray-800"
                            required placeholder="PT. Nama Perusahaan">
                        <?php if ($validation->hasError('nama_mitra')): ?>
                            <p class="text-red-500 text-xs mt-1 flex items-center"><ion-icon name="alert-circle"
                                    class="mr-1"></ion-icon><?= $validation->getError('nama_mitra') ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Bidang Usaha -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Bidang Usaha <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="bidang_usaha"
                            value="<?= old('bidang_usaha', $mitra['bidang_usaha'] ?? '') ?>"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all"
                            required placeholder="Contoh: Konstruksi, TIK, Manufaktur">
                    </div>

                    <!-- Kategori & Skala -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                            <div class="relative">
                                <select name="kategori"
                                    class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all appearance-none bg-white">
                                    <option value="">-- Pilih --</option>
                                    <?php
                                    $kategoris = ['Industri', 'Pemerintah', 'Pendidikan', 'NGO', 'Lainnya'];
                                    foreach ($kategoris as $k): ?>
                                        <option value="<?= $k ?>" <?= (old('kategori', $mitra['kategori'] ?? '') == $k) ? 'selected' : '' ?>><?= $k ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <ion-icon name="chevron-down-outline"
                                    class="absolute right-4 top-3.5 text-gray-400 pointer-events-none"></ion-icon>
                            </div>
                        </div>
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Skala</label>
                            <div class="relative">
                                <select name="skala"
                                    class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all appearance-none bg-white">
                                    <option value="">-- Pilih --</option>
                                    <?php
                                    $skalas = ['Lokal', 'Nasional', 'Internasional'];
                                    foreach ($skalas as $s): ?>
                                        <option value="<?= $s ?>" <?= (old('skala', $mitra['skala'] ?? '') == $s) ? 'selected' : '' ?>><?= $s ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <ion-icon name="chevron-down-outline"
                                    class="absolute right-4 top-3.5 text-gray-400 pointer-events-none"></ion-icon>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat Lengkap -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea name="alamat" rows="3"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all"><?= old('alamat', $mitra['alamat'] ?? '') ?></textarea>
                    </div>

                    <!-- Kota, Provinsi, Lokasi Provinsi -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kota</label>
                            <input type="text" name="kota" value="<?= old('kota', $mitra['kota'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                        </div>
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Provinsi</label>
                            <input type="text" name="provinsi" value="<?= old('provinsi', $mitra['provinsi'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                        </div>
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi (Ref)</label>
                            <input type="text" name="lokasi_provinsi"
                                value="<?= old('lokasi_provinsi', $mitra['lokasi_provinsi'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all"
                                placeholder="Opsional">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Kontak Person (CP) -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 text-amber-600 mb-2 border-b border-amber-50 pb-2">
                        <ion-icon name="call-outline" class="text-xl"></ion-icon>
                        <h3 class="font-bold text-lg">Kontak Person (CP)</h3>
                    </div>

                    <!-- Nama Penanggung Jawab -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab"
                            value="<?= old('penanggung_jawab', $mitra['penanggung_jawab'] ?? '') ?>"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all font-medium">
                    </div>

                    <!-- Jabatan -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jabatan</label>
                        <input type="text" name="jabatan_penanggung_jawab"
                            value="<?= old('jabatan_penanggung_jawab', $mitra['jabatan_penanggung_jawab'] ?? '') ?>"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all"
                            placeholder="Manajer HRD, Direktur, dll.">
                    </div>

                    <!-- No Telp & Email -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">No. Telp / WA</label>
                            <input type="text" name="no_telp" value="<?= old('no_telp', $mitra['no_telp'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                        </div>
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="<?= old('email', $mitra['email'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                        </div>
                    </div>

                    <!-- Jenis Kerjasama -->
                    <div class="bg-amber-50 p-4 rounded-xl border border-amber-100">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kerjasama</label>
                        <input type="text" name="jenis_kerjasama"
                            value="<?= old('jenis_kerjasama', $mitra['jenis_kerjasama'] ?? '') ?>"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all bg-white"
                            placeholder="MoU, MoA, Magang, Rekruitmen...">
                        <p class="text-xs text-amber-700 mt-2 flex items-center"><ion-icon
                                name="information-circle-outline" class="mr-1"></ion-icon> Sebutkan jenis kerjasama
                            utama.</p>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-amber-50/50 p-4 rounded-xl border border-amber-100">
                        <p class="text-sm text-amber-700 flex items-start">
                            <ion-icon name="information-circle" class="mr-2 mt-0.5 text-lg"></ion-icon>
                            <span>Data mitra akan digunakan untuk keperluan pelaporan IKU 5 (Kerjasama dengan
                                Industri).</span>
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
                        class="px-8 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold shadow-lg hover:shadow-amber-500/30 hover:scale-[1.02] transition-all duration-300 flex items-center">
                        <ion-icon name="save-outline" class="mr-2 text-xl"></ion-icon> Simpan Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
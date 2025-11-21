<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow">
    <div class="flex justify-center items-center space-x-6 mb-6">
        <img src="<?= base_url('public/assets/images/logo_polsri.png') ?>" alt="logo" class="h-20">
        <img src="<?= base_url('public/assets/images/logo_program.png') ?>" alt="logo" class="h-20">
    </div>

    <h1 class="text-2xl font-bold text-center"><?= esc($nama_jurusan) ?></h1>
    <p class="text-center text-gray-600 mb-6"><?= esc($kampus_nama) ?></p>

    <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse">
            <tbody>
                <tr class="border-b">
                    <td class="w-1/3 bg-gray-100 px-4 py-3 font-medium">Jurusan</td>
                    <td class="px-4 py-3"><?= esc($nama_jurusan) ?></td>
                </tr>
                <tr class="border-b">
                    <td class="bg-gray-100 px-4 py-3 font-medium">Jumlah Prodi</td>
                    <td class="px-4 py-3"><?= (int) $jumlah_prodi ?></td>
                </tr>
                <tr class="border-b align-top">
                    <td class="bg-gray-100 px-4 py-3 font-medium">Nama-nama Prodi</td>
                    <td class="px-4 py-3">
                        <?php if (!empty($nama_nama_prodi)): ?>
                            <ol class="list-decimal list-inside">
                            <?php foreach ($nama_nama_prodi as $idx => $p): ?>
                                <li><?= esc($p) ?></li>
                            <?php endforeach; ?>
                            </ol>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <tr class="border-b">
                    <td class="bg-gray-100 px-4 py-3 font-medium">Lokasi</td>
                    <td class="px-4 py-3"><?= esc($lokasi) ?></td>
                </tr>
                <tr class="border-b">
                    <td class="bg-gray-100 px-4 py-3 font-medium">Jumlah Mahasiswa Aktif</td>
                    <td class="px-4 py-3"><?= number_format((int)$jumlah_mahasiswa_aktif) ?></td>
                </tr>
                <tr class="border-b">
                    <td class="bg-gray-100 px-4 py-3 font-medium">Jumlah Lulusan Satu Tahun Terakhir</td>
                    <td class="px-4 py-3"><?= number_format((int)$jumlah_lulusan_satu_tahun) ?></td>
                </tr>
                <tr>
                    <td class="bg-gray-100 px-4 py-3 font-medium">Jumlah Dosen</td>
                    <td class="px-4 py-3"><?= number_format((int)$jumlah_dosen) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex space-x-3">
        <a href="<?= site_url('admin/akun-edit') ?>" class="px-4 py-2 bg-purple-100 text-purple-700 rounded inline-flex items-center">
            <ion-icon name="create-outline"></ion-icon>
            <span class="ml-2">Edit Akun</span>
        </a>
        <a href="<?= site_url('admin/dashboard') ?>" class="px-4 py-2 bg-gray-100 rounded">Back</a>
    </div>
</div>

<?= $this->endSection() ?>

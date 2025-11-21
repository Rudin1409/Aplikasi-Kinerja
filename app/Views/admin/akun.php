<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto">
    <div class="text-center mb-6">
        <img src="<?= base_url('assets/images/logo-polsri.png') ?>" alt="Logo Polsri" class="h-28 mx-auto mb-4">
        <h1 class="text-3xl font-bold text-gray-800"><?= $kampus_data['nama'] ?></h1>
    </div>

    <div class="bg-white border rounded shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <tbody>
                <tr class="border-b">
                    <th class="w-48 bg-gray-100 px-4 py-2 text-left font-medium">Kampus</th>
                    <td class="px-4 py-2"><?= $kampus_data['nama'] ?></td>
                </tr>
                <tr class="border-b">
                    <th class="bg-gray-100 px-4 py-2 text-left font-medium">Jumlah Jurusan</th>
                    <td class="px-4 py-2"><?= $jumlah_jurusan ?></td>
                </tr>
                <tr class="border-b align-top">
                    <th class="bg-gray-100 px-4 py-2 text-left font-medium align-top">Nama-nama Jurusan</th>
                    <td class="px-4 py-2">
                        <ol class="list-decimal list-inside space-y-0 leading-relaxed">
                            <?php foreach ($jurusan_list as $i => $j): ?>
                                <li><?= esc($j) ?></li>
                            <?php endforeach; ?>
                        </ol>
                    </td>
                </tr>
                <tr class="border-b">
                    <th class="bg-gray-100 px-4 py-2 text-left font-medium">Jumlah Prodi</th>
                    <td class="px-4 py-2"><?= $jumlah_prodi ?></td>
                </tr>
                <tr class="border-b">
                    <th class="bg-gray-100 px-4 py-2 text-left font-medium">Jumlah Mahasiswa Aktif</th>
                    <td class="px-4 py-2"><?= $jumlah_mahasiswa_aktif ?></td>
                </tr>
                <tr class="border-b">
                    <th class="bg-gray-100 px-4 py-2 text-left font-medium">Jumlah Lulusan Satu Tahun Terakhir</th>
                    <td class="px-4 py-2"><?= $jumlah_lulusan_satu_tahun ?></td>
                </tr>
                <tr>
                    <th class="bg-gray-100 px-4 py-2 text-left font-medium">Jumlah Dosen</th>
                    <td class="px-4 py-2"><?= $jumlah_dosen ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="flex justify-center space-x-6 mt-6">
        <a href="<?= base_url('admin/akun-edit') ?>" class="inline-flex items-center px-6 py-2 rounded bg-purple-200 hover:bg-purple-300 text-purple-900 text-sm font-medium transition">
            <ion-icon name="create-outline" class="mr-2 text-base"></ion-icon>
            Edit Akun
        </a>
        <a href="<?= base_url('logout') ?>" class="inline-flex items-center px-6 py-2 rounded bg-purple-200 hover:bg-purple-300 text-purple-900 text-sm font-medium transition">
            <ion-icon name="log-out-outline" class="mr-2 text-base"></ion-icon>
            Logout
        </a>
    </div>
</div>

<?= $this->endSection() ?>
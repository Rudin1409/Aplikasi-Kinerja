<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Edit Informasi Kampus</h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <form action="<?= site_url('admin/akun-save') ?>" method="post">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Nama Kampus</label>
            <div class="px-3 py-2 bg-gray-100 rounded"><?= esc($kampus['nama'] ?? 'Politeknik Negeri Sriwijaya') ?></div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Jumlah Mahasiswa Aktif</label>
                <input type="number" name="jumlah_mahasiswa_aktif" value="<?= esc($kampus['jumlah_mahasiswa_aktif'] ?? '') ?>" class="w-full border px-3 py-2 rounded" min="0">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Jumlah Lulusan 1 Thn</label>
                <input type="number" name="jumlah_lulusan_satu_tahun" value="<?= esc($kampus['jumlah_lulusan_satu_tahun'] ?? '') ?>" class="w-full border px-3 py-2 rounded" min="0">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Jumlah Dosen</label>
                <input type="number" name="jumlah_dosen" value="<?= esc($kampus['jumlah_dosen'] ?? '') ?>" class="w-full border px-3 py-2 rounded" min="0">
            </div>
        </div>

        <div class="mt-6 flex space-x-3">
            <button class="px-4 py-2 bg-purple-600 text-white rounded">Simpan</button>
            <a href="<?= site_url('admin/akun') ?>" class="px-4 py-2 bg-gray-200 rounded">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

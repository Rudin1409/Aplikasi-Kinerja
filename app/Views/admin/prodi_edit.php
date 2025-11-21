<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Edit Program Studi</h1>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form action="<?= site_url('admin/prodi-update/' . $prodi['id']) ?>" method="post">
        <?= csrf_field() ?>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Kode Prodi</label>
                <input type="text" name="kode_prodi" value="<?= esc($prodi['kode_prodi']) ?>" readonly class="w-full border px-3 py-2 rounded bg-gray-100" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Jurusan</label>
                <select name="jurusan_id" class="w-full border px-3 py-2 rounded">
                    <?php foreach ($jurusan_list as $j): ?>
                        <option value="<?= $j['kode'] ?>" <?= ($prodi['jurusan_id'] && $j['kode'] == ($prodi['jurusan_kode'] ?? '')) ? 'selected' : '' ?>><?= esc($j['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Nama Program Studi</label>
                <input type="text" name="nama_prodi" value="<?= esc($prodi['nama_prodi']) ?>" class="w-full border px-3 py-2 rounded" required />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Jenjang</label>
                <select name="jenjang" class="w-full border px-3 py-2 rounded" required>
                    <option value="D3" <?= $prodi['jenjang'] == 'D3' ? 'selected' : '' ?>>DIII</option>
                    <option value="D4" <?= $prodi['jenjang'] == 'D4' ? 'selected' : '' ?>>DIV / Sarjana Terapan</option>
                    <option value="S2" <?= $prodi['jenjang'] == 'S2' ? 'selected' : '' ?>>S2 Terapan / Magister Terapan</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Lokasi</label>
                <input type="text" name="lokasi" value="<?= esc($prodi['lokasi'] ?? '') ?>" class="w-full border px-3 py-2 rounded" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <select name="status" class="w-full border px-3 py-2 rounded">
                    <option value="active" <?= ($prodi['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($prodi['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
        </div>

        <div class="mt-6 flex space-x-3">
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded">Simpan Perubahan</button>
            <a href="<?= site_url('admin/prodi') ?>" class="px-4 py-2 bg-gray-200 rounded">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

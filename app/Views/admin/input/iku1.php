<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto">

    <!-- Header Form -->
    <div class="bg-blue-600 rounded-t-lg p-6 text-white shadow-lg">
        <h1 class="text-xl font-bold flex items-center space-x-2">
            <ion-icon name="stats-chart-outline" class="text-2xl"></ion-icon>
            <span>Formulir Data IKU 1: Angka Efisiensi Edukasi (AEE)</span>
        </h1>
        <p class="text-blue-100 text-sm mt-1">Masukkan data kohort mahasiswa untuk menghitung efisiensi edukasi.</p>
    </div>

    <form action="<?= base_url('admin/iku-save/1') ?>" method="post" class="bg-white rounded-b-lg shadow-md p-8">

        <input type="hidden" name="jurusan_kode" value="<?= esc($jurusan_kode) ?>">
        <input type="hidden" name="nama_prodi" value="<?= esc($nama_prodi) ?>">
        <input type="hidden" name="jenjang" value="<?= esc($jenjang) ?>">
        <input type="hidden" name="back_url" value="<?= esc($back_url ?? '') ?>">

        <!-- Info Prodi -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="block text-gray-500 font-semibold uppercase">Program Studi</span>
                    <span class="text-gray-800 font-bold text-lg"><?= esc(strtoupper($nama_prodi)) ?></span>
                </div>
                <div>
                    <span class="block text-gray-500 font-semibold uppercase">Jenjang</span>
                    <span class="text-gray-800 font-bold text-lg"><?= esc($jenjang) ?></span>
                </div>
            </div>

            <div class="mt-3 text-xs text-gray-600">
                <ion-icon name="information-circle-outline" class="align-middle"></ion-icon>
                <span>Target AEE Ideal untuk jenjang <strong><?= esc($jenjang) ?></strong> adalah
                    <?php
                    $jenjang_raw = strtoupper($jenjang);
                    if ($jenjang_raw == 'D3' || $jenjang_raw == 'S3')
                        echo '33%';
                    elseif ($jenjang_raw == 'S2')
                        echo '50%';
                    else
                        echo '25%'; // D4, S1
                    ?>.
                </span>
            </div>
        </div>

        <!-- Input Data -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Tahun Masuk -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tahun Akademi Masuk</label>
                <input type="number" name="tahun_masuk"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Contoh: 2021" required min="2000" max="2100">
                <p class="text-xs text-gray-400 mt-1">Tahun angkatan yang dihitung.</p>
            </div>

            <!-- Jumlah Masuk -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Jml Mahasiswa Masuk</label>
                <input type="number" name="jml_mhs_masuk"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="0" required min="1">
                <p class="text-xs text-gray-400 mt-1">Total mahasiswa terdaftar pada tahun tersebut.</p>
            </div>

            <!-- Jumlah Lulus Tepat Waktu -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Jml Lulus Tepat Waktu</label>
                <input type="number" name="jml_lulus_tepat_waktu"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="0" required min="0">
                <p class="text-xs text-gray-400 mt-1">Mahasiswa yang lulus sesuai masa studi standar.</p>
            </div>
        </div>

        <!-- Info Formula -->
        <div class="mb-8 border-l-4 border-blue-500 pl-4 py-2 bg-blue-50 text-sm text-blue-800">
            <strong>Formula AEE:</strong> <br>
            (Jml Lulus Tepat Waktu / Jml Mahasiswa Masuk) x 100%
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 border-t pt-6">
            <a href="<?= esc($back_url) ?>"
                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-100 font-semibold transition">Batal</a>
            <button type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold shadow-md transition flex items-center space-x-2">
                <ion-icon name="save-outline"></ion-icon>
                <span>Simpan & Hitung</span>
            </button>
        </div>

    </form>
</div>

<?= $this->endSection() ?>
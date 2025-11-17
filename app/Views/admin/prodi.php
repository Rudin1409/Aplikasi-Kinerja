<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Master Data Program Studi</h1>
            <p class="text-gray-600">Kelola daftar program studi (prodi) di Politeknik Negeri Sriwijaya.</p>
        </div>
        <button onclick="openModal('modal-tambah-prodi')"
                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition duration-300">
            <ion-icon name="add-outline" class="text-xl"></ion-icon>
            <span>Tambah Prodi</span>
        </button>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md w-full">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Daftar Program Studi</h2>
        
        <div class="mb-4">
            <label for="filterJurusan" class="text-sm text-gray-600">Filter berdasarkan Jurusan:</label>
            <select id="filterJurusan" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-full md:w-1/3">
                <option value="">Semua Jurusan</option>
                <?php foreach ($jurusan_list as $jurusan): ?>
                    <option value="<?= $jurusan['kode'] ?>"><?= $jurusan['nama'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">No</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">Kode Prodi</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">Nama Program Studi</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">Jenjang</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">Jurusan</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600 uppercase border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody id="prodi-table-body" class="text-gray-700">
                    
                    <?php $no = 1; ?>
                    <?php foreach ($prodi_list as $prodi): ?>
                    <tr class="prodi-row hover:bg-gray-50" data-jurusan="<?= $prodi['jurusan_kode'] ?>">
                        <td class="px-4 py-3 border-b"><?= $no++ ?></td>
                        <td class="px-4 py-3 border-b font-medium"><?= $prodi['kode_prodi'] ?></td>
                        <td class="px-4 py-3 border-b">
                            <a href="<?= base_url('admin/iku-prodi/' . $prodi['jurusan_kode'] . '/' . rawurlencode($prodi['nama_prodi']) . '/' . $prodi['jenjang']) ?>"
                               class="font-medium text-purple-700 hover:text-purple-900 transition duration-300">
                                <?= $prodi['nama_prodi'] ?>
                            </a>
                        </td>
                        <td class="px-4 py-3 border-b"><?= $prodi['jenjang'] ?></td>
                        <td class="px-4 py-3 border-b"><?= $prodi['nama_jurusan'] ?></td>
                        <td class="px-4 py-3 border-b text-center">
                            <button class="text-blue-500 hover:text-blue-700 p-1" title="Edit">
                                <ion-icon name="create-outline" class="text-xl"></ion-icon>
                            </button>
                            <button class="text-red-500 hover:text-red-700 p-1" title="Hapus">
                                <ion-icon name="trash-outline" class="text-xl"></ion-icon>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                </tbody>
            </table>
        </div>
    </div>

    <div id="modal-tambah-prodi" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm hidden">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-lg m-4">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Tambah Program Studi Baru</h2>
                <button onclick="closeModal('modal-tambah-prodi')" class="text-gray-500 hover:text-gray-800">
                    <ion-icon name="close-outline" class="text-2xl"></ion-icon>
                </button>
            </div>
            <form action="<?= base_url('admin/prodi/save') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="space-y-4">
                    <div>
                        <label for="jurusan_id" class="block text-sm font-medium text-gray-700 mb-1">Jurusan *</label>
                        <select id="jurusan_id" name="jurusan_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500" required>
                            <option value="">-- Pilih Jurusan --</option>
                            <?php foreach ($jurusan_list as $jurusan): ?>
                                <option value="<?= $jurusan['kode'] ?>"><?= $jurusan['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="kode_prodi" class="block text-sm font-medium text-gray-700 mb-1">Kode Prodi *</label>
                        <input type="text" id="kode_prodi" name="kode_prodi"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                               placeholder="Contoh: P01" required>
                    </div>
                    <div>
                        <label for="nama_prodi" class="block text-sm font-medium text-gray-700 mb-1">Nama Program Studi *</label>
                        <input type="text" id="nama_prodi" name="nama_prodi"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                               placeholder="Contoh: Manajemen Informatika" required>
                    </div>
                    <div>
                        <label for="jenjang" class="block text-sm font-medium text-gray-700 mb-1">Jenjang *</label>
                        <select id="jenjang" name="jenjang"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500" required>
                            <option value="">-- Pilih Jenjang --</option>
                            <option value="D3">DIII</option>
                            <option value="D4">DIV / Sarjana Terapan</option>
                            <option value="S2">S2 Terapan / Magister Terapan</option>
                        </select>
                    </div>
                </div>
                <div class="border-t pt-4 mt-6 text-right">
                    <button type="button" onclick="closeModal('modal-tambah-prodi')"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg mr-2 transition duration-300">
                        Batal
                    </button>
                    <button type="submit"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
        
        // 
        // ===== SCRIPT BARU UNTUK FILTER =====
        // 
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Ambil elemen dropdown
            const filter = document.getElementById('filterJurusan');
            
            // 2. Ambil semua baris data prodi
            const prodiRows = document.querySelectorAll('.prodi-row');

            // 3. Tambahkan event listener saat dropdown berubah
            filter.addEventListener('change', function() {
                const selectedJurusan = this.value;

                // 4. Loop setiap baris prodi
                prodiRows.forEach(row => {
                    const rowJurusan = row.getAttribute('data-jurusan');
                    
                    // 5. Tampilkan jika 'Semua Jurusan' dipilih ATAU jika jurusan baris = jurusan dipilih
                    if (selectedJurusan === "" || rowJurusan === selectedJurusan) {
                        row.style.display = 'table-row'; // Tampilkan baris
                    } else {
                        row.style.display = 'none'; // Sembunyikan baris
                    }
                });
            });
        });
    </script>

<?= $this->endSection() ?>
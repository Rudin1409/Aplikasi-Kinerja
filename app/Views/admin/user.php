<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Master Data User</h1>
            <p class="text-gray-600">Kelola akun pengguna untuk setiap role.</p>
        </div>
        <button onclick="openModal('modal-tambah-user')"
                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition duration-300">
            <ion-icon name="add-outline" class="text-xl"></ion-icon>
            <span>Tambah User</span>
        </button>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md w-full">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Daftar User</h2>
        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">No</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">Nama Lengkap</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">Email (Username)</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">Role</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase border-b">Status</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600 uppercase border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    
                    <?php $no = 1; ?>
                    <?php foreach ($user_list as $user): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border-b"><?= $no++ ?></td>
                        <td class="px-4 py-3 border-b font-medium"><?= $user['nama_lengkap'] ?></td>
                        <td class="px-4 py-3 border-b"><?= $user['email'] ?></td>
                        <td class="px-4 py-3 border-b">
                            <?php 
                                $color = 'bg-gray-200 text-gray-800';
                                if ($user['role'] == 'admin') $color = 'bg-red-200 text-red-800';
                                if ($user['role'] == 'prodi') $color = 'bg-blue-200 text-blue-800';
                                if ($user['role'] == 'jurusan') $color = 'bg-green-200 text-green-800';
                                if ($user['role'] == 'pimpinan') $color = 'bg-yellow-200 text-yellow-800';
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 border-b">
                            <?php $color = $user['status'] == 'aktif' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'; ?>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                                <?= ucfirst($user['status']) ?>
                            </span>
                        </td>
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

    <!-- Modal Tambah User -->
    <div id="modal-tambah-user" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm hidden">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-lg m-4">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Tambah User Baru</h2>
                <button onclick="closeModal('modal-tambah-user')" class="text-gray-500 hover:text-gray-800">
                    <ion-icon name="close-outline" class="text-2xl"></ion-icon>
                </button>
            </div>
            
            <form action="<?= base_url('admin/user/save') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="space-y-4">
                    
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                               placeholder="Contoh: Bahrudin" required>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email (untuk login) *</label>
                        <input type="email" id="email" name="email"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                               placeholder="Contoh: bahrudin@polsri.ac.id" required>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                        <input type="password" id="password" name="password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                               placeholder="••••••••" required>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                            <select id="role" name="role"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin">Admin</option>
                                <option value="pimpinan">Pimpinan</option>
                                <option value="jurusan">Jurusan</option>
                                <option value="prodi">Prodi</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select id="status" name="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500" required>
                                <option value="aktif">Aktif</option>
                                <option value="non-aktif">Non-Aktif</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Relasi Fields Container -->
                    <div id="relasi-container" class="space-y-4 hidden">
                        <div id="jurusan-field">
                            <label for="relasi_jurusan" class="block text-sm font-medium text-gray-700 mb-1">Tautkan ke Jurusan *</label>
                            <select id="relasi_jurusan" name="relasi_jurusan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                <option value="">-- Pilih Jurusan --</option>
                                <?php foreach ($jurusan_list as $jurusan): ?>
                                    <option value="<?= $jurusan['kode'] ?>"><?= $jurusan['nama'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="prodi-field" class="hidden">
                            <label for="relasi_prodi" class="block text-sm font-medium text-gray-700 mb-1">Tautkan ke Prodi *</label>
                            <select id="relasi_prodi"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                <option value="">-- Pilih Prodi --</option>
                            </select>
                        </div>
                    </div>
                    <!-- End of Relasi Fields -->

                    <input type="hidden" id="relasi_kode_final" name="relasi_kode">

                </div>

                <div class="border-t pt-4 mt-6 text-right">
                    <button type="button" onclick="closeModal('modal-tambah-user')"
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
    // --- REFACTORED SCRIPT FOR USER FORM ---

    // Data prodi dari controller
    const PRODI_DATA_GROUPED = <?= $prodi_list_json ?? "{}" ?>;

    // Element selectors
    const modal = document.getElementById('modal-tambah-user');
    const roleSelect = document.getElementById('role');
    const relasiContainer = document.getElementById('relasi-container');
    const jurusanField = document.getElementById('jurusan-field');
    const prodiField = document.getElementById('prodi-field');
    const relasiJurusanSelect = document.getElementById('relasi_jurusan');
    const relasiProdiSelect = document.getElementById('relasi_prodi');
    const relasiFinalInput = document.getElementById('relasi_kode_final');

    // Modal functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        // Reset form state when opening
        document.querySelector(`#${modalId} form`).reset();
        handleRoleChange();
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    /**
     * Handles UI changes and required attributes based on the selected role.
     */
    function handleRoleChange() {
        const role = roleSelect.value;

        // Hide all relasi fields by default
        relasiContainer.classList.add('hidden');
        jurusanField.classList.remove('hidden'); // Jurusan field is inside container
        prodiField.classList.add('hidden');
        
        // Remove required attributes by default
        relasiJurusanSelect.required = false;
        relasiProdiSelect.required = false;

        if (role === 'jurusan') {
            relasiContainer.classList.remove('hidden');
            prodiField.classList.add('hidden');
            relasiJurusanSelect.required = true;
        } else if (role === 'prodi') {
            relasiContainer.classList.remove('hidden');
            prodiField.classList.remove('hidden');
            relasiJurusanSelect.required = true;
            relasiProdiSelect.required = true;
            populateProdiDropdown(); // Populate prodi based on current jurusan
        }
        
        updateFinalRelasiCode(); // Update hidden input value
    }

    /**
     * Populates the 'Prodi' dropdown based on the selected 'Jurusan'.
     */
    function populateProdiDropdown() {
        const jurusanKode = relasiJurusanSelect.value;
        relasiProdiSelect.innerHTML = '<option value="">-- Pilih Prodi --</option>'; // Reset

        if (jurusanKode && PRODI_DATA_GROUPED[jurusanKode]) {
            PRODI_DATA_GROUPED[jurusanKode].forEach(prodi => {
                // Value contains both name and jenjang, separated by a pipe
                const optionValue = `${prodi.nama_prodi}|${prodi.jenjang}`;
                const optionText = `${prodi.nama_prodi} (${prodi.jenjang})`;
                
                const option = new Option(optionText, optionValue);
                relasiProdiSelect.add(option);
            });
        }
        updateFinalRelasiCode(); // Update hidden input value
    }

    /**
     * Constructs the final 'relasi_kode' string and sets it in the hidden input.
     * Format yang disimpan:
     * - role=jurusan: "J01" (hanya kode jurusan)
     * - role=prodi: "J01|P01" (kode jurusan | kode prodi)
     */
    function updateFinalRelasiCode() {
        const role = roleSelect.value;
        const jurusanKode = relasiJurusanSelect.value;
        const prodiData = relasiProdiSelect.value; // Format: "Nama Prodi|Jenjang"

        relasiFinalInput.value = ''; // Start with a clean slate

        if (role === 'jurusan' && jurusanKode) {
            // Untuk jurusan, simpan hanya kode jurusan: "J01"
            relasiFinalInput.value = jurusanKode;
        } else if (role === 'prodi' && jurusanKode && prodiData) {
            const [namaProdi, jenjang] = prodiData.split('|');
            if (namaProdi && jenjang) {
                // Cari kode prodi berdasarkan nama_prodi dan jenjang
                let prodiKode = 'P01'; // Default
                // Loop melalui PRODI_DATA_GROUPED untuk mencari kode prodi
                if (PRODI_DATA_GROUPED[jurusanKode]) {
                    PRODI_DATA_GROUPED[jurusanKode].forEach((p, index) => {
                        if (p.nama_prodi === namaProdi && p.jenjang === jenjang) {
                            // Buat kode prodi: P01, P02, P03, dst
                            prodiKode = 'P' + String(index + 1).padStart(2, '0');
                        }
                    });
                }
                // Simpan format: "J01|P01" (kode jurusan | kode prodi)
                relasiFinalInput.value = `${jurusanKode}|${prodiKode}`;
            }
        }
        // For 'admin' dan 'pimpinan', the value remains an empty string, which is correct.
    }

    // --- Attach Event Listeners ---
    roleSelect.addEventListener('change', handleRoleChange);
    relasiJurusanSelect.addEventListener('change', populateProdiDropdown);
    relasiProdiSelect.addEventListener('change', updateFinalRelasiCode);

</script>

<?= $this->endSection() ?>
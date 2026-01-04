<!-- ==============================================
     MODAL KELOLA DATA UMP (Upah Minimum Provinsi)
     Include this in: iku_detail.php
     NOTE: Tombol trigger sudah ada di halaman parent
     ============================================== -->

<!-- Modal UMP -->
<div id="umpModal" class="fixed inset-0 z-[9999] hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeUmpModal()"></div>

    <!-- Modal Content -->
    <div
        class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center text-white">
                        <ion-icon name="cash" class="text-3xl mr-3"></ion-icon>
                        <div>
                            <h3 class="text-xl font-bold">Manajemen Data UMP</h3>
                            <p class="text-amber-100 text-sm">Upah Minimum Provinsi Indonesia</p>
                        </div>
                    </div>
                    <button onclick="closeUmpModal()" class="text-white hover:text-amber-200 transition">
                        <ion-icon name="close-circle" class="text-3xl"></ion-icon>
                    </button>
                </div>
            </div>

            <!-- Form Tambah/Edit -->
            <div class="p-6 bg-amber-50 border-b border-amber-100">
                <form id="formUmp" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <input type="hidden" name="id" id="ump_id">

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase">Nama Provinsi</label>
                        <input type="text" name="provinsi" id="ump_provinsi"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-300 focus:border-amber-500"
                            placeholder="Contoh: Jawa Tengah" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase">Nilai UMP (Rp)</label>
                        <input type="text" name="nilai_ump" id="ump_nilai"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-300 focus:border-amber-500"
                            placeholder="3.000.000" required oninput="formatUmpInput(this)">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1 uppercase">Tahun</label>
                        <input type="number" name="tahun" id="ump_tahun"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-300 focus:border-amber-500"
                            placeholder="2025" value="<?= date('Y') ?>" min="2020" max="2030" required>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" id="btnSaveUmp"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition flex items-center justify-center">
                            <ion-icon name="save" class="mr-2"></ion-icon>
                            <span id="btnSaveText">Simpan</span>
                        </button>
                    </div>
                </form>

                <!-- Search -->
                <div class="mt-4">
                    <div class="relative">
                        <ion-icon name="search"
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></ion-icon>
                        <input type="text" id="searchUmp"
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-200"
                            placeholder="Cari provinsi..." oninput="searchUmpData(this.value)">
                    </div>
                </div>
            </div>

            <!-- Table Content -->
            <div class="overflow-y-auto max-h-[400px]">
                <table class="w-full text-left">
                    <thead class="sticky top-0 bg-gray-100 text-xs uppercase text-gray-600">
                        <tr>
                            <th class="px-6 py-3 font-semibold">No</th>
                            <th class="px-6 py-3 font-semibold">Provinsi</th>
                            <th class="px-6 py-3 font-semibold text-right">Nilai UMP</th>
                            <th class="px-6 py-3 font-semibold text-center">Tahun</th>
                            <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="umpTableBody" class="text-sm divide-y divide-gray-100">
                        <!-- Data akan di-load via AJAX -->
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <ion-icon name="hourglass" class="text-4xl mb-2 animate-spin"></ion-icon>
                                <p>Memuat data...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    <span id="umpTotalCount">0</span> provinsi tersimpan
                </div>
                <button onclick="closeUmpModal()"
                    class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-lg transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript untuk Modal UMP -->
<script>
    const UMP_API_URL = '<?= base_url("admin/ump") ?>';

    // Open/Close Modal
    function openUmpModal() {
        document.getElementById('umpModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        loadUmpData();
    }

    function closeUmpModal() {
        document.getElementById('umpModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        resetUmpForm();
    }

    // Reset Form
    function resetUmpForm() {
        document.getElementById('formUmp').reset();
        document.getElementById('ump_id').value = '';
        document.getElementById('ump_tahun').value = new Date().getFullYear();
        document.getElementById('btnSaveText').textContent = 'Simpan';
    }

    // Format Input UMP (Thousands Separator)
    function formatUmpInput(input) {
        let value = input.value.replace(/\D/g, '');
        input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Load Data UMP
    function loadUmpData() {
        fetch(UMP_API_URL)
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    renderUmpTable(result.data);
                    document.getElementById('umpTotalCount').textContent = result.total;
                }
            })
            .catch(error => {
                console.error('Error loading UMP:', error);
                document.getElementById('umpTableBody').innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-red-500">
                        <ion-icon name="alert-circle" class="text-4xl mb-2"></ion-icon>
                        <p>Gagal memuat data</p>
                    </td>
                </tr>
            `;
            });
    }

    // Render Table
    function renderUmpTable(data) {
        const tbody = document.getElementById('umpTableBody');

        if (data.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                    <ion-icon name="file-tray-outline" class="text-4xl mb-2"></ion-icon>
                    <p>Belum ada data UMP</p>
                </td>
            </tr>
        `;
            return;
        }

        let html = '';
        data.forEach((row, index) => {
            html += `
            <tr class="hover:bg-amber-50 transition">
                <td class="px-6 py-3 text-gray-500">${index + 1}</td>
                <td class="px-6 py-3 font-medium text-gray-800">${escapeHtml(row.provinsi)}</td>
                <td class="px-6 py-3 text-right font-bold text-green-600">${row.nilai_ump_formatted}</td>
                <td class="px-6 py-3 text-center">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold">${row.tahun}</span>
                </td>
                <td class="px-6 py-3 text-center">
                    <div class="inline-flex space-x-1">
                        <button onclick="editUmp(${row.id}, '${escapeHtml(row.provinsi)}', ${row.nilai_ump}, ${row.tahun})" 
                            class="p-1.5 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded transition" title="Edit">
                            <ion-icon name="create-outline"></ion-icon>
                        </button>
                        <button onclick="deleteUmp(${row.id}, '${escapeHtml(row.provinsi)}')" 
                            class="p-1.5 bg-red-100 hover:bg-red-200 text-red-600 rounded transition" title="Hapus">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        });

        tbody.innerHTML = html;
    }

    // Edit UMP
    function editUmp(id, provinsi, nilai, tahun) {
        document.getElementById('ump_id').value = id;
        document.getElementById('ump_provinsi').value = provinsi;
        document.getElementById('ump_nilai').value = nilai.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        document.getElementById('ump_tahun').value = tahun;
        document.getElementById('btnSaveText').textContent = 'Update';

        // Scroll ke form
        document.getElementById('formUmp').scrollIntoView({ behavior: 'smooth' });
    }

    // Delete UMP
    function deleteUmp(id, provinsi) {
        if (!confirm(`Yakin ingin menghapus data UMP "${provinsi}"?`)) return;

        fetch(`${UMP_API_URL}/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    showToast(result.message, 'success');
                    loadUmpData();
                } else {
                    showToast(result.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Gagal menghapus data', 'error');
            });
    }

    // Search UMP
    function searchUmpData(keyword) {
        const url = keyword ? `${UMP_API_URL}/search?q=${encodeURIComponent(keyword)}` : UMP_API_URL;

        fetch(url)
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    renderUmpTable(result.data);
                    document.getElementById('umpTotalCount').textContent = result.total;
                }
            });
    }

    // Form Submit Handler
    document.getElementById('formUmp').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const btn = document.getElementById('btnSaveUmp');
        btn.disabled = true;
        btn.innerHTML = '<ion-icon name="hourglass" class="mr-2 animate-spin"></ion-icon> Menyimpan...';

        fetch(`${UMP_API_URL}/store`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(result => {
                btn.disabled = false;
                btn.innerHTML = '<ion-icon name="save" class="mr-2"></ion-icon><span id="btnSaveText">Simpan</span>';

                if (result.status === 'success') {
                    showToast(result.message, 'success');
                    resetUmpForm();
                    loadUmpData();

                    // Update dropdown provinsi jika ada
                    if (typeof updateProvinsiDropdown === 'function') {
                        updateProvinsiDropdown();
                    }
                } else {
                    showToast(result.message, 'error');
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = '<ion-icon name="save" class="mr-2"></ion-icon><span id="btnSaveText">Simpan</span>';
                console.error('Error:', error);
                showToast('Gagal menyimpan data', 'error');
            });
    });

    // Toast Notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-[10000] px-6 py-4 rounded-xl shadow-2xl flex items-center space-x-3 animate-bounce ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
        toast.innerHTML = `
        <ion-icon name="${type === 'success' ? 'checkmark-circle' : 'alert-circle'}" class="text-2xl"></ion-icon>
        <span class="font-bold">${message}</span>
    `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }

    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
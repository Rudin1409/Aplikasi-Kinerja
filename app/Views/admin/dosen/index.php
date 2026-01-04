<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<!-- Header & Stats Section -->
<div class="mb-8">
    <nav class="flex items-center text-sm text-gray-500 mb-5">
        <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center hover:text-emerald-600 transition-colors">
            <ion-icon name="home-outline" class="mr-2 text-lg"></ion-icon>
            <span class="font-medium">Dashboard</span>
        </a>
        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>
        <a href="<?= $back_url ?>" class="flex items-center hover:text-emerald-600 transition-colors">
            <ion-icon name="stats-chart-outline" class="mr-2 text-lg"></ion-icon>
            <span class="font-medium">Capaian IKU</span>
        </a>
        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>
        <div
            class="flex items-center text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100 shadow-sm">
            <ion-icon name="school-outline" class="mr-2 mt-0.5"></ion-icon>
            <span class="font-bold">Master Dosen</span>
        </div>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Title & Action Card -->
        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative group flex flex-col justify-between h-full">
            <div class="absolute inset-0 overflow-hidden rounded-2xl">
                <div
                    class="absolute top-0 right-0 -mr-20 -mt-20 w-72 h-72 rounded-full bg-gradient-to-br from-emerald-100 to-teal-50 opacity-60 blur-3xl group-hover:scale-110 transition-transform duration-700">
                </div>
            </div>
            <div class="relative z-10">
                <div class="mb-4">
                    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 tracking-tight leading-tight mb-2">
                        <?= $title ?>
                    </h1>
                    <?php if (isset($prodi_info) && $prodi_info): ?>
                        <p class="text-gray-600 flex items-center text-sm">
                            <ion-icon name="school-outline" class="mr-2 text-emerald-500"></ion-icon>Data Dosen Prodi
                        </p>
                    <?php elseif (isset($jurusan_info) && $jurusan_info): ?>
                        <p class="text-gray-600 flex items-center text-sm">
                            <ion-icon name="business-outline" class="mr-2 text-emerald-500"></ion-icon>Jurusan:
                            <?= esc($jurusan_info['nama_jurusan']) ?>
                        </p>
                    <?php endif; ?>

                    <!-- IKU Usage Description -->
                    <div class="mt-3 p-3 bg-emerald-50/70 rounded-xl border border-emerald-100">
                        <div class="flex items-start text-xs text-emerald-700">
                            <ion-icon name="information-circle" class="mr-2 mt-0.5 text-base flex-shrink-0"></ion-icon>
                            <div>
                                <span class="font-semibold">Digunakan untuk:</span>
                                <span class="text-emerald-600">IKU 3 (Dosen Berkegiatan Luar)</span> &bull;
                                <span class="text-emerald-600">IKU 4 (Kualifikasi S3/Sertifikasi)</span> &bull;
                                <span class="text-emerald-600">IKU 5 (Hasil Riset & Sitasi)</span>
                            </div>
                        </div>
                    </div>
                </div>


                <?php
                $query_params = [];
                if ($nama_prodi)
                    $query_params['prodi'] = $nama_prodi;
                if ($jenjang)
                    $query_params['jenjang'] = $jenjang;
                if ($jurusan_kode)
                    $query_params['jurusan'] = $jurusan_kode;
                $query_string = http_build_query($query_params);
                ?>
                <div class="flex flex-wrap gap-2 mt-auto">
                    <a href="<?= base_url('admin/dosen/import') . ($query_string ? '?' . $query_string : '') ?>"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:text-gray-900 transition shadow-sm text-sm">
                        <ion-icon name="cloud-upload-outline" class="text-lg mr-2 text-green-600"></ion-icon>Import
                    </a>

                    <!-- Export Dropdown -->
                    <div class="relative" id="export-dropdown-container">
                        <button type="button" id="export-dropdown-btn"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:text-gray-900 transition shadow-sm text-sm">
                            <ion-icon name="cloud-download-outline" class="text-lg mr-2 text-yellow-600"></ion-icon>
                            Export
                            <ion-icon name="chevron-down-outline" class="ml-2 text-xs"></ion-icon>
                        </button>
                        <div id="export-dropdown-menu"
                            class="hidden absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50 border border-gray-200">
                            <a href="<?= base_url('admin/dosen/export') . ($query_string ? '?' . $query_string : '') ?>"
                                class="flex items-center px-4 py-2 text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 transition text-sm">
                                <ion-icon name="document-outline" class="mr-2 text-lg"></ion-icon>Export Semua Data
                            </a>
                            <button type="button" onclick="exportSelected()" id="btn-export-selected"
                                class="w-full flex items-center px-4 py-2 text-gray-400 cursor-not-allowed transition text-sm"
                                disabled>
                                <ion-icon name="checkmark-done-outline" class="mr-2 text-lg"></ion-icon>Export Yang
                                Dipilih
                            </button>
                        </div>
                    </div>

                    <a href="<?= base_url('admin/dosen/create') . ($query_string ? '?' . $query_string : '') ?>"
                        class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-md transition text-sm">
                        <ion-icon name="add-circle-outline" class="text-lg mr-2"></ion-icon>Tambah
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Card -->
        <div
            class="relative overflow-hidden text-white p-8 flex items-center justify-between shadow-lg transform hover:-translate-y-1 transition-transform duration-300 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 h-full">
            <div class="relative z-10">
                <p class="text-emerald-100 text-sm font-semibold uppercase tracking-wider mb-2">Total Dosen</p>
                <h2 class="text-5xl font-black"><?= number_format($pager->getTotal('dosen') ?? 0) ?></h2>
                <p class="text-emerald-100 text-sm mt-2">Dosen Terdaftar</p>
            </div>
            <div class="bg-white/20 p-5 rounded-full backdrop-blur-sm">
                <ion-icon name="school" class="text-4xl"></ion-icon>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action Toolbar -->
<div id="bulk-action-bar"
    class="hidden mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex flex-wrap items-center justify-between gap-4 shadow-sm animate-pulse">
    <div class="flex items-center space-x-3">
        <div class="bg-emerald-600 text-white px-3 py-1.5 rounded-lg font-bold text-sm">
            <span id="selected-count">0</span> Dipilih
        </div>
        <button type="button" onclick="clearSelection()" class="text-gray-500 hover:text-gray-700 p-1 rounded">
            <ion-icon name="close-circle" class="text-xl"></ion-icon>
        </button>
    </div>
    <div class="flex items-center space-x-2">
        <button type="button" onclick="exportSelected()"
            class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg transition text-sm shadow">
            <ion-icon name="download-outline" class="mr-2"></ion-icon>Export Dipilih
        </button>
        <button type="button" onclick="deleteSelected()"
            class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition text-sm shadow">
            <ion-icon name="trash-outline" class="mr-2"></ion-icon>Hapus Dipilih
        </button>
    </div>
</div>

<!-- Flash Message -->
<?php if (session()->getFlashdata('success') || session()->getFlashdata('error')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const msg = "<?= esc(session()->getFlashdata('success') ?? session()->getFlashdata('error')) ?>";
            const isError = <?= session()->getFlashdata('error') ? 'true' : 'false' ?>;
            const bgClass = isError ? 'bg-red-500' : 'bg-emerald-500';
            const icon = isError ? 'alert-circle' : 'checkmark-circle';
            const toast = document.createElement('div');
            toast.className = `fixed top-6 right-6 z-[9999] ${bgClass} text-white px-6 py-4 rounded-xl shadow-2xl flex items-center space-x-3 animate-bounce`;
            toast.innerHTML = `<ion-icon name="${icon}" class="text-2xl"></ion-icon><span class="font-bold">${msg}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.transition = 'all 0.5s ease-out'; toast.style.opacity = '0'; toast.style.transform = 'translateY(-20px)'; setTimeout(() => toast.remove(), 500); }, 4000);
        });
    </script>
<?php endif; ?>

<!-- Main Content Card -->
<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    <div
        class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <ion-icon name="list-outline" class="mr-2 text-emerald-600"></ion-icon>
            Daftar Dosen
        </h2>
        <form action="<?= base_url('admin/dosen') ?>" method="get" class="flex items-center space-x-2">
            <?php if ($nama_prodi): ?><input type="hidden" name="prodi" value="<?= esc($nama_prodi) ?>"><?php endif; ?>
            <?php if ($jenjang): ?><input type="hidden" name="jenjang" value="<?= esc($jenjang) ?>"><?php endif; ?>
            <?php if ($jurusan_kode): ?><input type="hidden" name="jurusan"
                    value="<?= esc($jurusan_kode) ?>"><?php endif; ?>
            <div class="relative">
                <select name="per_page" onchange="this.form.submit()"
                    class="pl-3 pr-8 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 appearance-none shadow-sm cursor-pointer font-medium text-gray-600">
                    <option value="20" <?= ($per_page == 20) ? 'selected' : '' ?>>20</option>
                    <option value="50" <?= ($per_page == 50) ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= ($per_page == 100) ? 'selected' : '' ?>>100</option>
                </select>
                <ion-icon name="chevron-down-outline"
                    class="absolute right-2 top-2.5 text-gray-400 pointer-events-none text-xs"></ion-icon>
            </div>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <ion-icon name="search-outline"
                        class="text-gray-400 group-focus-within:text-emerald-500 transition-colors"></ion-icon>
                </div>
                <input type="text" name="keyword" value="<?= esc($keyword) ?>" placeholder="Cari Nama / NIDN..."
                    class="pl-10 pr-16 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 w-48 sm:w-64 transition-all shadow-sm">
                <button type="submit"
                    class="absolute inset-y-0 right-0 px-3 text-sm font-semibold text-emerald-600 hover:text-emerald-800 rounded-r-lg transition-colors">Cari</button>
            </div>
        </form>
    </div>

    <!-- Table with Checkboxes -->
    <form id="bulk-form" action="<?= base_url('admin/dosen/bulk-delete') ?>" method="post">
        <?= csrf_field() ?>
        <?php if ($nama_prodi): ?><input type="hidden" name="prodi" value="<?= esc($nama_prodi) ?>"><?php endif; ?>
        <?php if ($jenjang): ?><input type="hidden" name="jenjang" value="<?= esc($jenjang) ?>"><?php endif; ?>
        <?php if ($jurusan_kode): ?><input type="hidden" name="jurusan"
                value="<?= esc($jurusan_kode) ?>"><?php endif; ?>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-xs text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-4 py-4 text-center w-12">
                            <input type="checkbox" id="select-all"
                                class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        </th>
                        <th class="px-4 py-4 text-center w-12 font-semibold">No</th>

                        <?php
                        $sort_by = $sort_by ?? 'nama_lengkap';
                        $sort_order = $sort_order ?? 'asc';

                        $base_params = [];
                        if ($nama_prodi)
                            $base_params['prodi'] = $nama_prodi;
                        if ($jenjang)
                            $base_params['jenjang'] = $jenjang;
                        if ($jurusan_kode)
                            $base_params['jurusan'] = $jurusan_kode;
                        if ($keyword)
                            $base_params['keyword'] = $keyword;
                        if ($per_page)
                            $base_params['per_page'] = $per_page;

                        $createSortLink = function ($field, $label) use ($base_params, $sort_by, $sort_order) {
                            $new_order = ($sort_by === $field && $sort_order === 'asc') ? 'desc' : 'asc';
                            $params = array_merge($base_params, ['sort_by' => $field, 'sort_order' => $new_order]);
                            $url = base_url('admin/dosen') . '?' . http_build_query($params);
                            $icon = 'swap-vertical-outline';
                            $iconClass = 'text-gray-300';
                            if ($sort_by === $field) {
                                $icon = $sort_order === 'asc' ? 'caret-up-outline' : 'caret-down-outline';
                                $iconClass = 'text-emerald-600';
                            }
                            return "<a href='{$url}' class='group flex items-center justify-between hover:text-emerald-600 transition-colors cursor-pointer'><span>{$label}</span><ion-icon name='{$icon}' class='ml-1 text-sm {$iconClass} group-hover:text-emerald-500'></ion-icon></a>";
                        };
                        ?>

                        <th class="px-6 py-4 font-semibold"><?= $createSortLink('nama_lengkap', 'Identitas Dosen') ?>
                        </th>
                        <th class="px-6 py-4 font-semibold">Kontak</th>
                        <th class="px-6 py-4 font-semibold"><?= $createSortLink('kode_prodi', 'Prodi') ?></th>
                        <th class="px-6 py-4 font-semibold text-center">Ket. Data</th>
                        <th class="px-6 py-4 font-semibold text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php $i = 1 + ($per_page * (($pager->getCurrentPage('dosen') ?? 1) - 1)); ?>
                    <?php foreach ($dosen as $d): ?>
                        <tr class="hover:bg-emerald-50/30 transition-colors duration-200 group"
                            data-nidn="<?= esc($d['nidn']) ?>">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" name="selected_nidns[]" value="<?= esc($d['nidn']) ?>"
                                    class="row-checkbox h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            </td>
                            <td class="px-4 py-4 text-center text-gray-500 font-medium"><?= $i++ ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="h-10 w-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs mr-3 shadow-sm group-hover:scale-110 transition-transform">
                                        <?= strtoupper(substr($d['nama_lengkap'], 0, 2)) ?>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">
                                            <?= esc(($d['gelar_depan'] ? $d['gelar_depan'] . ' ' : '') . $d['nama_lengkap'] . ($d['gelar_belakang'] ? ', ' . $d['gelar_belakang'] : '')) ?>
                                        </div>
                                        <div
                                            class="text-xs text-gray-500 font-mono mt-0.5 bg-gray-100 inline-block px-1.5 py-0.5 rounded border border-gray-200">
                                            NIDN: <?= esc($d['nidn']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-1">
                                    <?php if (!empty($d['email'])): ?>
                                        <div class="flex items-center text-xs text-gray-600">
                                            <ion-icon name="mail-outline"
                                                class="mr-1.5 text-gray-400"></ion-icon><?= esc($d['email']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($d['no_hp'])): ?>
                                        <div class="flex items-center text-xs text-gray-600">
                                            <ion-icon name="call-outline"
                                                class="mr-1.5 text-gray-400"></ion-icon><?= esc($d['no_hp']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (empty($d['email']) && empty($d['no_hp'])): ?>
                                        <span class="text-gray-400 text-xs italic">Tidak ada kontak</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex w-fit items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <?= esc($prodi_map[$d['kode_prodi']] ?? $d['kode_prodi']) ?>
                                </span>
                                <?php if (!empty($d['homebase'])): ?>
                                    <div class="text-xs text-gray-500 mt-1 flex items-center">
                                        <ion-icon name="location-outline" class="mr-1"></ion-icon><?= esc($d['homebase']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php $isComplete = !empty($d['email']) && !empty($d['no_hp']) && !empty($d['nidn']); ?>
                                <?php if ($isComplete): ?>
                                    <div class="inline-flex items-center px-2 py-1 rounded bg-green-50 text-green-600 text-xs font-medium border border-green-100"
                                        title="Data Lengkap">
                                        <ion-icon name="checkmark-done-circle" class="mr-1 text-lg"></ion-icon> Lengkap
                                    </div>
                                <?php else: ?>
                                    <div class="inline-flex items-center px-2 py-1 rounded bg-orange-50 text-orange-600 text-xs font-medium border border-orange-100"
                                        title="Data Belum Lengkap">
                                        <ion-icon name="alert-circle" class="mr-1 text-lg"></ion-icon> Belum Lengkap
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div
                                    class="flex items-center justify-center space-x-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <a href="<?= base_url('admin/dosen/edit/' . $d['nidn']) ?>"
                                        class="p-2 bg-white text-emerald-600 rounded-lg border border-gray-200 hover:bg-emerald-50 hover:border-emerald-200 transition-all shadow-sm"
                                        title="Edit Data">
                                        <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                    </a>
                                    <a href="<?= base_url('admin/dosen/delete/' . $d['nidn']) ?>"
                                        onclick="return confirm('Apakah anda yakin ingin menghapus data dosen ini?')"
                                        class="p-2 bg-white text-red-500 rounded-lg border border-gray-200 hover:bg-red-50 hover:border-red-200 transition-all shadow-sm"
                                        title="Hapus">
                                        <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($dosen)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-50 p-4 rounded-full mb-3">
                                        <ion-icon name="file-tray-outline" class="text-3xl text-gray-400"></ion-icon>
                                    </div>
                                    <h3 class="text-gray-500 font-medium text-lg">Tidak ada data ditemukan</h3>
                                    <p class="text-gray-400 text-sm mt-1">Silakan tambah data baru atau import dari Excel.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>

    <!-- Hidden form for export selected -->
    <form id="export-selected-form" action="<?= base_url('admin/dosen/export-selected') ?>" method="post"
        class="hidden">
        <?= csrf_field() ?>
        <input type="hidden" name="selected_nidns" id="export-selected-nidns">
    </form>

    <!-- Pagination -->
    <div
        class="p-6 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
        <span class="text-sm text-gray-600">
            Menampilkan <strong><?= count($dosen) ?></strong> dari <strong><?= $pager->getTotal('dosen') ?></strong>
            data
        </span>
        <?= $pager->links('dosen', 'default_full') ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkActionBar = document.getElementById('bulk-action-bar');
        const selectedCountEl = document.getElementById('selected-count');
        const btnExportSelected = document.getElementById('btn-export-selected');
        const exportDropdownBtn = document.getElementById('export-dropdown-btn');
        const exportDropdownMenu = document.getElementById('export-dropdown-menu');

        // Toggle export dropdown
        exportDropdownBtn?.addEventListener('click', function (e) {
            e.stopPropagation();
            exportDropdownMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', function () {
            exportDropdownMenu?.classList.add('hidden');
        });

        function updateBulkUI() {
            const checked = document.querySelectorAll('.row-checkbox:checked');
            const count = checked.length;
            selectedCountEl.textContent = count;
            if (count > 0) {
                bulkActionBar.classList.remove('hidden');
                btnExportSelected.disabled = false;
                btnExportSelected.classList.remove('text-gray-400', 'cursor-not-allowed');
                btnExportSelected.classList.add('text-gray-700', 'hover:bg-emerald-50', 'hover:text-emerald-600');
            } else {
                bulkActionBar.classList.add('hidden');
                btnExportSelected.disabled = true;
                btnExportSelected.classList.add('text-gray-400', 'cursor-not-allowed');
                btnExportSelected.classList.remove('text-gray-700', 'hover:bg-emerald-50', 'hover:text-emerald-600');
            }
        }

        selectAllCheckbox?.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkUI();
        });

        rowCheckboxes.forEach(cb => cb.addEventListener('change', function () {
            if (!this.checked) selectAllCheckbox.checked = false;
            else if (document.querySelectorAll('.row-checkbox:checked').length === rowCheckboxes.length) selectAllCheckbox.checked = true;
            updateBulkUI();
        }));
    });

    function clearSelection() {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        document.getElementById('bulk-action-bar').classList.add('hidden');
        document.getElementById('selected-count').textContent = '0';
    }

    function deleteSelected() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        if (checked.length === 0) { alert('Pilih data terlebih dahulu.'); return; }
        if (confirm(`Apakah Anda yakin ingin menghapus ${checked.length} data dosen yang dipilih?`)) {
            document.getElementById('bulk-form').submit();
        }
    }

    function exportSelected() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        if (checked.length === 0) { alert('Pilih data terlebih dahulu.'); return; }
        const nidns = Array.from(checked).map(cb => cb.value);
        document.getElementById('export-selected-nidns').value = JSON.stringify(nidns);
        document.getElementById('export-selected-form').submit();
    }
</script>

<?= $this->endSection() ?>
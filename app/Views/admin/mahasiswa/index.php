<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

<!-- Header & Stats Section (Side by Side) -->
<div class="mb-8">
    <nav class="flex items-center text-sm text-gray-500 mb-5">
        <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center hover:text-indigo-600 transition-colors">
            <ion-icon name="home-outline" class="mr-2 text-lg"></ion-icon>
            <span class="font-medium">Dashboard</span>
        </a>
        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>

        <a href="<?= $back_url ?>" class="flex items-center hover:text-indigo-600 transition-colors">
            <ion-icon name="stats-chart-outline" class="mr-2 text-lg"></ion-icon>
            <span class="font-medium">Capaian IKU</span>
        </a>

        <ion-icon name="chevron-forward-outline" class="mx-3 text-gray-300 text-xs"></ion-icon>
        <div
            class="flex items-center text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100 shadow-sm">
            <ion-icon name="people-outline" class="mr-2 mt-0.5"></ion-icon>
            <span class="font-bold">Master Mahasiswa</span>
        </div>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Title & Action Card -->
        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative group flex flex-col justify-between h-full">
            <!-- Decorative Background (Clipped) -->
            <div class="absolute inset-0 overflow-hidden rounded-2xl">
                <div
                    class="absolute top-0 right-0 -mr-20 -mt-20 w-72 h-72 rounded-full bg-gradient-to-br from-indigo-100 to-purple-50 opacity-60 blur-3xl group-hover:scale-110 transition-transform duration-700">
                </div>
            </div>
            <div class="relative z-10">
                <div class="mb-4">
                    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 tracking-tight leading-tight mb-2">
                        <?= $title ?>
                    </h1>
                    <?php if (isset($prodi_info) && $prodi_info): ?>
                        <p class="text-gray-600 flex items-center text-sm">
                            <ion-icon name="school-outline" class="mr-2 text-indigo-500"></ion-icon>Data Mahasiswa Prodi
                        </p>
                    <?php elseif (isset($jurusan_info) && $jurusan_info): ?>
                        <p class="text-gray-600 flex items-center text-sm">
                            <ion-icon name="business-outline" class="mr-2 text-indigo-500"></ion-icon>Jurusan:
                            <?= $jurusan_info['nama_jurusan'] ?>
                        </p>
                    <?php endif; ?>

                    <!-- IKU Usage Description -->
                    <div class="mt-3 p-3 bg-indigo-50/70 rounded-xl border border-indigo-100">
                        <div class="flex items-start text-xs text-indigo-700">
                            <ion-icon name="information-circle" class="mr-2 mt-0.5 text-base flex-shrink-0"></ion-icon>
                            <div>
                                <span class="font-semibold">Digunakan untuk:</span>
                                <span class="text-indigo-600">IKU 1 (Tracer Study Lulusan)</span> &bull;
                                <span class="text-indigo-600">IKU 2 (Mahasiswa MBKM)</span> &bull;
                                <span class="text-indigo-600">IKU 3 (Prestasi Mahasiswa)</span>
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
                    <a href="<?= base_url('admin/mahasiswa/import') . ($query_string ? '?' . $query_string : '') ?>"
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
                            <a href="<?= base_url('admin/mahasiswa/export') . ($query_string ? '?' . $query_string : '') ?>"
                                class="flex items-center px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition text-sm">
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

                    <a href="<?= base_url('admin/mahasiswa/create') . ($query_string ? '?' . $query_string : '') ?>"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-md transition text-sm">
                        <ion-icon name="add-circle-outline" class="text-lg mr-2"></ion-icon>Tambah
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Card -->
        <div
            class="relative overflow-hidden text-white p-8 flex items-center justify-between shadow-lg transform hover:-translate-y-1 transition-transform duration-300 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 h-full">
            <div class="relative z-10">
                <h3 class="text-sm uppercase font-semibold mb-2 opacity-90 tracking-wide">TOTAL MAHASISWA</h3>
                <div class="text-5xl font-extrabold leading-none">
                    <?= number_format($pager->getTotal('mahasiswa') ?? 0) ?>
                </div>
                <div class="text-sm opacity-90 mt-2 font-medium bg-white/20 inline-block px-3 py-1 rounded-full">
                    Mahasiswa Terdaftar</div>
            </div>
            <div
                class="bg-white/20 rounded-full h-24 w-24 flex items-center justify-center backdrop-blur-sm shadow-inner absolute -right-6 -bottom-6 md:static md:h-20 md:w-20">
                <ion-icon name="people" class="text-5xl md:text-4xl"></ion-icon>
            </div>
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white opacity-10 blur-2xl"></div>
        </div>
    </div>
</div>

<!-- Flash Message -->
<?php if (session()->getFlashdata('success') || session()->getFlashdata('error')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const msg = "<?= esc(session()->getFlashdata('success') ?? session()->getFlashdata('error')) ?>";
            const isError = <?= session()->getFlashdata('error') ? 'true' : 'false' ?>;
            const bgClass = isError ? 'bg-red-500' : 'bg-indigo-500';
            const icon = isError ? 'alert-circle' : 'checkmark-circle';
            const toast = document.createElement('div');
            toast.className = `fixed top-6 right-6 z-[9999] ${bgClass} text-white px-6 py-4 rounded-xl shadow-2xl flex items-center space-x-3 animate-bounce`;
            toast.innerHTML = `<ion-icon name="${icon}" class="text-2xl"></ion-icon><span class="font-bold">${msg}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.transition = 'all 0.5s ease-out'; toast.style.opacity = '0'; setTimeout(() => toast.remove(), 500); }, 4000);
        });
    </script>
<?php endif; ?>

<!-- Bulk Action Toolbar (Hidden by default) -->
<div id="bulk-action-bar"
    class="hidden mb-4 bg-indigo-600 text-white px-6 py-4 rounded-xl shadow-lg flex items-center justify-between transition-all">
    <div class="flex items-center space-x-4">
        <ion-icon name="checkmark-done-circle" class="text-2xl"></ion-icon>
        <span class="font-bold"><span id="selected-count">0</span> data dipilih</span>
    </div>
    <div class="flex items-center space-x-3">
        <button type="button" onclick="exportSelected()"
            class="flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg font-semibold text-sm transition">
            <ion-icon name="download-outline" class="mr-2"></ion-icon>Export Dipilih
        </button>
        <button type="button" onclick="deleteSelected()"
            class="flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 rounded-lg font-semibold text-sm transition shadow">
            <ion-icon name="trash-outline" class="mr-2"></ion-icon>Hapus Dipilih
        </button>
        <button type="button" onclick="clearSelection()"
            class="flex items-center px-3 py-2 hover:bg-white/20 rounded-lg text-sm transition">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>
</div>

<!-- Main Content Card -->
<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    <!-- Card Header with Search -->
    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <ion-icon name="list-outline" class="mr-2 text-indigo-600"></ion-icon>Daftar Mahasiswa
            </h2>
            <form action="<?= base_url('admin/mahasiswa') ?>" method="get" class="flex items-center gap-3">
                <?php if ($nama_prodi): ?><input type="hidden" name="prodi"
                        value="<?= esc($nama_prodi) ?>"><?php endif; ?>
                <?php if ($jenjang): ?><input type="hidden" name="jenjang" value="<?= esc($jenjang) ?>"><?php endif; ?>
                <?php if ($jurusan_kode): ?><input type="hidden" name="jurusan"
                        value="<?= esc($jurusan_kode) ?>"><?php endif; ?>
                <div class="relative">
                    <select name="per_page" onchange="this.form.submit()"
                        class="pl-3 pr-8 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 appearance-none shadow-sm cursor-pointer font-medium text-gray-600">
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
                            class="text-gray-400 group-focus-within:text-indigo-500 transition-colors"></ion-icon>
                    </div>
                    <input type="text" name="keyword" value="<?= esc($keyword) ?>" placeholder="Cari Nama / NIM..."
                        class="pl-10 pr-16 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 w-48 sm:w-64 transition-all shadow-sm">
                    <button type="submit"
                        class="absolute inset-y-0 right-0 px-3 text-sm font-semibold text-indigo-600 hover:text-indigo-800 rounded-r-lg transition-colors">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table with Checkboxes -->
    <form id="bulk-form" action="<?= base_url('admin/mahasiswa/bulk-delete') ?>" method="post">
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
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </th>
                        <th class="px-4 py-4 text-center w-12 font-semibold">No</th>

                        <?php
                        // Ensure sort variables have default values (passed from controller)
                        $sort_by = $sort_by ?? 'nim';
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
                            $url = base_url('admin/mahasiswa') . '?' . http_build_query($params);

                            $icon = 'swap-vertical-outline'; // Default neutral icon
                            $iconClass = 'text-gray-300';

                            if ($sort_by === $field) {
                                if ($sort_order === 'asc') {
                                    $icon = 'caret-up-outline';
                                    $iconClass = 'text-indigo-600';
                                } else {
                                    $icon = 'caret-down-outline';
                                    $iconClass = 'text-indigo-600';
                                }
                            }

                            return "
                                <a href='{$url}' class='group flex items-center justify-between hover:text-indigo-600 transition-colors cursor-pointer'>
                                    <span>{$label}</span>
                                    <ion-icon name='{$icon}' class='ml-1 text-sm {$iconClass} group-hover:text-indigo-500'></ion-icon>
                                </a>
                            ";
                        };
                        ?>

                        <th class="px-6 py-4 font-semibold">
                            <?= $createSortLink('nama_lengkap', 'Identitas Mahasiswa') ?>
                        </th>
                        <th class="px-4 py-4 font-semibold">
                            <?= $createSortLink('jenis_kelamin', 'JK') ?>
                        </th>
                        <th class="px-6 py-4 font-semibold">Kontak</th>
                        <th class="px-6 py-4 font-semibold">
                            <?= $createSortLink('tahun_masuk', 'Angkatan') ?>
                        </th>
                        <th class="px-6 py-4 font-semibold text-center">
                            <div class="flex items-center justify-center">
                                <?= $createSortLink('status', 'Status') ?>
                            </div>
                        </th>
                        <th class="px-6 py-4 font-semibold text-center">Ket. Data</th>
                        <th class="px-6 py-4 font-semibold text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php $i = 1 + ($per_page * (($pager->getCurrentPage('mahasiswa') ?? 1) - 1)); ?>
                    <?php foreach ($mahasiswa as $m): ?>
                        <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group"
                            data-nim="<?= esc($m['nim']) ?>">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" name="selected_nims[]" value="<?= esc($m['nim']) ?>"
                                    class="row-checkbox h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </td>
                            <td class="px-4 py-4 text-center text-gray-500 font-medium"><?= $i++ ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs mr-3 shadow-sm group-hover:scale-110 transition-transform">
                                        <?= strtoupper(substr($m['nama_lengkap'], 0, 2)) ?>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800"><?= esc($m['nama_lengkap']) ?></div>
                                        <div
                                            class="text-xs text-gray-500 font-mono mt-0.5 bg-gray-100 inline-block px-1.5 py-0.5 rounded border border-gray-200">
                                            <?= esc($m['nim']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-gray-600">
                                <?php if (!empty($m['jenis_kelamin'])): ?>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $m['jenis_kelamin'] == 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' ?>">
                                        <?= esc($m['jenis_kelamin']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-1">
                                    <?php if (!empty($m['email'])): ?>
                                        <div class="flex items-center text-xs text-gray-600">
                                            <ion-icon name="mail-outline" class="mr-1.5 text-gray-400"></ion-icon>
                                            <?= esc($m['email']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($m['no_hp'])): ?>
                                        <div class="flex items-center text-xs text-gray-600">
                                            <ion-icon name="call-outline" class="mr-1.5 text-gray-400"></ion-icon>
                                            <?= esc($m['no_hp']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (empty($m['email']) && empty($m['no_hp'])): ?>
                                        <span class="text-gray-400 text-xs italic">Tidak ada kontak</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 font-medium"><?= esc($m['tahun_masuk']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php
                                $statusClass = match ($m['status'] ?? 'Aktif') {
                                    'Aktif' => 'bg-green-100 text-green-700 border-green-200',
                                    'Lulus' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'Cuti' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                    'Keluar', 'Drop Out' => 'bg-red-100 text-red-700 border-red-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200'
                                };
                                ?>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $statusClass ?>"><?= esc($m['status'] ?? 'Aktif') ?></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php
                                // Check completeness: NIK, Email, No HP, JK
                                $isComplete = !empty($m['nik']) && !empty($m['email']) && !empty($m['no_hp']) && !empty($m['jenis_kelamin']);
                                ?>
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
                                    <a href="<?= base_url('admin/mahasiswa/edit/' . $m['nim']) ?>"
                                        class="p-2 bg-white text-indigo-600 rounded-lg border border-gray-200 hover:bg-indigo-50 hover:border-indigo-200 transition-all shadow-sm"
                                        title="Edit">
                                        <ion-icon name="create-outline" class="text-lg"></ion-icon>
                                    </a>
                                    <a href="<?= base_url('admin/mahasiswa/delete/' . $m['nim']) ?>"
                                        onclick="return confirm('Yakin hapus data ini?')"
                                        class="p-2 bg-white text-red-500 rounded-lg border border-gray-200 hover:bg-red-50 hover:border-red-200 transition-all shadow-sm"
                                        title="Hapus">
                                        <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($mahasiswa)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-50 p-4 rounded-full mb-3"><ion-icon name="file-tray-outline"
                                            class="text-3xl text-gray-400"></ion-icon></div>
                                    <h3 class="text-gray-500 font-medium text-lg">Tidak ada data ditemukan</h3>
                                    <p class="text-gray-400 text-sm mt-1">Silakan tambah data atau ubah pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>

    <!-- Pagination Footer -->
    <div
        class="p-6 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
        <?php
        $currentPage = $pager->getCurrentPage('mahasiswa') ?? 1;
        $totalPages = $pager->getPageCount('mahasiswa');
        $total = $pager->getTotal('mahasiswa');
        $from = ($currentPage - 1) * $per_page + 1;
        $to = min($currentPage * $per_page, $total);
        if ($total == 0) {
            $from = 0;
            $to = 0;
        }
        ?>
        <div class="text-sm text-gray-600">
            Menampilkan <span class="font-bold text-gray-800"><?= $from ?>-<?= $to ?></span> dari <span
                class="font-bold text-gray-800"><?= number_format($total) ?></span> data
        </div>
        <div class="flex items-center space-x-2">
            <?php
            $baseUrl = base_url('admin/mahasiswa') . '?';
            $params = [];
            if ($nama_prodi)
                $params[] = 'prodi=' . urlencode($nama_prodi);
            if ($jenjang)
                $params[] = 'jenjang=' . urlencode($jenjang);
            if ($jurusan_kode)
                $params[] = 'jurusan=' . $jurusan_kode;
            if ($keyword)
                $params[] = 'keyword=' . urlencode($keyword);
            if ($per_page)
                $params[] = 'per_page=' . $per_page;
            $baseUrl .= implode('&', $params);
            ?>
            <?php if ($currentPage > 1): ?>
                <a href="<?= $baseUrl . '&page=' . ($currentPage - 1) ?>"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm text-sm">
                    <ion-icon name="chevron-back-outline" class="mr-1"></ion-icon>Sebelumnya
                </a>
            <?php else: ?>
                <span
                    class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-200 text-gray-400 font-medium rounded-lg text-sm cursor-not-allowed">
                    <ion-icon name="chevron-back-outline" class="mr-1"></ion-icon>Sebelumnya
                </span>
            <?php endif; ?>
            <span class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-lg text-sm shadow-md"><?= $currentPage ?>
                / <?= max($totalPages, 1) ?></span>
            <?php if ($currentPage < $totalPages): ?>
                <a href="<?= $baseUrl . '&page=' . ($currentPage + 1) ?>"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm text-sm">
                    Selanjutnya<ion-icon name="chevron-forward-outline" class="ml-1"></ion-icon>
                </a>
            <?php else: ?>
                <span
                    class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-200 text-gray-400 font-medium rounded-lg text-sm cursor-not-allowed">
                    Selanjutnya<ion-icon name="chevron-forward-outline" class="ml-1"></ion-icon>
                </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Hidden form for export selected -->
<form id="export-selected-form" action="<?= base_url('admin/mahasiswa/export-selected') ?>" method="post"
    class="hidden">
    <?= csrf_field() ?>
    <input type="hidden" name="selected_nims" id="export-selected-nims">
</form>

<script>
    // Export dropdown toggle
    document.getElementById('export-dropdown-btn').addEventListener('click', function (e) {
        e.stopPropagation();
        document.getElementById('export-dropdown-menu').classList.toggle('hidden');
    });
    document.addEventListener('click', function () {
        document.getElementById('export-dropdown-menu').classList.add('hidden');
    });

    // Checkbox selection logic
    const selectAllCheckbox = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkActionBar = document.getElementById('bulk-action-bar');
    const selectedCountSpan = document.getElementById('selected-count');
    const btnExportSelected = document.getElementById('btn-export-selected');

    function updateBulkActionBar() {
        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        selectedCountSpan.textContent = checkedCount;

        if (checkedCount > 0) {
            bulkActionBar.classList.remove('hidden');
            btnExportSelected.disabled = false;
            btnExportSelected.classList.remove('text-gray-400', 'cursor-not-allowed');
            btnExportSelected.classList.add('text-gray-700', 'hover:bg-indigo-50', 'hover:text-indigo-600');
        } else {
            bulkActionBar.classList.add('hidden');
            btnExportSelected.disabled = true;
            btnExportSelected.classList.add('text-gray-400', 'cursor-not-allowed');
            btnExportSelected.classList.remove('text-gray-700', 'hover:bg-indigo-50', 'hover:text-indigo-600');
        }
    }

    selectAllCheckbox.addEventListener('change', function () {
        rowCheckboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActionBar();
    });

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            const allChecked = document.querySelectorAll('.row-checkbox:checked').length === rowCheckboxes.length;
            selectAllCheckbox.checked = allChecked;
            updateBulkActionBar();
        });
    });

    function clearSelection() {
        selectAllCheckbox.checked = false;
        rowCheckboxes.forEach(cb => cb.checked = false);
        updateBulkActionBar();
    }

    function deleteSelected() {
        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        if (checkedCount === 0) {
            alert('Pilih data yang ingin dihapus.');
            return;
        }
        if (confirm(`Yakin ingin menghapus ${checkedCount} data mahasiswa yang dipilih?`)) {
            document.getElementById('bulk-form').submit();
        }
    }

    function exportSelected() {
        const selected = [];
        document.querySelectorAll('.row-checkbox:checked').forEach(cb => selected.push(cb.value));
        if (selected.length === 0) {
            alert('Pilih data yang ingin diexport.');
            return;
        }
        document.getElementById('export-selected-nims').value = JSON.stringify(selected);
        document.getElementById('export-selected-form').submit();
    }
</script>

<?= $this->endSection() ?>
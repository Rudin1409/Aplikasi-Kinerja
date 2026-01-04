<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard Admin' ?> - APK Polsri</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-thumb {
            background: #9ca3af;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        ::-webkit-scrollbar-track {
            background: #e5e7eb;
        }

        /* ... (CSS Sidebar Mini & Fly-out Anda) ... */
        #sidebar,
        #main-content {
            transition: all 0.3s ease-in-out;
        }

        body.sidebar-mini #sidebar {
            width: 5rem;
        }

        body.sidebar-mini #main-content {
            margin-left: 5rem;
        }

        body.sidebar-mini #logo-text,
        body.sidebar-mini .menu-text {
            opacity: 0;
            width: 0;
            visibility: hidden;
        }

        body.sidebar-mini #sidebar nav a {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        body.sidebar-mini #sidebar .menu-group-title {
            text-align: center;
        }

        body.sidebar-mini #sidebar .menu-group-title span {
            display: none;
        }

        body.sidebar-mini #sidebar .menu-group-title::after {
            content: '...';
            font-weight: 900;
        }

        body.sidebar-mini #sidebar-toggle ion-icon {
            transform: rotate(180deg);
        }

        body.sidebar-mini #sidebar nav ul li {
            position: relative;
        }

        body.sidebar-mini .menu-text {
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 0.75rem;
            background-color: #111827;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
            white-space: nowrap;
            z-index: 50;
        }

        body.sidebar-mini nav ul li a:hover .menu-text {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>

<body class="flex min-h-screen">

    <aside id="sidebar" class="w-64 h-screen fixed left-0 top-0 z-40 bg-gray-900 text-gray-300 flex flex-col shadow-lg">

        <div class="flex items-center justify-between h-20 shadow-md bg-gray-900 px-4 flex-shrink-0">
            <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center overflow-hidden">
                <img src="<?= base_url('assets/images/logo-polsri.png') ?>" alt="Logo Polsri"
                    class="h-10 w-10 object-contain flex-shrink-0">
                <span id="logo-text"
                    class="text-xl font-bold text-white ml-2 transition-opacity duration-300 whitespace-nowrap">
                    APKP POLSRI
                </span>
            </a>
            <button id="sidebar-toggle" class="text-gray-400 hover:text-white transition-transform duration-300">
                <ion-icon name="chevron-back-outline" class="text-2xl"></ion-icon>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto py-4">

            <span class="menu-group-title uppercase text-xs text-gray-500 font-bold px-4 pt-4 pb-2 block">
                <span>PAGES</span>
            </span>
            <ul>
                <?php
                $role = session()->get('role');
                $relasi_kode = session()->get('relasi_kode');

                // Logika URL Dashboard berdasarkan role
                if ($role == 'jurusan') {
                    $dashboard_url = base_url('admin/prodi-capaian/' . $relasi_kode);
                } elseif ($role == 'prodi') {
                    // Untuk prodi, dashboard adalah halaman iku-prodi
                    // relasi_kode format: J01|P01
                    // Kita ambil bagian J01 (jurusan) saja untuk redirect ke prodi-dashboard-redirect
                    $dashboard_url = base_url('admin/prodi-dashboard-redirect');
                } else {
                    $dashboard_url = base_url('admin/dashboard');
                }

                // Logika kelas aktif untuk dashboard
                $is_jurusan_dashboard = ($role == 'jurusan' && strpos(current_url(), 'prodi-capaian/' . $relasi_kode) !== false);
                $is_prodi_dashboard = ($role == 'prodi' && (strpos(current_url(), 'iku-prodi/') !== false));
                $dashboard_active_class = ($page == 'dashboard' || $is_jurusan_dashboard || $is_prodi_dashboard) ? 'bg-purple-600 text-white' : '';
                ?>
                <li>
                    <a href="<?= $dashboard_url ?>"
                        class="flex items-center space-x-3 block py-3 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white <?= $dashboard_active_class ?>">
                        <ion-icon name="grid-outline" class="text-lg flex-shrink-0"></ion-icon>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>

                <?php if ($role == 'admin'): ?>
                    <li>
                        <a href="#"
                            class="flex items-center space-x-3 block py-3 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white <?= ($page == 'input') ? 'bg-purple-600 text-white' : '' ?>">
                            <ion-icon name="create-outline" class="text-lg flex-shrink-0"></ion-icon>
                            <span class="menu-text">Input Capaian Kinerja</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="<?= base_url('admin/laporan') ?>"
                        class="flex items-center space-x-3 block py-3 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white <?= ($page == 'laporan') ? 'bg-purple-600 text-white' : '' ?>">
                        <ion-icon name="document-attach-outline" class="text-lg flex-shrink-0"></ion-icon>
                        <span class="menu-text">Laporan</span>
                    </a>
                </li>
            </ul>

            <span class="menu-group-title uppercase text-xs text-gray-500 font-bold px-4 pt-6 pb-2 block">
                <span>MASTER DATA</span>
            </span>
            <ul>
                <?php if ($role == 'admin'): ?>
                    <li>
                        <a href="<?= base_url('admin/user') ?>"
                            class="flex items-center space-x-3 block py-3 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white <?= ($page == 'user') ? 'bg-purple-600 text-white' : '' ?>">
                            <ion-icon name="people-outline" class="text-lg flex-shrink-0"></ion-icon>
                            <span class="menu-text">User</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role == 'admin' || $role == 'pimpinan'): ?>
                    <li>
                        <a href="<?= base_url('admin/jurusan-capaian') ?>"
                            class="flex items-center space-x-3 block py-3 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white <?= ($page == 'jurusan') ? 'bg-purple-600 text-white' : '' ?>">
                            <ion-icon name="business-outline" class="text-lg flex-shrink-0"></ion-icon>
                            <span class="menu-text">Jurusan</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/prodi') ?>"
                            class="flex items-center space-x-3 block py-3 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white <?= ($page == 'prodi') ? 'bg-purple-600 text-white' : '' ?>">
                            <ion-icon name="school-outline" class="text-lg flex-shrink-0"></ion-icon>
                            <span class="menu-text">Prodi</span>
                        </a>
                    </li>
                <?php endif; ?>

                <li>
                    <a href="<?= base_url('admin/iku') ?>"
                        class="flex items-center space-x-3 block py-3 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white <?= ($page == 'iku' && strpos(current_url(), 'iku-prodi/') === false && strpos(current_url(), 'iku-detail/') === false) ? 'bg-purple-600 text-white' : '' ?>">
                        <ion-icon name="document-text-outline" class="text-lg flex-shrink-0"></ion-icon>
                        <span class="menu-text">IKU</span>
                    </a>
                </li>

                <?php if ($role == 'admin'): ?>
                    <li>
                        <a href="<?= base_url('admin/akun') ?>"
                            class="flex items-center space-x-3 block py-3 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white <?= ($page == 'akun') ? 'bg-purple-600 text-white' : '' ?>">
                            <ion-icon name="cog-outline" class="text-lg flex-shrink-0"></ion-icon>
                            <span class="menu-text">Akun</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <span class="menu-group-title uppercase text-xs text-gray-500 font-bold px-4 pt-6 pb-2 block">
                <span>BANTUAN</span>
            </span>
            <ul>
                <li>
                    <a href="<?= base_url('admin/panduan') ?>"
                        class="flex items-center space-x-3 block py-3 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white <?= ($page == 'panduan') ? 'bg-purple-600 text-white' : '' ?>">
                        <ion-icon name="book-outline" class="text-lg flex-shrink-0"></ion-icon>
                        <span class="menu-text">Panduan</span>
                    </a>
                </li>
            </ul>

            <span class="menu-group-title uppercase text-xs text-gray-500 font-bold px-4 pt-6 pb-2 block">
                <span>AKUN</span>
            </span>
            <ul>
                <li>
                    <a href="<?= base_url('admin/pengaturan') ?>"
                        class="flex items-center space-x-3 block py-3 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white <?= ($page == 'pengaturan') ? 'bg-purple-600 text-white' : '' ?>">
                        <ion-icon name="settings-outline" class="text-lg flex-shrink-0"></ion-icon>
                        <span class="menu-text">Pengaturan</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <div id="main-content" class="flex-1 ml-64 overflow-x-hidden transition-all duration-300">

        <header class="bg-white shadow-md p-4 flex justify-between items-center sticky top-0 z-30">
            <div class="relative">
            </div>

            <div class="relative">
                <button id="user-menu-button" class="flex items-center space-x-2 p-2 rounded-full hover:bg-gray-100">

                    <?php
                    // 1. Ambil nama & avatar dari session terlebih dahulu
                    $avatar = session()->get('avatar');
                    $nama = session()->get('nama_lengkap');

                    // 2. Jika nama belum ada (session lama / belum terset), coba ambil dari database
                    if (!$nama) {
                        $userId = session()->get('user_id');
                        if ($userId) {
                            try {
                                $userModelClass = '\\App\\Models\\UserModel';
                                if (class_exists($userModelClass)) {
                                    $userModel = new $userModelClass();
                                    $userData = $userModel->find($userId);
                                    if ($userData) {
                                        // Gunakan kolom nama_lengkap jika ada, fallback ke nama
                                        if (!$nama) {
                                            $namaFromDb = $userData['nama_lengkap'] ?? ($userData['nama'] ?? null);
                                            if ($namaFromDb) {
                                                $nama = $namaFromDb;
                                                // Perbarui session agar konsisten di request berikutnya
                                                session()->set(['nama_lengkap' => $nama]);
                                            }
                                        }
                                        // PERBAIKAN: Selalu sinkronkan avatar dari database (bukan hanya saat kosong)
                                        // Ini memastikan avatar terbaru dari DB selalu tampil
                                        if (isset($userData['avatar']) && $userData['avatar']) {
                                            $avatar = $userData['avatar'];
                                            session()->set(['avatar' => $avatar]);
                                        }
                                    }
                                }
                            } catch (\Throwable $e) {
                                // Diamkan: fallback aman ke inisial "U"
                            }
                        }
                        // Tutup blok if (!$nama)
                    }

                    // 3. Jika tetap belum ada nama gunakan default "User"
                    if (!$nama) {
                        $nama = 'User';
                    }

                    // 4. Hitung inisial untuk lingkaran avatar placeholder
                    $inisial = strtoupper(substr($nama, 0, 1));
                    ?>

                    <?php
                    // Cek lokasi avatar di PUBLIC terlebih dahulu
                    $writableAvatarPath = WRITEPATH . 'uploads/avatars/' . $avatar;
                    if ($avatar && file_exists($writableAvatarPath)) {
                        $avatarUrl = base_url('avatar/' . $avatar) . '?t=' . time();
                        echo '<img src="' . $avatarUrl . '" alt="Avatar" class="h-8 w-8 rounded-full object-cover">';
                    } else {
                        echo '<div class="h-8 w-8 rounded-full bg-purple-600 flex items-center justify-center text-white font-bold text-sm">' . $inisial . '</div>';
                    }
                    ?>

                </button>

                <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50
                            ring-1 ring-black ring-opacity-5">
                    <a href="<?= base_url('admin/pengaturan') ?>"
                        class="flex items-center space-x-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <ion-icon name="settings-outline" class="text-lg"></ion-icon>
                        <span>Pengaturan</span>
                    </a>
                    <a href="<?= base_url('logout') ?>"
                        class="flex items-center space-x-2 px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                        <ion-icon name="log-out-outline" class="text-lg"></ion-icon>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </header>

        <main class="p-6">
            <?= $this->renderSection('content') ?>
        </main>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const SIDEBAR_KEY = 'apkp_sidebar_mini';

            // Terapkan state tersimpan saat load halaman
            try {
                if (localStorage.getItem(SIDEBAR_KEY) === '1') {
                    document.body.classList.add('sidebar-mini');
                }
            } catch (e) { /* ignore storage errors */ }

            // --- 1. SCRIPT UNTUK SIDEBAR TOGGLE (dengan persist) ---
            const toggleButton = document.getElementById('sidebar-toggle');
            if (toggleButton) {
                toggleButton.addEventListener('click', function () {
                    const collapsed = document.body.classList.toggle('sidebar-mini');
                    // Simpan state ke localStorage
                    try { localStorage.setItem(SIDEBAR_KEY, collapsed ? '1' : '0'); } catch (e) { }
                    // Paksa Chart.js melakukan resize setelah transisi sidebar selesai
                    setTimeout(() => {
                        window.dispatchEvent(new Event('resize'));
                    }, 320); // sedikit lebih lama dari transition 300ms
                });
            }

            // --- 2. SCRIPT BARU UNTUK USER DROPDOWN ---
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenuDropdown = document.getElementById('user-menu-dropdown');

            if (userMenuButton) {
                userMenuButton.addEventListener('click', function (event) {
                    userMenuDropdown.classList.toggle('hidden');
                    event.stopPropagation();
                });
            }

            window.addEventListener('click', function (e) {
                if (userMenuDropdown && !userMenuDropdown.classList.contains('hidden')) {
                    if (!userMenuDropdown.contains(e.target) && e.target !== userMenuButton) {
                        userMenuDropdown.classList.add('hidden');
                    }
                }
            });

            // Tambahan: resize ulang saat kembali fokus (misal setelah tab pindah)
            window.addEventListener('focus', () => {
                setTimeout(() => window.dispatchEvent(new Event('resize')), 50);
            });
        });
    </script>
</body>

</html>
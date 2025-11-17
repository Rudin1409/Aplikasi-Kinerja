<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Pengaturan</h1>
        <p class="text-gray-600">Kelola pengaturan akun dan preferensi aplikasi Anda.</p>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex border-b border-gray-200">
            <button class="tab-button flex-1 px-6 py-4 text-center font-medium transition-colors duration-200 border-b-2 border-purple-600 text-purple-600" 
                    data-tab="profil">
                Profil
            </button>
            <button class="tab-button flex-1 px-6 py-4 text-center font-medium transition-colors duration-200 border-b-2 border-transparent text-gray-500 hover:text-gray-700" 
                    data-tab="keamanan">
                Keamanan
            </button>
            <button class="tab-button flex-1 px-6 py-4 text-center font-medium transition-colors duration-200 border-b-2 border-transparent text-gray-500 hover:text-gray-700" 
                    data-tab="tampilan">
                Tampilan
            </button>
        </div>

        <div class="p-8">
            
            <div id="tab-profil" class="tab-content">
                <div class="max-w-3xl">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Profil Publik</h2>
                    <p class="text-gray-600 mb-8">Informasi ini akan ditampilkan kepada anggota tim lainnya.</p>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
                            <span class="block sm:inline"><?= session()->getFlashdata('success') ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/pengaturan/update-profil') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="mb-8 text-center">
                            <div class="inline-block relative">
                                <?php 
                                // Ambil dari session atau database
                                $userId = session()->get('user_id');
                                $avatar = session()->get('avatar');
                                $nama = session()->get('nama_lengkap') ?? 'A';
                                
                                // Ambil avatar terbaru langsung dari DB (sinkron) untuk preview
                                if ($userId) {
                                    try {
                                        $userModel = new \App\Models\UserModel();
                                        $userData = $userModel->find($userId);
                                        if ($userData && !empty($userData['avatar'])) {
                                            $avatar = $userData['avatar'];
                                        }
                                    } catch (\Throwable $e) {
                                        // Silence error
                                    }
                                }
                                $inisial = strtoupper(substr($nama, 0, 1));
                                $hasAvatarFile = ($avatar && file_exists(WRITEPATH . 'uploads/avatars/' . $avatar));
                                $avatarUrl = $hasAvatarFile
                                    ? base_url('avatar/' . $avatar) . '?t=' . time()
                                    : 'https://via.placeholder.com/128/ccc/666?text=' . $inisial;
                                ?>
                                
                                <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center text-5xl font-bold text-gray-600 mx-auto mb-4 overflow-hidden" id="avatar-preview-container">
                                    
                                    <?php if ($hasAvatarFile): ?>
                                        <img id="avatar-preview-img" 
                                             src="<?= $avatarUrl ?>" 
                                             alt="Avatar Preview" 
                                             class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div id="avatar-preview-initials" class="absolute inset-0 flex items-center justify-center"><?= $inisial ?></div>
                                    <?php endif; ?>

                                </div>
                                
                                <input type="file" id="avatar-input" name="avatar" class="hidden" accept="image/*">
                                <!-- Tombol ganti foto: seluruh area clickable -->
                                <button type="button" id="avatar-trigger"
                                        class="w-full mt-2 flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 active:scale-[0.99] transition focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <ion-icon name="camera-outline" class="text-lg mr-2"></ion-icon>
                                    <span>Ubah Foto</span>
                                </button>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="nama_lengkap" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" 
                                    value="<?= session()->get('nama_lengkap') ?? '' ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm 
                                            focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="Masukkan nama lengkap Anda">
                        </div>

                        <div class="mb-6">
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" id="email" name="email" 
                                    value="<?= session()->get('email') ?? '' ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm 
                                            focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="contoh@email.com">
                        </div>

                        <div class="flex justify-start pt-4">
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold 
                                            py-3 px-6 rounded-lg transition duration-200 shadow-md">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="tab-keamanan" class="tab-content hidden">
                <div class="max-w-3xl">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Keamanan Akun</h2>
                    <p class="text-gray-600 mb-8">Perbarui password Anda untuk menjaga keamanan akun.</p>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                            <span class="block sm:inline"><?= session()->getFlashdata('error') ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/pengaturan/update-password') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="mb-6">
                            <label for="password_lama" class="block text-sm font-semibold text-gray-700 mb-2">Password Saat Ini</label>
                            <input type="password" id="password_lama" name="password_lama"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm 
                                            focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="Masukkan password lama" required>
                        </div>
                        
                        <div class="mb-6">
                            <label for="password_baru" class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                            <input type="password" id="password_baru" name="password_baru"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm 
                                            focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="Masukkan password baru" required>
                            <p class="mt-2 text-sm text-gray-500">Minimal 8 karakter, kombinasi huruf dan angka.</p>
                        </div>

                        <div class="mb-6">
                            <label for="konfirmasi_password" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                            <input type="password" id="konfirmasi_password" name="konfirmasi_password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm 
                                            focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="Ulangi password baru" required>
                        </div>

                        <div class="flex justify-start pt-4">
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold 
                                            py-3 px-6 rounded-lg transition duration-200 shadow-md">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="tab-tampilan" class="tab-content hidden">
                <div class="max-w-3xl">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Preferensi Tampilan</h2>
                    <p class="text-gray-600 mb-8">Sesuaikan tampilan aplikasi sesuai keinginan Anda.</p>

                    <form action="<?= base_url('admin/pengaturan/update-tampilan') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="mb-8 pb-8 border-b border-gray-200">
                            <label class="block text-sm font-semibold text-gray-700 mb-4">Mode Tema</label>
                            <div class="grid grid-cols-3 gap-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="theme" value="light" class="peer sr-only" checked>
                                    <div class="p-4 border-2 border-gray-300 rounded-lg peer-checked:border-purple-600 peer-checked:bg-purple-50 hover:border-gray-400 transition">
                                        <ion-icon name="sunny-outline" class="text-3xl text-gray-600 mb-2"></ion-icon>
                                        <p class="font-medium text-gray-700">Terang</p>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="theme" value="dark" class="peer sr-only">
                                    <div class="p-4 border-2 border-gray-300 rounded-lg peer-checked:border-purple-600 peer-checked:bg-purple-50 hover:border-gray-400 transition">
                                        <ion-icon name="moon-outline" class="text-3xl text-gray-600 mb-2"></ion-icon>
                                        <p class="font-medium text-gray-700">Gelap</p>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="theme" value="auto" class="peer sr-only">
                                    <div class="p-4 border-2 border-gray-300 rounded-lg peer-checked:border-purple-600 peer-checked:bg-purple-50 hover:border-gray-400 transition">
                                        <ion-icon name="contrast-outline" class="text-3xl text-gray-600 mb-2"></ion-icon>
                                        <p class="font-medium text-gray-700">Otomatis</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="flex items-center justify-between cursor-pointer">
                                <div>
                                    <p class="font-semibold text-gray-700">Sidebar Mini secara default</p>
                                    <p class="text-sm text-gray-500">Tampilkan sidebar dalam mode minimalis saat membuka aplikasi</p>
                                </div>
                                <div class="relative">
                                    <input type="checkbox" name="sidebar_mini" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </div>
                            </label>
                        </div>

                        <div class="flex justify-start pt-4">
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold 
                                            py-3 px-6 rounded-lg transition duration-200 shadow-md">
                                Simpan Preferensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching logic
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');
                    
                    // Remove active state from all buttons
                    tabButtons.forEach(btn => {
                        btn.classList.remove('border-purple-600', 'text-purple-600');
                        btn.classList.add('border-transparent', 'text-gray-500');
                    });
                    
                    // Add active state to clicked button
                    this.classList.remove('border-transparent', 'text-gray-500');
                    this.classList.add('border-purple-600', 'text-purple-600');
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Show target tab content
                    document.getElementById('tab-' + targetTab).classList.remove('hidden');
                });
            });

            // Avatar preview + full button trigger
            const avatarInput = document.getElementById('avatar-input');
            const avatarPreviewContainer = document.getElementById('avatar-preview-container');
            const avatarTrigger = document.getElementById('avatar-trigger');

            if (avatarTrigger) {
                avatarTrigger.addEventListener('click', () => {
                    avatarInput.click();
                });
            }
            
            if (avatarInput) {
                avatarInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // 1. Buat tag <img> baru
                            const img = document.createElement('img');
                            img.id = 'avatar-preview-img';
                            img.src = e.target.result;
                            img.alt = 'Preview';
                            img.className = 'w-full h-full object-cover'; // Kelas styling

                            // 2. Kosongkan container lama dan sisipkan gambar baru
                            avatarPreviewContainer.innerHTML = '';
                            avatarPreviewContainer.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>

<?= $this->endSection() ?>
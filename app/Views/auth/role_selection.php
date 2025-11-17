<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Role - Aplikasi Pengukuran Kinerja Polsri</title>
    <!-- Tailwind CSS CDN (untuk pengembangan cepat, disarankan instalasi penuh untuk produksi) --><script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Anda bisa menambahkan custom CSS di sini jika diperlukan */
        body {
            font-family: 'Inter', sans-serif; /* Contoh font modern */
        }
        .glass-container {
            background: rgba(255, 255, 255, 0.1); /* Warna dasar transparan */
            backdrop-filter: blur(10px); /* Efek blur */
            border: 1px solid rgba(255, 255, 255, 0.2); /* Border transparan */
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); /* Sedikit bayangan */
        }
        .role-card {
            background: rgba(255, 255, 255, 0.15); /* Lebih padat dari container */
            transition: all 0.3s ease-in-out;
        }
        .role-card:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        }
    </style>
    <!-- Google Fonts - Inter (Contoh font modern) --><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="flex items-center justify-center min-h-screen" style="background: linear-gradient(135deg, #8A2BE2, #C71585);">
    <div class="text-center p-8 max-w-4xl w-full">
        <!-- Logo Polsri - Ganti dengan path logo Anda --><div class="mb-8">
            <img src="<?= base_url('assets/images/logo-polsri.png') ?>" alt="Logo Polsri" class="mx-auto h-41 w-40 object-contain">
        </div>

        <h1 class="text-5xl font-bold text-white mb-4 drop-shadow-lg">
            Aplikasi Pengukuran Kinerja
        </h1>
        <h2 class="text-4xl font-semibold text-white mb-8 drop-shadow-lg">
            Politeknik Negeri Sriwijaya
        </h2>
        <p class="text-white text-xl mb-12 drop-shadow-md">Silakan pilih salah satu peran:</p>

        <div class="glass-container p-8 rounded-2xl mx-auto max-w-3xl">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Role Card: Admin --><a href="<?= base_url('auth/login/admin') ?>" class="role-card flex flex-col items-center justify-center p-6 rounded-xl cursor-pointer text-white hover:scale-105 active:scale-95">
                    <img src="<?= base_url('assets/images/iconlogin.png') ?>" alt="Admin Icon" class="h-16 w-16 mb-3 object-contain filter drop-shadow-lg">
                    <span class="text-lg font-medium">Admin</span>
                </a>

                <!-- Role Card: Prodi --><a href="<?= base_url('auth/login/prodi') ?>" class="role-card flex flex-col items-center justify-center p-6 rounded-xl cursor-pointer text-white hover:scale-105 active:scale-95">
                    <img src="<?= base_url('assets/images/iconlogin.png') ?>" alt="Prodi Icon" class="h-16 w-16 mb-3 object-contain filter drop-shadow-lg">
                    <span class="text-lg font-medium">Prodi</span>
                </a>

                <!-- Role Card: Jurusan --><a href="<?= base_url('auth/login/jurusan') ?>" class="role-card flex flex-col items-center justify-center p-6 rounded-xl cursor-pointer text-white hover:scale-105 active:scale-95">
                    <img src="<?= base_url('assets/images/iconlogin.png') ?>" alt="Jurusan Icon" class="h-16 w-16 mb-3 object-contain filter drop-shadow-lg">
                    <span class="text-lg font-medium">Jurusan</span>
                </a>

                <!-- Role Card: Pimpinan --><a href="<?= base_url('auth/login/pimpinan') ?>" class="role-card flex flex-col items-center justify-center p-6 rounded-xl cursor-pointer text-white hover:scale-105 active:scale-95">
                    <img src="<?= base_url('assets/images/iconlogin.png') ?>" alt="Pimpinan Icon" class="h-16 w-16 mb-3 object-contain filter drop-shadow-lg">
                    <span class="text-lg font-medium">Pimpinan</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
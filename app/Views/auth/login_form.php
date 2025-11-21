<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login <?= ucfirst($role ?? 'User') ?> - Aplikasi Kinerja Polsri</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        .form-input {
            background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
        }
        .form-input::placeholder { color: rgba(255, 255, 255, 0.7); }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
</head>
<body class="flex items-center justify-center min-h-screen" style="background: linear-gradient(135deg, #8A2BE2, #C71585);">

    <a href="<?= base_url('/') ?>" class="absolute top-8 left-8 p-3 rounded-full glass-card text-white hover:bg-white/20 transition duration-300">
        <ion-icon name="arrow-back-outline" class="text-2xl"></ion-icon>
    </a>

    <div class="glass-card w-full max-w-md p-8 rounded-2xl text-white">
        
        <div class="text-center mb-8">
            <img src="<?= base_url('assets/images/logo-polsri.png') ?>" alt="Logo Polsri" class="mx-auto h-20 w-20 object-contain mb-4">
            <h1 class="text-3xl font-bold">Selamat Datang!</h1>
            <p class="text-xl font-light">Silakan login sebagai 
                <span class="font-semibold capitalize"><?= $role ?? 'User' ?></span>
            </p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= session()->getFlashdata('error') ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/processLogin') ?>" method="POST">
            
            <?= csrf_field() ?>
            <input type="hidden" name="role" value="<?= $role ?? '' ?>">

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium mb-2">Email (Username)</label>
                <input type="email" id="email" name="email"
                       class="form-input w-full px-4 py-3 rounded-lg border-none focus:ring-2 focus:ring-white/50"
                       placeholder="masukkan@email.anda" required>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium mb-2">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password"
                           class="form-input w-full px-4 py-3 rounded-lg border-none focus:ring-2 focus:ring-white/50"
                           placeholder="••••••••" required>
                    <button type="button" id="toggle-password" aria-label="Tampilkan password"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-white/80 hover:text-white">
                        <ion-icon name="eye-off-outline" id="toggle-icon" class="text-xl"></ion-icon>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <button type="submit"
                        class="w-full bg-white text-purple-700 font-bold py-3 px-4 rounded-lg
                               hover:bg-purple-100 transition duration-300 ease-in-out
                               focus:outline-none focus:ring-2 focus:ring-purple-300">
                    LOGIN
                </button>
            </div>
        </form>
    </div>
</form>
    </div>
    <script>
        (function(){
            const pw = document.getElementById('password');
            const btn = document.getElementById('toggle-password');
            const icon = document.getElementById('toggle-icon');
            if (btn && pw) {
                btn.addEventListener('click', function(){
                    if (pw.type === 'password') {
                        pw.type = 'text';
                        if (icon) icon.setAttribute('name','eye-outline');
                        btn.setAttribute('aria-label','Sembunyikan password');
                    } else {
                        pw.type = 'password';
                        if (icon) icon.setAttribute('name','eye-off-outline');
                        btn.setAttribute('aria-label','Tampilkan password');
                    }
                });
            }
        })();
    </script>
</body>
</html>
</body>
</html>
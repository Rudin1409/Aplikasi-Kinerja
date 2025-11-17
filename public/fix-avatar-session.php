<!DOCTYPE html>
<html>
<head>
    <title>Fix Avatar Session</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        .button { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #8B5CF6; color: white; text-decoration: none; border-radius: 5px; }
        .button:hover { background: #7C3AED; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix Avatar Session</h1>
        <p>Script ini akan mengupdate session dengan data avatar dari database.</p>
        <hr>
        
        <?php
        // Load CodeIgniter
        require_once __DIR__ . '/../app/Config/Autoload.php';
        require_once __DIR__ . '/../system/bootstrap.php';
        
        $app = \Config\Services::codeigniter();
        $app->initialize();
        
        // Start session
        $session = \Config\Services::session();
        
        // Get user ID from session
        $userId = $session->get('user_id');
        
        if (!$userId) {
            echo '<p class="error">❌ Anda belum login. Silakan login terlebih dahulu.</p>';
            echo '<a href="' . base_url() . '" class="button">Login</a>';
            exit;
        }
        
        // Load user model
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        
        if (!$user) {
            echo '<p class="error">❌ User tidak ditemukan di database.</p>';
            exit;
        }
        
        echo '<h3>📋 Data User:</h3>';
        echo '<ul>';
        echo '<li><strong>ID:</strong> ' . $user['id'] . '</li>';
        echo '<li><strong>Nama:</strong> ' . $user['nama_lengkap'] . '</li>';
        echo '<li><strong>Email:</strong> ' . $user['email'] . '</li>';
        echo '<li><strong>Role:</strong> ' . $user['role'] . '</li>';
        echo '<li><strong>Avatar DB:</strong> ' . ($user['avatar'] ?? '<em>NULL</em>') . '</li>';
        echo '<li><strong>Avatar Session:</strong> ' . ($session->get('avatar') ?? '<em>NULL</em>') . '</li>';
        echo '</ul>';
        
        if ($user['avatar']) {
            // Cek file fisik
            $avatarPath = WRITEPATH . 'uploads/avatars/' . $user['avatar'];
            $fileExists = file_exists($avatarPath);
            
            echo '<h3>📁 Status File:</h3>';
            echo '<ul>';
            echo '<li><strong>Path:</strong> ' . $avatarPath . '</li>';
            echo '<li><strong>File Exists:</strong> ' . ($fileExists ? '<span class="success">✅ YES</span>' : '<span class="error">❌ NO</span>') . '</li>';
            
            if ($fileExists) {
                echo '<li><strong>File Size:</strong> ' . number_format(filesize($avatarPath) / 1024, 2) . ' KB</li>';
                echo '<li><strong>URL:</strong> <a href="' . base_url('writable/uploads/avatars/' . $user['avatar']) . '" target="_blank">Klik untuk lihat</a></li>';
            }
            echo '</ul>';
            
            // Update session
            $session->set('avatar', $user['avatar']);
            
            echo '<hr>';
            echo '<p class="success">✅ Avatar berhasil diupdate ke session!</p>';
            echo '<p class="info">Silakan refresh halaman atau klik tombol di bawah untuk kembali.</p>';
            
        } else {
            echo '<hr>';
            echo '<p class="error">❌ User ini belum upload avatar di database.</p>';
            echo '<p class="info">Silakan upload avatar di menu Pengaturan terlebih dahulu.</p>';
        }
        ?>
        
        <a href="<?= base_url('admin/dashboard') ?>" class="button">← Kembali ke Dashboard</a>
        <a href="<?= base_url('admin/pengaturan') ?>" class="button">⚙️ Ke Pengaturan</a>
    </div>
</body>
</html>

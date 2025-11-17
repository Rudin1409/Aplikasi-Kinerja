<?php
/**
 * Simple Avatar Session Fix
 * Versi sederhana tanpa load full CodeIgniter
 */

// Start PHP session
session_start();

// Database config (sesuaikan dengan config Anda)
$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Biasanya kosong di XAMPP
$db_name = 'db_apkb_polsri'; // GANTI INI dengan nama database Anda

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Avatar Session</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f5f5f5; }
        .container { max-width: 700px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: green; font-weight: bold; padding: 15px; background: #d4edda; border-radius: 5px; margin: 15px 0; }
        .error { color: red; font-weight: bold; padding: 15px; background: #f8d7da; border-radius: 5px; margin: 15px 0; }
        .info { color: blue; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .warning { color: orange; background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .button { display: inline-block; margin: 10px 5px; padding: 12px 24px; background: #8B5CF6; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .button:hover { background: #7C3AED; }
        .button.secondary { background: #6B7280; }
        .button.secondary:hover { background: #4B5563; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        table td:first-child { font-weight: bold; width: 150px; color: #6B7280; }
        h1 { color: #1F2937; }
        h3 { color: #374151; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix Avatar Session - Simple Version</h1>
        <p>Script ini akan mengupdate session dengan data avatar dari database.</p>
        <hr>
        
        <?php
        // Cek apakah user sudah login (cek session CodeIgniter)
        if (!isset($_SESSION['user_id'])) {
            echo '<div class="error">❌ Anda belum login. Silakan login terlebih dahulu.</div>';
            echo '<a href="../" class="button">Login Sekarang</a>';
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Connect ke database
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        
        // Cek koneksi
        if ($conn->connect_error) {
            echo '<div class="error">❌ Koneksi database gagal: ' . $conn->connect_error . '</div>';
            echo '<div class="warning">💡 <strong>Solusi:</strong> Edit file ini dan ubah konfigurasi database di baris 10-13</div>';
            echo '<pre>';
            echo '$db_host = \'localhost\';' . "\n";
            echo '$db_user = \'root\';' . "\n";
            echo '$db_pass = \'\'; // Password MySQL Anda' . "\n";
            echo '$db_name = \'nama_database_anda\'; // GANTI INI!' . "\n";
            echo '</pre>';
            exit;
        }
        
        // Query ambil data user
        $stmt = $conn->prepare("SELECT id, nama_lengkap, email, role, avatar FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!$user) {
            echo '<div class="error">❌ User dengan ID ' . $userId . ' tidak ditemukan di database.</div>';
            exit;
        }
        
        // Tampilkan data user
        echo '<h3>📋 Data User Saat Ini:</h3>';
        echo '<table>';
        echo '<tr><td>ID</td><td>' . $user['id'] . '</td></tr>';
        echo '<tr><td>Nama</td><td>' . htmlspecialchars($user['nama_lengkap']) . '</td></tr>';
        echo '<tr><td>Email</td><td>' . htmlspecialchars($user['email']) . '</td></tr>';
        echo '<tr><td>Role</td><td><strong>' . strtoupper($user['role']) . '</strong></td></tr>';
        echo '<tr><td>Avatar DB</td><td>' . ($user['avatar'] ? '<strong style="color:green;">' . htmlspecialchars($user['avatar']) . '</strong>' : '<em style="color:red;">NULL (belum upload)</em>') . '</td></tr>';
        echo '<tr><td>Avatar Session</td><td>' . (isset($_SESSION['avatar']) && $_SESSION['avatar'] ? '<strong style="color:green;">' . htmlspecialchars($_SESSION['avatar']) . '</strong>' : '<em style="color:red;">NULL (tidak ada di session)</em>') . '</td></tr>';
        echo '</table>';
        
        if ($user['avatar']) {
            // Cek file fisik
            $avatarPath = dirname(__DIR__) . '/writable/uploads/avatars/' . $user['avatar'];
            $fileExists = file_exists($avatarPath);
            
            echo '<h3>📁 Status File Avatar:</h3>';
            echo '<table>';
            echo '<tr><td>Nama File</td><td>' . htmlspecialchars($user['avatar']) . '</td></tr>';
            echo '<tr><td>Path</td><td><code>' . htmlspecialchars($avatarPath) . '</code></td></tr>';
            echo '<tr><td>File Exists</td><td>' . ($fileExists ? '<span style="color:green; font-weight:bold;">✅ YES</span>' : '<span style="color:red; font-weight:bold;">❌ NO</span>') . '</td></tr>';
            
            if ($fileExists) {
                $fileSize = filesize($avatarPath);
                echo '<tr><td>File Size</td><td>' . number_format($fileSize / 1024, 2) . ' KB</td></tr>';
                echo '<tr><td>Preview</td><td><img src="../writable/uploads/avatars/' . htmlspecialchars($user['avatar']) . '" style="width:100px;height:100px;object-fit:cover;border-radius:50%;border:3px solid #8B5CF6;"></td></tr>';
            }
            echo '</table>';
            
            // Update session
            $_SESSION['avatar'] = $user['avatar'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['email'] = $user['email'];
            
            echo '<div class="success">';
            echo '✅ <strong>Avatar berhasil diupdate ke session!</strong><br>';
            echo '📌 Session avatar sekarang: <strong>' . htmlspecialchars($user['avatar']) . '</strong><br>';
            echo '🎉 Silakan refresh dashboard Anda untuk melihat avatar.';
            echo '</div>';
            
        } else {
            echo '<div class="warning">';
            echo '⚠️ <strong>User ini belum upload avatar di database.</strong><br>';
            echo '📝 Kolom avatar masih NULL/kosong.<br>';
            echo '💡 Silakan upload avatar di menu <strong>Pengaturan → Tab Profil</strong>.';
            echo '</div>';
        }
        
        $stmt->close();
        $conn->close();
        ?>
        
        <hr style="margin: 30px 0;">
        <a href="../admin/dashboard" class="button">← Kembali ke Dashboard</a>
        <a href="../admin/pengaturan" class="button secondary">⚙️ Ke Pengaturan</a>
    </div>
</body>
</html>

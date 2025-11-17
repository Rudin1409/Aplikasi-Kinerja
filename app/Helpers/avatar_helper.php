<?php

/**
 * Helper function untuk mendapatkan avatar user
 * Cek session dulu, jika tidak ada cek database
 */
if (!function_exists('getUserAvatar')) {
    function getUserAvatar() {
        $avatar = session()->get('avatar');
        
        // Jika session kosong, ambil dari database
        if (!$avatar) {
            $userId = session()->get('user_id');
            if ($userId) {
                $userModel = new \App\Models\UserModel();
                $user = $userModel->find($userId);
                if ($user && isset($user['avatar']) && $user['avatar']) {
                    $avatar = $user['avatar'];
                    // Update session agar tidak query lagi
                    session()->set('avatar', $avatar);
                }
            }
        }
        
        return $avatar;
    }
}

/**
 * Helper function untuk mendapatkan URL avatar
 * Return URL avatar atau null
 */
if (!function_exists('getAvatarUrl')) {
    function getAvatarUrl() {
        $avatar = getUserAvatar();
        
        if ($avatar && file_exists(WRITEPATH . 'uploads/avatars/' . $avatar)) {
            return base_url('writable/uploads/avatars/' . $avatar);
        }
        
        return null;
    }
}

/**
 * Helper function untuk mendapatkan inisial nama
 */
if (!function_exists('getUserInitial')) {
    function getUserInitial() {
        $nama = session()->get('nama_lengkap') ?? 'User';
        return strtoupper(substr($nama, 0, 1));
    }
}

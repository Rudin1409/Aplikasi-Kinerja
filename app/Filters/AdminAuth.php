<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();

        // 1. Cek jika tidak login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); 
        }


        // 2. Cek apakah rolenya sesuai
        $allowedRoles = ['admin', 'pimpinan', 'jurusan', 'prodi'];
        if (!in_array($session->get('role'), $allowedRoles)) {
            // Jika role tidak diizinkan, tendang
            return redirect()->to('/');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Kosongkan saja
    }
}
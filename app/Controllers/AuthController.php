<?php

namespace App\Controllers;

use App\Models\AdminModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('admin_id') !== null) {
            return redirect()->to(site_url('/'));
        }

        return view('auth/login', [
            'errors' => session()->getFlashdata('errors') ?? [],
            'username' => old('username', ''),
        ]);
    }

    public function attemptLogin()
    {
        $rules = [
            'username' => 'required|max_length[80]',
            'password' => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return view('auth/login', [
                'errors' => $this->validator->getErrors(),
                'username' => old('username', ''),
            ]);
        }

        $admin = (new AdminModel())
            ->where('username', trim((string) $this->request->getPost('username')))
            ->where('is_active', 1)
            ->first();

        if ($admin === null || ! password_verify((string) $this->request->getPost('password'), $admin['password_hash'])) {
            return view('auth/login', [
                'errors' => ['login' => 'Tên đăng nhập hoặc mật khẩu không chính xác.'],
                'username' => trim((string) $this->request->getPost('username')),
            ]);
        }

        session()->regenerate();
        session()->set([
            'admin_id' => (int) $admin['id'],
            'admin_name' => $admin['full_name'],
            'admin_username' => $admin['username'],
        ]);

        return redirect()->to(site_url('/'));
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('login'));
    }
}

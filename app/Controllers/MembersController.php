<?php

namespace App\Controllers;

use App\Models\MemberModel;

class MembersController extends BaseController
{
    public function register()
    {
        if (session()->get('member_id') !== null) {
            return redirect()->to(site_url('chat'));
        }

        return view('members/register', [
            'errors' => [],
            'old' => [],
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost(['full_name', 'username', 'email']);
        $data['username'] = trim((string) ($data['username'] ?? ''));
        $rules = [
            'full_name' => 'required|min_length[2]|max_length[120]',
            'username' => [
                'rules' => 'required|min_length[3]|max_length[80]|regex_match[/^[\p{L}\p{N} .@_-]+$/u]|is_unique[members.username]',
                'errors' => [
                    'regex_match' => 'Tên đăng nhập chỉ dùng chữ, số, dấu cách và các ký tự . @ _ -.',
                ],
            ],
            'email' => 'required|valid_email|max_length[190]|is_unique[members.email]',
            'password' => 'required|min_length[10]|max_length[255]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return view('members/register', [
                'errors' => $this->validator->getErrors(),
                'old' => $data,
            ]);
        }

        $memberId = (new MemberModel())->insert([
            'full_name' => trim((string) $data['full_name']),
            'username' => trim((string) $data['username']),
            'email' => trim((string) $data['email']),
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'is_active' => 1,
        ], true);

        session()->remove('admin_id');
        session()->remove('admin_name');
        session()->remove('admin_username');
        session()->regenerate();
        session()->set([
            'member_id' => (int) $memberId,
            'member_name' => trim((string) $data['full_name']),
            'member_username' => trim((string) $data['username']),
        ]);

        return redirect()->to(site_url('chat'))->with('success', 'Tạo tài khoản thành viên thành công.');
    }

    public function login()
    {
        if (session()->get('member_id') !== null) {
            return redirect()->to(site_url('chat'));
        }

        return view('members/login', [
            'errors' => session()->getFlashdata('errors') ?? [],
            'username' => session()->getFlashdata('username') ?? '',
        ]);
    }

    public function attemptLogin()
    {
        $username = trim((string) $this->request->getPost('username'));
        if (! $this->validate([
            'username' => 'required|max_length[190]',
            'password' => 'required|max_length[255]',
        ])) {
            return view('members/login', [
                'errors' => $this->validator->getErrors(),
                'username' => $username,
            ]);
        }

        $member = (new MemberModel())
            ->groupStart()
            ->where('username', $username)
            ->orWhere('email', $username)
            ->groupEnd()
            ->where('is_active', 1)
            ->first();

        if ($member === null || ! password_verify((string) $this->request->getPost('password'), $member['password_hash'])) {
            return view('members/login', [
                'errors' => ['login' => 'Tên đăng nhập/email hoặc mật khẩu không chính xác.'],
                'username' => $username,
            ]);
        }

        session()->remove('admin_id');
        session()->remove('admin_name');
        session()->remove('admin_username');
        session()->regenerate();
        session()->set([
            'member_id' => (int) $member['id'],
            'member_name' => $member['full_name'],
            'member_username' => $member['username'],
        ]);

        return redirect()->to(site_url('chat'));
    }

    public function logout()
    {
        session()->remove('member_id');
        session()->remove('member_name');
        session()->remove('member_username');
        session()->regenerate();

        return redirect()->to(site_url('member/login'));
    }
}

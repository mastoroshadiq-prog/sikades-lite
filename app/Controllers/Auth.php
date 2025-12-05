<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Show login page
     */
    public function login()
    {
        // If already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Login - Siskeudes Lite',
        ];

        return view('auth/login', $data);
    }

    /**
     * Process login attempt
     */
    public function attemptLogin()
    {
        // Validation rules
        $rules = [
            'username' => 'required',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Find user by username
        $user = $this->userModel->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        // Set session data
        $sessionData = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'kode_desa' => $user['kode_desa'],
            'isLoggedIn' => true,
        ];

        $this->session->set($sessionData);

        // Log login activity
        log_message('info', 'User logged in: ' . $username . ' (Role: ' . $user['role'] . ')');

        // Check for redirect URL
        $redirectUrl = $this->session->get('redirect_url') ?? '/dashboard';
        $this->session->remove('redirect_url');

        return redirect()->to($redirectUrl)->with('success', 'Selamat datang, ' . $username . '!');
    }

    /**
     * Logout
     */
    public function logout()
    {
        $username = $this->session->get('username');

        // Log logout activity
        if ($username) {
            log_message('info', 'User logged out: ' . $username);
        }

        // Destroy session
        $this->session->destroy();

        return redirect()->to('/login')->with('success', 'Anda telah logout.');
    }
}

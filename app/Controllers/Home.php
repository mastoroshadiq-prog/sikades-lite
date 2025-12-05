<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // If already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            return redirect()->to('/dashboard');
        }
        
        // Show landing page
        $data = [
            'title' => 'Siskeudes Lite - Sistem Keuangan Desa',
        ];
        
        return view('home', $data);
    }
}

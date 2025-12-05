<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\DataUmumDesaModel;
use App\Models\RefRekeningModel;

class Master extends BaseController
{
    protected $userModel;
    protected $desaModel;
    protected $rekeningModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->desaModel = new DataUmumDesaModel();
        $this->rekeningModel = new RefRekeningModel();
    }

    /**
     * Data Desa Management
     */
    public function desa()
    {
        // Only Admin can access
        if (!$this->hasRole('Administrator')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $desa = $this->desaModel->where('kode_desa', $kodeDesa)->first();

        $data = array_merge($this->data, [
            'title' => 'Data Umum Desa',
            'desa' => $desa,
        ]);

        return view('master/desa', $data);
    }

    /**
     * Save/Update Data Desa
     */
    public function saveDesa()
    {
        if (!$this->hasRole('Administrator')) {
            return $this->respondError('Akses ditolak', 403);
        }

        $rules = [
            'kode_desa' => 'required',
            'nama_desa' => 'required',
            'nama_kepala_desa' => 'required',
            'nama_bendahara' => 'required',
            'tahun_anggaran' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kode_desa' => $this->request->getPost('kode_desa'),
            'nama_desa' => $this->request->getPost('nama_desa'),
            'nama_kepala_desa' => $this->request->getPost('nama_kepala_desa'),
            'nama_bendahara' => $this->request->getPost('nama_bendahara'),
            'npwp' => $this->request->getPost('npwp'),
            'tahun_anggaran' => $this->request->getPost('tahun_anggaran'),
        ];

        // Check if exists
        $existing = $this->desaModel->where('kode_desa', $data['kode_desa'])->first();

        if ($existing) {
            $this->desaModel->update($existing['id'], $data);
            $message = 'Data desa berhasil diperbarui';
        } else {
            $this->desaModel->insert($data);
            $message = 'Data desa berhasil disimpan';
        }

        return redirect()->to('/master/desa')->with('success', $message);
    }

    /**
     * Users Management
     */
    public function users()
    {
        if (!$this->hasRole('Administrator')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $users = $this->userModel->findAll();

        $data = array_merge($this->data, [
            'title' => 'Manajemen User',
            'users' => $users,
        ]);

        return view('master/users', $data);
    }

    /**
     * Create User Form
     */
    public function createUser()
    {
        if (!$this->hasRole('Administrator')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $data = array_merge($this->data, [
            'title' => 'Tambah User Baru',
        ]);

        return view('master/user_form', $data);
    }

    /**
     * Save New User
     */
    public function saveUser()
    {
        if (!$this->hasRole('Administrator')) {
            return $this->respondError('Akses ditolak', 403);
        }

        $rules = [
            'username' => 'required|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'role' => 'required|in_list[Administrator,Operator Desa,Kepala Desa]',
            'kode_desa' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
            'kode_desa' => $this->request->getPost('kode_desa'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->userModel->insert($data);

        return redirect()->to('/master/users')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Edit User Form
     */
    public function editUser($id)
    {
        if (!$this->hasRole('Administrator')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/master/users')->with('error', 'User tidak ditemukan');
        }

        $data = array_merge($this->data, [
            'title' => 'Edit User',
            'user' => $user,
        ]);

        return view('master/user_form', $data);
    }

    /**
     * Update User
     */
    public function updateUser($id)
    {
        if (!$this->hasRole('Administrator')) {
            return $this->respondError('Akses ditolak', 403);
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/master/users')->with('error', 'User tidak ditemukan');
        }

        $rules = [
            'username' => "required|is_unique[users.username,id,{$id}]",
            'role' => 'required|in_list[Administrator,Operator Desa,Kepala Desa]',
            'kode_desa' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'role' => $this->request->getPost('role'),
            'kode_desa' => $this->request->getPost('kode_desa'),
        ];

        // Update password if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);

        return redirect()->to('/master/users')->with('success', 'User berhasil diperbarui');
    }

    /**
     * Delete User
     */
    public function deleteUser($id)
    {
        if (!$this->hasRole('Administrator')) {
            return $this->respondError('Akses ditolak', 403);
        }

        // Prevent deleting own account
        if ($id == $this->getUserId()) {
            return $this->respondError('Tidak dapat menghapus akun sendiri');
        }

        $this->userModel->delete($id);

        return $this->respondSuccess(null, 'User berhasil dihapus');
    }

    /**
     * Rekening (Chart of Accounts)
     */
    public function rekening()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $rekening = $this->rekeningModel->orderBy('kode_akun', 'ASC')->findAll();

        $data = array_merge($this->data, [
            'title' => 'Referensi Rekening',
            'rekening' => $rekening,
        ]);

        return view('master/rekening', $data);
    }

    /**
     * Import Rekening (would typically import from CSV/Excel)
     */
    public function importRekening()
    {
        if (!$this->hasRole('Administrator')) {
            return $this->respondError('Akses ditolak', 403);
        }

        // This would handle file upload and import
        // For now, just a placeholder
        return redirect()->to('/master/rekening')->with('info', 'Import rekening akan diimplementasikan');
    }
}

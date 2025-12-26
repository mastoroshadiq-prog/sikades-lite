<?php

namespace App\Controllers;

use App\Models\StrukturOrganisasiModel;

class Organisasi extends BaseController
{
    protected $organisasiModel;

    public function __construct()
    {
        $this->organisasiModel = new StrukturOrganisasiModel();
    }

    /**
     * Display organizational structure
     */
    public function index()
    {
        $kodeDesa = session()->get('kode_desa');
        
        $perangkat = $this->organisasiModel->getAllByDesa($kodeDesa);
        $stats = $this->organisasiModel->getStats($kodeDesa);

        $data = array_merge($this->data, [
            'title' => 'Struktur Organisasi',
            'perangkat' => $perangkat,
            'stats' => $stats,
        ]);

        return view('master/organisasi/index', $data);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $data = array_merge($this->data, [
            'title' => 'Tambah Perangkat Desa',
        ]);

        return view('master/organisasi/form', $data);
    }

    /**
     * Save new staff
     */
    public function store()
    {
        $kodeDesa = session()->get('kode_desa');

        $postData = [
            'kode_desa' => $kodeDesa,
            'nama' => $this->request->getPost('nama'),
            'jabatan' => $this->request->getPost('jabatan'),
            'nip' => $this->request->getPost('nip'),
            'pangkat_golongan' => $this->request->getPost('pangkat_golongan'),
            'pendidikan' => $this->request->getPost('pendidikan'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
            'tanggal_pengangkatan' => $this->request->getPost('tanggal_pengangkatan') ?: null,
            'no_sk' => $this->request->getPost('no_sk'),
            'urutan' => $this->request->getPost('urutan') ?: 0,
            'aktif' => $this->request->getPost('aktif') === '1',
        ];

        if (!$this->organisasiModel->save($postData)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: ' . implode(', ', $this->organisasiModel->errors()));
        }

        return redirect()->to('/master/organisasi')
            ->with('message', 'Data perangkat berhasil ditambahkan');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $perangkat = $this->organisasiModel->find($id);

        if (!$perangkat) {
            return redirect()->to('/master/organisasi')
                ->with('error', 'Data tidak ditemukan');
        }

        $data = array_merge($this->data, [
            'title' => 'Edit Perangkat Desa',
            'perangkat' => $perangkat,
        ]);

        return view('master/organisasi/form', $data);
    }

    /**
     * Update staff
     */
    public function update($id)
    {
        $perangkat = $this->organisasiModel->find($id);

        if (!$perangkat) {
            return redirect()->to('/master/organisasi')
                ->with('error', 'Data tidak ditemukan');
        }

        $postData = [
            'nama' => $this->request->getPost('nama'),
            'jabatan' => $this->request->getPost('jabatan'),
            'nip' => $this->request->getPost('nip'),
            'pangkat_golongan' => $this->request->getPost('pangkat_golongan'),
            'pendidikan' => $this->request->getPost('pendidikan'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
            'tanggal_pengangkatan' => $this->request->getPost('tanggal_pengangkatan') ?: null,
            'no_sk' => $this->request->getPost('no_sk'),
            'urutan' => $this->request->getPost('urutan') ?: 0,
            'aktif' => $this->request->getPost('aktif') === '1',
        ];

        if (!$this->organisasiModel->update($id, $postData)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate data: ' . implode(', ', $this->organisasiModel->errors()));
        }

        return redirect()->to('/master/organisasi')
            ->with('message', 'Data perangkat berhasil diupdate');
    }

    /**
     * Delete staff
     */
    public function delete($id)
    {
        if (!$this->organisasiModel->delete($id)) {
            return redirect()->to('/master/organisasi')
                ->with('error', 'Gagal menghapus data');
        }

        return redirect()->to('/master/organisasi')
            ->with('message', 'Data perangkat berhasil dihapus');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        $perangkat = $this->organisasiModel->find($id);

        if (!$perangkat) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $newStatus = !$perangkat['aktif'];
        $this->organisasiModel->update($id, ['aktif' => $newStatus]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Status berhasil diubah',
            'aktif' => $newStatus
        ]);
    }
}

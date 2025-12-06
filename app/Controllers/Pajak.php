<?php

namespace App\Controllers;

use App\Models\PajakModel;
use App\Models\BkuModel;

class Pajak extends BaseController
{
    protected $pajakModel;
    protected $bkuModel;

    public function __construct()
    {
        $this->pajakModel = new PajakModel();
        $this->bkuModel = new BkuModel();
    }

    /**
     * List Pajak
     */
    public function index()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa', 'Kepala Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $pajakList = $this->pajakModel->getPajakWithBKU($kodeDesa, $tahun);

        // Calculate totals
        $totalPPN = $this->pajakModel->getTotalByJenis($kodeDesa, 'PPN', $tahun);
        $totalPPh = $this->pajakModel->getTotalByJenis($kodeDesa, 'PPh', $tahun);
        $totalBelumBayar = $this->pajakModel->getTotalBelumBayar($kodeDesa, $tahun);

        $data = array_merge($this->data, [
            'title' => 'Pajak - Pencatatan Pajak',
            'pajak_list' => $pajakList,
            'tahun' => $tahun,
            'total_ppn' => $totalPPN,
            'total_pph' => $totalPPh,
            'total_belum_bayar' => $totalBelumBayar,
        ]);

        return view('pajak/index', $data);
    }

    /**
     * Create Pajak Form
     */
    public function create()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return redirect()->to('/pajak')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        
        // Get BKU entries for linking
        $bkuEntries = $this->bkuModel->where('kode_desa', $kodeDesa)
            ->where('jenis_transaksi', 'Belanja')
            ->orderBy('tanggal', 'DESC')
            ->findAll();

        $data = array_merge($this->data, [
            'title' => 'Tambah Pajak',
            'bku_entries' => $bkuEntries,
        ]);

        return view('pajak/form', $data);
    }

    /**
     * Save new Pajak
     */
    public function save()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $rules = [
            'bku_id' => 'required|integer',
            'jenis_pajak' => 'required|in_list[PPN,PPh]',
            'tarif' => 'required|decimal',
            'npwp' => 'permit_empty',
            'nama_wajib_pajak' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get BKU to calculate pajak
        $bku = $this->bkuModel->find($this->request->getPost('bku_id'));
        $tarif = $this->request->getPost('tarif');
        $jumlahPajak = $bku['kredit'] * ($tarif / 100);

        $data = [
            'bku_id' => $this->request->getPost('bku_id'),
            'jenis_pajak' => $this->request->getPost('jenis_pajak'),
            'tarif' => $tarif,
            'jumlah_pajak' => $jumlahPajak,
            'npwp' => $this->request->getPost('npwp'),
            'nama_wajib_pajak' => $this->request->getPost('nama_wajib_pajak'),
            'status_pembayaran' => 'Belum',
            'tanggal_pajak' => $bku['tanggal'],
        ];

        $this->pajakModel->insert($data);

        return redirect()->to('/pajak')->with('success', 'Pajak berhasil ditambahkan');
    }

    /**
     * Edit Pajak Form
     */
    public function edit($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return redirect()->to('/pajak')->with('error', 'Akses ditolak.');
        }

        $pajak = $this->pajakModel->find($id);

        if (!$pajak) {
            return redirect()->to('/pajak')->with('error', 'Data tidak ditemukan');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $bkuEntries = $this->bkuModel->where('kode_desa', $kodeDesa)
            ->where('jenis_transaksi', 'Belanja')
            ->orderBy('tanggal', 'DESC')
            ->findAll();

        $data = array_merge($this->data, [
            'title' => 'Edit Pajak',
            'pajak' => $pajak,
            'bku_entries' => $bkuEntries,
        ]);

        return view('pajak/form', $data);
    }

    /**
     * Update Pajak
     */
    public function update($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $pajak = $this->pajakModel->find($id);

        if (!$pajak) {
            return redirect()->to('/pajak')->with('error', 'Data tidak ditemukan');
        }

        $rules = [
            'jenis_pajak' => 'required|in_list[PPN,PPh]',
            'tarif' => 'required|decimal',
            'npwp' => 'permit_empty',
            'nama_wajib_pajak' => 'required',
            'status_pembayaran' => 'required|in_list[Belum,Sudah]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bku = $this->bkuModel->find($pajak['bku_id']);
        $tarif = $this->request->getPost('tarif');
        $jumlahPajak = $bku['kredit'] * ($tarif / 100);

        $data = [
            'jenis_pajak' => $this->request->getPost('jenis_pajak'),
            'tarif' => $tarif,
            'jumlah_pajak' => $jumlahPajak,
            'npwp' => $this->request->getPost('npwp'),
            'nama_wajib_pajak' => $this->request->getPost('nama_wajib_pajak'),
            'status_pembayaran' => $this->request->getPost('status_pembayaran'),
        ];

        // If paid, record payment date
        if ($this->request->getPost('status_pembayaran') == 'Sudah') {
            $data['tanggal_setor'] = $this->request->getPost('tanggal_setor');
            $data['nomor_bukti_setor'] = $this->request->getPost('nomor_bukti_setor');
        }

        $this->pajakModel->update($id, $data);

        return redirect()->to('/pajak')->with('success', 'Pajak berhasil diperbarui');
    }

    /**
     * Delete Pajak
     */
    public function delete($id)
    {
        if (!$this->hasRole(['Administrator'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $pajak = $this->pajakModel->find($id);

        if (!$pajak) {
            return $this->respondError('Data tidak ditemukan');
        }

        $this->pajakModel->delete($id);

        return $this->respondSuccess(null, 'Pajak berhasil dihapus');
    }

    /**
     * Mark as paid
     */
    public function bayar($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $pajak = $this->pajakModel->find($id);

        if (!$pajak) {
            return $this->respondError('Data tidak ditemukan');
        }

        $this->pajakModel->update($id, [
            'status_pembayaran' => 'Sudah',
            'tanggal_setor' => date('Y-m-d'),
        ]);

        return $this->respondSuccess(null, 'Status pembayaran berhasil diperbarui');
    }
}

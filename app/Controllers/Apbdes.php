<?php

namespace App\Controllers;

use App\Models\ApbdesModel;
use App\Models\RefRekeningModel;

class Apbdes extends BaseController
{
    protected $apbdesModel;
    protected $rekeningModel;

    public function __construct()
    {
        $this->apbdesModel = new ApbdesModel();
        $this->rekeningModel = new RefRekeningModel();
    }

    /**
     * List APBDes
     */
    public function index()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa', 'Kepala Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $anggaran = $this->apbdesModel->getAnggaranWithRekening($kodeDesa, $tahun);

        $data = array_merge($this->data, [
            'title' => 'APBDes - Anggaran Pendapatan dan Belanja Desa',
            'anggaran' => $anggaran,
            'tahun' => $tahun,
        ]);

        return view('apbdes/index', $data);
    }

    /**
     * Create APBDes Form
     */
    public function create()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        // Get all rekening for dropdown
        $rekening = $this->rekeningModel->orderBy('kode_akun', 'ASC')->findAll();

        $data = array_merge($this->data, [
            'title' => 'Tambah Anggaran',
            'rekening' => $rekening,
        ]);

        return view('apbdes/form', $data);
    }

    /**
     * Save APBDes
     */
    public function save()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $rules = [
            'ref_rekening_id' => 'required|integer',
            'uraian' => 'required',
            'anggaran' => 'required|decimal',
            'sumber_dana' => 'required|in_list[DDS,ADD,PAD,Bankeu]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kode_desa' => $this->session->get('kode_desa'),
            'tahun' => $this->request->getPost('tahun') ?? date('Y'),
            'ref_rekening_id' => $this->request->getPost('ref_rekening_id'),
            'uraian' => $this->request->getPost('uraian'),
            'anggaran' => $this->request->getPost('anggaran'),
            'sumber_dana' => $this->request->getPost('sumber_dana'),
        ];

        // Validasi: Anggaran tidak boleh minus
        if ($data['anggaran'] < 0) {
            return redirect()->back()->withInput()->with('error', 'Anggaran tidak boleh bernilai minus');
        }

        $this->apbdesModel->insert($data);

        return redirect()->to('/apbdes')->with('success', 'Anggaran berhasil ditambahkan');
    }

    /**
     * Edit APBDes Form
     */
    public function edit($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $anggaran = $this->apbdesModel->find($id);

        if (!$anggaran) {
            return redirect()->to('/apbdes')->with('error', 'Data tidak ditemukan');
        }

        // Check if belongs to user's desa
        if ($anggaran['kode_desa'] != $this->session->get('kode_desa')) {
            return redirect()->to('/apbdes')->with('error', 'Akses ditolak');
        }

        $rekening = $this->rekeningModel->orderBy('kode_akun', 'ASC')->findAll();

        $data = array_merge($this->data, [
            'title' => 'Edit Anggaran',
            'anggaran' => $anggaran,
            'rekening' => $rekening,
        ]);

        return view('apbdes/form', $data);
    }

    /**
     * Update APBDes
     */
    public function update($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $anggaran = $this->apbdesModel->find($id);

        if (!$anggaran || $anggaran['kode_desa'] != $this->session->get('kode_desa')) {
            return redirect()->to('/apbdes')->with('error', 'Data tidak ditemukan');
        }

        $rules = [
            'ref_rekening_id' => 'required|integer',
            'uraian' => 'required',
            'anggaran' => 'required|decimal',
            'sumber_dana' => 'required|in_list[DDS,ADD,PAD,Bankeu]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'tahun' => $this->request->getPost('tahun') ?? $anggaran['tahun'],
            'ref_rekening_id' => $this->request->getPost('ref_rekening_id'),
            'uraian' => $this->request->getPost('uraian'),
            'anggaran' => $this->request->getPost('anggaran'),
            'sumber_dana' => $this->request->getPost('sumber_dana'),
        ];

        if ($data['anggaran'] < 0) {
            return redirect()->back()->withInput()->with('error', 'Anggaran tidak boleh bernilai minus');
        }

        $this->apbdesModel->update($id, $data);

        return redirect()->to('/apbdes')->with('success', 'Anggaran berhasil diperbarui');
    }

    /**
     * Delete APBDes
     */
    public function delete($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $anggaran = $this->apbdesModel->find($id);

        if (!$anggaran || $anggaran['kode_desa'] != $this->session->get('kode_desa')) {
            return $this->respondError('Data tidak ditemukan', 404);
        }

        $this->apbdesModel->delete($id);

        return $this->respondSuccess(null, 'Anggaran berhasil dihapus');
    }

    /**
     * APBDes Report
     */
    public function report()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa', 'Kepala Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $anggaran = $this->apbdesModel->getAnggaranWithRekening($kodeDesa, $tahun);

        // Group by level for tree view
        $grouped = [
            'pendapatan' => [],
            'belanja' => [],
            'pembiayaan' => [],
        ];

        foreach ($anggaran as $item) {
            $kodeAkun = $item['kode_akun'];
            
            if (strpos($kodeAkun, '4.') === 0) {
                $grouped['pendapatan'][] = $item;
            } elseif (strpos($kodeAkun, '5.') === 0) {
                $grouped['belanja'][] = $item;
            } elseif (strpos($kodeAkun, '6.') === 0) {
                $grouped['pembiayaan'][] = $item;
            }
        }

        $data = array_merge($this->data, [
            'title' => 'Laporan APBDes',
            'grouped' => $grouped,
            'tahun' => $tahun,
        ]);

        return view('apbdes/report', $data);
    }
}

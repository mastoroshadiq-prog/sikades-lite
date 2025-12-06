<?php

namespace App\Controllers;

use App\Models\SppModel;
use App\Models\SppRincianModel;
use App\Models\ApbdesModel;
use App\Models\UserModel;

class Spp extends BaseController
{
    protected $sppModel;
    protected $sppRincianModel;
    protected $apbdesModel;
    protected $userModel;

    public function __construct()
    {
        $this->sppModel = new SppModel();
        $this->sppRincianModel = new SppRincianModel();
        $this->apbdesModel = new ApbdesModel();
        $this->userModel = new UserModel();
    }

    /**
     * List SPP
     */
    public function index()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa', 'Kepala Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $status = $this->request->getGet('status') ?? '';
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $sppList = $this->sppModel->getSppWithDetails($kodeDesa, $status, $tahun);

        $data = array_merge($this->data, [
            'title' => 'SPP - Surat Permintaan Pembayaran',
            'spp_list' => $sppList,
            'status_filter' => $status,
            'tahun' => $tahun,
        ]);

        return view('spp/index', $data);
    }

    /**
     * Create SPP Form
     */
    public function create()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return redirect()->to('/spp')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $tahun = date('Y');
        
        // Get available budget items
        $anggaran = $this->apbdesModel->getAnggaranWithRekening($kodeDesa, $tahun);

        $data = array_merge($this->data, [
            'title' => 'Buat SPP Baru',
            'anggaran' => $anggaran,
        ]);

        return view('spp/form', $data);
    }

    /**
     * Save new SPP
     */
    public function save()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $rules = [
            'nomor_spp' => 'required',
            'tanggal_spp' => 'required|valid_date',
            'uraian' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Generate SPP data
        $sppData = [
            'kode_desa' => $this->session->get('kode_desa'),
            'nomor_spp' => $this->request->getPost('nomor_spp'),
            'tanggal_spp' => $this->request->getPost('tanggal_spp'),
            'uraian' => $this->request->getPost('uraian'),
            'jumlah' => 0, // Will calculate from rincian
            'status' => 'Draft',
            'created_by' => $this->getUserId(),
        ];

        // Insert SPP
        $sppId = $this->sppModel->insert($sppData);

        if (!$sppId) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan SPP');
        }

        // Insert SPP Rincian (line items)
        $apbdesIds = $this->request->getPost('apbdes_id') ?? [];
        $jumlahs = $this->request->getPost('jumlah_rincian') ?? [];
        $uraians = $this->request->getPost('uraian_rincian') ?? [];
        
        $totalJumlah = 0;
        
        foreach ($apbdesIds as $index => $apbdesId) {
            if (!empty($apbdesId) && !empty($jumlahs[$index])) {
                $rincianData = [
                    'spp_id' => $sppId,
                    'apbdes_id' => $apbdesId,
                    'uraian' => $uraians[$index] ?? '',
                    'jumlah' => $jumlahs[$index],
                ];
                
                $this->sppRincianModel->insert($rincianData);
                $totalJumlah += $jumlahs[$index];
            }
        }

        // Update SPP total
        $this->sppModel->update($sppId, ['jumlah' => $totalJumlah]);

        return redirect()->to('/spp')->with('success', 'SPP berhasil dibuat');
    }

    /**
     * Edit SPP Form
     */
    public function edit($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return redirect()->to('/spp')->with('error', 'Akses ditolak.');
        }

        $spp = $this->sppModel->find($id);

        if (!$spp || $spp['kode_desa'] != $this->session->get('kode_desa')) {
            return redirect()->to('/spp')->with('error', 'Data tidak ditemukan');
        }

        // Can only edit Draft status
        if ($spp['status'] != 'Draft') {
            return redirect()->to('/spp')->with('error', 'Hanya SPP berstatus Draft yang dapat diedit');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $tahun = date('Y');
        
        $anggaran = $this->apbdesModel->getAnggaranWithRekening($kodeDesa, $tahun);
        $rincian = $this->sppRincianModel->where('spp_id', $id)->findAll();

        $data = array_merge($this->data, [
            'title' => 'Edit SPP',
            'spp' => $spp,
            'rincian' => $rincian,
            'anggaran' => $anggaran,
        ]);

        return view('spp/form', $data);
    }

    /**
     * Update SPP
     */
    public function update($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $spp = $this->sppModel->find($id);

        if (!$spp || $spp['kode_desa'] != $this->session->get('kode_desa')) {
            return redirect()->to('/spp')->with('error', 'Data tidak ditemukan');
        }

        if ($spp['status'] != 'Draft') {
            return redirect()->to('/spp')->with('error', 'Hanya SPP berstatus Draft yang dapat diedit');
        }

        $rules = [
            'nomor_spp' => 'required',
            'tanggal_spp' => 'required|valid_date',
            'uraian' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Update SPP header
        $sppData = [
            'nomor_spp' => $this->request->getPost('nomor_spp'),
            'tanggal_spp' => $this->request->getPost('tanggal_spp'),
            'uraian' => $this->request->getPost('uraian'),
        ];

        $this->sppModel->update($id, $sppData);

        // Delete old rincian
        $this->sppRincianModel->where('spp_id', $id)->delete();

        // Insert new rincian
        $apbdesIds = $this->request->getPost('apbdes_id') ?? [];
        $jumlahs = $this->request->getPost('jumlah_rincian') ?? [];
        $uraians = $this->request->getPost('uraian_rincian') ?? [];
        
        $totalJumlah = 0;
        
        foreach ($apbdesIds as $index => $apbdesId) {
            if (!empty($apbdesId) && !empty($jumlahs[$index])) {
                $rincianData = [
                    'spp_id' => $id,
                    'apbdes_id' => $apbdesId,
                    'uraian' => $uraians[$index] ?? '',
                    'jumlah' => $jumlahs[$index],
                ];
                
                $this->sppRincianModel->insert($rincianData);
                $totalJumlah += $jumlahs[$index];
            }
        }

        // Update SPP total
        $this->sppModel->update($id, ['jumlah' => $totalJumlah]);

        return redirect()->to('/spp')->with('success', 'SPP berhasil diperbarui');
    }

    /**
     * View SPP Detail
     */
    public function detail($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa', 'Kepala Desa'])) {
            return redirect()->to('/spp')->with('error', 'Akses ditolak.');
        }

        $spp = $this->sppModel->getDetailWithRincian($id);

        if (!$spp || $spp['kode_desa'] != $this->session->get('kode_desa')) {
            return redirect()->to('/spp')->with('error', 'Data tidak ditemukan');
        }

        $data = array_merge($this->data, [
            'title' => 'Detail SPP',
            'spp' => $spp,
        ]);

        return view('spp/detail', $data);
    }

    /**
     * Verify SPP (Operator)
     */
    public function verify($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $spp = $this->sppModel->find($id);

        if (!$spp || $spp['kode_desa'] != $this->session->get('kode_desa')) {
            return $this->respondError('Data tidak ditemukan');
        }

        if ($spp['status'] != 'Draft') {
            return $this->respondError('Hanya SPP berstatus Draft yang dapat diverifikasi');
        }

        $this->sppModel->update($id, [
            'status' => 'Verified',
            'verified_by' => $this->getUserId(),
        ]);

        return $this->respondSuccess(null, 'SPP berhasil diverifikasi');
    }

    /**
     * Approve SPP (Kepala Desa)
     */
    public function approve($id)
    {
        if (!$this->hasRole(['Administrator', 'Kepala Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $spp = $this->sppModel->find($id);

        if (!$spp || $spp['kode_desa'] != $this->session->get('kode_desa')) {
            return $this->respondError('Data tidak ditemukan');
        }

        if ($spp['status'] != 'Verified') {
            return $this->respondError('Hanya SPP berstatus Verified yang dapat disetujui');
        }

        $this->sppModel->update($id, [
            'status' => 'Approved',
            'approved_by' => $this->getUserId(),
        ]);

        return $this->respondSuccess(null, 'SPP berhasil disetujui');
    }

    /**
     * Delete SPP
     */
    public function delete($id)
    {
        if (!$this->hasRole(['Administrator'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $spp = $this->sppModel->find($id);

        if (!$spp || $spp['kode_desa'] != $this->session->get('kode_desa')) {
            return $this->respondError('Data tidak ditemukan');
        }

        // Can only delete Draft status
        if ($spp['status'] != 'Draft') {
            return $this->respondError('Hanya SPP berstatus Draft yang dapat dihapus');
        }

        // Delete rincian first
        $this->sppRincianModel->where('spp_id', $id)->delete();
        
        // Delete SPP
        $this->sppModel->delete($id);

        return $this->respondSuccess(null, 'SPP berhasil dihapus');
    }
}

<?php

namespace App\Controllers;

use App\Models\PakModel;
use App\Models\PakDetailModel;
use App\Models\ApbdesModel;
use App\Models\ActivityLogModel;

class Pak extends BaseController
{
    protected $pakModel;
    protected $pakDetailModel;
    protected $apbdesModel;

    public function __construct()
    {
        $this->pakModel = new PakModel();
        $this->pakDetailModel = new PakDetailModel();
        $this->apbdesModel = new ApbdesModel();
    }

    /**
     * List all PAK
     */
    public function index()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa', 'Kepala Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $pakList = $this->pakModel->getPakWithDetails($kodeDesa, (int)$tahun);

        $data = array_merge($this->data, [
            'title' => 'Perubahan Anggaran (PAK)',
            'pakList' => $pakList,
            'tahun' => $tahun,
        ]);

        return view('pak/index', $data);
    }

    /**
     * Create PAK form
     */
    public function create()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return redirect()->to('/pak')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Get existing APBDes for this year
        $anggaran = $this->apbdesModel->getAnggaranWithRekening($kodeDesa, $tahun);
        
        // Generate nomor PAK
        $nomorPak = $this->pakModel->generateNomorPak($kodeDesa, (int)$tahun);

        $data = array_merge($this->data, [
            'title' => 'Buat Perubahan Anggaran (PAK)',
            'anggaran' => $anggaran,
            'tahun' => $tahun,
            'nomorPak' => $nomorPak,
        ]);

        return view('pak/form', $data);
    }

    /**
     * Save PAK
     */
    public function save()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return redirect()->to('/pak')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'nomor_pak' => 'required',
            'tanggal_pak' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kodeDesa = $this->session->get('kode_desa');
        $tahun = (int) $this->request->getPost('tahun');

        try {
            // Create PAK header
            $pakData = [
                'kode_desa' => (string) $kodeDesa,
                'tahun' => $tahun,
                'nomor_pak' => (string) $this->request->getPost('nomor_pak'),
                'tanggal_pak' => $this->request->getPost('tanggal_pak'),
                'keterangan' => (string) ($this->request->getPost('keterangan') ?? ''),
                'status' => 'Draft',
                'created_by' => (int) $this->getUserId(),
            ];

            $pakId = $this->pakModel->insert($pakData);

            if (!$pakId) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan PAK');
            }

            // Save PAK details
            $apbdesIds = $this->request->getPost('apbdes_id');
            $anggaranSesudah = $this->request->getPost('anggaran_sesudah');
            $keteranganItems = $this->request->getPost('keterangan_item');

            // Ensure arrays
            if (!is_array($apbdesIds)) $apbdesIds = [];
            if (!is_array($anggaranSesudah)) $anggaranSesudah = [];
            if (!is_array($keteranganItems)) $keteranganItems = [];

            foreach ($apbdesIds as $idx => $apbdesId) {
                if (empty($apbdesId)) continue;
                
                // Ensure apbdesId is integer
                $apbdesId = (int) $apbdesId;
                
                // Get current anggaran
                $apbdes = $this->apbdesModel->find($apbdesId);
                if (!$apbdes) continue;
                
                $sebelum = (float) ($apbdes['anggaran'] ?? 0);
                $sesudah = (float) ($anggaranSesudah[$idx] ?? $sebelum);
                
                // Only save if there's a change
                if (abs($sebelum - $sesudah) > 0.01) {
                    $detailData = [
                        'pak_id' => (int) $pakId,
                        'apbdes_id' => (int) $apbdesId,
                        'anggaran_sebelum' => (float) $sebelum,
                        'anggaran_sesudah' => (float) $sesudah,
                        'selisih' => (float) ($sesudah - $sebelum),
                        'keterangan' => (string) ($keteranganItems[$idx] ?? ''),
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                    
                    // Use direct db insert to avoid model issues
                    $db = \Config\Database::connect();
                    $db->table('pak_detail')->insert($detailData);
                }
            }

            ActivityLogModel::log('create', 'pak', "Buat PAK: " . $pakData['nomor_pak']);

            return redirect()->to('/pak')->with('success', 'PAK berhasil dibuat');

        } catch (\Exception $e) {
            log_message('error', 'PAK Save Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * View PAK detail
     */
    public function detail($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa', 'Kepala Desa'])) {
            return redirect()->to('/pak')->with('error', 'Akses ditolak.');
        }

        $pak = $this->pakModel->getDetailWithItems($id);

        if (!$pak || $pak['kode_desa'] != $this->session->get('kode_desa')) {
            return redirect()->to('/pak')->with('error', 'Data tidak ditemukan');
        }

        $data = array_merge($this->data, [
            'title' => 'Detail PAK',
            'pak' => $pak,
        ]);

        return view('pak/detail', $data);
    }

    /**
     * Approve PAK (Kepala Desa)
     */
    public function approve($id)
    {
        if (!$this->hasRole(['Administrator', 'Kepala Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $pak = $this->pakModel->find($id);

        if (!$pak || $pak['kode_desa'] != $this->session->get('kode_desa')) {
            return $this->respondError('Data tidak ditemukan', 404);
        }

        if ($pak['status'] !== 'Draft') {
            return $this->respondError('PAK sudah diproses');
        }

        // Update status
        $this->pakModel->update($id, [
            'status' => 'Disetujui',
            'approved_by' => $this->getUserId(),
        ]);

        // Apply changes to APBDes
        $this->pakModel->applyPak($id);

        ActivityLogModel::log('approve', 'pak', "Approve PAK: " . $pak['nomor_pak']);

        return $this->respondSuccess(null, 'PAK berhasil disetujui dan diterapkan ke APBDes');
    }

    /**
     * Reject PAK
     */
    public function reject($id)
    {
        if (!$this->hasRole(['Administrator', 'Kepala Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $pak = $this->pakModel->find($id);

        if (!$pak || $pak['kode_desa'] != $this->session->get('kode_desa')) {
            return $this->respondError('Data tidak ditemukan', 404);
        }

        if ($pak['status'] !== 'Draft') {
            return $this->respondError('PAK sudah diproses');
        }

        $this->pakModel->update($id, [
            'status' => 'Ditolak',
            'approved_by' => $this->getUserId(),
        ]);

        ActivityLogModel::log('reject', 'pak', "Tolak PAK: " . $pak['nomor_pak']);

        return $this->respondSuccess(null, 'PAK ditolak');
    }

    /**
     * Delete PAK (Draft only)
     */
    public function delete($id)
    {
        if (!$this->hasRole(['Administrator'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $pak = $this->pakModel->find($id);

        if (!$pak || $pak['kode_desa'] != $this->session->get('kode_desa')) {
            return $this->respondError('Data tidak ditemukan', 404);
        }

        if ($pak['status'] !== 'Draft') {
            return $this->respondError('Hanya PAK berstatus Draft yang dapat dihapus');
        }

        // Delete details first (cascade should handle this, but just to be safe)
        $this->pakDetailModel->where('pak_id', $id)->delete();
        $this->pakModel->delete($id);

        ActivityLogModel::log('delete', 'pak', "Hapus PAK: " . $pak['nomor_pak']);

        return $this->respondSuccess(null, 'PAK berhasil dihapus');
    }
}

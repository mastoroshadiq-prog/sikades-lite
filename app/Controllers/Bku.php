<?php

namespace App\Controllers;

use App\Models\BkuModel;
use App\Models\RefRekeningModel;
use App\Models\SppModel;

class Bku extends BaseController
{
    protected $bkuModel;
    protected $rekeningModel;
    protected $sppModel;

    public function __construct()
    {
        $this->bkuModel = new BkuModel();
        $this->rekeningModel = new RefRekeningModel();
        $this->sppModel = new SppModel();
    }

    /**
     * List BKU with running balance
     */
    public function index()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa', 'Kepala Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? '';

        // Get BKU entries with running balance
        $bkuEntries = $this->bkuModel->getBkuWithBalance($kodeDesa, $tahun, $bulan);

        // Calculate totals
        $totalDebet = $this->bkuModel->getTotalDebet($kodeDesa, $tahun, $bulan);
        $totalKredit = $this->bkuModel->getTotalKredit($kodeDesa, $tahun, $bulan);
        $saldo = $totalDebet - $totalKredit;

        $data = array_merge($this->data, [
            'title' => 'BKU - Buku Kas Umum',
            'bku_entries' => $bkuEntries,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'total_debet' => $totalDebet,
            'total_kredit' => $totalKredit,
            'saldo' => $saldo,
        ]);

        return view('bku/index', $data);
    }

    /**
     * Create BKU Form
     */
    public function create()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return redirect()->to('/bku')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        
        // Get rekening for dropdown
        $rekening = $this->rekeningModel->orderBy('kode_akun', 'ASC')->findAll();
        
        // Get approved SPP for optional linking
        $sppList = $this->sppModel->where('kode_desa', $kodeDesa)
            ->where('status', 'Approved')
            ->orderBy('tanggal_spp', 'DESC')
            ->findAll();

        $data = array_merge($this->data, [
            'title' => 'Tambah Transaksi BKU',
            'rekening' => $rekening,
            'spp_list' => $sppList,
        ]);

        return view('bku/form', $data);
    }

    /**
     * Save new BKU entry
     */
    public function save()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $rules = [
            'tanggal' => 'required|valid_date',
            'no_bukti' => 'required',
            'uraian' => 'required',
            'ref_rekening_id' => 'required|integer',
            'jenis_transaksi' => 'required|in_list[Pendapatan,Belanja,Mutasi]',
        ];

        // Validate either debet or kredit is filled
        $debet = $this->request->getPost('debet') ?? 0;
        $kredit = $this->request->getPost('kredit') ?? 0;

        if ($debet == 0 && $kredit == 0) {
            return redirect()->back()->withInput()->with('error', 'Minimal isi Debet atau Kredit');
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kode_desa' => $this->session->get('kode_desa'),
            'tanggal' => $this->request->getPost('tanggal'),
            'no_bukti' => $this->request->getPost('no_bukti'),
            'uraian' => $this->request->getPost('uraian'),
            'ref_rekening_id' => $this->request->getPost('ref_rekening_id'),
            'jenis_transaksi' => $this->request->getPost('jenis_transaksi'),
            'debet' => $debet,
            'kredit' => $kredit,
            'spp_id' => $this->request->getPost('spp_id') ?: null,
        ];

        // Calculate running balance
        $previousBalance = $this->bkuModel->getLastBalance($this->session->get('kode_desa'), $data['tanggal']);
        $data['saldo_kumulatif'] = $previousBalance + $debet - $kredit;

        $this->bkuModel->insert($data);
        $newBkuId = $this->bkuModel->getInsertID();

        // ===============================================
        // SIPADES INTEGRATION: Detect Belanja Modal (5.3.x)
        // ===============================================
        $refRekeningId = $this->request->getPost('ref_rekening_id');
        $isBelanjModal = $this->checkIsBelanjModal($refRekeningId);

        if ($isBelanjModal) {
            // Redirect to BKU list with asset creation prompt
            return redirect()->to('/bku')
                ->with('success', 'Transaksi BKU berhasil ditambahkan')
                ->with('show_asset_prompt', true)
                ->with('bku_id', $newBkuId)
                ->with('bku_uraian', $data['uraian'])
                ->with('bku_nilai', max($debet, $kredit));
        }

        // ===============================================
        // BUMDES INTEGRATION: Detect Penyertaan Modal (6.2.x)
        // ===============================================
        $isPenyertaanModal = $this->checkIsPenyertaanModal($refRekeningId);

        if ($isPenyertaanModal) {
            // Redirect to BKU list with BUMDes journal creation prompt
            return redirect()->to('/bku')
                ->with('success', 'Transaksi BKU berhasil ditambahkan')
                ->with('show_bumdes_prompt', true)
                ->with('bku_id', $newBkuId)
                ->with('bku_uraian', $data['uraian'])
                ->with('bku_nilai', max($debet, $kredit));
        }

        return redirect()->to('/bku')->with('success', 'Transaksi BKU berhasil ditambahkan');
    }

    /**
     * Check if rekening code starts with 5.3 (Belanja Modal)
     */
    private function checkIsBelanjModal($refRekeningId): bool
    {
        $rekening = $this->rekeningModel->find($refRekeningId);
        if ($rekening && isset($rekening['kode_akun'])) {
            return strpos($rekening['kode_akun'], '5.3') === 0;
        }
        return false;
    }

    /**
     * Check if rekening code is 6.2.1 (Penyertaan Modal Desa ke BUMDes)
     */
    private function checkIsPenyertaanModal($refRekeningId): bool
    {
        $rekening = $this->rekeningModel->find($refRekeningId);
        if ($rekening && isset($rekening['kode_akun'])) {
            return strpos($rekening['kode_akun'], '6.2.1') === 0 || strpos($rekening['kode_akun'], '6.2') === 0;
        }
        return false;
    }

    /**
     * Edit BKU Form
     */
    public function edit($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return redirect()->to('/bku')->with('error', 'Akses ditolak.');
        }

        $bku = $this->bkuModel->find($id);

        if (!$bku || $bku['kode_desa'] != $this->session->get('kode_desa')) {
            return redirect()->to('/bku')->with('error', 'Data tidak ditemukan');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $rekening = $this->rekeningModel->orderBy('kode_akun', 'ASC')->findAll();
        $sppList = $this->sppModel->where('kode_desa', $kodeDesa)
            ->where('status', 'Approved')
            ->orderBy('tanggal_spp', 'DESC')
            ->findAll();

        $data = array_merge($this->data, [
            'title' => 'Edit Transaksi BKU',
            'bku' => $bku,
            'rekening' => $rekening,
            'spp_list' => $sppList,
        ]);

        return view('bku/form', $data);
    }

    /**
     * Update BKU entry
     */
    public function update($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $bku = $this->bkuModel->find($id);

        if (!$bku || $bku['kode_desa'] != $this->session->get('kode_desa')) {
            return redirect()->to('/bku')->with('error', 'Data tidak ditemukan');
        }

        $rules = [
            'tanggal' => 'required|valid_date',
            'no_bukti' => 'required',
            'uraian' => 'required',
            'ref_rekening_id' => 'required|integer',
            'jenis_transaksi' => 'required|in_list[Pendapatan,Belanja,Mutasi]',
        ];

        $debet = $this->request->getPost('debet') ?? 0;
        $kredit = $this->request->getPost('kredit') ?? 0;

        if ($debet == 0 && $kredit == 0) {
            return redirect()->back()->withInput()->with('error', 'Minimal isi Debet atau Kredit');
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'tanggal' => $this->request->getPost('tanggal'),
            'no_bukti' => $this->request->getPost('no_bukti'),
            'uraian' => $this->request->getPost('uraian'),
            'ref_rekening_id' => $this->request->getPost('ref_rekening_id'),
            'jenis_transaksi' => $this->request->getPost('jenis_transaksi'),
            'debet' => $debet,
            'kredit' => $kredit,
            'spp_id' => $this->request->getPost('spp_id') ?: null,
        ];

        $this->bkuModel->update($id, $data);

        // Recalculate all balances after this date
        $this->bkuModel->recalculateBalances($this->session->get('kode_desa'), $data['tanggal']);

        return redirect()->to('/bku')->with('success', 'Transaksi BKU berhasil diperbarui');
    }

    /**
     * Delete BKU entry
     */
    public function delete($id)
    {
        if (!$this->hasRole(['Administrator'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $bku = $this->bkuModel->find($id);

        if (!$bku || $bku['kode_desa'] != $this->session->get('kode_desa')) {
            return $this->respondError('Data tidak ditemukan');
        }

        $this->bkuModel->delete($id);

        // Recalculate balances
        $this->bkuModel->recalculateBalances($this->session->get('kode_desa'), $bku['tanggal']);

        return $this->respondSuccess(null, 'Transaksi BKU berhasil dihapus');
    }

    /**
     * Monthly Report
     */
    public function report()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa', 'Kepala Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');

        $bkuEntries = $this->bkuModel->getBkuWithBalance($kodeDesa, $tahun, $bulan);

        $data = array_merge($this->data, [
            'title' => 'Laporan BKU Bulanan',
            'bku_entries' => $bkuEntries,
            'tahun' => $tahun,
            'bulan' => $bulan,
        ]);

        return view('bku/report', $data);
    }
}

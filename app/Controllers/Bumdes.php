<?php

namespace App\Controllers;

use App\Models\BumdesUnitModel;
use App\Models\BumdesJurnalModel;

class Bumdes extends BaseController
{
    protected $unitModel;
    protected $jurnalModel;
    protected $user;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->unitModel   = new BumdesUnitModel();
        $this->jurnalModel = new BumdesJurnalModel();
        $this->user        = session()->get();
    }

    // ========================================
    // DASHBOARD
    // ========================================

    public function index()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $units = $this->unitModel->getWithSummary($kodeDesa);
        
        // Overall stats
        $totalUnit = count($units);
        $totalModal = array_sum(array_column($units, 'modal_awal'));
        $unitAktif = count(array_filter($units, fn($u) => $u['status'] === 'AKTIF'));

        $data = [
            'title'      => 'BUMDes Dashboard - Siskeudes Lite',
            'user'       => $this->user,
            'units'      => $units,
            'totalUnit'  => $totalUnit,
            'totalModal' => $totalModal,
            'unitAktif'  => $unitAktif,
        ];

        return view('bumdes/index', $data);
    }

    // ========================================
    // UNIT USAHA CRUD
    // ========================================

    public function units()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $units = $this->unitModel->getWithSummary($kodeDesa);

        $data = [
            'title' => 'Unit Usaha BUMDes - Siskeudes Lite',
            'user'  => $this->user,
            'units' => $units,
        ];

        return view('bumdes/unit/index', $data);
    }

    public function createUnit()
    {
        $data = [
            'title' => 'Tambah Unit Usaha - Siskeudes Lite',
            'user'  => $this->user,
        ];

        return view('bumdes/unit/form', $data);
    }

    public function saveUnit()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;

        $rules = [
            'nama_unit' => 'required|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kode_desa'        => $kodeDesa,
            'nama_unit'        => $this->request->getPost('nama_unit'),
            'jenis_usaha'      => $this->request->getPost('jenis_usaha'),
            'penanggung_jawab' => $this->request->getPost('penanggung_jawab'),
            'modal_awal'       => str_replace(['.', ','], ['', '.'], $this->request->getPost('modal_awal') ?: 0),
            'tanggal_mulai'    => $this->request->getPost('tanggal_mulai'),
            'status'           => $this->request->getPost('status') ?: 'AKTIF',
            'alamat'           => $this->request->getPost('alamat'),
            'no_telp'          => $this->request->getPost('no_telp'),
        ];

        $this->unitModel->insert($data);

        return redirect()->to('/bumdes/unit')->with('success', 'Unit usaha berhasil ditambahkan');
    }

    public function editUnit($id)
    {
        $unit = $this->unitModel->find($id);
        if (!$unit) {
            return redirect()->to('/bumdes/unit')->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Unit Usaha - Siskeudes Lite',
            'user'  => $this->user,
            'unit'  => $unit,
        ];

        return view('bumdes/unit/form', $data);
    }

    public function updateUnit($id)
    {
        $unit = $this->unitModel->find($id);
        if (!$unit) {
            return redirect()->to('/bumdes/unit')->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'nama_unit'        => $this->request->getPost('nama_unit'),
            'jenis_usaha'      => $this->request->getPost('jenis_usaha'),
            'penanggung_jawab' => $this->request->getPost('penanggung_jawab'),
            'modal_awal'       => str_replace(['.', ','], ['', '.'], $this->request->getPost('modal_awal') ?: 0),
            'tanggal_mulai'    => $this->request->getPost('tanggal_mulai'),
            'status'           => $this->request->getPost('status'),
            'alamat'           => $this->request->getPost('alamat'),
            'no_telp'          => $this->request->getPost('no_telp'),
        ];

        $this->unitModel->update($id, $data);

        return redirect()->to('/bumdes/unit')->with('success', 'Unit usaha berhasil diupdate');
    }

    public function detailUnit($id)
    {
        $unit = $this->unitModel->find($id);
        if (!$unit) {
            return redirect()->to('/bumdes/unit')->with('error', 'Data tidak ditemukan');
        }

        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $jurnalList = $this->jurnalModel->getByUnit($id, ['tahun' => $tahun]);
        $monthlySummary = $this->unitModel->getMonthlySummary($id, $tahun);

        $data = [
            'title'          => 'Detail ' . $unit['nama_unit'] . ' - Siskeudes Lite',
            'user'           => $this->user,
            'unit'           => $unit,
            'jurnalList'     => $jurnalList,
            'monthlySummary' => $monthlySummary,
            'tahun'          => $tahun,
        ];

        return view('bumdes/unit/detail', $data);
    }

    // ========================================
    // JURNAL (DOUBLE ENTRY)
    // ========================================

    public function jurnal($unitId)
    {
        $unit = $this->unitModel->find($unitId);
        if (!$unit) {
            return redirect()->to('/bumdes')->with('error', 'Unit tidak ditemukan');
        }

        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bulan = $this->request->getGet('bulan');
        
        $jurnalList = $this->jurnalModel->getByUnit($unitId, [
            'tahun' => $tahun,
            'bulan' => $bulan,
        ]);

        $data = [
            'title'      => 'Jurnal Umum - ' . $unit['nama_unit'],
            'user'       => $this->user,
            'unit'       => $unit,
            'jurnalList' => $jurnalList,
            'tahun'      => $tahun,
            'bulan'      => $bulan,
        ];

        return view('bumdes/jurnal/index', $data);
    }

    public function createJurnal($unitId)
    {
        $unit = $this->unitModel->find($unitId);
        if (!$unit) {
            return redirect()->to('/bumdes')->with('error', 'Unit tidak ditemukan');
        }

        $db = \Config\Database::connect();
        $akunList = $db->table('bumdes_akun')
            ->where('is_header', 0)
            ->orderBy('urutan')
            ->get()
            ->getResultArray();

        $noBukti = $this->jurnalModel->generateNoBukti($unitId);

        $data = [
            'title'    => 'Tambah Jurnal - ' . $unit['nama_unit'],
            'user'     => $this->user,
            'unit'     => $unit,
            'akunList' => $akunList,
            'noBukti'  => $noBukti,
        ];

        return view('bumdes/jurnal/form', $data);
    }

    public function saveJurnal($unitId)
    {
        $unit = $this->unitModel->find($unitId);
        if (!$unit) {
            return redirect()->to('/bumdes')->with('error', 'Unit tidak ditemukan');
        }

        $details = $this->request->getPost('details');
        
        // Validate balance
        $totalDebet = 0;
        $totalKredit = 0;
        foreach ($details as $d) {
            $totalDebet += floatval(str_replace(['.', ','], ['', '.'], $d['debet'] ?? 0));
            $totalKredit += floatval(str_replace(['.', ','], ['', '.'], $d['kredit'] ?? 0));
        }

        if (abs($totalDebet - $totalKredit) > 0.01) {
            return redirect()->back()->withInput()->with('error', 'Debet dan Kredit harus balance! Selisih: ' . number_format(abs($totalDebet - $totalKredit)));
        }

        $jurnalData = [
            'unit_id'    => $unitId,
            'no_bukti'   => $this->request->getPost('no_bukti'),
            'tanggal'    => $this->request->getPost('tanggal'),
            'deskripsi'  => $this->request->getPost('deskripsi'),
            'created_by' => $this->user['id'] ?? null,
        ];

        $cleanDetails = [];
        foreach ($details as $d) {
            if (!empty($d['akun_id'])) {
                $cleanDetails[] = [
                    'akun_id'    => $d['akun_id'],
                    'debet'      => floatval(str_replace(['.', ','], ['', '.'], $d['debet'] ?? 0)),
                    'kredit'     => floatval(str_replace(['.', ','], ['', '.'], $d['kredit'] ?? 0)),
                    'keterangan' => $d['keterangan'] ?? null,
                ];
            }
        }

        $jurnalId = $this->jurnalModel->createWithDetails($jurnalData, $cleanDetails);

        return redirect()->to("/bumdes/jurnal/{$unitId}")
            ->with('success', 'Jurnal berhasil disimpan');
    }

    public function detailJurnal($unitId, $jurnalId)
    {
        $unit = $this->unitModel->find($unitId);
        $jurnal = $this->jurnalModel->getWithDetails($jurnalId);
        
        if (!$unit || !$jurnal) {
            return redirect()->to('/bumdes')->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'title'  => 'Detail Jurnal - ' . $jurnal['no_bukti'],
            'user'   => $this->user,
            'unit'   => $unit,
            'jurnal' => $jurnal,
        ];

        return view('bumdes/jurnal/detail', $data);
    }

    // ========================================
    // LAPORAN KEUANGAN
    // ========================================

    public function laporanLabaRugi($unitId)
    {
        $unit = $this->unitModel->find($unitId);
        if (!$unit) {
            return redirect()->to('/bumdes')->with('error', 'Unit tidak ditemukan');
        }

        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $labaRugi = $this->jurnalModel->getProfitLoss($unitId, $tahun);

        $data = [
            'title'    => 'Laporan Laba Rugi - ' . $unit['nama_unit'],
            'user'     => $this->user,
            'unit'     => $unit,
            'labaRugi' => $labaRugi,
            'tahun'    => $tahun,
        ];

        return view('bumdes/laporan/laba_rugi', $data);
    }

    public function laporanNeraca($unitId)
    {
        $unit = $this->unitModel->find($unitId);
        if (!$unit) {
            return redirect()->to('/bumdes')->with('error', 'Unit tidak ditemukan');
        }

        $tanggal = $this->request->getGet('tanggal') ?: date('Y-m-d');
        $neraca = $this->jurnalModel->getBalanceSheet($unitId, $tanggal);

        $data = [
            'title'   => 'Laporan Neraca - ' . $unit['nama_unit'],
            'user'    => $this->user,
            'unit'    => $unit,
            'neraca'  => $neraca,
            'tanggal' => $tanggal,
        ];

        return view('bumdes/laporan/neraca', $data);
    }

    public function laporanNeracaSaldo($unitId)
    {
        $unit = $this->unitModel->find($unitId);
        if (!$unit) {
            return redirect()->to('/bumdes')->with('error', 'Unit tidak ditemukan');
        }

        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $startDate = "{$tahun}-01-01";
        $endDate = "{$tahun}-12-31";
        
        $trialBalance = $this->jurnalModel->getTrialBalance($unitId, $startDate, $endDate);

        $data = [
            'title'        => 'Neraca Saldo - ' . $unit['nama_unit'],
            'user'         => $this->user,
            'unit'         => $unit,
            'trialBalance' => $trialBalance,
            'tahun'        => $tahun,
        ];

        return view('bumdes/laporan/neraca_saldo', $data);
    }

    // ========================================
    // CHART OF ACCOUNTS
    // ========================================

    public function akun()
    {
        $db = \Config\Database::connect();
        $akunList = $db->table('bumdes_akun')
            ->orderBy('urutan')
            ->get()
            ->getResultArray();

        $data = [
            'title'    => 'Chart of Accounts - BUMDes',
            'user'     => $this->user,
            'akunList' => $akunList,
        ];

        return view('bumdes/akun/index', $data);
    }
}

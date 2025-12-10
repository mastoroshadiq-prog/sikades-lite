<?php

namespace App\Controllers;

use App\Models\TutupBukuModel;
use App\Models\BkuModel;
use App\Models\ActivityLogModel;

class TutupBuku extends BaseController
{
    protected $tutupBukuModel;
    protected $bkuModel;

    public function __construct()
    {
        $this->tutupBukuModel = new TutupBukuModel();
        $this->bkuModel = new BkuModel();
    }

    /**
     * Dashboard Tutup Buku
     */
    public function index()
    {
        $kodeDesa = session()->get('kode_desa');
        $currentYear = (int)date('Y');
        
        // Get all years with status
        $years = $this->tutupBukuModel->getAvailableYears($kodeDesa);
        
        // If no years, add current year
        if (empty($years)) {
            $years = [
                [
                    'tahun' => $currentYear,
                    'status' => 'Open',
                    'record' => null
                ]
            ];
        }
        
        // Get current year summary
        $currentSummary = $this->tutupBukuModel->calculateYearSummary($kodeDesa, $currentYear);
        
        $data = [
            'title' => 'Tutup Buku Akhir Tahun',
            'years' => $years,
            'currentYear' => $currentYear,
            'currentSummary' => $currentSummary,
        ];

        return view('tutup_buku/index', $data);
    }

    /**
     * Preview tutup buku untuk tahun tertentu
     */
    public function preview($tahun = null)
    {
        $kodeDesa = session()->get('kode_desa');
        $tahun = $tahun ?? date('Y');
        
        // Check if already closed
        if ($this->tutupBukuModel->isClosed($kodeDesa, $tahun)) {
            return redirect()->to('/tutup-buku')->with('error', "Tahun {$tahun} sudah ditutup");
        }
        
        // Get summary
        $summary = $this->tutupBukuModel->calculateYearSummary($kodeDesa, $tahun);
        
        // Get previous year status
        $prevYear = $this->tutupBukuModel->getByTahun($kodeDesa, $tahun - 1);
        $prevYearClosed = $prevYear && $prevYear['status'] === 'Closed';
        
        // Get transaction count
        $db = \Config\Database::connect();
        $txCount = $db->table('bku')
            ->where('kode_desa', $kodeDesa)
            ->where('EXTRACT(YEAR FROM tanggal)::int', $tahun)
            ->countAllResults();
        
        $sppCount = $db->table('spp')
            ->where('kode_desa', $kodeDesa)
            ->where('EXTRACT(YEAR FROM tanggal_spp)::int', $tahun)
            ->countAllResults();
        
        // Get pending SPP (not approved)
        $pendingSpp = $db->table('spp')
            ->where('kode_desa', $kodeDesa)
            ->where('EXTRACT(YEAR FROM tanggal_spp)::int', $tahun)
            ->where('status !=', 'Approved')
            ->countAllResults();
        
        // Check warnings
        $warnings = [];
        if (!$prevYearClosed && $tahun > 2020) { // arbitrary start year check
            $warnings[] = "Tahun " . ($tahun - 1) . " belum ditutup. Disarankan untuk menutup tahun sebelumnya terlebih dahulu.";
        }
        if ($pendingSpp > 0) {
            $warnings[] = "Masih ada {$pendingSpp} SPP yang belum di-approve untuk tahun ini.";
        }
        if ($tahun == date('Y') && date('m') < 12) {
            $warnings[] = "Tahun berjalan belum berakhir. Tutup buku umumnya dilakukan di akhir tahun (Desember).";
        }
        
        $data = [
            'title' => 'Preview Tutup Buku Tahun ' . $tahun,
            'tahun' => $tahun,
            'summary' => $summary,
            'txCount' => $txCount,
            'sppCount' => $sppCount,
            'pendingSpp' => $pendingSpp,
            'prevYearClosed' => $prevYearClosed,
            'warnings' => $warnings,
        ];

        return view('tutup_buku/preview', $data);
    }

    /**
     * Proses tutup buku
     */
    public function process()
    {
        // Only admin can close books
        if (session()->get('role') !== 'Administrator') {
            return redirect()->to('/tutup-buku')->with('error', 'Hanya Administrator yang dapat melakukan tutup buku');
        }
        
        $kodeDesa = session()->get('kode_desa');
        $tahun = (int)$this->request->getPost('tahun');
        $catatan = $this->request->getPost('catatan');
        $userId = session()->get('user_id');
        
        // Validate
        if (!$tahun) {
            return redirect()->to('/tutup-buku')->with('error', 'Tahun tidak valid');
        }
        
        // Check if already closed
        if ($this->tutupBukuModel->isClosed($kodeDesa, $tahun)) {
            return redirect()->to('/tutup-buku')->with('error', "Tahun {$tahun} sudah ditutup");
        }
        
        // Process closing
        $result = $this->tutupBukuModel->closeEXTRACT(YEAR FROM $kodeDesa, $tahun, $userId, $catatan)::int;
        
        if ($result) {
            // Log activity
            ActivityLogModel::log('close_year', 'tutup_buku', "Tutup buku tahun {$tahun} berhasil");
            
            return redirect()->to('/tutup-buku')->with('success', "Tutup buku tahun {$tahun} berhasil dilakukan");
        } else {
            return redirect()->to('/tutup-buku')->with('error', 'Gagal melakukan tutup buku. Silakan coba lagi.');
        }
    }

    /**
     * Detail tutup buku untuk tahun tertentu
     */
    public function detail($tahun)
    {
        $kodeDesa = session()->get('kode_desa');
        
        $record = $this->tutupBukuModel->getByTahun($kodeDesa, $tahun);
        
        if (!$record) {
            return redirect()->to('/tutup-buku')->with('error', 'Data tidak ditemukan');
        }
        
        // Get user who closed
        $closedByUser = null;
        if ($record['closed_by']) {
            $userModel = new \App\Models\UserModel();
            $closedByUser = $userModel->find($record['closed_by']);
        }
        
        // Get transaction summary per month
        $db = \Config\Database::connect();
        $monthlyData = $db->query("
            SELECT 
                EXTRACT(MONTH FROM tanggal)::int as bulan,
                SUM(debet) as total_debet,
                SUM(kredit) as total_kredit,
                COUNT(*) as jumlah_transaksi
            FROM bku 
            WHERE kode_desa = ? AND EXTRACT(YEAR FROM tanggal)::int = ?
            GROUP BY EXTRACT(MONTH FROM tanggal)::int
            ORDER BY bulan
        ", [$kodeDesa, $tahun])->getResultArray();
        
        $data = [
            'title' => 'Detail Tutup Buku Tahun ' . $tahun,
            'tahun' => $tahun,
            'record' => $record,
            'closedByUser' => $closedByUser,
            'monthlyData' => $monthlyData,
        ];

        return view('tutup_buku/detail', $data);
    }

    /**
     * Reopen closed year (admin only, with confirmation)
     */
    public function reopen()
    {
        // Only admin
        if (session()->get('role') !== 'Administrator') {
            return redirect()->to('/tutup-buku')->with('error', 'Akses ditolak');
        }
        
        $kodeDesa = session()->get('kode_desa');
        $tahun = (int)$this->request->getPost('tahun');
        
        if (!$tahun) {
            return redirect()->to('/tutup-buku')->with('error', 'Tahun tidak valid');
        }
        
        // Check if year is closed
        if (!$this->tutupBukuModel->isClosed($kodeDesa, $tahun)) {
            return redirect()->to('/tutup-buku')->with('error', "Tahun {$tahun} tidak dalam status Closed");
        }
        
        // Reopen
        $result = $this->tutupBukuModel->reopenEXTRACT(YEAR FROM $kodeDesa, $tahun)::int;
        
        if ($result) {
            ActivityLogModel::log('reopen_year', 'tutup_buku', "Membuka kembali tahun {$tahun}");
            return redirect()->to('/tutup-buku')->with('success', "Tahun {$tahun} berhasil dibuka kembali");
        } else {
            return redirect()->to('/tutup-buku')->with('error', 'Gagal membuka kembali tahun');
        }
    }

    /**
     * Get summary data (AJAX)
     */
    public function getSummary($tahun)
    {
        $kodeDesa = session()->get('kode_desa');
        
        $summary = $this->tutupBukuModel->calculateYearSummary($kodeDesa, $tahun);
        $record = $this->tutupBukuModel->getByTahun($kodeDesa, $tahun);
        
        return $this->response->setJSON([
            'success' => true,
            'summary' => $summary,
            'status' => $record ? $record['status'] : 'Open'
        ]);
    }
}

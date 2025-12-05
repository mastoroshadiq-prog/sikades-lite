<?php

namespace App\Controllers;

use App\Models\ApbdesModel;
use App\Models\BkuModel;
use App\Models\SppModel;

class Dashboard extends BaseController
{
    protected $apbdesModel;
    protected $bkuModel;
    protected $sppModel;

    public function __construct()
    {
        $this->apbdesModel = new ApbdesModel();
        $this->bkuModel = new BkuModel();
        $this->sppModel = new SppModel();
    }

    public function index()
    {
        $kodeDesa = $this->session->get('kode_desa');
        $role = $this->getUserRole();

        // Get dashboard statistics
        $stats = $this->getDashboardStats($kodeDesa);

        $data = array_merge($this->data, [
            'title' => 'Dashboard - Siskeudes Lite',
            'stats' => $stats,
            'role' => $role,
        ]);

        return view('dashboard/index', $data);
    }

    /**
     * Get dashboard statistics
     *
     * @param string|null $kodeDesa
     * @return array
     */
    private function getDashboardStats(?string $kodeDesa): array
    {
        $stats = [
            'total_anggaran' => 0,
            'total_realisasi' => 0,
            'total_pendapatan' => 0,
            'total_belanja' => 0,
            'saldo_kas' => 0,
            'spp_pending' => 0,
        ];

        if (!$kodeDesa) {
            return $stats;
        }

        // Total Anggaran (from APBDes)
        $totalAnggaran = $this->apbdesModel
            ->where('kode_desa', $kodeDesa)
            ->selectSum('anggaran')
            ->first();
        $stats['total_anggaran'] = $totalAnggaran['anggaran'] ?? 0;

        // Total Realisasi (from BKU - Kredit/Belanja)
        $totalRealisasi = $this->bkuModel
            ->where('kode_desa', $kodeDesa)
            ->where('jenis_transaksi', 'Belanja')
            ->selectSum('kredit')
            ->first();
        $stats['total_realisasi'] = $totalRealisasi['kredit'] ?? 0;

        // Total Pendapatan (from BKU - Debet)
        $totalPendapatan = $this->bkuModel
            ->where('kode_desa', $kodeDesa)
            ->selectSum('debet')
            ->first();
        $stats['total_pendapatan'] = $totalPendapatan['debet'] ?? 0;

        // Total Belanja (from BKU - Kredit)
        $totalBelanja = $this->bkuModel
            ->where('kode_desa', $kodeDesa)
            ->selectSum('kredit')
            ->first();
        $stats['total_belanja'] = $totalBelanja['kredit'] ?? 0;

        // Saldo Kas (Pendapatan - Belanja)
        $stats['saldo_kas'] = $stats['total_pendapatan'] - $stats['total_belanja'];

        // SPP Pending
        $sppPending = $this->sppModel
            ->where('kode_desa', $kodeDesa)
            ->whereIn('status', ['Draft', 'Verified'])
            ->countAllResults();
        $stats['spp_pending'] = $sppPending;

        return $stats;
    }
}

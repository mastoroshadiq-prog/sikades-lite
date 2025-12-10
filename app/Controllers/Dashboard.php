<?php

namespace App\Controllers;

use App\Models\ApbdesModel;
use App\Models\BkuModel;
use App\Models\SppModel;
use App\Models\RkpdesaModel;
use App\Models\KegiatanModel;

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
        $tahun = date('Y');

        // Get dashboard statistics
        $stats = $this->getDashboardStats($kodeDesa);
        
        // Get monthly chart data
        $monthlyData = $this->getMonthlyChartData($kodeDesa, $tahun);
        
        // Get recent transactions
        $recentTransactions = $this->bkuModel
            ->where('kode_desa', $kodeDesa)
            ->orderBy('tanggal', 'DESC')
            ->limit(5)
            ->findAll();
        
        // Get pending SPP
        $pendingSpp = $this->sppModel
            ->where('kode_desa', $kodeDesa)
            ->whereIn('status', ['Draft', 'Verified'])
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();
        
        // Get budget progress per sumber dana
        $budgetProgress = $this->getBudgetProgress($kodeDesa, $tahun);

        $data = array_merge($this->data, [
            'title' => 'Dashboard - Siskeudes Lite',
            'stats' => $stats,
            'role' => $role,
            'tahun' => $tahun,
            'monthlyData' => $monthlyData,
            'recentTransactions' => $recentTransactions,
            'pendingSpp' => $pendingSpp,
            'budgetProgress' => $budgetProgress,
        ]);

        return view('dashboard/index', $data);
    }

    /**
     * Get dashboard statistics
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
            'persentase_realisasi' => 0,
        ];

        if (!$kodeDesa) {
            return $stats;
        }

        $tahun = date('Y');

        // Total Anggaran (from APBDes)
        $totalAnggaran = $this->apbdesModel
            ->where('kode_desa', $kodeDesa)
            ->where('tahun', $tahun)
            ->selectSum('anggaran')
            ->first();
        $stats['total_anggaran'] = $totalAnggaran['anggaran'] ?? 0;

        // Total Realisasi (from BKU - Kredit/Belanja)
        $totalRealisasi = $this->bkuModel
            ->where('kode_desa', $kodeDesa)
            ->where('EXTRACT(YEAR FROM tanggal)::int', $tahun)
            ->where('jenis_transaksi', 'Belanja')
            ->selectSum('kredit')
            ->first();
        $stats['total_realisasi'] = $totalRealisasi['kredit'] ?? 0;

        // Total Pendapatan (from BKU - Debet)
        $totalPendapatan = $this->bkuModel
            ->where('kode_desa', $kodeDesa)
            ->where('EXTRACT(YEAR FROM tanggal)::int', $tahun)
            ->selectSum('debet')
            ->first();
        $stats['total_pendapatan'] = $totalPendapatan['debet'] ?? 0;

        // Total Belanja (from BKU - Kredit)
        $totalBelanja = $this->bkuModel
            ->where('kode_desa', $kodeDesa)
            ->where('EXTRACT(YEAR FROM tanggal)::int', $tahun)
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
        
        // Persentase Realisasi
        $stats['persentase_realisasi'] = $stats['total_anggaran'] > 0 
            ? round(($stats['total_realisasi'] / $stats['total_anggaran']) * 100, 2) 
            : 0;

        return $stats;
    }

    /**
     * Get monthly chart data for current year
     */
    private function getMonthlyChartData(?string $kodeDesa, int $tahun): array
    {
        if (!$kodeDesa) {
            return ['labels' => [], 'pendapatan' => [], 'belanja' => []];
        }

        $db = \Config\Database::connect();
        
        $result = $db->query("
            SELECT 
                EXTRACT(MONTH FROM tanggal)::int as bulan,
                SUM(debet) as pendapatan,
                SUM(kredit) as belanja
            FROM bku 
            WHERE kode_desa = ? AND EXTRACT(YEAR FROM tanggal)::int = ?
            GROUP BY EXTRACT(MONTH FROM tanggal)
            ORDER BY bulan
        ", [$kodeDesa, $tahun])->getResultArray();
        
        $bulanNama = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        
        $labels = [];
        $pendapatan = [];
        $belanja = [];
        
        // Initialize all months with 0
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = $bulanNama[$i];
            $pendapatan[$i] = 0;
            $belanja[$i] = 0;
        }
        
        // Fill actual data
        foreach ($result as $row) {
            $pendapatan[(int)$row['bulan']] = (float)$row['pendapatan'];
            $belanja[(int)$row['bulan']] = (float)$row['belanja'];
        }
        
        return [
            'labels' => $labels,
            'pendapatan' => array_values($pendapatan),
            'belanja' => array_values($belanja)
        ];
    }

    /**
     * Get budget progress per sumber dana
     */
    private function getBudgetProgress(?string $kodeDesa, int $tahun): array
    {
        if (!$kodeDesa) {
            return [];
        }

        $db = \Config\Database::connect();
        
        $result = $db->query("
            SELECT 
                sumber_dana,
                SUM(anggaran) as total_anggaran
            FROM apbdes 
            WHERE kode_desa = ? AND tahun = ?
            GROUP BY sumber_dana
        ", [$kodeDesa, $tahun])->getResultArray();
        
        $progress = [];
        foreach ($result as $row) {
            $progress[] = [
                'sumber_dana' => $row['sumber_dana'],
                'anggaran' => (float)$row['total_anggaran'],
            ];
        }
        
        return $progress;
    }
    
    /**
     * Get chart data as JSON (for AJAX)
     */
    public function chartData()
    {
        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $monthlyData = $this->getMonthlyChartData($kodeDesa, (int)$tahun);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $monthlyData
        ]);
    }
}


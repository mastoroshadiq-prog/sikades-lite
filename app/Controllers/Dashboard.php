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
        
        // Get selected year from request or default to current year
        $tahun = (int)($this->request->getGet('tahun') ?? date('Y'));
        
        // Get available years from APBDes and BKU for dropdown
        $availableYears = $this->getAvailableYears($kodeDesa);
        if (!in_array($tahun, $availableYears) && !empty($availableYears)) {
            // If selected year not in list, add it
            $availableYears[] = $tahun;
            sort($availableYears);
        }
        if (empty($availableYears)) {
            $availableYears = [date('Y')];
        }

        // Get dashboard statistics for selected year
        $stats = $this->getDashboardStats($kodeDesa, $tahun);
        
        // Get monthly chart data
        $monthlyData = $this->getMonthlyChartData($kodeDesa, $tahun);
        
        // Get recent transactions (for selected year)
        $recentTransactions = $this->bkuModel
            ->where('kode_desa', $kodeDesa)
            ->where('EXTRACT(YEAR FROM tanggal)::int', $tahun)
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
            'availableYears' => $availableYears,
            'monthlyData' => $monthlyData,
            'recentTransactions' => $recentTransactions,
            'pendingSpp' => $pendingSpp,
            'budgetProgress' => $budgetProgress,
        ]);

        return view('dashboard/index', $data);
    }
    
    /**
     * Get available years from APBDes and BKU
     */
    private function getAvailableYears(?string $kodeDesa): array
    {
        if (!$kodeDesa) {
            return [date('Y')];
        }
        
        $db = \Config\Database::connect();
        
        // Get years from APBDes
        $apbdesYears = $db->query("
            SELECT DISTINCT tahun FROM apbdes 
            WHERE kode_desa = ? 
            ORDER BY tahun DESC
        ", [$kodeDesa])->getResultArray();
        
        // Get years from BKU
        $bkuYears = $db->query("
            SELECT DISTINCT EXTRACT(YEAR FROM tanggal)::int as tahun 
            FROM bku 
            WHERE kode_desa = ? 
            ORDER BY tahun DESC
        ", [$kodeDesa])->getResultArray();
        
        $years = array_merge(
            array_column($apbdesYears, 'tahun'),
            array_column($bkuYears, 'tahun')
        );
        
        $years = array_unique($years);
        rsort($years); // Descending order
        
        return array_values($years);
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats(?string $kodeDesa, int $tahun = null): array
    {
        if ($tahun === null) {
            $tahun = (int)date('Y');
        }
        
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
    
    /**
     * Get drilldown data for Total Anggaran
     */
    public function drilldownAnggaran()
    {
        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        if (!$kodeDesa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kode desa tidak ditemukan']);
        }
        
        $db = \Config\Database::connect();
        
        // Get detailed anggaran breakdown by rekening
        $result = $db->query("
            SELECT 
                a.id,
                a.uraian,
                a.anggaran,
                a.sumber_dana,
                r.kode_akun,
                r.nama_akun,
                r.level
            FROM apbdes a
            LEFT JOIN ref_rekening r ON r.id = a.ref_rekening_id
            WHERE a.kode_desa = ? AND a.tahun = ?
            ORDER BY r.kode_akun ASC
        ", [$kodeDesa, $tahun])->getResultArray();
        
        // Calculate totals by sumber dana
        $totalBySumber = [];
        $grandTotal = 0;
        foreach ($result as $row) {
            $sumber = $row['sumber_dana'] ?? 'Lainnya';
            if (!isset($totalBySumber[$sumber])) {
                $totalBySumber[$sumber] = 0;
            }
            $totalBySumber[$sumber] += (float)$row['anggaran'];
            $grandTotal += (float)$row['anggaran'];
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $result,
            'summary' => [
                'total_by_sumber' => $totalBySumber,
                'grand_total' => $grandTotal
            ],
            'tahun' => $tahun
        ]);
    }
    
    /**
     * Get drilldown data for Total Realisasi
     */
    public function drilldownRealisasi()
    {
        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        if (!$kodeDesa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kode desa tidak ditemukan']);
        }
        
        $db = \Config\Database::connect();
        
        // Get realisasi grouped by month
        $monthlyRealisasi = $db->query("
            SELECT 
                EXTRACT(MONTH FROM tanggal)::int as bulan,
                SUM(kredit) as total_realisasi,
                COUNT(*) as jumlah_transaksi
            FROM bku 
            WHERE kode_desa = ? 
                AND EXTRACT(YEAR FROM tanggal)::int = ?
                AND jenis_transaksi = 'Belanja'
            GROUP BY EXTRACT(MONTH FROM tanggal)
            ORDER BY bulan
        ", [$kodeDesa, $tahun])->getResultArray();
        
        // Get realisasi by rekening
        $byRekening = $db->query("
            SELECT 
                r.kode_akun,
                r.nama_akun,
                SUM(b.kredit) as total_realisasi,
                COUNT(*) as jumlah_transaksi
            FROM bku b
            LEFT JOIN ref_rekening r ON r.id = b.ref_rekening_id
            WHERE b.kode_desa = ? 
                AND EXTRACT(YEAR FROM b.tanggal)::int = ?
                AND b.jenis_transaksi = 'Belanja'
            GROUP BY r.kode_akun, r.nama_akun
            ORDER BY r.kode_akun ASC
        ", [$kodeDesa, $tahun])->getResultArray();
        
        // Get recent belanja transactions
        $recentTransactions = $db->query("
            SELECT 
                b.id,
                b.tanggal,
                b.no_bukti,
                b.uraian,
                b.kredit,
                r.kode_akun,
                r.nama_akun
            FROM bku b
            LEFT JOIN ref_rekening r ON r.id = b.ref_rekening_id
            WHERE b.kode_desa = ? 
                AND EXTRACT(YEAR FROM b.tanggal)::int = ?
                AND b.jenis_transaksi = 'Belanja'
            ORDER BY b.tanggal DESC, b.id DESC
            LIMIT 20
        ", [$kodeDesa, $tahun])->getResultArray();
        
        $grandTotal = array_sum(array_column($monthlyRealisasi, 'total_realisasi'));
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'monthly' => $monthlyRealisasi,
                'by_rekening' => $byRekening,
                'recent_transactions' => $recentTransactions
            ],
            'summary' => [
                'grand_total' => $grandTotal,
                'total_transactions' => array_sum(array_column($monthlyRealisasi, 'jumlah_transaksi'))
            ],
            'tahun' => $tahun
        ]);
    }
    
    /**
     * Get drilldown data for Realisasi per Bulan (monthly detail)
     */
    public function drilldownRealisasiBulan()
    {
        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan');
        
        if (!$kodeDesa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kode desa tidak ditemukan']);
        }
        
        if (!$bulan || $bulan < 1 || $bulan > 12) {
            return $this->response->setJSON(['success' => false, 'message' => 'Bulan tidak valid']);
        }
        
        $db = \Config\Database::connect();
        
        // Get all belanja transactions for the specified month
        $transactions = $db->query("
            SELECT 
                b.id,
                b.tanggal,
                b.no_bukti,
                b.uraian,
                b.kredit,
                r.kode_akun,
                r.nama_akun
            FROM bku b
            LEFT JOIN ref_rekening r ON r.id = b.ref_rekening_id
            WHERE b.kode_desa = ? 
                AND EXTRACT(YEAR FROM b.tanggal)::int = ?
                AND EXTRACT(MONTH FROM b.tanggal)::int = ?
                AND b.jenis_transaksi = 'Belanja'
            ORDER BY b.tanggal ASC, b.id ASC
        ", [$kodeDesa, $tahun, $bulan])->getResultArray();
        
        $totalBulan = array_sum(array_column($transactions, 'kredit'));
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $transactions,
            'summary' => [
                'total' => $totalBulan,
                'count' => count($transactions)
            ],
            'tahun' => $tahun,
            'bulan' => $bulan
        ]);
    }
    
    /**
     * Get drilldown data for BKU Detail (individual items)
     */
    public function drilldownBkuDetail()
    {
        $bkuId = $this->request->getGet('bku_id');
        
        if (!$bkuId) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID BKU tidak ditemukan']);
        }
        
        $db = \Config\Database::connect();
        
        // Get BKU transaction info
        $bku = $db->query("
            SELECT 
                b.id,
                b.tanggal,
                b.no_bukti,
                b.uraian,
                b.kredit,
                r.kode_akun,
                r.nama_akun
            FROM bku b
            LEFT JOIN ref_rekening r ON r.id = b.ref_rekening_id
            WHERE b.id = ?
        ", [$bkuId])->getRowArray();
        
        if (!$bku) {
            return $this->response->setJSON(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
        }
        
        // Get detail items
        $details = $db->query("
            SELECT 
                id,
                nama_item,
                spesifikasi,
                satuan,
                jumlah,
                harga_satuan,
                subtotal,
                keterangan
            FROM bku_detail
            WHERE bku_id = ?
            ORDER BY id ASC
        ", [$bkuId])->getResultArray();
        
        $totalFromDetails = array_sum(array_column($details, 'subtotal'));
        
        return $this->response->setJSON([
            'success' => true,
            'bku' => $bku,
            'details' => $details,
            'summary' => [
                'item_count' => count($details),
                'total_from_details' => $totalFromDetails,
                'bku_amount' => $bku['kredit'],
                'has_details' => count($details) > 0
            ]
        ]);
    }
    
    /**
     * Get drilldown data for Anggaran per Sumber Dana
     */
    public function drilldownSumberDana()
    {
        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $sumberDana = $this->request->getGet('sumber_dana');
        
        if (!$kodeDesa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kode desa tidak ditemukan']);
        }
        
        $db = \Config\Database::connect();
        
        // Get detail by specific sumber dana
        $query = "
            SELECT 
                a.id,
                a.uraian,
                a.anggaran,
                a.sumber_dana,
                r.kode_akun,
                r.nama_akun,
                r.level
            FROM apbdes a
            LEFT JOIN ref_rekening r ON r.id = a.ref_rekening_id
            WHERE a.kode_desa = ? AND a.tahun = ?
        ";
        
        $params = [$kodeDesa, $tahun];
        
        if ($sumberDana) {
            $query .= " AND a.sumber_dana = ?";
            $params[] = $sumberDana;
        }
        
        $query .= " ORDER BY a.sumber_dana, r.kode_akun ASC";
        
        $result = $db->query($query, $params)->getResultArray();
        
        // Get realisasi per sumber dana
        $realisasiQuery = "
            SELECT 
                a.sumber_dana,
                SUM(a.anggaran) as total_anggaran,
                COALESCE(SUM(b.total_realisasi), 0) as total_realisasi
            FROM apbdes a
            LEFT JOIN (
                SELECT 
                    ref_rekening_id,
                    SUM(kredit) as total_realisasi
                FROM bku
                WHERE kode_desa = ? 
                    AND EXTRACT(YEAR FROM tanggal)::int = ?
                    AND jenis_transaksi = 'Belanja'
                GROUP BY ref_rekening_id
            ) b ON b.ref_rekening_id = a.ref_rekening_id
            WHERE a.kode_desa = ? AND a.tahun = ?
        ";
        
        $realisasiParams = [$kodeDesa, $tahun, $kodeDesa, $tahun];
        
        if ($sumberDana) {
            $realisasiQuery .= " AND a.sumber_dana = ?";
            $realisasiParams[] = $sumberDana;
        }
        
        $realisasiQuery .= " GROUP BY a.sumber_dana ORDER BY a.sumber_dana";
        
        $realisasiSummary = $db->query($realisasiQuery, $realisasiParams)->getResultArray();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $result,
            'realisasi_summary' => $realisasiSummary,
            'sumber_dana' => $sumberDana,
            'tahun' => $tahun
        ]);
    }
    
    /**
     * Get drilldown data for Pie Chart comparison (Anggaran vs Realisasi)
     */
    public function drilldownPieChart()
    {
        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        if (!$kodeDesa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kode desa tidak ditemukan']);
        }
        
        $db = \Config\Database::connect();
        
        // Get total anggaran
        $totalAnggaran = $this->apbdesModel
            ->where('kode_desa', $kodeDesa)
            ->where('tahun', $tahun)
            ->selectSum('anggaran')
            ->first();
        $anggaran = (float)($totalAnggaran['anggaran'] ?? 0);
        
        // Get total realisasi
        $totalRealisasi = $this->bkuModel
            ->where('kode_desa', $kodeDesa)
            ->where('EXTRACT(YEAR FROM tanggal)::int', $tahun)
            ->where('jenis_transaksi', 'Belanja')
            ->selectSum('kredit')
            ->first();
        $realisasi = (float)($totalRealisasi['kredit'] ?? 0);
        
        // Get comparison by sumber dana
        $comparisonBySumber = $db->query("
            SELECT 
                a.sumber_dana,
                SUM(a.anggaran) as total_anggaran,
                COALESCE(SUM(b.total_realisasi), 0) as total_realisasi
            FROM apbdes a
            LEFT JOIN (
                SELECT 
                    ref_rekening_id,
                    SUM(kredit) as total_realisasi
                FROM bku
                WHERE kode_desa = ? 
                    AND EXTRACT(YEAR FROM tanggal)::int = ?
                    AND jenis_transaksi = 'Belanja'
                GROUP BY ref_rekening_id
            ) b ON b.ref_rekening_id = a.ref_rekening_id
            WHERE a.kode_desa = ? AND a.tahun = ?
            GROUP BY a.sumber_dana
            ORDER BY a.sumber_dana
        ", [$kodeDesa, $tahun, $kodeDesa, $tahun])->getResultArray();
        
        // Get monthly comparison
        $monthlyComparison = $db->query("
            WITH monthly_anggaran AS (
                SELECT 
                    SUM(anggaran) / 12 as monthly_target
                FROM apbdes
                WHERE kode_desa = ? AND tahun = ?
            ),
            monthly_realisasi AS (
                SELECT 
                    EXTRACT(MONTH FROM tanggal)::int as bulan,
                    SUM(kredit) as realisasi
                FROM bku
                WHERE kode_desa = ? 
                    AND EXTRACT(YEAR FROM tanggal)::int = ?
                    AND jenis_transaksi = 'Belanja'
                GROUP BY EXTRACT(MONTH FROM tanggal)
            )
            SELECT 
                mr.bulan,
                ma.monthly_target as target,
                COALESCE(mr.realisasi, 0) as realisasi
            FROM monthly_realisasi mr
            CROSS JOIN monthly_anggaran ma
            ORDER BY mr.bulan
        ", [$kodeDesa, $tahun, $kodeDesa, $tahun])->getResultArray();
        
        // Calculate percentages and additional metrics
        $persentase = $anggaran > 0 ? round(($realisasi / $anggaran) * 100, 2) : 0;
        $sisaAnggaran = $anggaran - $realisasi;
        
        // Process comparison by sumber for percentages
        foreach ($comparisonBySumber as &$item) {
            $item['total_anggaran'] = (float)$item['total_anggaran'];
            $item['total_realisasi'] = (float)$item['total_realisasi'];
            $item['sisa'] = $item['total_anggaran'] - $item['total_realisasi'];
            $item['persentase'] = $item['total_anggaran'] > 0 
                ? round(($item['total_realisasi'] / $item['total_anggaran']) * 100, 2) 
                : 0;
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_anggaran' => $anggaran,
                    'total_realisasi' => $realisasi,
                    'sisa_anggaran' => $sisaAnggaran,
                    'persentase_realisasi' => $persentase
                ],
                'by_sumber_dana' => $comparisonBySumber,
                'monthly_comparison' => $monthlyComparison
            ],
            'tahun' => $tahun
        ]);
    }
    
    /**
     * Get drilldown data for Proyek Pembangunan (Construction Project Detail)
     */
    public function drilldownProyek()
    {
        $proyekId = $this->request->getGet('proyek_id');
        $apbdesId = $this->request->getGet('apbdes_id');
        $kodeDesa = $this->session->get('kode_desa');
        
        if (!$kodeDesa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kode desa tidak ditemukan']);
        }
        
        $db = \Config\Database::connect();
        
        // If apbdes_id provided, try to find linked proyek
        if ($apbdesId && !$proyekId) {
            // Try to find project by matching APBDes uraian with project name
            $apbdes = $db->query("
                SELECT a.*, r.kode_akun, r.nama_akun 
                FROM apbdes a
                LEFT JOIN ref_rekening r ON r.id = a.ref_rekening_id
                WHERE a.id = ?
            ", [$apbdesId])->getRowArray();
            
            if ($apbdes) {
                // Try to find matching project
                $proyek = $db->query("
                    SELECT * FROM proyek_pembangunan 
                    WHERE kode_desa = ? 
                        AND (
                            apbdes_id = ? 
                            OR LOWER(nama_proyek) LIKE LOWER(?)
                            OR LOWER(nama_proyek) LIKE LOWER(?)
                        )
                    LIMIT 1
                ", [
                    $kodeDesa, 
                    $apbdesId,
                    '%' . $apbdes['uraian'] . '%',
                    '%' . ($apbdes['nama_akun'] ?? '') . '%'
                ])->getRowArray();
                
                if ($proyek) {
                    $proyekId = $proyek['id'];
                } else {
                    // No project found, return APBDes info only
                    return $this->response->setJSON([
                        'success' => true,
                        'has_project' => false,
                        'apbdes' => $apbdes,
                        'message' => 'Item anggaran ini belum terhubung dengan proyek pembangunan'
                    ]);
                }
            }
        }
        
        if (!$proyekId) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID Proyek tidak ditemukan']);
        }
        
        // Get project detail
        $proyek = $db->query("
            SELECT 
                p.*,
                u.username as created_by_name
            FROM proyek_pembangunan p
            LEFT JOIN users u ON u.id = p.created_by
            WHERE p.id = ? AND p.kode_desa = ?
        ", [$proyekId, $kodeDesa])->getRowArray();
        
        if (!$proyek) {
            return $this->response->setJSON(['success' => false, 'message' => 'Proyek tidak ditemukan']);
        }
        
        // Get linked APBDes (funding source)
        $apbdesInfo = null;
        if ($proyek['apbdes_id']) {
            $apbdesInfo = $db->query("
                SELECT a.*, r.kode_akun, r.nama_akun
                FROM apbdes a
                LEFT JOIN ref_rekening r ON r.id = a.ref_rekening_id
                WHERE a.id = ?
            ", [$proyek['apbdes_id']])->getRowArray();
        } else {
            // Try to find matching APBDes by name
            $apbdesInfo = $db->query("
                SELECT a.*, r.kode_akun, r.nama_akun
                FROM apbdes a
                LEFT JOIN ref_rekening r ON r.id = a.ref_rekening_id
                WHERE a.kode_desa = ?
                    AND (
                        LOWER(a.uraian) LIKE LOWER(?)
                        OR LOWER(r.nama_akun) LIKE LOWER(?)
                    )
                LIMIT 1
            ", [
                $kodeDesa,
                '%' . $proyek['nama_proyek'] . '%',
                '%' . $proyek['nama_proyek'] . '%'
            ])->getRowArray();
        }
        
        // Get linked Kegiatan (planning)
        $kegiatanInfo = null;
        if (!empty($proyek['kode_kegiatan'])) {
            $kegiatanInfo = $db->query("
                SELECT k.*, r.tahun as rkp_tahun, r.tema as rkp_tema
                FROM kegiatan k
                LEFT JOIN rkpdesa r ON r.id = k.rkpdesa_id
                WHERE k.kode_desa = ? AND k.id = ?
            ", [$kodeDesa, $proyek['kode_kegiatan']])->getRowArray();
        }
        
        // Get progress history
        $progressHistory = $db->query("
            SELECT 
                pp.*,
                u.username as created_by_name
            FROM progress_proyek pp
            LEFT JOIN users u ON u.id = pp.created_by
            WHERE pp.proyek_id = ?
            ORDER BY pp.tanggal_laporan DESC
        ", [$proyekId])->getResultArray();
        
        // Get related BKU transactions (realisasi)
        $realisasiTransactions = [];
        if ($apbdesInfo) {
            $realisasiTransactions = $db->query("
                SELECT b.*, r.kode_akun, r.nama_akun
                FROM bku b
                LEFT JOIN ref_rekening r ON r.id = b.ref_rekening_id
                WHERE b.kode_desa = ?
                    AND b.ref_rekening_id = ?
                    AND b.jenis_transaksi = 'Belanja'
                ORDER BY b.tanggal DESC
                LIMIT 10
            ", [$kodeDesa, $apbdesInfo['ref_rekening_id']])->getResultArray();
        }
        
        // Calculate summary
        $totalRealisasi = 0;
        foreach ($realisasiTransactions as $trx) {
            $totalRealisasi += (float)$trx['kredit'];
        }
        
        $persentaseKeuangan = $proyek['anggaran'] > 0 
            ? round(($totalRealisasi / $proyek['anggaran']) * 100, 2) 
            : 0;
        
        return $this->response->setJSON([
            'success' => true,
            'has_project' => true,
            'proyek' => [
                'id' => $proyek['id'],
                'nama' => $proyek['nama_proyek'],
                'lokasi' => $proyek['lokasi_detail'],
                'volume_target' => $proyek['volume_target'],
                'satuan' => $proyek['satuan'],
                'anggaran' => (float)$proyek['anggaran'],
                'persentase_fisik' => (int)($proyek['persentase_fisik'] ?? 0),
                'persentase_keuangan' => (float)($proyek['persentase_keuangan'] ?? 0),
                'status' => $proyek['status'],
                'pelaksana' => $proyek['pelaksana_kegiatan'],
                'kontraktor' => $proyek['kontraktor'] ?? null,
                'tgl_mulai' => $proyek['tgl_mulai'],
                'tgl_selesai_target' => $proyek['tgl_selesai_target'],
                'tgl_selesai_aktual' => $proyek['tgl_selesai_aktual'] ?? null,
                'foto_0' => $proyek['foto_0'] ?? null,
                'foto_50' => $proyek['foto_50'] ?? null,
                'foto_100' => $proyek['foto_100'] ?? null,
                'koordinat' => [
                    'lat' => $proyek['lat'] ? (float)$proyek['lat'] : null,
                    'lng' => $proyek['lng'] ? (float)$proyek['lng'] : null
                ],
                'created_at' => $proyek['created_at'],
                'created_by' => $proyek['created_by_name'] ?? '-'
            ],
            'apbdes' => $apbdesInfo ? [
                'id' => $apbdesInfo['id'],
                'kode_akun' => $apbdesInfo['kode_akun'],
                'nama_akun' => $apbdesInfo['nama_akun'],
                'uraian' => $apbdesInfo['uraian'],
                'anggaran' => (float)$apbdesInfo['anggaran'],
                'sumber_dana' => $apbdesInfo['sumber_dana']
            ] : null,
            'kegiatan' => $kegiatanInfo ? [
                'id' => $kegiatanInfo['id'],
                'nama' => $kegiatanInfo['nama_kegiatan'],
                'lokasi' => $kegiatanInfo['lokasi'],
                'volume' => $kegiatanInfo['volume'],
                'satuan' => $kegiatanInfo['satuan'],
                'pagu' => (float)($kegiatanInfo['pagu'] ?? 0),
                'rkp_tahun' => $kegiatanInfo['rkp_tahun'] ?? null,
                'rkp_tema' => $kegiatanInfo['rkp_tema'] ?? null
            ] : null,
            'progress' => $progressHistory,
            'realisasi' => [
                'transactions' => $realisasiTransactions,
                'total' => $totalRealisasi,
                'persentase' => $persentaseKeuangan
            ],
            'summary' => [
                'anggaran' => (float)$proyek['anggaran'],
                'realisasi' => $totalRealisasi,
                'sisa' => (float)$proyek['anggaran'] - $totalRealisasi,
                'persentase_fisik' => (int)($proyek['persentase_fisik'] ?? 0),
                'persentase_keuangan' => $persentaseKeuangan
            ]
        ]);
    }
    
    /**
     * Get list of all projects for drilldown
     */
    public function drilldownProyekList()
    {
        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        if (!$kodeDesa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kode desa tidak ditemukan']);
        }
        
        $db = \Config\Database::connect();
        
        $projects = $db->query("
            SELECT 
                p.id,
                p.nama_proyek,
                p.lokasi_detail,
                p.anggaran,
                p.persentase_fisik,
                p.status,
                p.lat,
                p.lng,
                p.tgl_mulai,
                p.tgl_selesai_target
            FROM proyek_pembangunan p
            WHERE p.kode_desa = ?
                AND (p.tahun = ? OR EXTRACT(YEAR FROM p.tgl_mulai)::int = ?)
            ORDER BY p.status DESC, p.persentase_fisik DESC
        ", [$kodeDesa, $tahun, $tahun])->getResultArray();
        
        $summary = [
            'total' => count($projects),
            'selesai' => 0,
            'proses' => 0,
            'rencana' => 0,
            'total_anggaran' => 0
        ];
        
        foreach ($projects as $p) {
            $summary['total_anggaran'] += (float)$p['anggaran'];
            if ($p['status'] === 'SELESAI') $summary['selesai']++;
            elseif ($p['status'] === 'PROSES') $summary['proses']++;
            else $summary['rencana']++;
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $projects,
            'summary' => $summary,
            'tahun' => $tahun
        ]);
    }
}


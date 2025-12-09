<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ProyekModel - Physical Infrastructure Project Management
 * 
 * Features:
 * - Financial vs Physical realization comparison
 * - Deviation detection (Red Flag alerts)
 * - GIS integration for project mapping
 */
class ProyekModel extends Model
{
    protected $table            = 'proyek_fisik';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_desa',
        'apbdes_id',
        'kode_kegiatan',
        'nama_proyek',
        'lokasi_detail',
        'volume_target',
        'satuan',
        'anggaran',
        'tgl_mulai',
        'tgl_selesai_target',
        'tgl_selesai_aktual',
        'pelaksana_kegiatan',
        'kontraktor',
        'lat',
        'lng',
        'foto_0',
        'foto_50',
        'foto_100',
        'persentase_fisik',
        'persentase_keuangan',
        'status',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Threshold for deviation alert (Financial % - Physical % > 20%)
    const DEVIATION_THRESHOLD = 20;

    /**
     * Get all projects with statistics
     */
    public function getWithStats(?string $kodeDesa, array $filters = []): array
    {
        $builder = $this->select('proyek_fisik.*')
            ->where('kode_desa', $kodeDesa);
        
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        
        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(tgl_mulai)', $filters['tahun']);
        }
        
        return $builder->orderBy('tgl_mulai', 'DESC')->findAll();
    }

    /**
     * Get project with full details including logs
     */
    public function getWithLogs(int $id): ?array
    {
        $project = $this->find($id);
        
        if (!$project) {
            return null;
        }
        
        $db = \Config\Database::connect();
        $project['logs'] = $db->table('proyek_log')
            ->where('proyek_id', $id)
            ->orderBy('tanggal_laporan', 'DESC')
            ->get()
            ->getResultArray();
        
        // Calculate deviation
        $project['deviation'] = $this->calculateDeviation($project);
        
        return $project;
    }

    /**
     * Calculate financial realization percentage from SPP
     */
    public function calculateKeuanganRealization(int $proyekId): float
    {
        $project = $this->find($proyekId);
        if (!$project || empty($project['apbdes_id']) || $project['anggaran'] <= 0) {
            return 0;
        }
        
        $db = \Config\Database::connect();
        
        // Get total SPP yang sudah cair untuk kegiatan ini
        $totalCair = $db->table('spp')
            ->selectSum('nilai')
            ->where('apbdes_id', $project['apbdes_id'])
            ->where('status', 'CAIR')
            ->get()
            ->getRow();
        
        $cair = $totalCair->nilai ?? 0;
        $persentase = ($cair / $project['anggaran']) * 100;
        
        return min(100, round($persentase, 2));
    }

    /**
     * Calculate deviation between financial and physical progress
     * Positive deviation = money spent faster than progress (potential issue)
     */
    public function calculateDeviation(array $project): array
    {
        $keuangan = (float) ($project['persentase_keuangan'] ?? 0);
        $fisik = (int) ($project['persentase_fisik'] ?? 0);
        
        $deviation = $keuangan - $fisik;
        
        return [
            'value' => round($deviation, 2),
            'is_alert' => $deviation > self::DEVIATION_THRESHOLD,
            'level' => $this->getDeviationLevel($deviation),
            'message' => $this->getDeviationMessage($deviation),
        ];
    }

    /**
     * Get deviation level for styling
     */
    private function getDeviationLevel(float $deviation): string
    {
        if ($deviation > 30) return 'danger';
        if ($deviation > 20) return 'warning';
        if ($deviation > 10) return 'info';
        return 'success';
    }

    /**
     * Get deviation message
     */
    private function getDeviationMessage(float $deviation): string
    {
        if ($deviation > 30) {
            return 'PERHATIAN SERIUS: Dana terserap jauh melebihi progres fisik!';
        }
        if ($deviation > 20) {
            return 'Peringatan: Ada ketimpangan antara realisasi keuangan dan fisik';
        }
        if ($deviation > 10) {
            return 'Perlu monitoring: Keuangan lebih cepat dari fisik';
        }
        if ($deviation < -20) {
            return 'Fisik berjalan lebih cepat dari pencairan dana';
        }
        return 'Normal: Progres fisik dan keuangan seimbang';
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(?string $kodeDesa, ?int $tahun = null): array
    {
        $tahun = $tahun ?? date('Y');
        
        $builder = $this->where('kode_desa', $kodeDesa)
            ->where('YEAR(tgl_mulai)', $tahun);
        
        $projects = $builder->findAll();
        
        $stats = [
            'total_proyek' => count($projects),
            'rencana' => 0,
            'proses' => 0,
            'selesai' => 0,
            'mangkrak' => 0,
            'total_anggaran' => 0,
            'total_realisasi' => 0,
            'avg_fisik' => 0,
            'avg_keuangan' => 0,
            'proyek_alert' => 0, // Projects with deviation alert
        ];
        
        $sumFisik = 0;
        $sumKeuangan = 0;
        
        foreach ($projects as $p) {
            $stats[$this->statusKey($p['status'])]++;
            $stats['total_anggaran'] += (float) $p['anggaran'];
            $stats['total_realisasi'] += ((float) $p['anggaran'] * (float) $p['persentase_keuangan'] / 100);
            
            $sumFisik += (int) $p['persentase_fisik'];
            $sumKeuangan += (float) $p['persentase_keuangan'];
            
            $deviation = $this->calculateDeviation($p);
            if ($deviation['is_alert']) {
                $stats['proyek_alert']++;
            }
        }
        
        if (count($projects) > 0) {
            $stats['avg_fisik'] = round($sumFisik / count($projects), 1);
            $stats['avg_keuangan'] = round($sumKeuangan / count($projects), 1);
        }
        
        return $stats;
    }

    private function statusKey(string $status): string
    {
        return strtolower($status);
    }

    /**
     * Get projects with deviation alerts
     */
    public function getAlertProjects(?string $kodeDesa): array
    {
        $projects = $this->where('kode_desa', $kodeDesa)
            ->where('status', 'PROSES')
            ->findAll();
        
        $alerts = [];
        foreach ($projects as $p) {
            $deviation = $this->calculateDeviation($p);
            if ($deviation['is_alert']) {
                $p['deviation'] = $deviation;
                $alerts[] = $p;
            }
        }
        
        // Sort by deviation value descending
        usort($alerts, fn($a, $b) => $b['deviation']['value'] <=> $a['deviation']['value']);
        
        return $alerts;
    }

    /**
     * Get projects for GIS layer
     */
    public function getForGis(?string $kodeDesa): array
    {
        return $this->select('proyek_fisik.*, 
                (SELECT foto FROM proyek_log WHERE proyek_id = proyek_fisik.id ORDER BY tanggal_laporan DESC LIMIT 1) as foto_terbaru')
            ->where('kode_desa', $kodeDesa)
            ->where('lat IS NOT NULL')
            ->where('lng IS NOT NULL')
            ->findAll();
    }

    /**
     * Get monthly progress trend
     */
    public function getMonthlyTrend(?string $kodeDesa, int $tahun): array
    {
        $db = \Config\Database::connect();
        
        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $result[$m] = [
                'bulan' => $m,
                'proyek_baru' => 0,
                'proyek_selesai' => 0,
                'total_anggaran' => 0,
            ];
        }
        
        // Projects started per month
        $started = $db->table('proyek_fisik')
            ->select('MONTH(tgl_mulai) as bulan, COUNT(*) as total, SUM(anggaran) as anggaran')
            ->where('kode_desa', $kodeDesa)
            ->where('YEAR(tgl_mulai)', $tahun)
            ->groupBy('MONTH(tgl_mulai)')
            ->get()
            ->getResultArray();
        
        foreach ($started as $s) {
            $result[(int)$s['bulan']]['proyek_baru'] = (int) $s['total'];
            $result[(int)$s['bulan']]['total_anggaran'] = (float) $s['anggaran'];
        }
        
        // Projects completed per month
        $completed = $db->table('proyek_fisik')
            ->select('MONTH(tgl_selesai_aktual) as bulan, COUNT(*) as total')
            ->where('kode_desa', $kodeDesa)
            ->where('YEAR(tgl_selesai_aktual)', $tahun)
            ->where('status', 'SELESAI')
            ->groupBy('MONTH(tgl_selesai_aktual)')
            ->get()
            ->getResultArray();
        
        foreach ($completed as $c) {
            $result[(int)$c['bulan']]['proyek_selesai'] = (int) $c['total'];
        }
        
        return array_values($result);
    }

    /**
     * Update financial realization for a project
     */
    public function updateKeuanganRealization(int $proyekId): bool
    {
        $persentase = $this->calculateKeuanganRealization($proyekId);
        
        return $this->update($proyekId, [
            'persentase_keuangan' => $persentase,
        ]);
    }

    /**
     * Get dropdown options for satuan
     */
    public static function getSatuanOptions(): array
    {
        return [
            'M' => 'Meter (M)',
            'M2' => 'Meter Persegi (M²)',
            'M3' => 'Meter Kubik (M³)',
            'Unit' => 'Unit',
            'Paket' => 'Paket',
            'Buah' => 'Buah',
            'Set' => 'Set',
            'Lokasi' => 'Lokasi',
        ];
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'RENCANA' => 'Rencana',
            'PROSES' => 'Dalam Proses',
            'SELESAI' => 'Selesai',
            'MANGKRAK' => 'Mangkrak/Tertunda',
        ];
    }
}

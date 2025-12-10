<?php

namespace App\Models;

use CodeIgniter\Model;

class PemeriksaanModel extends Model
{
    protected $table            = 'kes_pemeriksaan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'posyandu_id',
        'penduduk_id',
        'tanggal_periksa',
        'usia_bulan',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'lingkar_lengan',
        'vitamin_a',
        'imunisasi',
        'asi_eksklusif',
        'status_gizi',
        'z_score_bb_u',
        'z_score_tb_u',
        'z_score_bb_tb',
        'indikasi_stunting',
        'indikasi_gizi_buruk',
        'keterangan',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get pemeriksaan with penduduk info
     */
    public function getWithPenduduk(int $posyanduId, array $filters = []): array
    {
        $builder = $this->select('kes_pemeriksaan.*, p.nik, p.nama_lengkap, p.jenis_kelamin, p.tanggal_lahir, k.alamat, k.dusun')
            ->join('pop_penduduk p', 'p.id = kes_pemeriksaan.penduduk_id')
            ->join('pop_keluarga k', 'k.id = p.keluarga_id')
            ->where('kes_pemeriksaan.posyandu_id', $posyanduId);

        if (!empty($filters['bulan'])) {
            $builder->where('EXTRACT(MONTH FROM kes_pemeriksaan.tanggal_periksa)::int', $filters['bulan']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('EXTRACT(YEAR FROM kes_pemeriksaan.tanggal_periksa)::int', $filters['tahun']);
        }

        if (!empty($filters['stunting_only'])) {
            $builder->where('kes_pemeriksaan.indikasi_stunting', true);
        }

        return $builder->orderBy('kes_pemeriksaan.tanggal_periksa', 'DESC')->findAll();
    }

    /**
     * Get riwayat pemeriksaan satu balita
     */
    public function getRiwayatBalita(int $pendudukId): array
    {
        return $this->select('kes_pemeriksaan.*, pos.nama_posyandu')
            ->join('kes_posyandu pos', 'pos.id = kes_pemeriksaan.posyandu_id')
            ->where('kes_pemeriksaan.penduduk_id', $pendudukId)
            ->orderBy('kes_pemeriksaan.tanggal_periksa', 'ASC')
            ->findAll();
    }

    /**
     * Calculate age in months from birth date to checkup date
     */
    public function calculateAgeMonths(string $tanggalLahir, string $tanggalPeriksa): int
    {
        $birth = new \DateTime($tanggalLahir);
        $checkup = new \DateTime($tanggalPeriksa);
        $diff = $birth->diff($checkup);
        
        return ($diff->y * 12) + $diff->m;
    }

    /**
     * Get WHO standard for specific age, gender, and indicator
     */
    public function getWhoStandard(string $jenisKelamin, int $usiaBulan, string $indikator): ?array
    {
        $db = \Config\Database::connect();
        
        // Find closest age in standards
        $standard = $db->table('kes_standar_who')
            ->where('jenis_kelamin', $jenisKelamin)
            ->where('indikator', $indikator)
            ->where('usia_bulan <=', $usiaBulan)
            ->orderBy('usia_bulan', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();
        
        return $standard;
    }

    /**
     * Calculate Z-Score
     * Z = (measured value - median) / SD
     * Simplified calculation using LMS method approximation
     */
    public function calculateZScore(float $measuredValue, array $standard): float
    {
        $median = (float) $standard['median'];
        $sd_min1 = (float) $standard['sd_min1'];
        
        // Approximate SD (standard deviation)
        $sd = $median - $sd_min1;
        
        if ($sd == 0) {
            return 0;
        }
        
        $zScore = ($measuredValue - $median) / $sd;
        
        return round($zScore, 2);
    }

    /**
     * Calculate stunting status based on Height-for-Age
     * Stunting: TB/U < -2 SD
     * Severely Stunting: TB/U < -3 SD
     */
    public function calculateStunting(string $jenisKelamin, int $usiaBulan, float $tinggiBadan): array
    {
        $standard = $this->getWhoStandard($jenisKelamin, $usiaBulan, 'TB_U');
        
        if (!$standard) {
            return [
                'z_score' => null,
                'indikasi_stunting' => false,
                'kategori' => 'Data standar tidak tersedia',
            ];
        }
        
        $zScore = $this->calculateZScore($tinggiBadan, $standard);
        
        $indikasi = false;
        $kategori = 'Normal';
        
        if ($zScore < -3) {
            $indikasi = true;
            $kategori = 'Sangat Pendek (Severely Stunted)';
        } elseif ($zScore < -2) {
            $indikasi = true;
            $kategori = 'Pendek (Stunted)';
        } elseif ($zScore >= -2 && $zScore <= 2) {
            $kategori = 'Normal';
        } elseif ($zScore > 2) {
            $kategori = 'Tinggi';
        }
        
        return [
            'z_score' => $zScore,
            'indikasi_stunting' => $indikasi,
            'kategori' => $kategori,
            'standard' => $standard,
        ];
    }

    /**
     * Calculate nutritional status based on Weight-for-Age
     */
    public function calculateGiziStatus(string $jenisKelamin, int $usiaBulan, float $beratBadan): array
    {
        $standard = $this->getWhoStandard($jenisKelamin, $usiaBulan, 'BB_U');
        
        if (!$standard) {
            return [
                'z_score' => null,
                'status_gizi' => 'BAIK',
                'indikasi_gizi_buruk' => false,
            ];
        }
        
        $zScore = $this->calculateZScore($beratBadan, $standard);
        
        $status = 'BAIK';
        $giziBuruk = false;
        
        if ($zScore < -3) {
            $status = 'BURUK';
            $giziBuruk = true;
        } elseif ($zScore < -2) {
            $status = 'KURANG';
        } elseif ($zScore > 2) {
            $status = 'LEBIH';
        } elseif ($zScore > 3) {
            $status = 'OBESITAS';
        }
        
        return [
            'z_score' => $zScore,
            'status_gizi' => $status,
            'indikasi_gizi_buruk' => $giziBuruk,
        ];
    }

    /**
     * Process full health assessment
     */
    public function assessHealth(array $data, string $jenisKelamin, string $tanggalLahir): array
    {
        $usiaBulan = $this->calculateAgeMonths($tanggalLahir, $data['tanggal_periksa']);
        
        // Calculate stunting (Height-for-Age)
        $stuntingResult = $this->calculateStunting(
            $jenisKelamin, 
            $usiaBulan, 
            (float) $data['tinggi_badan']
        );
        
        // Calculate nutritional status (Weight-for-Age)
        $giziResult = $this->calculateGiziStatus(
            $jenisKelamin, 
            $usiaBulan, 
            (float) $data['berat_badan']
        );
        
        return [
            'usia_bulan'          => $usiaBulan,
            'z_score_tb_u'        => $stuntingResult['z_score'],
            'z_score_bb_u'        => $giziResult['z_score'],
            'indikasi_stunting'   => $stuntingResult['indikasi_stunting'],
            'kategori_stunting'   => $stuntingResult['kategori'],
            'status_gizi'         => $giziResult['status_gizi'],
            'indikasi_gizi_buruk' => $giziResult['indikasi_gizi_buruk'],
        ];
    }

    /**
     * Get stunting statistics for a village
     */
    public function getStuntingStats(string $kodeDesa): array
    {
        $db = \Config\Database::connect();
        
        // Get latest checkup per child
        $latestCheckups = $db->query("
            SELECT p.penduduk_id, MAX(p.id) as latest_id
            FROM kes_pemeriksaan p
            JOIN kes_posyandu pos ON pos.id = p.posyandu_id
            WHERE pos.kode_desa = ?
            GROUP BY p.penduduk_id
        ", [$kodeDesa])->getResultArray();
        
        $latestIds = array_column($latestCheckups, 'latest_id');
        
        if (empty($latestIds)) {
            return [
                'total_balita' => 0,
                'stunting' => 0,
                'normal' => 0,
                'percentage' => 0,
            ];
        }
        
        $total = count($latestIds);
        
        $stunting = $this->whereIn('id', $latestIds)
            ->where('indikasi_stunting', true)
            ->countAllResults();
        
        return [
            'total_balita' => $total,
            'stunting' => $stunting,
            'normal' => $total - $stunting,
            'percentage' => $total > 0 ? round(($stunting / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get stunting cases with location for GIS
     */
    public function getStuntingForGis(string $kodeDesa): array
    {
        $db = \Config\Database::connect();
        
        return $db->query("
            SELECT 
                p.id,
                p.penduduk_id,
                pd.nama_lengkap,
                pd.jenis_kelamin,
                pd.tanggal_lahir,
                p.usia_bulan,
                p.tinggi_badan,
                p.berat_badan,
                p.z_score_tb_u,
                p.tanggal_periksa,
                k.alamat,
                k.dusun,
                k.rt,
                k.rw,
                pos.nama_posyandu,
                pos.lat,
                pos.lng
            FROM kes_pemeriksaan p
            JOIN pop_penduduk pd ON pd.id = p.penduduk_id
            JOIN pop_keluarga k ON k.id = pd.keluarga_id
            JOIN kes_posyandu pos ON pos.id = p.posyandu_id
            WHERE pos.kode_desa = ?
            AND p.indikasi_stunting = 1
            AND p.id IN (
                SELECT MAX(id) FROM kes_pemeriksaan GROUP BY penduduk_id
            )
            ORDER BY p.z_score_tb_u ASC
        ", [$kodeDesa])->getResultArray();
    }

    /**
     * Get monthly trend
     */
    public function getMonthlyTrend(string $kodeDesa, int $tahun): array
    {
        $db = \Config\Database::connect();
        
        $result = $db->query("
            SELECT 
                EXTRACT(MONTH FROM p.tanggal_periksa)::int as bulan,
                COUNT(DISTINCT p.penduduk_id) as total_periksa,
                SUM(CASE WHEN p.indikasi_stunting = 1 THEN 1 ELSE 0 END) as stunting,
                SUM(CASE WHEN p.status_gizi = 'BURUK' THEN 1 ELSE 0 END) as gizi_buruk
            FROM kes_pemeriksaan p
            JOIN kes_posyandu pos ON pos.id = p.posyandu_id
            WHERE pos.kode_desa = ?
            AND EXTRACT(YEAR FROM p.tanggal_periksa)::int = ?
            GROUP BY EXTRACT(MONTH FROM p.tanggal_periksa)::int
            ORDER BY bulan
        ", [$kodeDesa, $tahun])->getResultArray();
        
        // Fill missing months
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = [
                'bulan' => $i,
                'total_periksa' => 0,
                'stunting' => 0,
                'gizi_buruk' => 0,
            ];
        }
        
        foreach ($result as $row) {
            $months[(int)$row['bulan']] = [
                'bulan' => (int) $row['bulan'],
                'total_periksa' => (int) $row['total_periksa'],
                'stunting' => (int) $row['stunting'],
                'gizi_buruk' => (int) $row['gizi_buruk'],
            ];
        }
        
        return array_values($months);
    }
}

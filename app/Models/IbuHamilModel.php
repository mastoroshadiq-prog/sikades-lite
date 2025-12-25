<?php

namespace App\Models;

use CodeIgniter\Model;

class IbuHamilModel extends Model
{
    protected $table            = 'kes_ibu_hamil';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'posyandu_id',
        'penduduk_id',
        'tanggal_hpht',
        'taksiran_persalinan',
        'usia_kandungan',
        'kehamilan_ke',
        'tinggi_badan_ibu',
        'berat_badan_sebelum',
        'golongan_darah',
        'resiko_tinggi',
        'faktor_resiko',
        'pemeriksaan_k1',
        'pemeriksaan_k2',
        'pemeriksaan_k3',
        'pemeriksaan_k4',
        'status',
        'tanggal_persalinan',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get ibu hamil with penduduk info
     */
    public function getWithPenduduk(int $posyanduId, array $filters = []): array
    {
        $builder = $this->select('kes_ibu_hamil.*, p.nik, p.nama_lengkap, p.tanggal_lahir, k.alamat, k.dusun')
            ->join('pop_penduduk p', 'p.id = kes_ibu_hamil.penduduk_id')
            ->join('pop_keluarga k', 'k.id = p.keluarga_id')
            ->where('kes_ibu_hamil.posyandu_id', $posyanduId);

        if (!empty($filters['status'])) {
            $builder->where('kes_ibu_hamil.status', $filters['status']);
        }

        if (!empty($filters['risti_only'])) {
            $builder->where('kes_ibu_hamil.resiko_tinggi', true);
        }

        return $builder->orderBy('kes_ibu_hamil.taksiran_persalinan', 'ASC')->findAll();
    }

    /**
     * Get active pregnancies for a village
     */
    public function getActivePregnancies(string $kodeDesa): array
    {
        return $this->select('kes_ibu_hamil.*, p.nik, p.nama_lengkap, k.dusun, pos.nama_posyandu')
            ->join('pop_penduduk p', 'p.id = kes_ibu_hamil.penduduk_id')
            ->join('pop_keluarga k', 'k.id = p.keluarga_id')
            ->join('kes_posyandu pos', 'pos.id = kes_ibu_hamil.posyandu_id')
            ->where('pos.kode_desa', $kodeDesa)
            ->where('kes_ibu_hamil.status', 'HAMIL')
            ->orderBy('kes_ibu_hamil.taksiran_persalinan', 'ASC')
            ->findAll();
    }

    /**
     * Get high-risk pregnancies
     */
    public function getRistiCases(string $kodeDesa): array
    {
        return $this->select('kes_ibu_hamil.*, p.nik, p.nama_lengkap, k.dusun, k.alamat, pos.nama_posyandu, pos.lat, pos.lng')
            ->join('pop_penduduk p', 'p.id = kes_ibu_hamil.penduduk_id')
            ->join('pop_keluarga k', 'k.id = p.keluarga_id')
            ->join('kes_posyandu pos', 'pos.id = kes_ibu_hamil.posyandu_id')
            ->where('pos.kode_desa', $kodeDesa)
            ->where('kes_ibu_hamil.status', 'HAMIL')
            ->where('kes_ibu_hamil.resiko_tinggi', true)
            ->orderBy('kes_ibu_hamil.taksiran_persalinan', 'ASC')
            ->findAll();
    }

    /**
     * Calculate HPL (Hari Perkiraan Lahir) from HPHT
     * Rumus Naegele: HPHT + 7 hari - 3 bulan + 1 tahun
     */
    public function calculateHPL(string $hpht): string
    {
        $date = new \DateTime($hpht);
        $date->modify('+7 days');
        $date->modify('-3 months');
        $date->modify('+1 year');
        
        return $date->format('Y-m-d');
    }

    /**
     * Calculate usia kandungan in weeks from HPHT
     */
    public function calculateUsiaKandungan(string $hpht): int
    {
        $hphtDate = new \DateTime($hpht);
        $now = new \DateTime();
        $diff = $hphtDate->diff($now);
        
        $days = $diff->days;
        $weeks = floor($days / 7);
        
        return (int) $weeks;
    }

    /**
     * Get pregnancy statistics
     */
    public function getStats(string $kodeDesa): array
    {
        $db = \Config\Database::connect();
        
        $totalHamil = $db->query("
            SELECT COUNT(*) as total FROM kes_ibu_hamil h
            JOIN kes_posyandu pos ON pos.id = h.posyandu_id
            WHERE pos.kode_desa = ? AND h.status = 'HAMIL'
        ", [$kodeDesa])->getRow()->total;
        
        $risti = $db->query("
            SELECT COUNT(*) as total FROM kes_ibu_hamil h
            JOIN kes_posyandu pos ON pos.id = h.posyandu_id
            WHERE pos.kode_desa = ? AND h.status = 'HAMIL' AND h.resiko_tinggi = true
        ", [$kodeDesa])->getRow()->total;
        
        $akanMelahirkan = $db->query("
            SELECT COUNT(*) as total FROM kes_ibu_hamil h
            JOIN kes_posyandu pos ON pos.id = h.posyandu_id
            WHERE pos.kode_desa = ? 
            AND h.status = 'HAMIL'
            AND h.taksiran_persalinan BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL '30 days'
        ", [$kodeDesa])->getRow()->total;
        
        // K1-K4 completion stats
        $k4Complete = $db->query("
            SELECT COUNT(*) as total FROM kes_ibu_hamil h
            JOIN kes_posyandu pos ON pos.id = h.posyandu_id
            WHERE pos.kode_desa = ? 
            AND h.status = 'HAMIL'
            AND h.pemeriksaan_k4 IS NOT NULL
        ", [$kodeDesa])->getRow()->total;
        
        return [
            'total_hamil' => (int) $totalHamil,
            'resiko_tinggi' => (int) $risti,
            'akan_melahirkan_30_hari' => (int) $akanMelahirkan,
            'k4_complete' => (int) $k4Complete,
            'k4_percentage' => $totalHamil > 0 ? round(($k4Complete / $totalHamil) * 100, 1) : 0,
        ];
    }

    /**
     * Risk factors options
     */
    public static function getFaktorResikoOptions(): array
    {
        return [
            'Usia < 20 tahun',
            'Usia > 35 tahun',
            'Tinggi badan < 145 cm',
            'Riwayat keguguran',
            'Riwayat operasi caesar',
            'Anemia (Hb < 11)',
            'Tekanan darah tinggi',
            'Diabetes gestasional',
            'Kehamilan kembar',
            'Jarak kehamilan < 2 tahun',
            'Kehamilan > 4',
            'Kelainan letak janin',
            'Pre-eklampsia',
            'Lain-lain',
        ];
    }
}

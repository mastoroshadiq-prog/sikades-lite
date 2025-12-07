<?php

namespace App\Models;

use CodeIgniter\Model;

class MutasiModel extends Model
{
    protected $table            = 'pop_mutasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'penduduk_id',
        'jenis_mutasi',
        'tanggal_peristiwa',
        'keterangan',
        'dokumen_pendukung',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'penduduk_id'       => 'required|integer',
        'jenis_mutasi'      => 'required|in_list[KELAHIRAN,KEMATIAN,PINDAH_MASUK,PINDAH_KELUAR,PERUBAHAN_DATA]',
        'tanggal_peristiwa' => 'required|valid_date',
    ];

    /**
     * Get mutasi dengan info penduduk
     */
    public function getWithPenduduk(string $kodeDesa, array $filters = []): array
    {
        $builder = $this->select('pop_mutasi.*, pop_penduduk.nik, pop_penduduk.nama_lengkap, pop_keluarga.no_kk')
            ->join('pop_penduduk', 'pop_penduduk.id = pop_mutasi.penduduk_id')
            ->join('pop_keluarga', 'pop_keluarga.id = pop_penduduk.keluarga_id')
            ->where('pop_keluarga.kode_desa', $kodeDesa);

        if (!empty($filters['jenis_mutasi'])) {
            $builder->where('pop_mutasi.jenis_mutasi', $filters['jenis_mutasi']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(pop_mutasi.tanggal_peristiwa)', $filters['tahun']);
        }

        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(pop_mutasi.tanggal_peristiwa)', $filters['bulan']);
        }

        return $builder
            ->orderBy('pop_mutasi.tanggal_peristiwa', 'DESC')
            ->findAll();
    }

    /**
     * Get mutasi statistics per tahun
     */
    public function getYearlyStats(string $kodeDesa, int $tahun): array
    {
        $db = \Config\Database::connect();
        
        $result = $db->query("
            SELECT 
                jenis_mutasi,
                COUNT(*) as jumlah
            FROM pop_mutasi m
            JOIN pop_penduduk p ON p.id = m.penduduk_id
            JOIN pop_keluarga k ON k.id = p.keluarga_id
            WHERE k.kode_desa = ? AND YEAR(m.tanggal_peristiwa) = ?
            GROUP BY jenis_mutasi
        ", [$kodeDesa, $tahun])->getResultArray();

        $stats = [
            'KELAHIRAN' => 0,
            'KEMATIAN' => 0,
            'PINDAH_MASUK' => 0,
            'PINDAH_KELUAR' => 0,
            'PERUBAHAN_DATA' => 0,
        ];

        foreach ($result as $row) {
            $stats[$row['jenis_mutasi']] = (int) $row['jumlah'];
        }

        return $stats;
    }

    /**
     * Get monthly mutasi for chart
     */
    public function getMonthlyStats(string $kodeDesa, int $tahun): array
    {
        $db = \Config\Database::connect();
        
        $result = $db->query("
            SELECT 
                MONTH(m.tanggal_peristiwa) as bulan,
                SUM(CASE WHEN m.jenis_mutasi = 'KELAHIRAN' THEN 1 ELSE 0 END) as kelahiran,
                SUM(CASE WHEN m.jenis_mutasi = 'KEMATIAN' THEN 1 ELSE 0 END) as kematian,
                SUM(CASE WHEN m.jenis_mutasi = 'PINDAH_MASUK' THEN 1 ELSE 0 END) as pindah_masuk,
                SUM(CASE WHEN m.jenis_mutasi = 'PINDAH_KELUAR' THEN 1 ELSE 0 END) as pindah_keluar
            FROM pop_mutasi m
            JOIN pop_penduduk p ON p.id = m.penduduk_id
            JOIN pop_keluarga k ON k.id = p.keluarga_id
            WHERE k.kode_desa = ? AND YEAR(m.tanggal_peristiwa) = ?
            GROUP BY MONTH(m.tanggal_peristiwa)
            ORDER BY bulan
        ", [$kodeDesa, $tahun])->getResultArray();

        // Build monthly array with all 12 months
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = [
                'bulan' => $i,
                'kelahiran' => 0,
                'kematian' => 0,
                'pindah_masuk' => 0,
                'pindah_keluar' => 0,
            ];
        }

        foreach ($result as $row) {
            $months[(int)$row['bulan']] = [
                'bulan' => (int)$row['bulan'],
                'kelahiran' => (int)$row['kelahiran'],
                'kematian' => (int)$row['kematian'],
                'pindah_masuk' => (int)$row['pindah_masuk'],
                'pindah_keluar' => (int)$row['pindah_keluar'],
            ];
        }

        return array_values($months);
    }

    /**
     * Record kematian (death) event
     */
    public function recordKematian(int $pendudukId, string $tanggal, ?string $keterangan = null, ?int $userId = null): bool
    {
        $pendudukModel = model('PendudukModel');
        
        // Update status penduduk
        $pendudukModel->update($pendudukId, ['status_dasar' => 'MATI']);
        
        // Record mutasi
        return $this->insert([
            'penduduk_id'       => $pendudukId,
            'jenis_mutasi'      => 'KEMATIAN',
            'tanggal_peristiwa' => $tanggal,
            'keterangan'        => $keterangan,
            'created_by'        => $userId,
        ]);
    }

    /**
     * Record pindah keluar
     */
    public function recordPindahKeluar(int $pendudukId, string $tanggal, ?string $keterangan = null, ?int $userId = null): bool
    {
        $pendudukModel = model('PendudukModel');
        
        // Update status penduduk
        $pendudukModel->update($pendudukId, ['status_dasar' => 'PINDAH']);
        
        // Record mutasi
        return $this->insert([
            'penduduk_id'       => $pendudukId,
            'jenis_mutasi'      => 'PINDAH_KELUAR',
            'tanggal_peristiwa' => $tanggal,
            'keterangan'        => $keterangan,
            'created_by'        => $userId,
        ]);
    }

    /**
     * Get jenis mutasi options
     */
    public static function getJenisMutasiOptions(): array
    {
        return [
            'KELAHIRAN'     => 'Kelahiran',
            'KEMATIAN'      => 'Kematian',
            'PINDAH_MASUK'  => 'Pindah Masuk',
            'PINDAH_KELUAR' => 'Pindah Keluar',
            'PERUBAHAN_DATA' => 'Perubahan Data',
        ];
    }
}

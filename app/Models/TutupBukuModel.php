<?php

namespace App\Models;

use CodeIgniter\Model;

class TutupBukuModel extends Model
{
    protected $table = 'tutup_buku';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'kode_desa',
        'tahun',
        'status',
        'saldo_awal',
        'total_pendapatan',
        'total_belanja',
        'saldo_akhir',
        'tanggal_tutup',
        'closed_by',
        'catatan'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get tutup buku by year
     */
    public function getByTahun(string $kodeDesa, int $tahun)
    {
        return $this->where('kode_desa', $kodeDesa)
                    ->where('tahun', $tahun)
                    ->first();
    }

    /**
     * Get all years with status
     */
    public function getAllYears(string $kodeDesa)
    {
        return $this->where('kode_desa', $kodeDesa)
                    ->orderBy('tahun', 'DESC')
                    ->findAll();
    }

    /**
     * Check if year is closed
     */
    public function isClosed(string $kodeDesa, int $tahun): bool
    {
        $record = $this->getByTahun($kodeDesa, $tahun);
        return $record && $record['status'] === 'Closed';
    }

    /**
     * Check if year is open for transactions
     */
    public function isOpen(string $kodeDesa, int $tahun): bool
    {
        $record = $this->getByTahun($kodeDesa, $tahun);
        return !$record || $record['status'] === 'Open';
    }

    /**
     * Get or create year record
     */
    public function getOrCreate(string $kodeDesa, int $tahun): array
    {
        $record = $this->getByTahun($kodeDesa, $tahun);
        
        if (!$record) {
            // Get previous year's saldo_akhir as saldo_awal
            $prevYear = $this->getByTahun($kodeDesa, $tahun - 1);
            $saldoAwal = $prevYear ? $prevYear['saldo_akhir'] : 0;
            
            $this->insert([
                'kode_desa' => $kodeDesa,
                'tahun' => $tahun,
                'status' => 'Open',
                'saldo_awal' => $saldoAwal
            ]);
            
            $record = $this->getByTahun($kodeDesa, $tahun);
        }
        
        return $record;
    }

    /**
     * Calculate year summary from BKU
     */
    public function calculateYearSummary(string $kodeDesa, int $tahun): array
    {
        $db = \Config\Database::connect();
        
        // Get saldo awal (from previous closed year or 0)
        $prevYear = $this->getByTahun($kodeDesa, $tahun - 1);
        $saldoAwal = $prevYear && $prevYear['status'] === 'Closed' 
                     ? (float)$prevYear['saldo_akhir'] 
                     : 0;
        
        // Calculate total pendapatan (debet)
        $pendapatan = $db->table('bku')
            ->selectSum('debet')
            ->where('kode_desa', $kodeDesa)
            ->where('EXTRACT(YEAR FROM tanggal)::int', $tahun)
            ->get()
            ->getRow();
        
        // Calculate total belanja (kredit)
        $belanja = $db->table('bku')
            ->selectSum('kredit')
            ->where('kode_desa', $kodeDesa)
            ->where('EXTRACT(YEAR FROM tanggal)::int', $tahun)
            ->get()
            ->getRow();
        
        $totalPendapatan = (float)($pendapatan->debet ?? 0);
        $totalBelanja = (float)($belanja->kredit ?? 0);
        $saldoAkhir = $saldoAwal + $totalPendapatan - $totalBelanja;
        
        return [
            'saldo_awal' => $saldoAwal,
            'total_pendapatan' => $totalPendapatan,
            'total_belanja' => $totalBelanja,
            'saldo_akhir' => $saldoAkhir
        ];
    }

    /**
     * Process year-end closing
     */
    public function closeYear(string $kodeDesa, int $tahun, int $userId, string $catatan = null): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Calculate summary
            $summary = $this->calculateYearSummary($kodeDesa, $tahun);
            
            // Get or create record
            $record = $this->getOrCreate($kodeDesa, $tahun);
            
            // Update tutup_buku record
            $this->update($record['id'], [
                'status' => 'Closed',
                'saldo_awal' => $summary['saldo_awal'],
                'total_pendapatan' => $summary['total_pendapatan'],
                'total_belanja' => $summary['total_belanja'],
                'saldo_akhir' => $summary['saldo_akhir'],
                'tanggal_tutup' => date('Y-m-d H:i:s'),
                'closed_by' => $userId,
                'catatan' => $catatan
            ]);
            
            // Lock all BKU transactions for this year
            $db->table('bku')
                ->where('kode_desa', $kodeDesa)
                ->where('EXTRACT(YEAR FROM tanggal)::int', $tahun)
                ->update(['is_locked' => 1]);
            
            // Lock all SPP for this year
            $db->table('spp')
                ->where('kode_desa', $kodeDesa)
                ->where('EXTRACT(YEAR FROM tanggal_spp)::int', $tahun)
                ->update(['is_locked' => 1]);
            
            // Lock APBDes for this year
            $db->table('apbdes')
                ->where('kode_desa', $kodeDesa)
                ->where('tahun', $tahun)
                ->update(['is_locked' => 1]);
            
            // Create next year record with saldo_awal
            $nextYear = $tahun + 1;
            $nextYearRecord = $this->getByTahun($kodeDesa, $nextYear);
            
            if (!$nextYearRecord) {
                $this->insert([
                    'kode_desa' => $kodeDesa,
                    'tahun' => $nextYear,
                    'status' => 'Open',
                    'saldo_awal' => $summary['saldo_akhir']
                ]);
            } else {
                $this->update($nextYearRecord['id'], [
                    'saldo_awal' => $summary['saldo_akhir']
                ]);
            }
            
            $db->transComplete();
            
            return $db->transStatus();
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Year-end closing failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reopen a closed year (admin only, with caution)
     */
    public function reopenYear(string $kodeDesa, int $tahun): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $record = $this->getByTahun($kodeDesa, $tahun);
            
            if (!$record) {
                return false;
            }
            
            // Update status
            $this->update($record['id'], [
                'status' => 'Open',
                'tanggal_tutup' => null,
                'closed_by' => null
            ]);
            
            // Unlock BKU
            $db->table('bku')
                ->where('kode_desa', $kodeDesa)
                ->where('EXTRACT(YEAR FROM tanggal)::int', $tahun)
                ->update(['is_locked' => 0]);
            
            // Unlock SPP
            $db->table('spp')
                ->where('kode_desa', $kodeDesa)
                ->where('EXTRACT(YEAR FROM tanggal_spp)::int', $tahun)
                ->update(['is_locked' => 0]);
            
            // Unlock APBDes
            $db->table('apbdes')
                ->where('kode_desa', $kodeDesa)
                ->where('tahun', $tahun)
                ->update(['is_locked' => 0]);
            
            $db->transComplete();
            
            return $db->transStatus();
            
        } catch (\Exception $e) {
            $db->transRollback();
            return false;
        }
    }

    /**
     * Get years available for closing
     */
    public function getAvailableYears(string $kodeDesa): array
    {
        $db = \Config\Database::connect();
        
        // Get years that have transactions
        $years = $db->table('bku')
            ->select('EXTRACT(YEAR FROM tanggal)::int as tahun')
            ->where('kode_desa', $kodeDesa)
            ->groupBy('EXTRACT(YEAR FROM tanggal)::int')
            ->orderBy('tahun', 'DESC')
            ->get()
            ->getResultArray();
        
        $result = [];
        foreach ($years as $y) {
            $tahun = (int)$y['tahun'];
            $record = $this->getByTahun($kodeDesa, $tahun);
            $result[] = [
                'tahun' => $tahun,
                'status' => $record ? $record['status'] : 'Open',
                'record' => $record
            ];
        }
        
        return $result;
    }
}

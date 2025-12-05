<?php

namespace App\Models;

use CodeIgniter\Model;

class BkuModel extends Model
{
    protected $table = 'bku';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'kode_desa',
        'tanggal',
        'nomor_bukti',
        'uraian',
        'jenis_transaksi',
        'debet',
        'kredit',
        'saldo_kumulatif',
        'spp_id',
    ];

    protected $validationRules = [
        'kode_desa' => 'required',
        'tanggal' => 'required|valid_date',
        'nomor_bukti' => 'required',
        'uraian' => 'required',
        'jenis_transaksi' => 'required|in_list[Pendapatan,Belanja,Mutasi]',
    ];

    /**
     * Calculate saldo for BKU report
     *
     * @param string $kodeDesa
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function calculateSaldo(string $kodeDesa, ?string $startDate = null, ?string $endDate = null): array
    {
        $builder = $this->where('kode_desa', $kodeDesa);
        
        if ($startDate) {
            $builder->where('tanggal >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('tanggal <=', $endDate);
        }
        
        $transactions = $builder->orderBy('tanggal', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
        
        $saldo = 0;
        $result = [];
        
        foreach ($transactions as $transaction) {
            $saldo += ($transaction['debet'] - $transaction['kredit']);
            $transaction['saldo'] = $saldo;
            $result[] = $transaction;
        }
        
        return $result;
    }

    /**
     * Get total pendapatan
     *
     * @param string $kodeDesa
     * @return float
     */
    public function getTotalPendapatan(string $kodeDesa): float
    {
        $result = $this->where('kode_desa', $kodeDesa)
            ->selectSum('debet')
            ->first();
        
        return (float) ($result['debet'] ?? 0);
    }

    /**
     * Get total belanja
     *
     * @param string $kodeDesa
     * @return float
     */
    public function getTotalBelanja(string $kodeDesa): float
    {
        $result = $this->where('kode_desa', $kodeDesa)
            ->selectSum('kredit')
            ->first();
        
        return (float) ($result['kredit'] ?? 0);
    }
}

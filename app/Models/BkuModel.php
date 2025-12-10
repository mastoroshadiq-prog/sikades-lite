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
        'no_bukti',
        'uraian',
        'ref_rekening_id',
        'jenis_transaksi',
        'debet',
        'kredit',
        'saldo_kumulatif',
        'spp_id',
        'bukti_file',
    ];

    protected $validationRules = [
        'kode_desa' => 'required',
        'tanggal' => 'required|valid_date',
        'no_bukti' => 'required',
        'uraian' => 'required',
        'jenis_transaksi' => 'required|in_list[Pendapatan,Belanja,Mutasi]',
    ];

    /**
     * Get BKU with running balance and rekening info
     *
     * @param string $kodeDesa
     * @param int|null $tahun
     * @param string|null $bulan
     * @return array
     */
    public function getBkuWithBalance(string $kodeDesa, ?int $tahun = null, ?string $bulan = null): array
    {
        $builder = $this->select('bku.*, ref_rekening.kode_akun, ref_rekening.nama_akun')
            ->join('ref_rekening', 'ref_rekening.id = bku.ref_rekening_id', 'left')
            ->where('bku.kode_desa', $kodeDesa);
        
        if ($tahun) {
            $builder->where('EXTRACT(YEAR FROM bku.tanggal)::int', $tahun);
        }
        
        if ($bulan) {
            $builder->where('EXTRACT(MONTH FROM bku.tanggal)::int', $bulan);
        }
        
        return $builder->orderBy('bku.tanggal', 'ASC')
            ->orderBy('bku.id', 'ASC')
            ->findAll();
    }

    /**
     * Get last balance before a certain date
     *
     * @param string $kodeDesa
     * @param string $beforeDate
     * @return float
     */
    public function getLastBalance(string $kodeDesa, string $beforeDate): float
    {
        $result = $this->select('saldo_kumulatif')
            ->where('kode_desa', $kodeDesa)
            ->where('tanggal <', $beforeDate)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();
        
        return (float) ($result['saldo_kumulatif'] ?? 0);
    }

    /**
     * Get total debet
     *
     * @param string $kodeDesa
     * @param int|null $tahun
     * @param string|null $bulan
     * @return float
     */
    public function getTotalDebet(string $kodeDesa, ?int $tahun = null, ?string $bulan = null): float
    {
        $builder = $this->selectSum('debet')
            ->where('kode_desa', $kodeDesa);
        
        if ($tahun) {
            $builder->where('EXTRACT(YEAR FROM tanggal)::int', $tahun);
        }
        
        if ($bulan) {
            $builder->where('EXTRACT(MONTH FROM tanggal)::int', $bulan);
        }
        
        $result = $builder->first();
        return (float) ($result['debet'] ?? 0);
    }

    /**
     * Get total kredit
     *
     * @param string $kodeDesa
     * @param int|null $tahun
     * @param string|null $bulan
     * @return float
     */
    public function getTotalKredit(string $kodeDesa, ?int $tahun = null, ?string $bulan = null): float
    {
        $builder = $this->selectSum('kredit')
            ->where('kode_desa', $kodeDesa);
        
        if ($tahun) {
            $builder->where('EXTRACT(YEAR FROM tanggal)::int', $tahun);
        }
        
        if ($bulan) {
            $builder->where('EXTRACT(MONTH FROM tanggal)::int', $bulan);
        }
        
        $result = $builder->first();
        return (float) ($result['kredit'] ?? 0);
    }

    /**
     * Recalculate balances from a certain date forward
     *
     * @param string $kodeDesa
     * @param string $fromDate
     * @return void
     */
    public function recalculateBalances(string $kodeDesa, string $fromDate): void
    {
        // Get all transactions from the date forward
        $transactions = $this->where('kode_desa', $kodeDesa)
            ->where('tanggal >=', $fromDate)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
        
        // Get balance before this date
        $previousBalance = $this->getLastBalance($kodeDesa, $fromDate);
        $runningBalance = $previousBalance;
        
        // Update each transaction's balance
        foreach ($transactions as $transaction) {
            $runningBalance += ($transaction['debet'] - $transaction['kredit']);
            $this->update($transaction['id'], ['saldo_kumulatif' => $runningBalance]);
        }
    }

    /**
     * Get total pendapatan (legacy method)
     *
     * @param string $kodeDesa
     * @return float
     */
    public function getTotalPendapatan(string $kodeDesa): float
    {
        return $this->getTotalDebet($kodeDesa);
    }

    /**
     * Get total belanja (legacy method)
     *
     * @param string $kodeDesa
     * @return float
     */
    public function getTotalBelanja(string $kodeDesa): float
    {
        return $this->getTotalKredit($kodeDesa);
    }
}

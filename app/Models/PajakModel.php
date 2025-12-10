<?php

namespace App\Models;

use CodeIgniter\Model;

class PajakModel extends Model
{
    protected $table = 'pajak';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'bku_id',
        'jenis_pajak',
        'tarif',
        'jumlah_pajak',
        'npwp',
        'nama_wajib_pajak',
        'tanggal_pajak',
        'status_pembayaran',
        'tanggal_setor',
        'nomor_bukti_setor',
    ];

    protected $validationRules = [
        'bku_id' => 'required|integer',
        'jenis_pajak' => 'required|in_list[PPN,PPh]',
        'tarif' => 'required|decimal',
        'nama_wajib_pajak' => 'required',
        'status_pembayaran' => 'required|in_list[Belum,Sudah]',
    ];

    /**
     * Get pajak with BKU details
     *
     * @param string $kodeDesa
     * @param int|null $tahun
     * @return array
     */
    public function getPajakWithBKU(string $kodeDesa, ?int $tahun = null): array
    {
        $builder = $this->select('pajak.*, bku.no_bukti, bku.uraian, bku.tanggal, bku.kredit')
            ->join('bku', 'bku.id = pajak.bku_id')
            ->where('bku.kode_desa', $kodeDesa);
        
        if ($tahun) {
            $builder->where('EXTRACT(YEAR FROM pajak.tanggal_pajak)::int', $tahun);
        }
        
        return $builder->orderBy('pajak.tanggal_pajak', 'DESC')->findAll();
    }

    /**
     * Get total by jenis pajak
     *
     * @param string $kodeDesa
     * @param string $jenisPajak
     * @param int|null $tahun
     * @return float
     */
    public function getTotalByJenis(string $kodeDesa, string $jenisPajak, ?int $tahun = null): float
    {
        $builder = $this->selectSum('pajak.jumlah_pajak')
            ->join('bku', 'bku.id = pajak.bku_id')
            ->where('bku.kode_desa', $kodeDesa)
            ->where('pajak.jenis_pajak', $jenisPajak);
        
        if ($tahun) {
            $builder->where('EXTRACT(YEAR FROM pajak.tanggal_pajak)::int', $tahun);
        }
        
        $result = $builder->first();
        return (float) ($result['jumlah_pajak'] ?? 0);
    }

    /**
     * Get total unpaid tax
     *
     * @param string $kodeDesa
     * @param int|null $tahun
     * @return float
     */
    public function getTotalBelumBayar(string $kodeDesa, ?int $tahun = null): float
    {
        $builder = $this->selectSum('pajak.jumlah_pajak')
            ->join('bku', 'bku.id = pajak.bku_id')
            ->where('bku.kode_desa', $kodeDesa)
            ->where('pajak.status_pembayaran', 'Belum');
        
        if ($tahun) {
            $builder->where('EXTRACT(YEAR FROM pajak.tanggal_pajak)::int', $tahun);
        }
        
        $result = $builder->first();
        return (float) ($result['jumlah_pajak'] ?? 0);
    }

    /**
     * Get total paid tax
     *
     * @param string $kodeDesa
     * @param int|null $tahun
     * @return float
     */
    public function getTotalSudahBayar(string $kodeDesa, ?int $tahun = null): float
    {
        $builder = $this->selectSum('pajak.jumlah_pajak')
            ->join('bku', 'bku.id = pajak.bku_id')
            ->where('bku.kode_desa', $kodeDesa)
            ->where('pajak.status_pembayaran', 'Sudah');
        
        if ($tahun) {
            $builder->where('EXTRACT(YEAR FROM pajak.tanggal_pajak)::int', $tahun);
        }
        
        $result = $builder->first();
        return (float) ($result['jumlah_pajak'] ?? 0);
    }
}

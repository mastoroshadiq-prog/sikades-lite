<?php

namespace App\Models;

use CodeIgniter\Model;

class ApbdesModel extends Model
{
    protected $table = 'apbdes';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'kode_desa',
        'tahun',
        'ref_rekening_id',
        'uraian',
        'anggaran',
        'sumber_dana',
    ];

    protected $validationRules = [
        'kode_desa' => 'required',
        'tahun' => 'required|integer',
        'ref_rekening_id' => 'required|integer',
        'uraian' => 'required',
        'anggaran' => 'required|decimal',
        'sumber_dana' => 'required|in_list[DDS,ADD,PAD,Bankeu]',
    ];

    /**
     * Get total anggaran by kode desa
     *
     * @param string $kodeDesa
     * @param int|null $tahun
     * @return float
     */
    public function getTotalAnggaran(string $kodeDesa, ?int $tahun = null): float
    {
        $builder = $this->where('kode_desa', $kodeDesa);
        
        if ($tahun) {
            $builder->where('tahun', $tahun);
        }
        
        $result = $builder->selectSum('anggaran')->first();
        
        return (float) ($result['anggaran'] ?? 0);
    }

    /**
     * Get anggaran with rekening details
     *
     * @param string $kodeDesa
     * @param int|null $tahun
     * @return array
     */
    public function getAnggaranWithRekening(string $kodeDesa, ?int $tahun = null): array
    {
        $builder = $this->select('apbdes.*, ref_rekening.kode_akun, ref_rekening.nama_akun, ref_rekening.level')
            ->join('ref_rekening', 'ref_rekening.id = apbdes.ref_rekening_id')
            ->where('apbdes.kode_desa', $kodeDesa);
        
        if ($tahun) {
            $builder->where('apbdes.tahun', $tahun);
        }
        
        return $builder->orderBy('ref_rekening.kode_akun', 'ASC')->findAll();
    }
}

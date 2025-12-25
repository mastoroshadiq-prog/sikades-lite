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
        'rkpdesa_id',      // Optional link to RKP Desa
        'kegiatan_id',     // Optional link to Kegiatan from RKP
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
    
    /**
     * Get anggaran with RKP and Kegiatan details
     *
     * @param string $kodeDesa
     * @param int|null $tahun
     * @return array
     */
    public function getAnggaranWithRkpDetails(string $kodeDesa, ?int $tahun = null): array
    {
        $builder = $this->select('apbdes.*, 
                ref_rekening.kode_akun, ref_rekening.nama_akun, ref_rekening.level,
                rkpdesa.tahun as rkp_tahun, rkpdesa.tema as rkp_tema,
                kegiatan.nama_kegiatan, kegiatan.lokasi as kegiatan_lokasi')
            ->join('ref_rekening', 'ref_rekening.id = apbdes.ref_rekening_id', 'left')
            ->join('rkpdesa', 'rkpdesa.id = apbdes.rkpdesa_id', 'left')
            ->join('kegiatan', 'kegiatan.id = apbdes.kegiatan_id', 'left')
            ->where('apbdes.kode_desa', $kodeDesa);
        
        if ($tahun) {
            $builder->where('apbdes.tahun', $tahun);
        }
        
        return $builder->orderBy('ref_rekening.kode_akun', 'ASC')->findAll();
    }
    
    /**
     * Get APBDes items linked to specific RKP
     *
     * @param int $rkpdesaId
     * @return array
     */
    public function getByRkpdesa(int $rkpdesaId): array
    {
        return $this->select('apbdes.*, ref_rekening.kode_akun, ref_rekening.nama_akun')
            ->join('ref_rekening', 'ref_rekening.id = apbdes.ref_rekening_id', 'left')
            ->where('apbdes.rkpdesa_id', $rkpdesaId)
            ->orderBy('ref_rekening.kode_akun', 'ASC')
            ->findAll();
    }
    
    /**
     * Get APBDes items linked to specific Kegiatan
     *
     * @param int $kegiatanId
     * @return array
     */
    public function getByKegiatan(int $kegiatanId): array
    {
        return $this->select('apbdes.*, ref_rekening.kode_akun, ref_rekening.nama_akun')
            ->join('ref_rekening', 'ref_rekening.id = apbdes.ref_rekening_id', 'left')
            ->where('apbdes.kegiatan_id', $kegiatanId)
            ->orderBy('ref_rekening.kode_akun', 'ASC')
            ->findAll();
    }
}


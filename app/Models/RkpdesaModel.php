<?php

namespace App\Models;

use CodeIgniter\Model;

class RkpdesaModel extends Model
{
    protected $table = 'rkpdesa';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'rpjmdesa_id',
        'kode_desa',
        'tahun',
        'tema',
        'prioritas',
        'status',
        'nomor_perdes',
        'tanggal_perdes',
        'total_pagu',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'kode_desa' => 'required',
        'tahun' => 'required|numeric',
    ];

    /**
     * Get RKP with RPJM info
     */
    public function getWithRpjm(string $kodeDesa, int $tahun = null)
    {
        $builder = $this->select('rkpdesa.*, rpjmdesa.visi, rpjmdesa.misi, 
                    rpjmdesa.tahun_awal, rpjmdesa.tahun_akhir')
                    ->join('rpjmdesa', 'rpjmdesa.id = rkpdesa.rpjmdesa_id', 'left')
                    ->where('rkpdesa.kode_desa', $kodeDesa);
        
        if ($tahun) {
            $builder->where('rkpdesa.tahun', $tahun);
        }
        
        return $builder->orderBy('rkpdesa.tahun', 'DESC')->findAll();
    }

    /**
     * Get RKP by year
     */
    public function getByTahun(string $kodeDesa, int $tahun)
    {
        return $this->where('kode_desa', $kodeDesa)
                    ->where('tahun', $tahun)
                    ->first();
    }

    /**
     * Get RKP with kegiatan count
     */
    public function getWithKegiatanCount(string $kodeDesa)
    {
        return $this->select('rkpdesa.*, 
                    (SELECT COUNT(*) FROM kegiatan WHERE kegiatan.rkpdesa_id = rkpdesa.id) as jumlah_kegiatan,
                    (SELECT SUM(pagu_anggaran) FROM kegiatan WHERE kegiatan.rkpdesa_id = rkpdesa.id) as total_anggaran')
                    ->where('kode_desa', $kodeDesa)
                    ->orderBy('tahun', 'DESC')
                    ->findAll();
    }

    /**
     * Update total pagu from kegiatan
     */
    public function updateTotalPagu(int $id)
    {
        $db = \Config\Database::connect();
        $total = $db->table('kegiatan')
                    ->selectSum('pagu_anggaran')
                    ->where('rkpdesa_id', $id)
                    ->get()
                    ->getRow();
        
        return $this->update($id, ['total_pagu' => $total->pagu_anggaran ?? 0]);
    }
}

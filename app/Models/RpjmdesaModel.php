<?php

namespace App\Models;

use CodeIgniter\Model;

class RpjmdesaModel extends Model
{
    protected $table = 'rpjmdesa';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'kode_desa',
        'tahun_awal',
        'tahun_akhir',
        'visi',
        'misi',
        'tujuan',
        'sasaran',
        'status',
        'nomor_perdes',
        'tanggal_perdes',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'kode_desa' => 'required',
        'tahun_awal' => 'required|numeric',
        'tahun_akhir' => 'required|numeric',
    ];

    /**
     * Get RPJM Desa yang aktif
     */
    public function getAktif(string $kodeDesa)
    {
        return $this->where('kode_desa', $kodeDesa)
                    ->where('status', 'Aktif')
                    ->first();
    }

    /**
     * Get RPJM with RKP count
     */
    public function getWithRkpCount(string $kodeDesa)
    {
        return $this->select('rpjmdesa.*, 
                    (SELECT COUNT(*) FROM rkpdesa WHERE rkpdesa.rpjmdesa_id = rpjmdesa.id) as jumlah_rkp,
                    (SELECT SUM(total_pagu) FROM rkpdesa WHERE rkpdesa.rpjmdesa_id = rpjmdesa.id) as total_pagu')
                    ->where('kode_desa', $kodeDesa)
                    ->orderBy('tahun_awal', 'DESC')
                    ->findAll();
    }

    /**
     * Get periode tahun RPJM
     */
    public function getPeriode(int $id): string
    {
        $rpjm = $this->find($id);
        if ($rpjm) {
            return $rpjm['tahun_awal'] . ' - ' . $rpjm['tahun_akhir'];
        }
        return '-';
    }
}

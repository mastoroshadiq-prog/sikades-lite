<?php

namespace App\Models;

use CodeIgniter\Model;

class SppModel extends Model
{
    protected $table = 'spp';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'no_spp',
        'tanggal',
        'kode_desa',
        'keterangan',
        'jumlah_total',
        'status',
    ];

    protected $validationRules = [
        'no_spp' => 'required',
        'tanggal' => 'required|valid_date',
        'kode_desa' => 'required',
        'keterangan' => 'required',
        'jumlah_total' => 'required|decimal',
        'status' => 'required|in_list[Draft,Verified,Approved]',
    ];

    /**
     * Get SPP with details
     *
     * @param int $id
     * @return array|null
     */
    public function getWithDetails(int $id): ?array
    {
        $spp = $this->find($id);
        
        if (!$spp) {
            return null;
        }
        
        // Get SPP details
        $sppRincianModel = new SppRincianModel();
        $spp['details'] = $sppRincianModel->where('spp_id', $id)
            ->join('apbdes', 'apbdes.id = spp_rincian.apbdes_id')
            ->join('ref_rekening', 'ref_rekening.id = apbdes.ref_rekening_id')
            ->select('spp_rincian.*, apbdes.uraian, ref_rekening.kode_akun, ref_rekening.nama_akun')
            ->findAll();
        
        return $spp;
    }
}

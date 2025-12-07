<?php

namespace App\Models;

use CodeIgniter\Model;

class SppModel extends Model
{
    protected $table = 'spp';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
    
    protected $allowedFields = [
        'kode_desa',
        'nomor_spp',
        'tanggal_spp',
        'uraian',
        'jumlah',
        'status',
        'created_by',
        'verified_by',
        'approved_by',
        'bukti_file',
    ];

    protected $validationRules = [
        'nomor_spp' => 'required',
        'tanggal_spp' => 'required|valid_date',
        'kode_desa' => 'required',
        'uraian' => 'required',
        'status' => 'required|in_list[Draft,Verified,Approved]',
    ];

    /**
     * Get SPP list with basic info
     *
     * @param string $kodeDesa
     * @param string $status
     * @param int|null $tahun
     * @return array
     */
    public function getSppWithDetails(string $kodeDesa, string $status = '', ?int $tahun = null): array
    {
        $builder = $this->where('kode_desa', $kodeDesa);
        
        if ($status) {
            $builder->where('status', $status);
        }
        
        if ($tahun) {
            $builder->where('YEAR(tanggal_spp)', $tahun);
        }
        
        return $builder->orderBy('tanggal_spp', 'DESC')->findAll();
    }

    /**
     * Get SPP with rincian details
     *
     * @param int $id
     * @return array|null
     */
    public function getDetailWithRincian(int $id): ?array
    {
        $spp = $this->find($id);
        
        if (!$spp) {
            return null;
        }
        
        // Get SPP rincian with budget details
        $sppRincianModel = new \App\Models\SppRincianModel();
        $spp['rincian'] = $sppRincianModel
            ->select('spp_rincian.*, ref_rekening.kode_akun, ref_rekening.nama_akun')
            ->join('apbdes', 'apbdes.id = spp_rincian.apbdes_id')
            ->join('ref_rekening', 'ref_rekening.id = apbdes.ref_rekening_id')
            ->where('spp_rincian.spp_id', $id)
            ->findAll();
        
        return $spp;
    }

    /**
     * Get total SPP by status
     *
     * @param string $kodeDesa
     * @param string $status
     * @return float
     */
    public function getTotalByStatus(string $kodeDesa, string $status): float
    {
        $result = $this->selectSum('jumlah')
            ->where('kode_desa', $kodeDesa)
            ->where('status', $status)
            ->first();
        
        return $result['jumlah'] ?? 0;
    }
}

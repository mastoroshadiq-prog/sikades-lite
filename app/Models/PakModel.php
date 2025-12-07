<?php

namespace App\Models;

use CodeIgniter\Model;

class PakModel extends Model
{
    protected $table = 'pak';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'kode_desa', 'tahun', 'nomor_pak', 'tanggal_pak',
        'keterangan', 'status', 'created_by', 'approved_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get PAK with details
     */
    public function getPakWithDetails(string $kodeDesa, int $tahun = null)
    {
        $builder = $this->select('pak.*, users.username as created_by_name')
            ->join('users', 'users.id = pak.created_by', 'left')
            ->where('pak.kode_desa', $kodeDesa);
        
        if ($tahun) {
            $builder->where('pak.tahun', $tahun);
        }
        
        return $builder->orderBy('pak.tanggal_pak', 'DESC')->findAll();
    }

    /**
     * Get single PAK with all details
     */
    public function getDetailWithItems(int $id)
    {
        $pak = $this->select('pak.*, users.username as created_by_name, 
                             approver.username as approved_by_name')
            ->join('users', 'users.id = pak.created_by', 'left')
            ->join('users as approver', 'approver.id = pak.approved_by', 'left')
            ->find($id);
        
        if ($pak) {
            $pakDetailModel = new PakDetailModel();
            $pak['items'] = $pakDetailModel->getItemsWithApbdes($id);
            
            // Calculate totals
            $pak['total_sebelum'] = array_sum(array_column($pak['items'], 'anggaran_sebelum'));
            $pak['total_sesudah'] = array_sum(array_column($pak['items'], 'anggaran_sesudah'));
            $pak['total_selisih'] = array_sum(array_column($pak['items'], 'selisih'));
        }
        
        return $pak;
    }

    /**
     * Generate PAK number
     */
    public function generateNomorPak(string $kodeDesa, int $tahun): string
    {
        $count = $this->where('kode_desa', $kodeDesa)
            ->where('tahun', $tahun)
            ->countAllResults();
        
        $urut = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        return "PAK/{$kodeDesa}/{$urut}/{$tahun}";
    }

    /**
     * Apply PAK to APBDes
     */
    public function applyPak(int $pakId): bool
    {
        $pak = $this->find($pakId);
        
        if (!$pak || $pak['status'] !== 'Disetujui') {
            return false;
        }
        
        $pakDetailModel = new PakDetailModel();
        $apbdesModel = new ApbdesModel();
        
        $items = $pakDetailModel->where('pak_id', $pakId)->findAll();
        
        foreach ($items as $item) {
            $apbdesModel->update($item['apbdes_id'], [
                'anggaran' => $item['anggaran_sesudah']
            ]);
        }
        
        return true;
    }
}

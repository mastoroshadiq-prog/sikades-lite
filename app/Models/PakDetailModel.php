<?php

namespace App\Models;

use CodeIgniter\Model;

class PakDetailModel extends Model
{
    protected $table = 'pak_detail';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'pak_id', 'apbdes_id', 'anggaran_sebelum', 
        'anggaran_sesudah', 'selisih', 'keterangan'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = false;

    /**
     * Get items with APBDes info
     */
    public function getItemsWithApbdes(int $pakId)
    {
        return $this->select('pak_detail.*, apbdes.uraian as nama_anggaran, 
                             apbdes.sumber_dana, ref_rekening.kode_akun, ref_rekening.nama_akun')
            ->join('apbdes', 'apbdes.id = pak_detail.apbdes_id', 'left')
            ->join('ref_rekening', 'ref_rekening.id = apbdes.ref_rekening_id', 'left')
            ->where('pak_detail.pak_id', $pakId)
            ->findAll();
    }

    /**
     * Calculate selisih before save
     */
    public function calculateSelisih(array $data): array
    {
        if (isset($data['data'])) {
            $data['data']['selisih'] = ($data['data']['anggaran_sesudah'] ?? 0) - ($data['data']['anggaran_sebelum'] ?? 0);
        }
        return $data;
    }

    protected $beforeInsert = ['calculateSelisih'];
    protected $beforeUpdate = ['calculateSelisih'];
}

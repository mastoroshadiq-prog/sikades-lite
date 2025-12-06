<?php

namespace App\Models;

use CodeIgniter\Model;

class RefBidangModel extends Model
{
    protected $table = 'ref_bidang';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'kode_bidang',
        'nama_bidang',
        'deskripsi',
        'urutan'
    ];

    protected $useTimestamps = false;

    /**
     * Get all bidang ordered
     */
    public function getAllOrdered()
    {
        return $this->orderBy('urutan', 'ASC')->findAll();
    }

    /**
     * Get for dropdown
     */
    public function getForDropdown()
    {
        $bidang = $this->getAllOrdered();
        $result = [];
        foreach ($bidang as $b) {
            $result[$b['id']] = $b['kode_bidang'] . ' - ' . $b['nama_bidang'];
        }
        return $result;
    }
}

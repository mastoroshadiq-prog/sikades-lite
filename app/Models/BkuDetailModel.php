<?php

namespace App\Models;

use CodeIgniter\Model;

class BkuDetailModel extends Model
{
    protected $table            = 'bku_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields = [
        'bku_id',
        'nama_item',
        'spesifikasi',
        'satuan',
        'jumlah',
        'harga_satuan',
        'keterangan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'bku_id'       => 'required|integer',
        'nama_item'    => 'required|min_length[2]|max_length[255]',
        'jumlah'       => 'required|numeric|greater_than[0]',
        'harga_satuan' => 'required|numeric|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'bku_id' => [
            'required' => 'ID BKU harus diisi',
        ],
        'nama_item' => [
            'required' => 'Nama item harus diisi',
            'min_length' => 'Nama item minimal 2 karakter',
        ],
        'jumlah' => [
            'required' => 'Jumlah harus diisi',
            'greater_than' => 'Jumlah harus lebih dari 0',
        ],
        'harga_satuan' => [
            'required' => 'Harga satuan harus diisi',
        ],
    ];

    /**
     * Get all detail items for a specific BKU transaction
     */
    public function getDetailsByBkuId(int $bkuId): array
    {
        return $this->where('bku_id', $bkuId)
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    /**
     * Get total amount for a BKU transaction from its details
     */
    public function getTotalByBkuId(int $bkuId): float
    {
        $result = $this->selectSum('subtotal', 'total')
                       ->where('bku_id', $bkuId)
                       ->first();
        return (float)($result['total'] ?? 0);
    }

    /**
     * Get count of items for a BKU transaction
     */
    public function getCountByBkuId(int $bkuId): int
    {
        return $this->where('bku_id', $bkuId)->countAllResults();
    }

    /**
     * Delete all details for a specific BKU
     */
    public function deleteByBkuId(int $bkuId): bool
    {
        return $this->where('bku_id', $bkuId)->delete();
    }

    /**
     * Insert multiple items at once
     */
    public function insertBatch(array $items): bool
    {
        if (empty($items)) {
            return true;
        }
        return $this->insertBatch($items) !== false;
    }

    /**
     * Check if a BKU has any detail items
     */
    public function hasDetails(int $bkuId): bool
    {
        return $this->getCountByBkuId($bkuId) > 0;
    }
}

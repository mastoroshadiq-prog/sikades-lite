<?php

namespace App\Models;

use CodeIgniter\Model;

class AsetKategoriModel extends Model
{
    protected $table            = 'aset_kategori';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_golongan',
        'nama_golongan',
        'uraian',
        'masa_manfaat',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all active categories
     */
    public function getActiveCategories(): array
    {
        return $this->where('is_active', 1)
                    ->orderBy('kode_golongan', 'ASC')
                    ->findAll();
    }

    /**
     * Get category with asset count
     */
    public function getCategoriesWithCount(string $kodeDesa): array
    {
        return $this->select('aset_kategori.*, COUNT(aset_inventaris.id) as jumlah_aset')
                    ->join('aset_inventaris', 'aset_inventaris.kategori_id = aset_kategori.id AND aset_inventaris.kode_desa = "' . $kodeDesa . '"', 'left')
                    ->where('aset_kategori.is_active', 1)
                    ->groupBy('aset_kategori.id')
                    ->orderBy('kode_golongan', 'ASC')
                    ->findAll();
    }

    /**
     * Get dropdown options
     */
    public function getDropdownOptions(): array
    {
        $categories = $this->getActiveCategories();
        $options = [];
        
        foreach ($categories as $cat) {
            $options[$cat['id']] = $cat['kode_golongan'] . ' - ' . $cat['nama_golongan'];
        }
        
        return $options;
    }
}

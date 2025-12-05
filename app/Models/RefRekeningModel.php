<?php

namespace App\Models;

use CodeIgniter\Model;

class RefRekeningModel extends Model
{
    protected $table = 'ref_rekening';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'kode_akun',
        'nama_akun',
        'level',
        'parent_id',
    ];

    protected $validationRules = [
        'kode_akun' => 'required',
        'nama_akun' => 'required',
        'level' => 'required|in_list[1,2,3,4]',
    ];

    /**
     * Get rekening tree structure
     *
     * @param int $level
     * @param int|null $parentId
     * @return array
     */
    public function getRekeningTree(int $level = 1, ?int $parentId = null): array
    {
        return $this->where('level', $level)
            ->where('parent_id', $parentId)
            ->orderBy('kode_akun', 'ASC')
            ->findAll();
    }

    /**
     * Get children of a rekening
     *
     * @param int $parentId
     * @return array
     */
    public function getChildren(int $parentId): array
    {
        return $this->where('parent_id', $parentId)
            ->orderBy('kode_akun', 'ASC')
            ->findAll();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class StrukturOrganisasiModel extends Model
{
    protected $table = 'struktur_organisasi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'kode_desa',
        'nama',
        'jabatan',
        'nip',
        'pangkat_golongan',
        'pendidikan',
        'tanggal_lahir',
        'tanggal_pengangkatan',
        'no_sk',
        'foto',
        'urutan',
        'aktif',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'kode_desa' => 'required',
        'nama' => 'required|min_length[3]|max_length[255]',
        'jabatan' => 'required|max_length[100]',
    ];

    protected $validationMessages = [
        'nama' => [
            'required' => 'Nama perangkat harus diisi',
            'min_length' => 'Nama minimal 3 karakter',
        ],
        'jabatan' => [
            'required' => 'Jabatan harus diisi',
        ],
    ];

    /**
     * Get active staff for a village
     */
    public function getAktif(string $kodeDesa): array
    {
        return $this->where('kode_desa', $kodeDesa)
            ->where('aktif', true)
            ->orderBy('urutan', 'ASC')
            ->findAll();
    }

    /**
     * Get all staff (active and inactive) for a village
     */
    public function getAllByDesa(string $kodeDesa): array
    {
        return $this->where('kode_desa', $kodeDesa)
            ->orderBy('aktif', 'DESC')
            ->orderBy('urutan', 'ASC')
            ->findAll();
    }

    /**
     * Get staff by jabatan
     */
    public function getByJabatan(string $kodeDesa, string $jabatan): ?array
    {
        return $this->where('kode_desa', $kodeDesa)
            ->where('jabatan', $jabatan)
            ->where('aktif', true)
            ->first();
    }

    /**
     * Get summary statistics
     */
    public function getStats(string $kodeDesa): array
    {
        $db = \Config\Database::connect();
        
        // Total active staff
        $aktif = $this->where('kode_desa', $kodeDesa)
            ->where('aktif', true)
            ->countAllResults();
        
        // Total inactive staff
        $nonAktif = $this->where('kode_desa', $kodeDesa)
            ->where('aktif', false)
            ->countAllResults();
        
        // Count struktural - using groupStart for proper WHERE grouping
        $struktural = $db->table($this->table)
            ->where('kode_desa', $kodeDesa)
            ->where('aktif', true)
            ->groupStart()
                ->like('jabatan', 'Kepala Desa')
                ->orLike('jabatan', 'Sekretaris')
                ->orLike('jabatan', 'Kaur')
                ->orLike('jabatan', 'Kasi')
            ->groupEnd()
            ->countAllResults();
        
        // Count Kepala Dusun
        $kadus = $db->table($this->table)
            ->where('kode_desa', $kodeDesa)
            ->where('aktif', true)
            ->like('jabatan', 'Dusun')
            ->countAllResults();
        
        return [
            'total_aktif' => $aktif,
            'total_non_aktif' => $nonAktif,
            'struktural' => $struktural,
            'kadus' => $kadus,
        ];
    }
}

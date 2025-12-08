<?php

namespace App\Models;

use CodeIgniter\Model;

class PosyanduModel extends Model
{
    protected $table            = 'kes_posyandu';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_desa',
        'nama_posyandu',
        'alamat_dusun',
        'rt',
        'rw',
        'ketua_posyandu',
        'no_telp',
        'lat',
        'lng',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get posyandu with stats (jumlah kader, balita, ibu hamil)
     */
    public function getWithStats(string $kodeDesa): array
    {
        $db = \Config\Database::connect();
        
        $posyandus = $this->where('kode_desa', $kodeDesa)->findAll();
        
        foreach ($posyandus as &$p) {
            // Count kader
            $p['jumlah_kader'] = $db->table('kes_kader')
                ->where('posyandu_id', $p['id'])
                ->where('status', 'AKTIF')
                ->countAllResults();
            
            // Count balita yang dimonitor (sudah pernah periksa)
            $p['jumlah_balita'] = $db->table('kes_pemeriksaan')
                ->where('posyandu_id', $p['id'])
                ->groupBy('penduduk_id')
                ->countAllResults();
            
            // Count ibu hamil aktif
            $p['jumlah_bumil'] = $db->table('kes_ibu_hamil')
                ->where('posyandu_id', $p['id'])
                ->where('status', 'HAMIL')
                ->countAllResults();
                
            // Count stunting
            $p['jumlah_stunting'] = $db->table('kes_pemeriksaan')
                ->select('penduduk_id')
                ->where('posyandu_id', $p['id'])
                ->where('indikasi_stunting', true)
                ->groupBy('penduduk_id')
                ->countAllResults();
        }
        
        return $posyandus;
    }

    /**
     * Get dropdown options
     */
    public function getDropdownOptions(string $kodeDesa): array
    {
        return $this->select('id, nama_posyandu')
            ->where('kode_desa', $kodeDesa)
            ->orderBy('nama_posyandu')
            ->findAll();
    }
}

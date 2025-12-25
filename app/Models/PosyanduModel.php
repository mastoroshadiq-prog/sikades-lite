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
            
            // Count balita yang dimonitor (sudah pernah periksa - distinct children)
            $queryBalita = $db->table('kes_pemeriksaan')
                ->select('COUNT(DISTINCT penduduk_id) as total')
                ->where('posyandu_id', $p['id'])
                ->get()
                ->getRowArray();
            $p['jumlah_balita'] = $queryBalita['total'] ?? 0;
            
            // Count ibu hamil aktif
            $p['jumlah_bumil'] = $db->table('kes_ibu_hamil')
                ->where('posyandu_id', $p['id'])
                ->where('status', 'HAMIL')
                ->countAllResults();
                
            // Count stunting - hanya dari pemeriksaan terakhir per balita
            // Sama seperti logika di getStuntingStats untuk konsistensi
            $latestCheckups = $db->query("
                SELECT p.penduduk_id, MAX(p.id) as latest_id
                FROM kes_pemeriksaan p
                WHERE p.posyandu_id = ?
                GROUP BY p.penduduk_id
            ", [$p['id']])->getResultArray();
            
            $latestIds = array_column($latestCheckups, 'latest_id');
            
            if (!empty($latestIds)) {
                $stuntingCount = $db->table('kes_pemeriksaan')
                    ->whereIn('id', $latestIds)
                    ->where('indikasi_stunting', true)
                    ->countAllResults();
                $p['jumlah_stunting'] = $stuntingCount;
            } else {
                $p['jumlah_stunting'] = 0;
            }
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

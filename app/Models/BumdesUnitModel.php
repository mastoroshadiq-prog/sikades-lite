<?php

namespace App\Models;

use CodeIgniter\Model;

class BumdesUnitModel extends Model
{
    protected $table            = 'bumdes_unit';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_desa',
        'nama_unit',
        'jenis_usaha',
        'penanggung_jawab',
        'modal_awal',
        'tanggal_mulai',
        'status',
        'alamat',
        'no_telp',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama_unit' => 'required|max_length[100]',
    ];

    /**
     * Get all units with financial summary
     */
    public function getWithSummary(string $kodeDesa): array
    {
        $units = $this->where('kode_desa', $kodeDesa)->orderBy('nama_unit')->findAll();
        
        $db = \Config\Database::connect();
        
        foreach ($units as &$unit) {
            // Get total debet and kredit from jurnal
            $result = $db->query("
                SELECT 
                    COALESCE(SUM(jd.debet), 0) as total_debet,
                    COALESCE(SUM(jd.kredit), 0) as total_kredit
                FROM bumdes_jurnal j
                JOIN bumdes_jurnal_detail jd ON j.id = jd.jurnal_id
                WHERE j.unit_id = ?
            ", [$unit['id']])->getRowArray();
            
            $unit['total_transaksi'] = $result['total_debet'] ?? 0;
            
            // Count jurnal entries
            $unit['jumlah_jurnal'] = $db->table('bumdes_jurnal')
                ->where('unit_id', $unit['id'])
                ->countAllResults();
        }
        
        return $units;
    }

    /**
     * Get monthly revenue summary
     */
    public function getMonthlySummary(int $unitId, int $tahun): array
    {
        $db = \Config\Database::connect();
        
        return $db->query("
            SELECT 
                MONTH(j.tanggal) as bulan,
                SUM(CASE WHEN a.tipe = 'PENDAPATAN' THEN jd.kredit - jd.debet ELSE 0 END) as pendapatan,
                SUM(CASE WHEN a.tipe = 'BEBAN' THEN jd.debet - jd.kredit ELSE 0 END) as beban
            FROM bumdes_jurnal j
            JOIN bumdes_jurnal_detail jd ON j.id = jd.jurnal_id
            JOIN bumdes_akun a ON jd.akun_id = a.id
            WHERE j.unit_id = ? AND YEAR(j.tanggal) = ?
            GROUP BY MONTH(j.tanggal)
            ORDER BY bulan
        ", [$unitId, $tahun])->getResultArray();
    }
}

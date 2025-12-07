<?php

namespace App\Models;

use CodeIgniter\Model;

class KeluargaModel extends Model
{
    protected $table            = 'pop_keluarga';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_desa',
        'no_kk',
        'kepala_keluarga',
        'alamat',
        'rt',
        'rw',
        'dusun',
        'kode_pos',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'no_kk'           => 'required|exact_length[16]|is_unique[pop_keluarga.no_kk,id,{id}]',
        'kepala_keluarga' => 'required|max_length[255]',
    ];

    protected $validationMessages = [
        'no_kk' => [
            'required'     => 'Nomor KK wajib diisi',
            'exact_length' => 'Nomor KK harus 16 digit',
            'is_unique'    => 'Nomor KK sudah terdaftar',
        ],
    ];

    /**
     * Get keluarga with member count
     */
    public function getWithMemberCount(string $kodeDesa): array
    {
        return $this->select('pop_keluarga.*, COUNT(pop_penduduk.id) as jumlah_anggota')
            ->join('pop_penduduk', 'pop_penduduk.keluarga_id = pop_keluarga.id', 'left')
            ->where('pop_keluarga.kode_desa', $kodeDesa)
            ->groupBy('pop_keluarga.id')
            ->orderBy('pop_keluarga.no_kk', 'ASC')
            ->findAll();
    }

    /**
     * Search keluarga by KK or nama kepala keluarga
     */
    public function search(string $kodeDesa, string $keyword): array
    {
        return $this->select('pop_keluarga.*, COUNT(pop_penduduk.id) as jumlah_anggota')
            ->join('pop_penduduk', 'pop_penduduk.keluarga_id = pop_keluarga.id', 'left')
            ->where('pop_keluarga.kode_desa', $kodeDesa)
            ->groupStart()
                ->like('pop_keluarga.no_kk', $keyword)
                ->orLike('pop_keluarga.kepala_keluarga', $keyword)
            ->groupEnd()
            ->groupBy('pop_keluarga.id')
            ->findAll();
    }

    /**
     * Get statistics per wilayah (RT/RW/Dusun)
     */
    public function getStatsByWilayah(string $kodeDesa, string $groupBy = 'dusun'): array
    {
        $validGroups = ['rt', 'rw', 'dusun'];
        $groupBy = in_array($groupBy, $validGroups) ? $groupBy : 'dusun';

        $db = \Config\Database::connect();
        
        return $db->query("
            SELECT {$groupBy}, COUNT(*) as jumlah_kk
            FROM pop_keluarga
            WHERE kode_desa = ? AND {$groupBy} IS NOT NULL AND {$groupBy} != ''
            GROUP BY {$groupBy}
            ORDER BY {$groupBy} ASC
        ", [$kodeDesa])->getResultArray();
    }

    /**
     * Get keluarga with full details including all members
     */
    public function getWithMembers(int $id): ?array
    {
        $keluarga = $this->find($id);
        if (!$keluarga) {
            return null;
        }

        $pendudukModel = model('PendudukModel');
        $keluarga['anggota'] = $pendudukModel
            ->where('keluarga_id', $id)
            ->where('status_dasar', 'HIDUP')
            ->orderBy("FIELD(status_hubungan, 'Kepala Keluarga', 'Istri', 'Anak', 'Famili Lain', 'Lainnya')")
            ->findAll();

        return $keluarga;
    }
}

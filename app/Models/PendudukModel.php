<?php

namespace App\Models;

use CodeIgniter\Model;

class PendudukModel extends Model
{
    protected $table            = 'pop_penduduk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'keluarga_id',
        'nik',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'pendidikan_terakhir',
        'pekerjaan',
        'status_perkawinan',
        'status_hubungan',
        'golongan_darah',
        'nama_ayah',
        'nama_ibu',
        'kewarganegaraan',
        'no_paspor',
        'no_kitas',
        'status_dasar',
        'is_miskin',
        'is_disabilitas',
        'jenis_disabilitas',
        'foto',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'keluarga_id'   => 'required|integer',
        'nik'           => 'required|exact_length[16]|is_unique[pop_penduduk.nik,id,{id}]',
        'nama_lengkap'  => 'required|max_length[255]',
        'jenis_kelamin' => 'required|in_list[L,P]',
    ];

    protected $validationMessages = [
        'nik' => [
            'required'     => 'NIK wajib diisi',
            'exact_length' => 'NIK harus 16 digit',
            'is_unique'    => 'NIK sudah terdaftar',
        ],
    ];

    // ========================================
    // QUERY METHODS
    // ========================================

    /**
     * Get penduduk with keluarga info
     */
    public function getWithKeluarga(string $kodeDesa, array $filters = []): array
    {
        $builder = $this->select('pop_penduduk.*, pop_keluarga.no_kk, pop_keluarga.alamat, pop_keluarga.rt, pop_keluarga.rw, pop_keluarga.dusun')
            ->join('pop_keluarga', 'pop_keluarga.id = pop_penduduk.keluarga_id')
            ->where('pop_keluarga.kode_desa', $kodeDesa);

        // Apply filters
        if (!empty($filters['status_dasar'])) {
            $builder->where('pop_penduduk.status_dasar', $filters['status_dasar']);
        } else {
            $builder->where('pop_penduduk.status_dasar', 'HIDUP');
        }

        if (!empty($filters['jenis_kelamin'])) {
            $builder->where('pop_penduduk.jenis_kelamin', $filters['jenis_kelamin']);
        }

        if (!empty($filters['dusun'])) {
            $builder->where('pop_keluarga.dusun', $filters['dusun']);
        }

        if (!empty($filters['is_miskin'])) {
            $builder->where('pop_penduduk.is_miskin', 1);
        }

        return $builder->orderBy('pop_penduduk.nama_lengkap', 'ASC')->findAll();
    }

    /**
     * Search penduduk by NIK or nama
     */
    public function search(string $kodeDesa, string $keyword): array
    {
        return $this->select('pop_penduduk.*, pop_keluarga.no_kk, pop_keluarga.alamat')
            ->join('pop_keluarga', 'pop_keluarga.id = pop_penduduk.keluarga_id')
            ->where('pop_keluarga.kode_desa', $kodeDesa)
            ->where('pop_penduduk.status_dasar', 'HIDUP')
            ->groupStart()
                ->like('pop_penduduk.nik', $keyword)
                ->orLike('pop_penduduk.nama_lengkap', $keyword)
                ->orLike('pop_keluarga.no_kk', $keyword)
            ->groupEnd()
            ->orderBy('pop_penduduk.nama_lengkap', 'ASC')
            ->findAll();
    }

    /**
     * Get detail penduduk with full keluarga info
     */
    public function getDetail(int $id): ?array
    {
        return $this->select('pop_penduduk.*, pop_keluarga.no_kk, pop_keluarga.kepala_keluarga, pop_keluarga.alamat, pop_keluarga.rt, pop_keluarga.rw, pop_keluarga.dusun, pop_keluarga.kode_pos')
            ->join('pop_keluarga', 'pop_keluarga.id = pop_penduduk.keluarga_id')
            ->find($id);
    }

    // ========================================
    // STATISTICS METHODS
    // ========================================

    /**
     * Get summary statistics
     */
    public function getSummary(string $kodeDesa): array
    {
        $db = \Config\Database::connect();
        
        $result = $db->query("
            SELECT 
                COUNT(*) as total_penduduk,
                SUM(CASE WHEN p.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as laki_laki,
                SUM(CASE WHEN p.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as perempuan,
                SUM(CASE WHEN p.is_miskin = 1 THEN 1 ELSE 0 END) as warga_miskin,
                SUM(CASE WHEN p.is_disabilitas = 1 THEN 1 ELSE 0 END) as penyandang_disabilitas,
                (SELECT COUNT(*) FROM pop_keluarga WHERE kode_desa = ?) as total_kk
            FROM pop_penduduk p
            JOIN pop_keluarga k ON k.id = p.keluarga_id
            WHERE k.kode_desa = ? AND p.status_dasar = 'HIDUP'
        ", [$kodeDesa, $kodeDesa])->getRowArray();

        return $result ?? [
            'total_penduduk' => 0,
            'laki_laki' => 0,
            'perempuan' => 0,
            'warga_miskin' => 0,
            'penyandang_disabilitas' => 0,
            'total_kk' => 0,
        ];
    }

    /**
     * Get age pyramid data
     */
    public function getAgePyramid(string $kodeDesa): array
    {
        $db = \Config\Database::connect();
        
        $result = $db->query("
            SELECT 
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 0 AND 4 THEN '0-4'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 5 AND 9 THEN '5-9'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 10 AND 14 THEN '10-14'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 15 AND 19 THEN '15-19'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 20 AND 24 THEN '20-24'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 25 AND 29 THEN '25-29'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 30 AND 34 THEN '30-34'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 35 AND 39 THEN '35-39'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 40 AND 44 THEN '40-44'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 45 AND 49 THEN '45-49'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 50 AND 54 THEN '50-54'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 55 AND 59 THEN '55-59'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 60 AND 64 THEN '60-64'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 65 AND 69 THEN '65-69'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 70 AND 74 THEN '70-74'
                    ELSE '75+'
                END as kelompok_umur,
                SUM(CASE WHEN jenis_kelamin = 'L' THEN 1 ELSE 0 END) as laki_laki,
                SUM(CASE WHEN jenis_kelamin = 'P' THEN 1 ELSE 0 END) as perempuan
            FROM pop_penduduk p
            JOIN pop_keluarga k ON k.id = p.keluarga_id
            WHERE k.kode_desa = ? AND p.status_dasar = 'HIDUP' AND p.tanggal_lahir IS NOT NULL
            GROUP BY kelompok_umur
            ORDER BY 
                CASE kelompok_umur
                    WHEN '0-4' THEN 1
                    WHEN '5-9' THEN 2
                    WHEN '10-14' THEN 3
                    WHEN '15-19' THEN 4
                    WHEN '20-24' THEN 5
                    WHEN '25-29' THEN 6
                    WHEN '30-34' THEN 7
                    WHEN '35-39' THEN 8
                    WHEN '40-44' THEN 9
                    WHEN '45-49' THEN 10
                    WHEN '50-54' THEN 11
                    WHEN '55-59' THEN 12
                    WHEN '60-64' THEN 13
                    WHEN '65-69' THEN 14
                    WHEN '70-74' THEN 15
                    ELSE 16
                END
        ", [$kodeDesa])->getResultArray();

        return $result;
    }

    /**
     * Get education statistics
     */
    public function getEducationStats(string $kodeDesa): array
    {
        $db = \Config\Database::connect();
        
        return $db->query("
            SELECT pendidikan_terakhir, COUNT(*) as jumlah
            FROM pop_penduduk p
            JOIN pop_keluarga k ON k.id = p.keluarga_id
            WHERE k.kode_desa = ? AND p.status_dasar = 'HIDUP' 
              AND p.pendidikan_terakhir IS NOT NULL AND p.pendidikan_terakhir != ''
            GROUP BY pendidikan_terakhir
            ORDER BY jumlah DESC
        ", [$kodeDesa])->getResultArray();
    }

    /**
     * Get occupation statistics
     */
    public function getOccupationStats(string $kodeDesa): array
    {
        $db = \Config\Database::connect();
        
        return $db->query("
            SELECT pekerjaan, COUNT(*) as jumlah
            FROM pop_penduduk p
            JOIN pop_keluarga k ON k.id = p.keluarga_id
            WHERE k.kode_desa = ? AND p.status_dasar = 'HIDUP' 
              AND p.pekerjaan IS NOT NULL AND p.pekerjaan != ''
            GROUP BY pekerjaan
            ORDER BY jumlah DESC
            LIMIT 15
        ", [$kodeDesa])->getResultArray();
    }

    /**
     * Get religion statistics
     */
    public function getReligionStats(string $kodeDesa): array
    {
        $db = \Config\Database::connect();
        
        return $db->query("
            SELECT agama, COUNT(*) as jumlah
            FROM pop_penduduk p
            JOIN pop_keluarga k ON k.id = p.keluarga_id
            WHERE k.kode_desa = ? AND p.status_dasar = 'HIDUP' 
              AND p.agama IS NOT NULL AND p.agama != ''
            GROUP BY agama
            ORDER BY jumlah DESC
        ", [$kodeDesa])->getResultArray();
    }

    /**
     * Get marital status statistics
     */
    public function getMaritalStats(string $kodeDesa): array
    {
        $db = \Config\Database::connect();
        
        return $db->query("
            SELECT status_perkawinan, COUNT(*) as jumlah
            FROM pop_penduduk p
            JOIN pop_keluarga k ON k.id = p.keluarga_id
            WHERE k.kode_desa = ? AND p.status_dasar = 'HIDUP' 
              AND p.status_perkawinan IS NOT NULL AND p.status_perkawinan != ''
            GROUP BY status_perkawinan
            ORDER BY jumlah DESC
        ", [$kodeDesa])->getResultArray();
    }

    /**
     * Get BLT/bantuan eligible residents
     */
    public function getBLTEligible(string $kodeDesa): array
    {
        return $this->select('pop_penduduk.*, pop_keluarga.no_kk, pop_keluarga.alamat, pop_keluarga.rt, pop_keluarga.rw, pop_keluarga.dusun')
            ->join('pop_keluarga', 'pop_keluarga.id = pop_penduduk.keluarga_id')
            ->where('pop_keluarga.kode_desa', $kodeDesa)
            ->where('pop_penduduk.status_dasar', 'HIDUP')
            ->where('pop_penduduk.is_miskin', 1)
            ->orderBy('pop_keluarga.dusun, pop_keluarga.rt, pop_keluarga.rw')
            ->findAll();
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Calculate age from tanggal_lahir
     */
    public function calculateAge(?string $tanggalLahir): ?int
    {
        if (!$tanggalLahir) {
            return null;
        }
        
        $birthDate = new \DateTime($tanggalLahir);
        $today = new \DateTime();
        return $birthDate->diff($today)->y;
    }

    /**
     * Get dropdown options for agama
     */
    public static function getAgamaOptions(): array
    {
        return [
            'Islam',
            'Kristen',
            'Katolik',
            'Hindu',
            'Buddha',
            'Konghucu',
            'Kepercayaan Terhadap Tuhan YME',
        ];
    }

    /**
     * Get dropdown options for status perkawinan
     */
    public static function getStatusPerkawinanOptions(): array
    {
        return [
            'Belum Kawin',
            'Kawin',
            'Cerai Hidup',
            'Cerai Mati',
        ];
    }

    /**
     * Get dropdown options for status hubungan dalam keluarga
     */
    public static function getStatusHubunganOptions(): array
    {
        return [
            'Kepala Keluarga',
            'Istri',
            'Anak',
            'Menantu',
            'Cucu',
            'Orang Tua',
            'Mertua',
            'Famili Lain',
            'Pembantu',
            'Lainnya',
        ];
    }

    /**
     * Get dropdown options for golongan darah
     */
    public static function getGolonganDarahOptions(): array
    {
        return ['A', 'B', 'AB', 'O', 'Tidak Tahu'];
    }

    /**
     * Get balita (children under 5 years old)
     */
    public function getBalita(string $kodeDesa): array
    {
        $fiveYearsAgo = date('Y-m-d', strtotime('-5 years'));
        
        return $this->select('pop_penduduk.*, pop_keluarga.no_kk, pop_keluarga.dusun, pop_keluarga.alamat')
            ->join('pop_keluarga', 'pop_keluarga.id = pop_penduduk.keluarga_id')
            ->where('pop_keluarga.kode_desa', $kodeDesa)
            ->where('pop_penduduk.status_dasar', 'HIDUP')
            ->where('pop_penduduk.tanggal_lahir >=', $fiveYearsAgo)
            ->orderBy('pop_penduduk.nama_lengkap')
            ->findAll();
    }

    /**
     * Get WUS (Wanita Usia Subur) - Women 15-49 years old
     */
    public function getWUS(string $kodeDesa): array
    {
        $minAge = date('Y-m-d', strtotime('-49 years'));
        $maxAge = date('Y-m-d', strtotime('-15 years'));
        
        return $this->select('pop_penduduk.*, pop_keluarga.no_kk, pop_keluarga.dusun, pop_keluarga.alamat')
            ->join('pop_keluarga', 'pop_keluarga.id = pop_penduduk.keluarga_id')
            ->where('pop_keluarga.kode_desa', $kodeDesa)
            ->where('pop_penduduk.status_dasar', 'HIDUP')
            ->where('pop_penduduk.jenis_kelamin', 'P')
            ->where('pop_penduduk.tanggal_lahir >=', $minAge)
            ->where('pop_penduduk.tanggal_lahir <=', $maxAge)
            ->orderBy('pop_penduduk.nama_lengkap')
            ->findAll();
    }
}


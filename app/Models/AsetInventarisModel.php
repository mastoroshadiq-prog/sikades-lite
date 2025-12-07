<?php

namespace App\Models;

use CodeIgniter\Model;

class AsetInventarisModel extends Model
{
    protected $table            = 'aset_inventaris';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_desa',
        'kode_register',
        'nama_barang',
        'kategori_id',
        'merk_type',
        'ukuran',
        'bahan',
        'tahun_perolehan',
        'harga_perolehan',
        'nilai_sisa',
        'kondisi',
        'status_penggunaan',
        'lokasi',
        'pengguna',
        'sumber_dana',
        'bku_id',
        'lat',
        'lng',
        'foto',
        'keterangan',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'kode_desa'        => 'required|max_length[20]',
        'nama_barang'      => 'required|max_length[255]',
        'kategori_id'      => 'required|integer',
        'tahun_perolehan'  => 'required|integer|greater_than[1990]|less_than_equal_to[2099]',
        'harga_perolehan'  => 'required|numeric|greater_than_equal_to[0]',
        'kondisi'          => 'required|in_list[Baik,Rusak Ringan,Rusak Berat]',
        'status_penggunaan' => 'required|in_list[Digunakan,Tidak Digunakan,Dipinjamkan,Dihapuskan]',
        'sumber_dana'      => 'required|in_list[APBDes,Hibah,Bantuan Pemerintah,Swadaya,Lainnya]',
    ];

    protected $validationMessages = [
        'nama_barang' => [
            'required' => 'Nama barang harus diisi',
        ],
        'kategori_id' => [
            'required' => 'Kategori aset harus dipilih',
        ],
        'tahun_perolehan' => [
            'required' => 'Tahun perolehan harus diisi',
        ],
        'harga_perolehan' => [
            'required' => 'Harga perolehan harus diisi',
        ],
    ];

    /**
     * Generate unique kode_register
     * Format: [KODE_GOLONGAN]/[KODE_DESA]/[TAHUN]/[SEQUENCE]
     * Example: 03/3201010001/2025/0001
     */
    public function generateKodeRegister(string $kodeDesa, int $kategoriId, int $tahun): string
    {
        $kategoriModel = new AsetKategoriModel();
        $kategori = $kategoriModel->find($kategoriId);
        $kodeGolongan = $kategori ? $kategori['kode_golongan'] : '00';

        // Get last sequence for this year and category
        $lastAset = $this->select('kode_register')
                         ->where('kode_desa', $kodeDesa)
                         ->where('kategori_id', $kategoriId)
                         ->where('tahun_perolehan', $tahun)
                         ->orderBy('id', 'DESC')
                         ->first();

        $sequence = 1;
        if ($lastAset) {
            $parts = explode('/', $lastAset['kode_register']);
            if (count($parts) === 4) {
                $sequence = intval($parts[3]) + 1;
            }
        }

        return sprintf('%s/%s/%d/%04d', $kodeGolongan, $kodeDesa, $tahun, $sequence);
    }

    /**
     * Get all assets with category info
     */
    public function getAsetWithKategori(string $kodeDesa, ?int $tahun = null): array
    {
        $builder = $this->select('aset_inventaris.*, aset_kategori.kode_golongan, aset_kategori.nama_golongan')
                        ->join('aset_kategori', 'aset_kategori.id = aset_inventaris.kategori_id', 'left')
                        ->where('aset_inventaris.kode_desa', $kodeDesa);

        if ($tahun) {
            $builder->where('aset_inventaris.tahun_perolehan', $tahun);
        }

        return $builder->orderBy('aset_inventaris.kode_register', 'ASC')
                       ->findAll();
    }

    /**
     * Get assets by category
     */
    public function getAsetByKategori(string $kodeDesa, int $kategoriId): array
    {
        return $this->select('aset_inventaris.*, aset_kategori.kode_golongan, aset_kategori.nama_golongan')
                    ->join('aset_kategori', 'aset_kategori.id = aset_inventaris.kategori_id', 'left')
                    ->where('aset_inventaris.kode_desa', $kodeDesa)
                    ->where('aset_inventaris.kategori_id', $kategoriId)
                    ->orderBy('aset_inventaris.tahun_perolehan', 'DESC')
                    ->findAll();
    }

    /**
     * Get assets by condition
     */
    public function getAsetByKondisi(string $kodeDesa, string $kondisi): array
    {
        return $this->select('aset_inventaris.*, aset_kategori.kode_golongan, aset_kategori.nama_golongan')
                    ->join('aset_kategori', 'aset_kategori.id = aset_inventaris.kategori_id', 'left')
                    ->where('aset_inventaris.kode_desa', $kodeDesa)
                    ->where('aset_inventaris.kondisi', $kondisi)
                    ->orderBy('aset_inventaris.nama_barang', 'ASC')
                    ->findAll();
    }

    /**
     * Get assets with GIS coordinates (for WebGIS)
     */
    public function getAsetWithCoordinates(string $kodeDesa): array
    {
        return $this->select('aset_inventaris.*, aset_kategori.kode_golongan, aset_kategori.nama_golongan')
                    ->join('aset_kategori', 'aset_kategori.id = aset_inventaris.kategori_id', 'left')
                    ->where('aset_inventaris.kode_desa', $kodeDesa)
                    ->where('aset_inventaris.lat IS NOT NULL')
                    ->where('aset_inventaris.lng IS NOT NULL')
                    ->findAll();
    }

    /**
     * Get asset linked to BKU transaction
     */
    public function getAsetByBkuId(int $bkuId): ?array
    {
        return $this->where('bku_id', $bkuId)->first();
    }

    /**
     * Get summary statistics
     */
    public function getSummary(string $kodeDesa): array
    {
        $summary = [
            'total_aset' => 0,
            'total_nilai' => 0,
            'by_kondisi' => [
                'Baik' => 0,
                'Rusak Ringan' => 0,
                'Rusak Berat' => 0,
            ],
            'by_kategori' => [],
        ];

        // Total count and value
        $result = $this->selectSum('harga_perolehan', 'total_nilai')
                       ->selectCount('id', 'total_aset')
                       ->where('kode_desa', $kodeDesa)
                       ->first();
        
        $summary['total_aset'] = $result['total_aset'] ?? 0;
        $summary['total_nilai'] = $result['total_nilai'] ?? 0;

        // By kondisi
        $kondisiData = $this->select('kondisi, COUNT(*) as jumlah')
                            ->where('kode_desa', $kodeDesa)
                            ->groupBy('kondisi')
                            ->findAll();
        
        foreach ($kondisiData as $k) {
            $summary['by_kondisi'][$k['kondisi']] = $k['jumlah'];
        }

        // By kategori
        $kategoriData = $this->select('aset_kategori.nama_golongan, COUNT(*) as jumlah, SUM(harga_perolehan) as nilai')
                             ->join('aset_kategori', 'aset_kategori.id = aset_inventaris.kategori_id', 'left')
                             ->where('aset_inventaris.kode_desa', $kodeDesa)
                             ->groupBy('aset_inventaris.kategori_id')
                             ->findAll();
        
        $summary['by_kategori'] = $kategoriData;

        return $summary;
    }

    /**
     * Check if BKU transaction is Belanja Modal (starts with 5.3)
     */
    public function isBelanjModal(int $refRekeningId): bool
    {
        $db = \Config\Database::connect();
        $rekening = $db->table('ref_rekening')
                       ->where('id', $refRekeningId)
                       ->get()
                       ->getRowArray();
        
        if ($rekening && isset($rekening['kode_akun'])) {
            return strpos($rekening['kode_akun'], '5.3') === 0;
        }
        
        return false;
    }
}

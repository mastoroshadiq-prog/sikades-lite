<?php

namespace App\Models;

use CodeIgniter\Model;

class AsetModel extends Model
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
        'kategori',
        'merk',
        'ukuran',
        'bahan',
        'tahun_perolehan',
        'harga_perolehan',
        'sumber_dana',
        'kondisi',
        'lokasi',
        'keterangan',
        'bku_id',
        'lat',
        'lng',
        'foto',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Kategori options
    public const KATEGORI = [
        'Tanah',
        'Peralatan dan Mesin',
        'Gedung dan Bangunan',
        'Jalan, Irigasi, dan Jaringan',
        'Aset Tetap Lainnya',
        'Konstruksi Dalam Pengerjaan',
    ];

    public const KONDISI = ['Baik', 'Rusak Ringan', 'Rusak Berat'];
    public const SUMBER_DANA = ['APBDes', 'Hibah', 'Bantuan', 'Lainnya'];

    /**
     * Generate kode register otomatis
     */
    public function generateKodeRegister(string $kodeDesa, string $kategori): string
    {
        $kodeKategori = match($kategori) {
            'Tanah' => '01',
            'Peralatan dan Mesin' => '02',
            'Gedung dan Bangunan' => '03',
            'Jalan, Irigasi, dan Jaringan' => '04',
            'Aset Tetap Lainnya' => '05',
            'Konstruksi Dalam Pengerjaan' => '06',
            default => '00',
        };

        $tahun = date('Y');
        $prefix = "{$kodeDesa}/{$kodeKategori}/{$tahun}/";

        // Get last number
        $last = $this->like('kode_register', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if ($last) {
            $parts = explode('/', $last['kode_register']);
            $num = (int)end($parts) + 1;
        } else {
            $num = 1;
        }

        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get stats by kategori
     */
    public function getStatsByKategori(string $kodeDesa): array
    {
        return $this->select('kategori, COUNT(*) as jumlah, SUM(harga_perolehan) as total_nilai')
            ->where('kode_desa', $kodeDesa)
            ->groupBy('kategori')
            ->findAll();
    }

    /**
     * Get stats by kondisi
     */
    public function getStatsByKondisi(string $kodeDesa): array
    {
        return $this->select('kondisi, COUNT(*) as jumlah')
            ->where('kode_desa', $kodeDesa)
            ->groupBy('kondisi')
            ->findAll();
    }

    /**
     * Get total nilai aset
     */
    public function getTotalNilai(string $kodeDesa): float
    {
        $result = $this->selectSum('harga_perolehan', 'total')
            ->where('kode_desa', $kodeDesa)
            ->first();
        
        return (float)($result['total'] ?? 0);
    }

    /**
     * Get assets with GPS coordinates
     */
    public function getWithCoordinates(string $kodeDesa): array
    {
        return $this->where('kode_desa', $kodeDesa)
            ->where('lat IS NOT NULL')
            ->where('lng IS NOT NULL')
            ->findAll();
    }

    /**
     * Search assets
     */
    public function search(string $kodeDesa, string $keyword): array
    {
        return $this->where('kode_desa', $kodeDesa)
            ->groupStart()
                ->like('kode_register', $keyword)
                ->orLike('nama_barang', $keyword)
                ->orLike('lokasi', $keyword)
            ->groupEnd()
            ->findAll();
    }
}

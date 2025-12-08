<?php

namespace App\Models;

use CodeIgniter\Model;

class GisWilayahModel extends Model
{
    protected $table            = 'gis_wilayah';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'kode_desa', 'nama_wilayah', 'tipe', 'parent_id',
        'center_lat', 'center_lng', 'geojson', 'luas_area',
        'warna', 'keterangan'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all wilayah by type for a village
     */
    public function getByType($kodeDesa, $tipe = 'DUSUN')
    {
        return $this->where('kode_desa', $kodeDesa)
                    ->where('tipe', $tipe)
                    ->orderBy('nama_wilayah', 'ASC')
                    ->findAll();
    }

    /**
     * Get wilayah with coordinates for map
     */
    public function getWithCoordinates($kodeDesa, $tipe = 'DUSUN')
    {
        return $this->where('kode_desa', $kodeDesa)
                    ->where('tipe', $tipe)
                    ->where('center_lat IS NOT NULL')
                    ->where('center_lng IS NOT NULL')
                    ->findAll();
    }

    /**
     * Get wilayah with GeoJSON boundaries
     */
    public function getWithBoundaries($kodeDesa, $tipe = null)
    {
        $builder = $this->where('kode_desa', $kodeDesa)
                        ->where('geojson IS NOT NULL');
        
        if ($tipe) {
            $builder->where('tipe', $tipe);
        }
        
        return $builder->findAll();
    }

    /**
     * Get or create wilayah by name
     */
    public function getOrCreate($kodeDesa, $namaWilayah, $tipe = 'DUSUN')
    {
        $existing = $this->where('kode_desa', $kodeDesa)
                         ->where('nama_wilayah', $namaWilayah)
                         ->where('tipe', $tipe)
                         ->first();
        
        if ($existing) {
            return $existing;
        }
        
        // Create new
        $id = $this->insert([
            'kode_desa'    => $kodeDesa,
            'nama_wilayah' => $namaWilayah,
            'tipe'         => $tipe,
        ]);
        
        return $this->find($id);
    }

    /**
     * Update coordinates for wilayah
     */
    public function updateCoordinates($id, $lat, $lng)
    {
        return $this->update($id, [
            'center_lat' => $lat,
            'center_lng' => $lng,
        ]);
    }

    /**
     * Update GeoJSON boundary
     */
    public function updateBoundary($id, $geojson, $luasArea = null)
    {
        $data = ['geojson' => $geojson];
        
        if ($luasArea !== null) {
            $data['luas_area'] = $luasArea;
        }
        
        return $this->update($id, $data);
    }

    /**
     * Sync wilayah from keluarga data
     * Creates wilayah entries from unique dusun values in pop_keluarga
     */
    public function syncFromKeluarga($kodeDesa)
    {
        $db = \Config\Database::connect();
        
        // Get unique dusun from keluarga
        $dusunList = $db->query("
            SELECT DISTINCT TRIM(dusun) as dusun
            FROM pop_keluarga
            WHERE kode_desa = ?
                AND dusun IS NOT NULL
                AND TRIM(dusun) != ''
            ORDER BY dusun
        ", [$kodeDesa])->getResultArray();
        
        $created = 0;
        foreach ($dusunList as $d) {
            $existing = $this->where('kode_desa', $kodeDesa)
                             ->where('nama_wilayah', $d['dusun'])
                             ->where('tipe', 'DUSUN')
                             ->first();
            
            if (!$existing) {
                $this->insert([
                    'kode_desa'    => $kodeDesa,
                    'nama_wilayah' => $d['dusun'],
                    'tipe'         => 'DUSUN',
                ]);
                $created++;
            }
        }
        
        return $created;
    }

    /**
     * Get GeoJSON FeatureCollection for all boundaries
     */
    public function getGeoJsonFeatureCollection($kodeDesa, $tipe = null)
    {
        $wilayahs = $this->getWithBoundaries($kodeDesa, $tipe);
        
        $features = [];
        foreach ($wilayahs as $w) {
            $geojson = json_decode($w['geojson'], true);
            if ($geojson) {
                $features[] = [
                    'type' => 'Feature',
                    'geometry' => $geojson,
                    'properties' => [
                        'id'           => $w['id'],
                        'nama_wilayah' => $w['nama_wilayah'],
                        'tipe'         => $w['tipe'],
                        'warna'        => $w['warna'],
                        'luas_area'    => $w['luas_area'],
                    ],
                ];
            }
        }
        
        return [
            'type'     => 'FeatureCollection',
            'features' => $features,
        ];
    }
}

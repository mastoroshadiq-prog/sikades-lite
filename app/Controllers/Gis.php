<?php

namespace App\Controllers;

use App\Models\AsetModel;

class Gis extends BaseController
{
    protected $user;
    protected $asetModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->user = session()->get();
        $this->asetModel = new AsetModel();
    }

    /**
     * Main GIS Map View
     */
    public function index()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        
        // Get village center (default: Indonesia center)
        $centerLat = -6.2088;
        $centerLng = 106.8456;
        
        // Get stats using raw query to avoid issues
        $db = \Config\Database::connect();
        $totalAset = $db->query("
            SELECT COUNT(*) as total FROM aset_inventaris 
            WHERE kode_desa = ? AND lat IS NOT NULL AND lng IS NOT NULL
        ", [$kodeDesa])->getRow()->total ?? 0;

        $data = [
            'title'     => 'WebGIS - Peta Aset Desa',
            'user'      => $this->user,
            'centerLat' => $centerLat,
            'centerLng' => $centerLng,
            'totalAset' => $totalAset,
        ];

        return view('gis/index', $data);
    }

    /**
     * Get JSON data for map markers
     */
    public function getJsonData()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        
        $aset = $this->db->query("
            SELECT 
                a.id, a.kode_register, a.nama_barang, 
                COALESCE(k.nama_golongan, 'Lainnya') as kategori,
                a.tahun_perolehan, a.harga_perolehan, a.kondisi,
                a.lat, a.lng, a.foto
            FROM aset_inventaris a
            LEFT JOIN aset_kategori k ON a.kategori_id = k.id
            WHERE a.kode_desa = ? AND a.lat IS NOT NULL AND a.lng IS NOT NULL
            ORDER BY a.nama_barang
        ", [$kodeDesa])->getResultArray();
        
        // Format for GeoJSON
        $features = [];
        foreach ($aset as $a) {
            $features[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$a['lng'], (float)$a['lat']],
                ],
                'properties' => [
                    'id' => $a['id'],
                    'kode_register' => $a['kode_register'],
                    'nama' => $a['nama_barang'],
                    'kategori' => $a['kategori'],
                    'tahun' => $a['tahun_perolehan'],
                    'nilai' => (float)$a['harga_perolehan'],
                    'kondisi' => $a['kondisi'],
                    'foto' => $a['foto'] ? base_url('/writable/uploads/aset/' . $a['foto']) : null,
                ],
            ];
        }
        
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
        
        return $this->response->setJSON($geojson);
    }

    /**
     * Get asset detail for popup
     */
    public function getAsetDetail($id)
    {
        $aset = $this->asetModel->find($id);
        
        if (!$aset) {
            return $this->response->setJSON(['error' => 'Not found']);
        }
        
        return $this->response->setJSON($aset);
    }

    /**
     * Full screen map view
     */
    public function fullscreen()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        
        $data = [
            'title'     => 'Peta Aset Desa - Fullscreen',
            'user'      => $this->user,
            'centerLat' => -6.2088,
            'centerLng' => 106.8456,
        ];

        return view('gis/fullscreen', $data);
    }
}

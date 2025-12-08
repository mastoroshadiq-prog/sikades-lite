<?php

namespace App\Controllers;

class Gis extends BaseController
{
    protected $user;
    protected $asetModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->user = session()->get();
        $this->asetModel = model('AsetModel');
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
        
        // Get stats
        $totalAset = $this->asetModel->where('kode_desa', $kodeDesa)
            ->where('lat IS NOT NULL')
            ->where('lng IS NOT NULL')
            ->countAllResults();

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
                id, kode_register, nama_barang, kategori,
                tahun_perolehan, harga_perolehan, kondisi,
                lat, lng, foto
            FROM desa_aset
            WHERE kode_desa = ? AND lat IS NOT NULL AND lng IS NOT NULL
            ORDER BY nama_barang
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

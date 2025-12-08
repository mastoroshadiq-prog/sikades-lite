<?php

namespace App\Controllers;

use App\Models\AsetModel;
use App\Models\PendudukModel;
use App\Models\KeluargaModel;

class Gis extends BaseController
{
    protected $user;
    protected $asetModel;
    protected $pendudukModel;
    protected $keluargaModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->user = session()->get();
        $this->asetModel = new AsetModel();
        $this->pendudukModel = new PendudukModel();
        $this->keluargaModel = new KeluargaModel();
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
        
        // Get population stats - join with keluarga to get kode_desa
        $totalPenduduk = $db->query("
            SELECT COUNT(*) as total 
            FROM pop_penduduk p
            LEFT JOIN pop_keluarga k ON p.keluarga_id = k.id
            WHERE k.kode_desa = ? AND p.status_dasar = 'HIDUP'
        ", [$kodeDesa])->getRow()->total ?? 0;

        $data = [
            'title'         => 'WebGIS - Peta Aset Desa',
            'user'          => $this->user,
            'centerLat'     => $centerLat,
            'centerLng'     => $centerLng,
            'totalAset'     => $totalAset,
            'totalPenduduk' => $totalPenduduk,
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

    /**
     * Get population density data for choropleth map
     * Returns population counts grouped by dusun/RT/RW
     */
    public function getPopulationData()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $db = \Config\Database::connect();
        
        // Get population by dusun - join with keluarga for wilayah data
        $dusunData = $db->query("
            SELECT 
                COALESCE(k.dusun, 'Tidak Diketahui') as wilayah,
                COUNT(*) as jumlah_penduduk,
                COUNT(DISTINCT k.no_kk) as jumlah_kk,
                SUM(CASE WHEN p.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as laki_laki,
                SUM(CASE WHEN p.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as perempuan
            FROM pop_penduduk p
            LEFT JOIN pop_keluarga k ON p.keluarga_id = k.id
            WHERE k.kode_desa = ? 
                AND p.status_dasar = 'HIDUP'
                AND k.dusun IS NOT NULL 
                AND k.dusun != ''
            GROUP BY k.dusun
            ORDER BY jumlah_penduduk DESC
        ", [$kodeDesa])->getResultArray();
        
        // Get by RT - join with keluarga
        $rtData = $db->query("
            SELECT 
                CONCAT(COALESCE(k.dusun, '-'), '/RT ', COALESCE(k.rt, '0')) as wilayah,
                k.dusun,
                k.rt,
                COUNT(*) as jumlah_penduduk,
                COUNT(DISTINCT k.no_kk) as jumlah_kk,
                SUM(CASE WHEN p.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as laki_laki,
                SUM(CASE WHEN p.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as perempuan
            FROM pop_penduduk p
            LEFT JOIN pop_keluarga k ON p.keluarga_id = k.id
            WHERE k.kode_desa = ? 
                AND p.status_dasar = 'HIDUP'
            GROUP BY k.dusun, k.rt
            ORDER BY k.dusun, k.rt
        ", [$kodeDesa])->getResultArray();
        
        // Calculate density ranges for legend
        $maxPenduduk = 0;
        foreach ($dusunData as $d) {
            if ($d['jumlah_penduduk'] > $maxPenduduk) {
                $maxPenduduk = $d['jumlah_penduduk'];
            }
        }
        
        // Return data
        return $this->response->setJSON([
            'by_dusun' => $dusunData,
            'by_rt'    => $rtData,
            'max'      => $maxPenduduk,
            'total'    => array_sum(array_column($dusunData, 'jumlah_penduduk')),
        ]);
    }
}


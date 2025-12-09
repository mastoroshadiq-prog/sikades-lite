<?php

namespace App\Controllers;

use App\Models\AsetModel;
use App\Models\PendudukModel;
use App\Models\KeluargaModel;
use App\Models\GisWilayahModel;

class Gis extends BaseController
{
    protected $user;
    protected $asetModel;
    protected $pendudukModel;
    protected $keluargaModel;
    protected $wilayahModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->user = session()->get();
        $this->asetModel = new AsetModel();
        $this->pendudukModel = new PendudukModel();
        $this->keluargaModel = new KeluargaModel();
        $this->wilayahModel = new GisWilayahModel();
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
     * Get infrastructure projects for GIS layer
     */
    public function getProyekData()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $db = \Config\Database::connect();
        
        // Check if proyek_fisik table exists
        if (!in_array('proyek_fisik', $db->listTables())) {
            return $this->response->setJSON([
                'type' => 'FeatureCollection',
                'features' => [],
            ]);
        }
        
        $proyek = $db->query("
            SELECT 
                p.*,
                (SELECT foto FROM proyek_log WHERE proyek_id = p.id ORDER BY tanggal_laporan DESC LIMIT 1) as foto_terbaru
            FROM proyek_fisik p
            WHERE p.kode_desa = ? AND p.lat IS NOT NULL AND p.lng IS NOT NULL
            ORDER BY p.tgl_mulai DESC
        ", [$kodeDesa])->getResultArray();
        
        $features = [];
        foreach ($proyek as $p) {
            // Calculate deviation
            $keuangan = (float) ($p['persentase_keuangan'] ?? 0);
            $fisik = (int) ($p['persentase_fisik'] ?? 0);
            $deviation = $keuangan - $fisik;
            
            $features[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$p['lng'], (float)$p['lat']],
                ],
                'properties' => [
                    'id'                  => $p['id'],
                    'nama'                => $p['nama_proyek'],
                    'lokasi'              => $p['lokasi_detail'],
                    'anggaran'            => (float) $p['anggaran'],
                    'persentase_fisik'    => (int) $p['persentase_fisik'],
                    'persentase_keuangan' => (float) $p['persentase_keuangan'],
                    'status'              => $p['status'],
                    'tgl_mulai'           => $p['tgl_mulai'],
                    'tgl_selesai_target'  => $p['tgl_selesai_target'],
                    'pelaksana'           => $p['pelaksana_kegiatan'],
                    'deviation'           => round($deviation, 2),
                    'is_alert'            => $deviation > 20,
                    'foto_0'              => $p['foto_0'] ? base_url($p['foto_0']) : null,
                    'foto_50'             => $p['foto_50'] ? base_url($p['foto_50']) : null,
                    'foto_100'            => $p['foto_100'] ? base_url($p['foto_100']) : null,
                    'foto_terbaru'        => $p['foto_terbaru'] ? base_url($p['foto_terbaru']) : null,
                ],
            ];
        }
        
        // Count by status
        $stats = [
            'total' => count($proyek),
            'rencana' => count(array_filter($proyek, fn($p) => $p['status'] === 'RENCANA')),
            'proses' => count(array_filter($proyek, fn($p) => $p['status'] === 'PROSES')),
            'selesai' => count(array_filter($proyek, fn($p) => $p['status'] === 'SELESAI')),
            'mangkrak' => count(array_filter($proyek, fn($p) => $p['status'] === 'MANGKRAK')),
            'alert' => count(array_filter($proyek, fn($p) => ((float)$p['persentase_keuangan'] - (int)$p['persentase_fisik']) > 20)),
        ];
        
        return $this->response->setJSON([
            'type' => 'FeatureCollection',
            'features' => $features,
            'stats' => $stats,
        ]);
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
                TRIM(k.dusun) as wilayah,
                COUNT(*) as jumlah_penduduk,
                COUNT(DISTINCT k.no_kk) as jumlah_kk,
                SUM(CASE WHEN p.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as laki_laki,
                SUM(CASE WHEN p.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as perempuan
            FROM pop_penduduk p
            LEFT JOIN pop_keluarga k ON p.keluarga_id = k.id
            WHERE k.kode_desa = ? 
                AND p.status_dasar = 'HIDUP'
                AND k.dusun IS NOT NULL 
                AND TRIM(k.dusun) != ''
            GROUP BY TRIM(k.dusun)
            HAVING wilayah != ''
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
        
        // Get wilayah with coordinates for circle markers (gracefully handle missing table)
        $wilayahData = [];
        try {
            $wilayahData = $this->wilayahModel->getWithCoordinates($kodeDesa, 'DUSUN');
        } catch (\Exception $e) {
            // Table might not exist yet - continue without coordinates
            log_message('warning', 'GIS wilayah table not ready: ' . $e->getMessage());
        }
        
        // Map population data to wilayah coordinates
        $dusunWithCoords = [];
        foreach ($dusunData as $d) {
            $coords = null;
            foreach ($wilayahData as $w) {
                if ($w['nama_wilayah'] === $d['wilayah']) {
                    $coords = [
                        'lat' => (float) $w['center_lat'],
                        'lng' => (float) $w['center_lng'],
                    ];
                    break;
                }
            }
            $dusunWithCoords[] = array_merge($d, ['coordinates' => $coords]);
        }
        
        // Return data
        return $this->response->setJSON([
            'by_dusun' => $dusunWithCoords,
            'by_rt'    => $rtData,
            'max'      => $maxPenduduk,
            'total'    => array_sum(array_column($dusunData, 'jumlah_penduduk')),
        ]);
    }

    /**
     * Wilayah Settings Page
     */
    public function wilayahSettings()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $db = \Config\Database::connect();
        
        // Check if table exists, if not create it
        try {
            $tables = $db->listTables();
            if (!in_array('gis_wilayah', $tables)) {
                // Create table
                $db->query("
                    CREATE TABLE IF NOT EXISTS gis_wilayah (
                        id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                        kode_desa VARCHAR(20) NOT NULL,
                        nama_wilayah VARCHAR(100) NOT NULL,
                        tipe ENUM('DESA','DUSUN','RW','RT') NOT NULL DEFAULT 'DUSUN',
                        parent_id INT(11) UNSIGNED NULL,
                        center_lat DECIMAL(10,8) NULL,
                        center_lng DECIMAL(11,8) NULL,
                        geojson LONGTEXT NULL,
                        luas_area DECIMAL(12,2) NULL,
                        warna VARCHAR(7) NULL,
                        keterangan TEXT NULL,
                        created_at DATETIME NULL,
                        updated_at DATETIME NULL,
                        PRIMARY KEY (id),
                        INDEX (kode_desa),
                        INDEX (tipe)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                ");
            }
            
            // Sync wilayah from keluarga data
            $this->wilayahModel->syncFromKeluarga($kodeDesa);
            
            $wilayahs = $this->wilayahModel->getByType($kodeDesa, 'DUSUN');
        } catch (\Exception $e) {
            log_message('error', 'Wilayah settings error: ' . $e->getMessage());
            $wilayahs = [];
        }
        
        $data = [
            'title'     => 'Pengaturan Wilayah GIS',
            'user'      => $this->user,
            'wilayahs'  => $wilayahs,
        ];
        
        return view('gis/wilayah_settings', $data);
    }

    /**
     * Update wilayah coordinates
     */
    public function updateWilayahCoordinates()
    {
        $id = $this->request->getPost('id');
        $lat = $this->request->getPost('lat');
        $lng = $this->request->getPost('lng');
        
        if (!$id || !$lat || !$lng) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
        }
        
        $this->wilayahModel->updateCoordinates($id, $lat, $lng);
        
        return $this->response->setJSON(['success' => true, 'message' => 'Koordinat berhasil diperbarui']);
    }

    /**
     * Upload GeoJSON boundary
     */
    public function uploadBoundary()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        
        $file = $this->request->getFile('geojson_file');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid');
        }
        
        $ext = $file->getClientExtension();
        if (!in_array($ext, ['json', 'geojson'])) {
            return redirect()->back()->with('error', 'Format file harus .json atau .geojson');
        }
        
        try {
            $content = file_get_contents($file->getTempName());
            $geojson = json_decode($content, true);
            
            if (!$geojson || !isset($geojson['type'])) {
                return redirect()->back()->with('error', 'Format GeoJSON tidak valid');
            }
            
            $imported = 0;
            
            // Handle FeatureCollection
            if ($geojson['type'] === 'FeatureCollection' && isset($geojson['features'])) {
                foreach ($geojson['features'] as $feature) {
                    if ($this->importFeature($kodeDesa, $feature)) {
                        $imported++;
                    }
                }
            }
            // Handle single Feature
            elseif ($geojson['type'] === 'Feature') {
                if ($this->importFeature($kodeDesa, $geojson)) {
                    $imported++;
                }
            }
            
            return redirect()->back()->with('success', "Berhasil mengimpor {$imported} wilayah");
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Import single GeoJSON feature
     */
    private function importFeature($kodeDesa, $feature)
    {
        if (!isset($feature['geometry']) || !isset($feature['properties'])) {
            return false;
        }
        
        // Get name from properties (try common field names)
        $name = $feature['properties']['nama'] 
             ?? $feature['properties']['name'] 
             ?? $feature['properties']['NAMA']
             ?? $feature['properties']['NAME']
             ?? $feature['properties']['DUSUN']
             ?? $feature['properties']['dusun']
             ?? null;
        
        if (!$name) {
            return false;
        }
        
        // Get or create wilayah
        $wilayah = $this->wilayahModel->getOrCreate($kodeDesa, $name, 'DUSUN');
        
        // Calculate center from geometry
        $center = $this->calculateGeometryCenter($feature['geometry']);
        
        // Update with boundary and center
        $this->wilayahModel->update($wilayah['id'], [
            'geojson'    => json_encode($feature['geometry']),
            'center_lat' => $center['lat'],
            'center_lng' => $center['lng'],
            'warna'      => $feature['properties']['fill'] ?? $feature['properties']['color'] ?? null,
        ]);
        
        return true;
    }

    /**
     * Calculate center point of geometry
     */
    private function calculateGeometryCenter($geometry)
    {
        $coords = [];
        
        // Extract all coordinates
        if ($geometry['type'] === 'Polygon') {
            $coords = $geometry['coordinates'][0]; // Outer ring
        } elseif ($geometry['type'] === 'MultiPolygon') {
            foreach ($geometry['coordinates'] as $polygon) {
                $coords = array_merge($coords, $polygon[0]);
            }
        } elseif ($geometry['type'] === 'Point') {
            return [
                'lng' => $geometry['coordinates'][0],
                'lat' => $geometry['coordinates'][1],
            ];
        }
        
        if (empty($coords)) {
            return ['lat' => null, 'lng' => null];
        }
        
        // Calculate centroid
        $sumLat = 0;
        $sumLng = 0;
        $count = count($coords);
        
        foreach ($coords as $coord) {
            $sumLng += $coord[0];
            $sumLat += $coord[1];
        }
        
        return [
            'lat' => $sumLat / $count,
            'lng' => $sumLng / $count,
        ];
    }

    /**
     * Get wilayah boundaries GeoJSON
     */
    public function getWilayahBoundaries()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        
        $geojson = $this->wilayahModel->getGeoJsonFeatureCollection($kodeDesa, 'DUSUN');
        
        return $this->response->setJSON($geojson);
    }

    /**
     * Get wilayah with coordinates for markers
     */
    public function getWilayahMarkers()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        
        $wilayahs = $this->wilayahModel->getWithCoordinates($kodeDesa, 'DUSUN');
        
        return $this->response->setJSON($wilayahs);
    }
}


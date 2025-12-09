<?php

namespace App\Controllers;

use App\Models\ProyekModel;
use App\Models\ProyekLogModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Pembangunan Controller - Infrastructure Monitoring
 * 
 * Features:
 * - Dashboard with Financial vs Physical comparison
 * - Red Flag alerts for deviation
 * - Project CRUD with photo uploads
 * - TPK progress input
 * - GIS integration
 */
class Pembangunan extends BaseController
{
    protected $proyekModel;
    protected $logModel;
    protected $user;
    protected $db;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->proyekModel = new ProyekModel();
        $this->logModel = new ProyekLogModel();
        $this->user = session()->get();
        $this->db = \Config\Database::connect();
        
        // Ensure tables exist
        $this->ensureTablesExist();
    }

    private function ensureTablesExist()
    {
        $tables = $this->db->listTables();
        
        if (!in_array('proyek_fisik', $tables)) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS proyek_fisik (
                    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    kode_desa VARCHAR(20) NOT NULL,
                    apbdes_id INT(11) UNSIGNED NULL,
                    kode_kegiatan VARCHAR(50) NULL,
                    nama_proyek VARCHAR(255) NOT NULL,
                    lokasi_detail VARCHAR(255) NULL,
                    volume_target DECIMAL(12,2) NULL,
                    satuan VARCHAR(20) NULL,
                    anggaran DECIMAL(18,2) DEFAULT 0,
                    tgl_mulai DATE NULL,
                    tgl_selesai_target DATE NULL,
                    tgl_selesai_aktual DATE NULL,
                    pelaksana_kegiatan VARCHAR(100) NULL,
                    kontraktor VARCHAR(100) NULL,
                    lat DECIMAL(10,8) NULL,
                    lng DECIMAL(11,8) NULL,
                    foto_0 VARCHAR(255) NULL,
                    foto_50 VARCHAR(255) NULL,
                    foto_100 VARCHAR(255) NULL,
                    persentase_fisik INT(3) DEFAULT 0,
                    persentase_keuangan DECIMAL(5,2) DEFAULT 0,
                    status ENUM('RENCANA','PROSES','SELESAI','MANGKRAK') DEFAULT 'RENCANA',
                    keterangan TEXT NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    PRIMARY KEY (id),
                    INDEX (kode_desa),
                    INDEX (status)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            $this->db->query("
                CREATE TABLE IF NOT EXISTS proyek_log (
                    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    proyek_id INT(11) UNSIGNED NOT NULL,
                    tanggal_laporan DATE NOT NULL,
                    persentase_fisik INT(3) NOT NULL,
                    volume_terealisasi DECIMAL(12,2) NULL,
                    kendala TEXT NULL,
                    solusi TEXT NULL,
                    foto VARCHAR(255) NULL,
                    pelapor VARCHAR(100) NULL,
                    created_by INT(11) UNSIGNED NULL,
                    created_at DATETIME NULL,
                    PRIMARY KEY (id),
                    INDEX (proyek_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }
    }

    // ========================================
    // DASHBOARD
    // ========================================

    /**
     * Dashboard e-Pembangunan
     */
    public function index()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        
        // Get statistics
        $stats = $this->proyekModel->getDashboardStats($kodeDesa, (int) $tahun);
        
        // Get projects with alerts
        $alertProjects = $this->proyekModel->getAlertProjects($kodeDesa);
        
        // Get recent projects
        $projects = $this->proyekModel->getWithStats($kodeDesa, ['tahun' => $tahun]);
        
        // Get monthly trend
        $monthlyTrend = $this->proyekModel->getMonthlyTrend($kodeDesa, (int) $tahun);
        
        $data = [
            'title'         => 'Dashboard e-Pembangunan - Siskeudes Lite',
            'user'          => $this->user,
            'stats'         => $stats,
            'alertProjects' => $alertProjects,
            'projects'      => $projects,
            'monthlyTrend'  => $monthlyTrend,
            'tahun'         => $tahun,
        ];
        
        return view('pembangunan/dashboard', $data);
    }

    // ========================================
    // PROJECT MANAGEMENT
    // ========================================

    /**
     * List all projects
     */
    public function proyek()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $status = $this->request->getGet('status');
        
        $filters = [];
        if ($status) $filters['status'] = $status;
        
        $projects = $this->proyekModel->getWithStats($kodeDesa, $filters);
        
        // Add deviation info to each project
        foreach ($projects as &$p) {
            $p['deviation'] = $this->proyekModel->calculateDeviation($p);
        }
        
        $data = [
            'title'    => 'Daftar Proyek - Siskeudes Lite',
            'user'     => $this->user,
            'projects' => $projects,
            'status'   => $status,
        ];
        
        return view('pembangunan/proyek/index', $data);
    }

    /**
     * Create new project
     */
    public function createProyek()
    {
        // Get APBDes kegiatan for linking (with fallback if ref_kegiatan doesn't exist)
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $kegiatanList = [];
        
        try {
            // Check if ref_kegiatan table exists
            $tables = $this->db->listTables();
            if (in_array('ref_kegiatan', $tables) && in_array('apbdes', $tables)) {
                $kegiatanList = $this->db->table('apbdes')
                    ->select('apbdes.id, apbdes.kode_kegiatan, ref_kegiatan.uraian, apbdes.pagu_anggaran')
                    ->join('ref_kegiatan', 'ref_kegiatan.kode_kegiatan = apbdes.kode_kegiatan', 'left')
                    ->where('apbdes.kode_desa', $kodeDesa)
                    ->where('apbdes.tahun_anggaran', date('Y'))
                    ->where('apbdes.kode_kegiatan LIKE', '2.%') // Bidang Pembangunan
                    ->get()
                    ->getResultArray();
            } elseif (in_array('apbdes', $tables)) {
                // Fallback without ref_kegiatan
                $kegiatanList = $this->db->table('apbdes')
                    ->select('id, kode_kegiatan, kode_kegiatan as uraian, pagu_anggaran')
                    ->where('kode_desa', $kodeDesa)
                    ->where('tahun_anggaran', date('Y'))
                    ->where('kode_kegiatan LIKE', '2.%')
                    ->get()
                    ->getResultArray();
            }
        } catch (\Exception $e) {
            // Log error but continue with empty list
            log_message('warning', 'Error fetching kegiatan: ' . $e->getMessage());
        }
        
        $data = [
            'title'         => 'Tambah Proyek Baru - Siskeudes Lite',
            'user'          => $this->user,
            'kegiatanList'  => $kegiatanList,
            'satuanOptions' => ProyekModel::getSatuanOptions(),
        ];
        
        return view('pembangunan/proyek/form', $data);
    }

    /**
     * Save new project
     */
    public function saveProyek()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        
        $data = [
            'kode_desa'          => $kodeDesa,
            'apbdes_id'          => $this->request->getPost('apbdes_id') ?: null,
            'kode_kegiatan'      => $this->request->getPost('kode_kegiatan'),
            'nama_proyek'        => $this->request->getPost('nama_proyek'),
            'lokasi_detail'      => $this->request->getPost('lokasi_detail'),
            'volume_target'      => $this->request->getPost('volume_target'),
            'satuan'             => $this->request->getPost('satuan'),
            'anggaran'           => str_replace(['.', ','], ['', '.'], $this->request->getPost('anggaran')),
            'tgl_mulai'          => $this->request->getPost('tgl_mulai'),
            'tgl_selesai_target' => $this->request->getPost('tgl_selesai_target'),
            'pelaksana_kegiatan' => $this->request->getPost('pelaksana_kegiatan'),
            'kontraktor'         => $this->request->getPost('kontraktor'),
            'lat'                => $this->request->getPost('lat') ?: null,
            'lng'                => $this->request->getPost('lng') ?: null,
            'keterangan'         => $this->request->getPost('keterangan'),
            'status'             => 'RENCANA',
        ];
        
        // Handle foto 0%
        $foto = $this->request->getFile('foto_0');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $newName = $foto->getRandomName();
            $foto->move(WRITEPATH . 'uploads/proyek', $newName);
            $data['foto_0'] = 'writable/uploads/proyek/' . $newName;
        }
        
        $this->proyekModel->insert($data);
        
        return redirect()->to('/pembangunan/proyek')
            ->with('success', 'Proyek berhasil ditambahkan');
    }

    /**
     * View project detail
     */
    public function detailProyek($id)
    {
        $project = $this->proyekModel->getWithLogs($id);
        
        if (!$project) {
            return redirect()->to('/pembangunan/proyek')
                ->with('error', 'Proyek tidak ditemukan');
        }
        
        // Get progress timeline for chart
        $timeline = $this->logModel->getProgressTimeline($id);
        
        $data = [
            'title'    => 'Detail Proyek - ' . $project['nama_proyek'],
            'user'     => $this->user,
            'project'  => $project,
            'timeline' => $timeline,
        ];
        
        return view('pembangunan/proyek/detail', $data);
    }

    // ========================================
    // PROGRESS INPUT (TPK)
    // ========================================

    /**
     * Form input progress
     */
    public function inputProgress($proyekId)
    {
        $project = $this->proyekModel->find($proyekId);
        
        if (!$project) {
            return redirect()->to('/pembangunan/proyek')
                ->with('error', 'Proyek tidak ditemukan');
        }
        
        $data = [
            'title'   => 'Input Progres - ' . $project['nama_proyek'],
            'user'    => $this->user,
            'project' => $project,
        ];
        
        return view('pembangunan/proyek/input_progress', $data);
    }

    /**
     * Save progress
     */
    public function saveProgress()
    {
        $proyekId = $this->request->getPost('proyek_id');
        
        // Validate foto is required
        $foto = $this->request->getFile('foto');
        if (!$foto || !$foto->isValid() || $foto->hasMoved()) {
            return redirect()->back()
                ->with('error', 'Foto dokumentasi wajib dilampirkan untuk setiap laporan progres')
                ->withInput();
        }
        
        $data = [
            'proyek_id'          => $proyekId,
            'tanggal_laporan'    => $this->request->getPost('tanggal_laporan'),
            'persentase_fisik'   => (int) $this->request->getPost('persentase_fisik'),
            'volume_terealisasi' => $this->request->getPost('volume_terealisasi'),
            'kendala'            => $this->request->getPost('kendala'),
            'solusi'             => $this->request->getPost('solusi'),
            'pelapor'            => $this->request->getPost('pelapor'),
            'created_by'         => $this->user['id'] ?? null,
            'created_at'         => date('Y-m-d H:i:s'),
        ];
        
        // Handle foto (already validated above)
        $newName = $foto->getRandomName();
        $foto->move(WRITEPATH . 'uploads/proyek', $newName);
        $data['foto'] = 'writable/uploads/proyek/' . $newName;
        
        $this->logModel->addProgress($data);
        
        // Update financial realization
        $this->proyekModel->updateKeuanganRealization($proyekId);
        
        return redirect()->to('/pembangunan/proyek/detail/' . $proyekId)
            ->with('success', 'Progres berhasil disimpan dengan foto dokumentasi');
    }

    // ========================================
    // GIS INTEGRATION
    // ========================================

    /**
     * Get projects for GIS layer
     */
    public function getGisData()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $projects = $this->proyekModel->getForGis($kodeDesa);
        
        $features = [];
        foreach ($projects as $p) {
            $deviation = $this->proyekModel->calculateDeviation($p);
            
            $features[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $p['lng'], (float) $p['lat']],
                ],
                'properties' => [
                    'id'                => $p['id'],
                    'nama'              => $p['nama_proyek'],
                    'lokasi'            => $p['lokasi_detail'],
                    'anggaran'          => (float) $p['anggaran'],
                    'persentase_fisik'  => (int) $p['persentase_fisik'],
                    'persentase_keuangan' => (float) $p['persentase_keuangan'],
                    'status'            => $p['status'],
                    'deviation'         => $deviation['value'],
                    'is_alert'          => $deviation['is_alert'],
                    'foto'              => $p['foto_terbaru'] ?? $p['foto_0'] ?? null,
                ],
            ];
        }
        
        return $this->response->setJSON([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    // ========================================
    // MONITORING & ALERTS
    // ========================================

    /**
     * Deviation monitoring page
     */
    public function monitoring()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        
        // Get all ongoing projects with deviation info
        $projects = $this->proyekModel->where('kode_desa', $kodeDesa)
            ->where('status', 'PROSES')
            ->orderBy('persentase_keuangan', 'DESC')
            ->findAll();
        
        foreach ($projects as &$p) {
            $p['deviation'] = $this->proyekModel->calculateDeviation($p);
        }
        
        // Sort by deviation (highest alert first)
        usort($projects, fn($a, $b) => $b['deviation']['value'] <=> $a['deviation']['value']);
        
        $data = [
            'title'    => 'Monitoring Deviasi - Siskeudes Lite',
            'user'     => $this->user,
            'projects' => $projects,
        ];
        
        return view('pembangunan/monitoring', $data);
    }

    /**
     * Update project status to Mangkrak
     */
    public function setMangkrak($id)
    {
        $project = $this->proyekModel->find($id);
        
        if (!$project) {
            return redirect()->to('/pembangunan/proyek')
                ->with('error', 'Proyek tidak ditemukan');
        }
        
        $this->proyekModel->update($id, [
            'status' => 'MANGKRAK',
            'keterangan' => $project['keterangan'] . "\n[" . date('Y-m-d') . "] Status diubah menjadi MANGKRAK",
        ]);
        
        return redirect()->to('/pembangunan/proyek/detail/' . $id)
            ->with('warning', 'Status proyek diubah menjadi MANGKRAK');
    }
}

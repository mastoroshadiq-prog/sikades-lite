<?php

namespace App\Controllers;

use App\Models\PosyanduModel;
use App\Models\PemeriksaanModel;
use App\Models\IbuHamilModel;
use App\Models\PendudukModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Posyandu extends BaseController
{
    protected $posyanduModel;
    protected $pemeriksaanModel;
    protected $ibuHamilModel;
    protected $pendudukModel;
    protected $user;
    protected $db;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->posyanduModel = new PosyanduModel();
        $this->pemeriksaanModel = new PemeriksaanModel();
        $this->ibuHamilModel = new IbuHamilModel();
        $this->pendudukModel = new PendudukModel();
        $this->user = session()->get();
        $this->db = \Config\Database::connect();
        
        // Check and create tables if not exist
        $this->ensureTablesExist();
    }

    /**
     * Ensure all kesehatan tables exist
     */
    private function ensureTablesExist()
    {
        $tables = $this->db->listTables();
        
        if (!in_array('kes_posyandu', $tables)) {
            // Create tables
            $this->db->query("
                CREATE TABLE IF NOT EXISTS kes_posyandu (
                    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    kode_desa VARCHAR(20) NOT NULL,
                    nama_posyandu VARCHAR(100) NOT NULL,
                    alamat_dusun VARCHAR(100) NULL,
                    rt VARCHAR(5) NULL,
                    rw VARCHAR(5) NULL,
                    ketua_posyandu VARCHAR(100) NULL,
                    no_telp VARCHAR(20) NULL,
                    lat DECIMAL(10,8) NULL,
                    lng DECIMAL(11,8) NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    PRIMARY KEY (id),
                    INDEX (kode_desa)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            $this->db->query("
                CREATE TABLE IF NOT EXISTS kes_kader (
                    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    posyandu_id INT(11) UNSIGNED NOT NULL,
                    penduduk_id INT(11) UNSIGNED NULL,
                    nama_kader VARCHAR(100) NOT NULL,
                    jabatan VARCHAR(50) NULL,
                    no_telp VARCHAR(20) NULL,
                    status ENUM('AKTIF','TIDAK_AKTIF') DEFAULT 'AKTIF',
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    PRIMARY KEY (id),
                    INDEX (posyandu_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            $this->db->query("
                CREATE TABLE IF NOT EXISTS kes_pemeriksaan (
                    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    posyandu_id INT(11) UNSIGNED NOT NULL,
                    penduduk_id INT(11) UNSIGNED NOT NULL,
                    tanggal_periksa DATE NOT NULL,
                    usia_bulan INT(3) NOT NULL,
                    berat_badan DECIMAL(5,2) NOT NULL,
                    tinggi_badan DECIMAL(5,2) NOT NULL,
                    lingkar_kepala DECIMAL(5,2) NULL,
                    lingkar_lengan DECIMAL(5,2) NULL,
                    vitamin_a BOOLEAN DEFAULT FALSE,
                    imunisasi VARCHAR(255) NULL,
                    asi_eksklusif BOOLEAN DEFAULT FALSE,
                    status_gizi ENUM('BURUK','KURANG','BAIK','LEBIH','OBESITAS') DEFAULT 'BAIK',
                    z_score_bb_u DECIMAL(4,2) NULL,
                    z_score_tb_u DECIMAL(4,2) NULL,
                    z_score_bb_tb DECIMAL(4,2) NULL,
                    indikasi_stunting BOOLEAN DEFAULT FALSE,
                    indikasi_gizi_buruk BOOLEAN DEFAULT FALSE,
                    keterangan TEXT NULL,
                    created_by INT(11) UNSIGNED NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    PRIMARY KEY (id),
                    INDEX (posyandu_id),
                    INDEX (penduduk_id),
                    INDEX (indikasi_stunting)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            $this->db->query("
                CREATE TABLE IF NOT EXISTS kes_ibu_hamil (
                    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    posyandu_id INT(11) UNSIGNED NOT NULL,
                    penduduk_id INT(11) UNSIGNED NOT NULL,
                    tanggal_hpht DATE NULL,
                    taksiran_persalinan DATE NULL,
                    usia_kandungan INT(2) NULL,
                    kehamilan_ke INT(2) DEFAULT 1,
                    tinggi_badan_ibu DECIMAL(5,2) NULL,
                    berat_badan_sebelum DECIMAL(5,2) NULL,
                    golongan_darah VARCHAR(5) NULL,
                    resiko_tinggi BOOLEAN DEFAULT FALSE,
                    faktor_resiko TEXT NULL,
                    pemeriksaan_k1 DATE NULL,
                    pemeriksaan_k2 DATE NULL,
                    pemeriksaan_k3 DATE NULL,
                    pemeriksaan_k4 DATE NULL,
                    status ENUM('HAMIL','MELAHIRKAN','KEGUGURAN','BATAL') DEFAULT 'HAMIL',
                    tanggal_persalinan DATE NULL,
                    keterangan TEXT NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    PRIMARY KEY (id),
                    INDEX (posyandu_id),
                    INDEX (penduduk_id),
                    INDEX (status)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            $this->db->query("
                CREATE TABLE IF NOT EXISTS kes_standar_who (
                    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    jenis_kelamin ENUM('L','P') NOT NULL,
                    usia_bulan INT(3) NOT NULL,
                    indikator VARCHAR(20) NOT NULL,
                    median DECIMAL(6,2) NOT NULL,
                    sd_min3 DECIMAL(6,2) NOT NULL,
                    sd_min2 DECIMAL(6,2) NOT NULL,
                    sd_min1 DECIMAL(6,2) NOT NULL,
                    sd_plus1 DECIMAL(6,2) NOT NULL,
                    sd_plus2 DECIMAL(6,2) NOT NULL,
                    sd_plus3 DECIMAL(6,2) NOT NULL,
                    PRIMARY KEY (id),
                    INDEX (jenis_kelamin, usia_bulan, indikator)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }
    }

    // ========================================
    // DASHBOARD
    // ========================================

    /**
     * Dashboard e-Posyandu
     */
    public function index()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        
        // Get posyandu list with stats
        $posyanduList = $this->posyanduModel->getWithStats($kodeDesa);
        
        // Get stunting stats
        $stuntingStats = $this->pemeriksaanModel->getStuntingStats($kodeDesa);
        
        // Get pregnancy stats
        $bumpilStats = $this->ibuHamilModel->getStats($kodeDesa);
        
        // Get monthly trend
        $monthlyTrend = $this->pemeriksaanModel->getMonthlyTrend($kodeDesa, $tahun);
        
        $data = [
            'title'         => 'Dashboard e-Posyandu - Siskeudes Lite',
            'user'          => $this->user,
            'posyanduList'  => $posyanduList,
            'stuntingStats' => $stuntingStats,
            'bumpilStats'   => $bumpilStats,
            'monthlyTrend'  => $monthlyTrend,
            'tahun'         => $tahun,
        ];
        
        return view('posyandu/dashboard', $data);
    }

    // ========================================
    // POSYANDU MANAGEMENT
    // ========================================

    /**
     * List posyandu
     */
    public function posyandu()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $posyanduList = $this->posyanduModel->getWithStats($kodeDesa);
        
        $data = [
            'title'        => 'Data Posyandu - Siskeudes Lite',
            'user'         => $this->user,
            'posyanduList' => $posyanduList,
        ];
        
        return view('posyandu/posyandu/index', $data);
    }

    /**
     * Create posyandu form
     */
    public function createPosyandu()
    {
        $data = [
            'title' => 'Tambah Posyandu - Siskeudes Lite',
            'user'  => $this->user,
        ];
        
        return view('posyandu/posyandu/form', $data);
    }

    /**
     * Save posyandu
     */
    public function savePosyandu()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        
        $data = [
            'kode_desa'      => $kodeDesa,
            'nama_posyandu'  => $this->request->getPost('nama_posyandu'),
            'alamat_dusun'   => $this->request->getPost('alamat_dusun'),
            'rt'             => $this->request->getPost('rt'),
            'rw'             => $this->request->getPost('rw'),
            'ketua_posyandu' => $this->request->getPost('ketua_posyandu'),
            'no_telp'        => $this->request->getPost('no_telp'),
            'lat'            => $this->request->getPost('lat') ?: null,
            'lng'            => $this->request->getPost('lng') ?: null,
        ];
        
        $this->posyanduModel->insert($data);
        
        return redirect()->to('/posyandu/posyandu')->with('success', 'Posyandu berhasil ditambahkan');
    }

    /**
     * Detail posyandu
     */
    public function detailPosyandu($id)
    {
        $posyandu = $this->posyanduModel->find($id);
        
        if (!$posyandu) {
            return redirect()->to('/posyandu/posyandu')->with('error', 'Data tidak ditemukan');
        }
        
        // Get kader
        $kaderList = $this->db->table('kes_kader')
            ->where('posyandu_id', $id)
            ->get()
            ->getResultArray();
        
        // Get recent pemeriksaan
        $pemeriksaanList = $this->pemeriksaanModel->getWithPenduduk($id, ['tahun' => date('Y')]);
        
        // Get ibu hamil
        $bumpilList = $this->ibuHamilModel->getWithPenduduk($id, ['status' => 'HAMIL']);
        
        $data = [
            'title'           => 'Detail Posyandu - ' . $posyandu['nama_posyandu'],
            'user'            => $this->user,
            'posyandu'        => $posyandu,
            'kaderList'       => $kaderList,
            'pemeriksaanList' => $pemeriksaanList,
            'bumpilList'      => $bumpilList,
        ];
        
        return view('posyandu/posyandu/detail', $data);
    }

    // ========================================
    // PEMERIKSAAN BALITA
    // ========================================

    /**
     * Form input pemeriksaan
     */
    public function createPemeriksaan($posyanduId)
    {
        $posyandu = $this->posyanduModel->find($posyanduId);
        
        if (!$posyandu) {
            return redirect()->to('/posyandu')->with('error', 'Posyandu tidak ditemukan');
        }
        
        // Get balita candidates (children under 5 years)
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $balitaList = $this->pendudukModel->getBalita($kodeDesa);
        
        $data = [
            'title'      => 'Input Pemeriksaan Balita - ' . $posyandu['nama_posyandu'],
            'user'       => $this->user,
            'posyandu'   => $posyandu,
            'balitaList' => $balitaList,
        ];
        
        return view('posyandu/pemeriksaan/form', $data);
    }

    /**
     * Save pemeriksaan with auto stunting calculation
     */
    public function savePemeriksaan()
    {
        $pendudukId = $this->request->getPost('penduduk_id');
        $posyanduId = $this->request->getPost('posyandu_id');
        
        // Get penduduk data for calculation
        $penduduk = $this->pendudukModel->find($pendudukId);
        
        if (!$penduduk) {
            return redirect()->back()->with('error', 'Data penduduk tidak ditemukan');
        }
        
        $tanggalPeriksa = $this->request->getPost('tanggal_periksa');
        $beratBadan = (float) $this->request->getPost('berat_badan');
        $tinggiBadan = (float) $this->request->getPost('tinggi_badan');
        
        // Calculate health assessment (stunting, gizi)
        $assessment = $this->pemeriksaanModel->assessHealth([
            'tanggal_periksa' => $tanggalPeriksa,
            'berat_badan'     => $beratBadan,
            'tinggi_badan'    => $tinggiBadan,
        ], $penduduk['jenis_kelamin'], $penduduk['tanggal_lahir']);
        
        $data = [
            'posyandu_id'         => $posyanduId,
            'penduduk_id'         => $pendudukId,
            'tanggal_periksa'     => $tanggalPeriksa,
            'usia_bulan'          => $assessment['usia_bulan'],
            'berat_badan'         => $beratBadan,
            'tinggi_badan'        => $tinggiBadan,
            'lingkar_kepala'      => $this->request->getPost('lingkar_kepala') ?: null,
            'lingkar_lengan'      => $this->request->getPost('lingkar_lengan') ?: null,
            'vitamin_a'           => $this->request->getPost('vitamin_a') ? true : false,
            'imunisasi'           => $this->request->getPost('imunisasi'),
            'asi_eksklusif'       => $this->request->getPost('asi_eksklusif') ? true : false,
            'z_score_bb_u'        => $assessment['z_score_bb_u'],
            'z_score_tb_u'        => $assessment['z_score_tb_u'],
            'status_gizi'         => $assessment['status_gizi'],
            'indikasi_stunting'   => $assessment['indikasi_stunting'],
            'indikasi_gizi_buruk' => $assessment['indikasi_gizi_buruk'],
            'keterangan'          => $this->request->getPost('keterangan'),
            'created_by'          => $this->user['id'] ?? null,
        ];
        
        $this->pemeriksaanModel->insert($data);
        
        // Prepare message
        $message = 'Data pemeriksaan berhasil disimpan.';
        if ($assessment['indikasi_stunting']) {
            return redirect()->to('/posyandu/posyandu/detail/' . $posyanduId)
                ->with('warning', $message . ' ⚠️ PERHATIAN: Anak terindikasi STUNTING! (' . $assessment['kategori_stunting'] . ')');
        }
        
        return redirect()->to('/posyandu/posyandu/detail/' . $posyanduId)
            ->with('success', $message);
    }

    /**
     * Riwayat pemeriksaan balita
     */
    public function riwayatBalita($pendudukId)
    {
        $penduduk = $this->pendudukModel->getDetail($pendudukId);
        
        if (!$penduduk) {
            return redirect()->to('/posyandu')->with('error', 'Data tidak ditemukan');
        }
        
        $riwayat = $this->pemeriksaanModel->getRiwayatBalita($pendudukId);
        
        $data = [
            'title'    => 'Riwayat Pemeriksaan - ' . $penduduk['nama_lengkap'],
            'user'     => $this->user,
            'penduduk' => $penduduk,
            'riwayat'  => $riwayat,
        ];
        
        return view('posyandu/pemeriksaan/riwayat', $data);
    }

    // ========================================
    // STUNTING MONITORING
    // ========================================

    /**
     * Stunting dashboard
     */
    public function stunting()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        
        $stuntingStats = $this->pemeriksaanModel->getStuntingStats($kodeDesa);
        $stuntingCases = $this->pemeriksaanModel->getStuntingForGis($kodeDesa);
        
        $data = [
            'title'         => 'Monitoring Stunting - Siskeudes Lite',
            'user'          => $this->user,
            'stuntingStats' => $stuntingStats,
            'stuntingCases' => $stuntingCases,
        ];
        
        return view('posyandu/stunting/index', $data);
    }

    /**
     * Get stunting data for GIS layer
     */
    public function getStuntingGis()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $cases = $this->pemeriksaanModel->getStuntingForGis($kodeDesa);
        
        $features = [];
        foreach ($cases as $case) {
            if ($case['lat'] && $case['lng']) {
                $features[] = [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [(float) $case['lng'], (float) $case['lat']],
                    ],
                    'properties' => [
                        'id'            => $case['id'],
                        'nama'          => $case['nama_lengkap'],
                        'usia_bulan'    => $case['usia_bulan'],
                        'tinggi_badan'  => $case['tinggi_badan'],
                        'z_score'       => $case['z_score_tb_u'],
                        'dusun'         => $case['dusun'],
                        'posyandu'      => $case['nama_posyandu'],
                    ],
                ];
            }
        }
        
        return $this->response->setJSON([
            'type' => 'FeatureCollection',
            'features' => $features,
            'stats' => $this->pemeriksaanModel->getStuntingStats($kodeDesa),
        ]);
    }

    // ========================================
    // IBU HAMIL
    // ========================================

    /**
     * Create ibu hamil record
     */
    public function createBumil($posyanduId)
    {
        $posyandu = $this->posyanduModel->find($posyanduId);
        
        if (!$posyandu) {
            return redirect()->to('/posyandu')->with('error', 'Posyandu tidak ditemukan');
        }
        
        // Get wanita usia subur
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $wusList = $this->pendudukModel->getWUS($kodeDesa);
        
        $data = [
            'title'          => 'Pendaftaran Ibu Hamil - ' . $posyandu['nama_posyandu'],
            'user'           => $this->user,
            'posyandu'       => $posyandu,
            'wusList'        => $wusList,
            'faktorResiko'   => IbuHamilModel::getFaktorResikoOptions(),
        ];
        
        return view('posyandu/bumil/form', $data);
    }

    /**
     * Save ibu hamil
     */
    public function saveBumil()
    {
        $hpht = $this->request->getPost('tanggal_hpht');
        $hpl = $this->ibuHamilModel->calculateHPL($hpht);
        $usiaKandungan = $this->ibuHamilModel->calculateUsiaKandungan($hpht);
        
        $faktorResiko = $this->request->getPost('faktor_resiko');
        $isRisti = !empty($faktorResiko);
        
        $data = [
            'posyandu_id'         => $this->request->getPost('posyandu_id'),
            'penduduk_id'         => $this->request->getPost('penduduk_id'),
            'tanggal_hpht'        => $hpht,
            'taksiran_persalinan' => $hpl,
            'usia_kandungan'      => $usiaKandungan,
            'kehamilan_ke'        => $this->request->getPost('kehamilan_ke'),
            'tinggi_badan_ibu'    => $this->request->getPost('tinggi_badan_ibu'),
            'berat_badan_sebelum' => $this->request->getPost('berat_badan_sebelum'),
            'golongan_darah'      => $this->request->getPost('golongan_darah'),
            'resiko_tinggi'       => $isRisti,
            'faktor_resiko'       => is_array($faktorResiko) ? implode(', ', $faktorResiko) : $faktorResiko,
            'pemeriksaan_k1'      => $this->request->getPost('pemeriksaan_k1') ?: null,
            'keterangan'          => $this->request->getPost('keterangan'),
            'status'              => 'HAMIL',
        ];
        
        $this->ibuHamilModel->insert($data);
        
        $posyanduId = $this->request->getPost('posyandu_id');
        
        if ($isRisti) {
            return redirect()->to('/posyandu/posyandu/detail/' . $posyanduId)
                ->with('warning', 'Data ibu hamil berhasil disimpan. ⚠️ PERHATIAN: Ibu hamil terdeteksi RESIKO TINGGI!');
        }
        
        return redirect()->to('/posyandu/posyandu/detail/' . $posyanduId)
            ->with('success', 'Data ibu hamil berhasil disimpan');
    }

    /**
     * List ibu hamil resiko tinggi
     */
    public function bumilRisti()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $ristiCases = $this->ibuHamilModel->getRistiCases($kodeDesa);
        
        $data = [
            'title'      => 'Ibu Hamil Resiko Tinggi - Siskeudes Lite',
            'user'       => $this->user,
            'ristiCases' => $ristiCases,
        ];
        
        return view('posyandu/bumil/risti', $data);
    }

    // ========================================
    // KADER MANAGEMENT
    // ========================================

    /**
     * Form tambah kader
     */
    public function createKader($posyanduId)
    {
        $posyandu = $this->posyanduModel->find($posyanduId);
        
        if (!$posyandu) {
            return redirect()->to('/posyandu')->with('error', 'Posyandu tidak ditemukan');
        }
        
        // Get penduduk for dropdown
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $pendudukList = $this->pendudukModel
            ->select('pop_penduduk.*, pop_keluarga.dusun')
            ->join('pop_keluarga', 'pop_keluarga.id = pop_penduduk.keluarga_id')
            ->where('pop_keluarga.kode_desa', $kodeDesa)
            ->where('pop_penduduk.status_dasar', 'HIDUP')
            ->orderBy('pop_penduduk.nama_lengkap')
            ->findAll();
        
        $data = [
            'title'        => 'Tambah Kader - ' . $posyandu['nama_posyandu'],
            'user'         => $this->user,
            'posyandu'     => $posyandu,
            'pendudukList' => $pendudukList,
        ];
        
        return view('posyandu/kader/form', $data);
    }

    /**
     * Simpan kader baru
     */
    public function saveKader()
    {
        $posyanduId = $this->request->getPost('posyandu_id');
        
        $data = [
            'posyandu_id' => $posyanduId,
            'penduduk_id' => $this->request->getPost('penduduk_id') ?: null,
            'nama_kader'  => $this->request->getPost('nama_kader'),
            'jabatan'     => $this->request->getPost('jabatan'),
            'no_telp'     => $this->request->getPost('no_telp'),
            'status'      => $this->request->getPost('status') ?: 'AKTIF',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        
        $this->db->table('kes_kader')->insert($data);
        
        return redirect()->to('/posyandu/posyandu/detail/' . $posyanduId)
            ->with('success', 'Kader berhasil ditambahkan');
    }

    /**
     * Edit kader
     */
    public function editKader($id)
    {
        $kader = $this->db->table('kes_kader')->where('id', $id)->get()->getRowArray();
        
        if (!$kader) {
            return redirect()->to('/posyandu')->with('error', 'Data kader tidak ditemukan');
        }
        
        $posyandu = $this->posyanduModel->find($kader['posyandu_id']);
        
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $pendudukList = $this->pendudukModel
            ->select('pop_penduduk.*, pop_keluarga.dusun')
            ->join('pop_keluarga', 'pop_keluarga.id = pop_penduduk.keluarga_id')
            ->where('pop_keluarga.kode_desa', $kodeDesa)
            ->where('pop_penduduk.status_dasar', 'HIDUP')
            ->orderBy('pop_penduduk.nama_lengkap')
            ->findAll();
        
        $data = [
            'title'        => 'Edit Kader - ' . $kader['nama_kader'],
            'user'         => $this->user,
            'posyandu'     => $posyandu,
            'kader'        => $kader,
            'pendudukList' => $pendudukList,
        ];
        
        return view('posyandu/kader/form', $data);
    }

    /**
     * Update kader
     */
    public function updateKader($id)
    {
        $kader = $this->db->table('kes_kader')->where('id', $id)->get()->getRowArray();
        
        if (!$kader) {
            return redirect()->to('/posyandu')->with('error', 'Data kader tidak ditemukan');
        }
        
        $data = [
            'penduduk_id' => $this->request->getPost('penduduk_id') ?: null,
            'nama_kader'  => $this->request->getPost('nama_kader'),
            'jabatan'     => $this->request->getPost('jabatan'),
            'no_telp'     => $this->request->getPost('no_telp'),
            'status'      => $this->request->getPost('status'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        
        $this->db->table('kes_kader')->where('id', $id)->update($data);
        
        return redirect()->to('/posyandu/posyandu/detail/' . $kader['posyandu_id'])
            ->with('success', 'Data kader berhasil diupdate');
    }

    /**
     * Hapus kader
     */
    public function deleteKader($id)
    {
        $kader = $this->db->table('kes_kader')->where('id', $id)->get()->getRowArray();
        
        if (!$kader) {
            return redirect()->to('/posyandu')->with('error', 'Data kader tidak ditemukan');
        }
        
        $posyanduId = $kader['posyandu_id'];
        
        $this->db->table('kes_kader')->where('id', $id)->delete();
        
        return redirect()->to('/posyandu/posyandu/detail/' . $posyanduId)
            ->with('success', 'Data kader berhasil dihapus');
    }
}


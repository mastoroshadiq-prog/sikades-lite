<?php

namespace App\Controllers;

use App\Models\KeluargaModel;
use App\Models\PendudukModel;
use App\Models\MutasiModel;

class Demografi extends BaseController
{
    protected $keluargaModel;
    protected $pendudukModel;
    protected $mutasiModel;
    protected $user;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->keluargaModel = new KeluargaModel();
        $this->pendudukModel = new PendudukModel();
        $this->mutasiModel   = new MutasiModel();
        $this->user          = session()->get();
    }

    // ========================================
    // DASHBOARD
    // ========================================

    /**
     * Dashboard Demografi dengan statistik lengkap
     */
    public function index()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $tahun = date('Y');

        // Get all statistics
        $summary = $this->pendudukModel->getSummary($kodeDesa);
        $agePyramid = $this->pendudukModel->getAgePyramid($kodeDesa);
        $educationStats = $this->pendudukModel->getEducationStats($kodeDesa);
        $occupationStats = $this->pendudukModel->getOccupationStats($kodeDesa);
        $religionStats = $this->pendudukModel->getReligionStats($kodeDesa);
        $maritalStats = $this->pendudukModel->getMaritalStats($kodeDesa);
        $mutasiStats = $this->mutasiModel->getYearlyStats($kodeDesa, $tahun);
        $wilayahStats = $this->keluargaModel->getStatsByWilayah($kodeDesa, 'dusun');

        // Recent mutasi
        $recentMutasi = $this->mutasiModel->getWithPenduduk($kodeDesa, ['tahun' => $tahun]);
        $recentMutasi = array_slice($recentMutasi, 0, 10);

        $data = [
            'title'           => 'Demografi Desa - Siskeudes Lite',
            'user'            => $this->user,
            'tahun'           => $tahun,
            'summary'         => $summary,
            'agePyramid'      => $agePyramid,
            'educationStats'  => $educationStats,
            'occupationStats' => $occupationStats,
            'religionStats'   => $religionStats,
            'maritalStats'    => $maritalStats,
            'mutasiStats'     => $mutasiStats,
            'wilayahStats'    => $wilayahStats,
            'recentMutasi'    => $recentMutasi,
        ];

        return view('demografi/index', $data);
    }

    // ========================================
    // KELUARGA (KK) CRUD
    // ========================================

    /**
     * List semua Kartu Keluarga
     */
    public function keluarga()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $search = $this->request->getGet('search');

        if ($search) {
            $keluargaList = $this->keluargaModel->search($kodeDesa, $search);
        } else {
            $keluargaList = $this->keluargaModel->getWithMemberCount($kodeDesa);
        }

        $data = [
            'title'        => 'Data Kartu Keluarga - Siskeudes Lite',
            'user'         => $this->user,
            'keluargaList' => $keluargaList,
            'search'       => $search,
        ];

        return view('demografi/keluarga/index', $data);
    }

    /**
     * Form tambah keluarga baru
     */
    public function createKeluarga()
    {
        $data = [
            'title' => 'Tambah Kartu Keluarga - Siskeudes Lite',
            'user'  => $this->user,
        ];

        return view('demografi/keluarga/form', $data);
    }

    /**
     * Simpan keluarga baru
     */
    public function saveKeluarga()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;

        $rules = [
            'no_kk'           => 'required|exact_length[16]|is_unique[pop_keluarga.no_kk]',
            'kepala_keluarga' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kode_desa'       => $kodeDesa,
            'no_kk'           => $this->request->getPost('no_kk'),
            'kepala_keluarga' => $this->request->getPost('kepala_keluarga'),
            'alamat'          => $this->request->getPost('alamat'),
            'rt'              => $this->request->getPost('rt'),
            'rw'              => $this->request->getPost('rw'),
            'dusun'           => $this->request->getPost('dusun'),
            'kode_pos'        => $this->request->getPost('kode_pos'),
        ];

        $this->keluargaModel->insert($data);
        $keluargaId = $this->keluargaModel->getInsertID();

        return redirect()->to('/demografi/keluarga/detail/' . $keluargaId)
            ->with('success', 'Kartu Keluarga berhasil ditambahkan');
    }

    /**
     * Detail Kartu Keluarga dengan daftar anggota
     */
    public function detailKeluarga($id)
    {
        $keluarga = $this->keluargaModel->getWithMembers($id);
        
        if (!$keluarga) {
            return redirect()->to('/demografi/keluarga')->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'title'    => 'Detail KK ' . $keluarga['no_kk'] . ' - Siskeudes Lite',
            'user'     => $this->user,
            'keluarga' => $keluarga,
        ];

        return view('demografi/keluarga/detail', $data);
    }

    /**
     * Edit Kartu Keluarga
     */
    public function editKeluarga($id)
    {
        $keluarga = $this->keluargaModel->find($id);
        
        if (!$keluarga) {
            return redirect()->to('/demografi/keluarga')->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'title'    => 'Edit Kartu Keluarga - Siskeudes Lite',
            'user'     => $this->user,
            'keluarga' => $keluarga,
        ];

        return view('demografi/keluarga/form', $data);
    }

    /**
     * Update Kartu Keluarga
     */
    public function updateKeluarga($id)
    {
        $keluarga = $this->keluargaModel->find($id);
        
        if (!$keluarga) {
            return redirect()->to('/demografi/keluarga')->with('error', 'Data tidak ditemukan');
        }

        $rules = [
            'no_kk'           => "required|exact_length[16]|is_unique[pop_keluarga.no_kk,id,{$id}]",
            'kepala_keluarga' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'no_kk'           => $this->request->getPost('no_kk'),
            'kepala_keluarga' => $this->request->getPost('kepala_keluarga'),
            'alamat'          => $this->request->getPost('alamat'),
            'rt'              => $this->request->getPost('rt'),
            'rw'              => $this->request->getPost('rw'),
            'dusun'           => $this->request->getPost('dusun'),
            'kode_pos'        => $this->request->getPost('kode_pos'),
        ];

        $this->keluargaModel->update($id, $data);

        return redirect()->to('/demografi/keluarga/detail/' . $id)
            ->with('success', 'Kartu Keluarga berhasil diupdate');
    }

    /**
     * Hapus Kartu Keluarga
     */
    public function deleteKeluarga($id)
    {
        $keluarga = $this->keluargaModel->find($id);
        
        if (!$keluarga) {
            return redirect()->to('/demografi/keluarga')->with('error', 'Data tidak ditemukan');
        }

        // Check if has members
        $memberCount = $this->pendudukModel->where('keluarga_id', $id)->countAllResults();
        if ($memberCount > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus KK yang masih memiliki anggota');
        }

        $this->keluargaModel->delete($id);

        return redirect()->to('/demografi/keluarga')
            ->with('success', 'Kartu Keluarga berhasil dihapus');
    }

    // ========================================
    // PENDUDUK CRUD
    // ========================================

    /**
     * List semua penduduk
     */
    public function penduduk()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $search = $this->request->getGet('search');
        
        $filters = [
            'status_dasar'  => $this->request->getGet('status') ?: 'HIDUP',
            'jenis_kelamin' => $this->request->getGet('gender'),
            'dusun'         => $this->request->getGet('dusun'),
            'is_miskin'     => $this->request->getGet('miskin'),
        ];

        if ($search) {
            $pendudukList = $this->pendudukModel->search($kodeDesa, $search);
        } else {
            $pendudukList = $this->pendudukModel->getWithKeluarga($kodeDesa, $filters);
        }

        // Get filter options
        $dusunList = $this->keluargaModel->getStatsByWilayah($kodeDesa, 'dusun');

        $data = [
            'title'        => 'Data Penduduk - Siskeudes Lite',
            'user'         => $this->user,
            'pendudukList' => $pendudukList,
            'filters'      => $filters,
            'dusunList'    => $dusunList,
            'search'       => $search,
        ];

        return view('demografi/penduduk/index', $data);
    }

    /**
     * Form tambah penduduk
     */
    public function createPenduduk($keluargaId = null)
    {
        $keluarga = null;
        if ($keluargaId) {
            $keluarga = $this->keluargaModel->find($keluargaId);
        }

        // Get all keluarga for dropdown
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $keluargaList = $this->keluargaModel->where('kode_desa', $kodeDesa)->findAll();

        // Get reference data
        $refPendidikan = $this->db->table('ref_pendidikan')->orderBy('urutan')->get()->getResultArray();
        $refPekerjaan = $this->db->table('ref_pekerjaan')->orderBy('nama')->get()->getResultArray();

        $data = [
            'title'          => 'Tambah Penduduk - Siskeudes Lite',
            'user'           => $this->user,
            'keluarga'       => $keluarga,
            'keluargaList'   => $keluargaList,
            'refPendidikan'  => $refPendidikan,
            'refPekerjaan'   => $refPekerjaan,
            'agamaOptions'   => PendudukModel::getAgamaOptions(),
            'kawinOptions'   => PendudukModel::getStatusPerkawinanOptions(),
            'hubunganOptions'=> PendudukModel::getStatusHubunganOptions(),
            'darahOptions'   => PendudukModel::getGolonganDarahOptions(),
        ];

        return view('demografi/penduduk/form', $data);
    }

    /**
     * Simpan penduduk baru
     */
    public function savePenduduk()
    {
        $rules = [
            'keluarga_id'   => 'required|integer',
            'nik'           => 'required|exact_length[16]|is_unique[pop_penduduk.nik]',
            'nama_lengkap'  => 'required|max_length[255]',
            'jenis_kelamin' => 'required|in_list[L,P]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'keluarga_id'         => $this->request->getPost('keluarga_id'),
            'nik'                 => $this->request->getPost('nik'),
            'nama_lengkap'        => $this->request->getPost('nama_lengkap'),
            'tempat_lahir'        => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir'       => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin'       => $this->request->getPost('jenis_kelamin'),
            'agama'               => $this->request->getPost('agama'),
            'pendidikan_terakhir' => $this->request->getPost('pendidikan_terakhir'),
            'pekerjaan'           => $this->request->getPost('pekerjaan'),
            'status_perkawinan'   => $this->request->getPost('status_perkawinan'),
            'status_hubungan'     => $this->request->getPost('status_hubungan'),
            'golongan_darah'      => $this->request->getPost('golongan_darah'),
            'nama_ayah'           => $this->request->getPost('nama_ayah'),
            'nama_ibu'            => $this->request->getPost('nama_ibu'),
            'kewarganegaraan'     => $this->request->getPost('kewarganegaraan') ?: 'WNI',
            'status_dasar'        => 'HIDUP',
            'is_miskin'           => $this->request->getPost('is_miskin') ? 1 : 0,
            'is_disabilitas'      => $this->request->getPost('is_disabilitas') ? 1 : 0,
            'jenis_disabilitas'   => $this->request->getPost('jenis_disabilitas'),
        ];

        $this->pendudukModel->insert($data);
        $pendudukId = $this->pendudukModel->getInsertID();

        // Record mutasi if this is a birth registration
        if ($this->request->getPost('is_kelahiran')) {
            $this->mutasiModel->insert([
                'penduduk_id'       => $pendudukId,
                'jenis_mutasi'      => 'KELAHIRAN',
                'tanggal_peristiwa' => $this->request->getPost('tanggal_lahir'),
                'keterangan'        => 'Registrasi kelahiran baru',
                'created_by'        => $this->user['id'] ?? null,
            ]);
        }

        return redirect()->to('/demografi/penduduk/detail/' . $pendudukId)
            ->with('success', 'Data penduduk berhasil ditambahkan');
    }

    /**
     * Detail penduduk
     */
    public function detailPenduduk($id)
    {
        $penduduk = $this->pendudukModel->getDetail($id);
        
        if (!$penduduk) {
            return redirect()->to('/demografi/penduduk')->with('error', 'Data tidak ditemukan');
        }

        // Get mutasi history
        $mutasiHistory = $this->mutasiModel
            ->where('penduduk_id', $id)
            ->orderBy('tanggal_peristiwa', 'DESC')
            ->findAll();

        // Calculate age
        $penduduk['umur'] = $this->pendudukModel->calculateAge($penduduk['tanggal_lahir']);

        $data = [
            'title'         => 'Detail Penduduk - ' . $penduduk['nama_lengkap'],
            'user'          => $this->user,
            'penduduk'      => $penduduk,
            'mutasiHistory' => $mutasiHistory,
        ];

        return view('demografi/penduduk/detail', $data);
    }

    /**
     * Edit penduduk
     */
    public function editPenduduk($id)
    {
        $penduduk = $this->pendudukModel->find($id);
        
        if (!$penduduk) {
            return redirect()->to('/demografi/penduduk')->with('error', 'Data tidak ditemukan');
        }

        $kodeDesa = $this->user['kode_desa'] ?? null;
        $keluargaList = $this->keluargaModel->where('kode_desa', $kodeDesa)->findAll();
        $refPendidikan = $this->db->table('ref_pendidikan')->orderBy('urutan')->get()->getResultArray();
        $refPekerjaan = $this->db->table('ref_pekerjaan')->orderBy('nama')->get()->getResultArray();

        $data = [
            'title'          => 'Edit Penduduk - Siskeudes Lite',
            'user'           => $this->user,
            'penduduk'       => $penduduk,
            'keluargaList'   => $keluargaList,
            'refPendidikan'  => $refPendidikan,
            'refPekerjaan'   => $refPekerjaan,
            'agamaOptions'   => PendudukModel::getAgamaOptions(),
            'kawinOptions'   => PendudukModel::getStatusPerkawinanOptions(),
            'hubunganOptions'=> PendudukModel::getStatusHubunganOptions(),
            'darahOptions'   => PendudukModel::getGolonganDarahOptions(),
        ];

        return view('demografi/penduduk/form', $data);
    }

    /**
     * Update penduduk
     */
    public function updatePenduduk($id)
    {
        $penduduk = $this->pendudukModel->find($id);
        
        if (!$penduduk) {
            return redirect()->to('/demografi/penduduk')->with('error', 'Data tidak ditemukan');
        }

        $rules = [
            'keluarga_id'   => 'required|integer',
            'nik'           => "required|exact_length[16]|is_unique[pop_penduduk.nik,id,{$id}]",
            'nama_lengkap'  => 'required|max_length[255]',
            'jenis_kelamin' => 'required|in_list[L,P]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'keluarga_id'         => $this->request->getPost('keluarga_id'),
            'nik'                 => $this->request->getPost('nik'),
            'nama_lengkap'        => $this->request->getPost('nama_lengkap'),
            'tempat_lahir'        => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir'       => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin'       => $this->request->getPost('jenis_kelamin'),
            'agama'               => $this->request->getPost('agama'),
            'pendidikan_terakhir' => $this->request->getPost('pendidikan_terakhir'),
            'pekerjaan'           => $this->request->getPost('pekerjaan'),
            'status_perkawinan'   => $this->request->getPost('status_perkawinan'),
            'status_hubungan'     => $this->request->getPost('status_hubungan'),
            'golongan_darah'      => $this->request->getPost('golongan_darah'),
            'nama_ayah'           => $this->request->getPost('nama_ayah'),
            'nama_ibu'            => $this->request->getPost('nama_ibu'),
            'kewarganegaraan'     => $this->request->getPost('kewarganegaraan') ?: 'WNI',
            'is_miskin'           => $this->request->getPost('is_miskin') ? 1 : 0,
            'is_disabilitas'      => $this->request->getPost('is_disabilitas') ? 1 : 0,
            'jenis_disabilitas'   => $this->request->getPost('jenis_disabilitas'),
        ];

        $this->pendudukModel->update($id, $data);

        return redirect()->to('/demografi/penduduk/detail/' . $id)
            ->with('success', 'Data penduduk berhasil diupdate');
    }

    // ========================================
    // MUTASI
    // ========================================

    /**
     * List mutasi
     */
    public function mutasi()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        
        $filters = [
            'jenis_mutasi' => $this->request->getGet('jenis'),
            'tahun'        => $tahun,
        ];

        $mutasiList = $this->mutasiModel->getWithPenduduk($kodeDesa, $filters);
        $yearlyStats = $this->mutasiModel->getYearlyStats($kodeDesa, $tahun);

        $data = [
            'title'       => 'Data Mutasi Penduduk - Siskeudes Lite',
            'user'        => $this->user,
            'mutasiList'  => $mutasiList,
            'yearlyStats' => $yearlyStats,
            'filters'     => $filters,
            'tahun'       => $tahun,
        ];

        return view('demografi/mutasi/index', $data);
    }

    /**
     * Form catat kematian
     */
    public function catatKematian($pendudukId = null)
    {
        $penduduk = null;
        if ($pendudukId) {
            $penduduk = $this->pendudukModel->getDetail($pendudukId);
        }

        // Get list penduduk untuk search
        $kodeDesa = $this->user['kode_desa'] ?? null;

        $data = [
            'title'    => 'Catat Kematian - Siskeudes Lite',
            'user'     => $this->user,
            'penduduk' => $penduduk,
        ];

        return view('demografi/mutasi/kematian', $data);
    }

    /**
     * Simpan data kematian
     */
    public function saveKematian()
    {
        $pendudukId = $this->request->getPost('penduduk_id');
        $tanggal = $this->request->getPost('tanggal_peristiwa');
        $keterangan = $this->request->getPost('keterangan');
        
        $this->mutasiModel->recordKematian(
            $pendudukId, 
            $tanggal, 
            $keterangan, 
            $this->user['id'] ?? null
        );

        return redirect()->to('/demografi/mutasi')
            ->with('success', 'Data kematian berhasil dicatat');
    }

    /**
     * Form catat pindah keluar
     */
    public function catatPindah($pendudukId = null)
    {
        $penduduk = null;
        if ($pendudukId) {
            $penduduk = $this->pendudukModel->getDetail($pendudukId);
        }

        $data = [
            'title'    => 'Catat Pindah Keluar - Siskeudes Lite',
            'user'     => $this->user,
            'penduduk' => $penduduk,
        ];

        return view('demografi/mutasi/pindah', $data);
    }

    /**
     * Simpan data pindah
     */
    public function savePindah()
    {
        $pendudukId = $this->request->getPost('penduduk_id');
        $tanggal = $this->request->getPost('tanggal_peristiwa');
        $keterangan = $this->request->getPost('keterangan');
        
        $this->mutasiModel->recordPindahKeluar(
            $pendudukId, 
            $tanggal, 
            $keterangan, 
            $this->user['id'] ?? null
        );

        return redirect()->to('/demografi/mutasi')
            ->with('success', 'Data pindah keluar berhasil dicatat');
    }

    // ========================================
    // IMPORT / EXPORT
    // ========================================

    /**
     * Form import data
     */
    public function import()
    {
        $data = [
            'title' => 'Import Data Penduduk - Siskeudes Lite',
            'user'  => $this->user,
        ];

        return view('demografi/import', $data);
    }

    /**
     * Process import CSV/Excel
     */
    public function processImport()
    {
        $file = $this->request->getFile('file');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid');
        }

        $extension = $file->getExtension();
        if (!in_array($extension, ['csv', 'xlsx', 'xls'])) {
            return redirect()->back()->with('error', 'Format file harus CSV atau Excel');
        }

        try {
            $kodeDesa = $this->session->get('kode_desa');
            
            // Load spreadsheet based on type
            if ($extension === 'csv') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            } elseif ($extension === 'xlsx') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            }
            
            $spreadsheet = $reader->load($file->getTempName());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Skip header row
            $header = array_shift($rows);
            
            // Map header to column index
            $headerMap = [];
            foreach ($header as $idx => $col) {
                $headerMap[strtoupper(trim($col ?? ''))] = $idx;
            }
            
            // Required columns
            $requiredCols = ['NO_KK', 'NIK', 'NAMA_LENGKAP', 'JENIS_KELAMIN'];
            foreach ($requiredCols as $col) {
                if (!isset($headerMap[$col])) {
                    return redirect()->back()->with('error', "Kolom {$col} tidak ditemukan dalam file");
                }
            }
            
            $imported = 0;
            $skipped = 0;
            $errors = [];
            
            foreach ($rows as $rowNum => $row) {
                $actualRow = $rowNum + 2; // Account for 0-index and header
                
                // Get values with fallback
                $noKk = $row[$headerMap['NO_KK']] ?? '';
                $nik = $row[$headerMap['NIK']] ?? '';
                $nama = $row[$headerMap['NAMA_LENGKAP']] ?? '';
                $jk = strtoupper($row[$headerMap['JENIS_KELAMIN']] ?? 'L');
                
                // Skip empty rows
                if (empty($noKk) || empty($nik) || empty($nama)) {
                    continue;
                }
                
                // Validate NIK (16 digits)
                $nik = trim(strval($nik));
                if (strlen($nik) != 16) {
                    $errors[] = "Baris {$actualRow}: NIK harus 16 digit";
                    $skipped++;
                    continue;
                }
                
                // Validate No KK (16 digits)
                $noKk = trim(strval($noKk));
                if (strlen($noKk) != 16) {
                    $errors[] = "Baris {$actualRow}: No KK harus 16 digit";
                    $skipped++;
                    continue;
                }
                
                // Check if NIK already exists
                if ($this->pendudukModel->where('nik', $nik)->first()) {
                    $errors[] = "Baris {$actualRow}: NIK {$nik} sudah terdaftar";
                    $skipped++;
                    continue;
                }
                
                // Find or create keluarga
                $keluarga = $this->keluargaModel->where('no_kk', $noKk)->first();
                if (!$keluarga) {
                    // Create new keluarga
                    $keluargaData = [
                        'kode_desa' => $kodeDesa,
                        'no_kk' => $noKk,
                        'kepala_keluarga' => $nama,
                        'alamat' => $row[$headerMap['ALAMAT'] ?? 999] ?? null,
                        'rt' => $row[$headerMap['RT'] ?? 999] ?? null,
                        'rw' => $row[$headerMap['RW'] ?? 999] ?? null,
                        'dusun' => $row[$headerMap['DUSUN'] ?? 999] ?? null,
                    ];
                    $this->keluargaModel->insert($keluargaData);
                    $keluargaId = $this->keluargaModel->getInsertID();
                } else {
                    $keluargaId = $keluarga['id'];
                }
                
                // Prepare penduduk data
                $pendudukData = [
                    'keluarga_id' => $keluargaId,
                    'nik' => $nik,
                    'nama_lengkap' => $nama,
                    'tempat_lahir' => $row[$headerMap['TEMPAT_LAHIR'] ?? 999] ?? null,
                    'tanggal_lahir' => $this->parseDate($row[$headerMap['TANGGAL_LAHIR'] ?? 999] ?? null),
                    'jenis_kelamin' => in_array($jk, ['L', 'P']) ? $jk : 'L',
                    'agama' => $row[$headerMap['AGAMA'] ?? 999] ?? null,
                    'pendidikan_terakhir' => $row[$headerMap['PENDIDIKAN'] ?? 999] ?? null,
                    'pekerjaan' => $row[$headerMap['PEKERJAAN'] ?? 999] ?? null,
                    'status_perkawinan' => $row[$headerMap['STATUS_KAWIN'] ?? 999] ?? null,
                    'status_hubungan' => $row[$headerMap['STATUS_HUBUNGAN'] ?? 999] ?? null,
                    'golongan_darah' => $row[$headerMap['GOLONGAN_DARAH'] ?? 999] ?? null,
                    'nama_ayah' => $row[$headerMap['NAMA_AYAH'] ?? 999] ?? null,
                    'nama_ibu' => $row[$headerMap['NAMA_IBU'] ?? 999] ?? null,
                    'kewarganegaraan' => 'WNI',
                    'status_dasar' => 'HIDUP',
                    'is_miskin' => ($row[$headerMap['IS_MISKIN'] ?? 999] ?? 0) == 1 ? 1 : 0,
                ];
                
                $this->pendudukModel->insert($pendudukData);
                $imported++;
            }
            
            $message = "Import selesai: {$imported} data berhasil diimport";
            if ($skipped > 0) {
                $message .= ", {$skipped} data dilewati";
            }
            
            if (!empty($errors)) {
                $this->session->setFlashdata('import_errors', array_slice($errors, 0, 10));
            }
            
            return redirect()->to('/demografi/penduduk')->with('success', $message);
            
        } catch (\Exception $e) {
            log_message('error', 'Import error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($value): ?string
    {
        if (empty($value)) return null;
        
        $value = trim(strval($value));
        
        // If already in Y-m-d format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }
        
        // Try d/m/Y format
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $m)) {
            return "{$m[3]}-" . str_pad($m[2], 2, '0', STR_PAD_LEFT) . "-" . str_pad($m[1], 2, '0', STR_PAD_LEFT);
        }
        
        // Try d-m-Y format
        if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $value, $m)) {
            return "{$m[3]}-" . str_pad($m[2], 2, '0', STR_PAD_LEFT) . "-" . str_pad($m[1], 2, '0', STR_PAD_LEFT);
        }
        
        // Excel numeric date
        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        
        return null;
    }

    /**
     * Download template import CSV
     */
    public function downloadTemplate()
    {
        $headers = [
            'NO_KK', 'NIK', 'NAMA_LENGKAP', 'TEMPAT_LAHIR', 'TANGGAL_LAHIR',
            'JENIS_KELAMIN', 'AGAMA', 'PENDIDIKAN', 'PEKERJAAN', 'STATUS_KAWIN',
            'STATUS_HUBUNGAN', 'GOLONGAN_DARAH', 'NAMA_AYAH', 'NAMA_IBU',
            'ALAMAT', 'RT', 'RW', 'DUSUN', 'IS_MISKIN'
        ];

        $output = implode(',', $headers) . "\n";
        $output .= "3201234567890123,3201234567890124,John Doe,Jakarta,1990-01-15,L,Islam,SLTA/Sederajat,Wiraswasta,Kawin,Kepala Keluarga,O,Father Name,Mother Name,Jl. Contoh No. 1,001,002,Dusun 1,0\n";
        $output .= "3201234567890123,3201234567890125,Jane Doe,Bandung,1995-05-20,P,Islam,SLTA/Sederajat,Mengurus Rumah Tangga,Kawin,Istri,A,Father Name,Mother Name,Jl. Contoh No. 1,001,002,Dusun 1,0\n";

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="template_import_penduduk.csv"')
            ->setBody($output);
    }

    // ========================================
    // API ENDPOINTS
    // ========================================

    /**
     * Search penduduk for AJAX
     */
    public function searchPenduduk()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $keyword = $this->request->getGet('q');

        if (strlen($keyword) < 3) {
            return $this->response->setJSON([]);
        }

        $results = $this->pendudukModel->search($kodeDesa, $keyword);
        
        $formatted = array_map(function($p) {
            return [
                'id'    => $p['id'],
                'text'  => $p['nik'] . ' - ' . $p['nama_lengkap'],
                'nik'   => $p['nik'],
                'nama'  => $p['nama_lengkap'],
                'no_kk' => $p['no_kk'],
            ];
        }, $results);

        return $this->response->setJSON($formatted);
    }

    /**
     * Get BLT eligible list
     */
    public function bltEligible()
    {
        $kodeDesa = $this->user['kode_desa'] ?? null;
        $eligible = $this->pendudukModel->getBLTEligible($kodeDesa);

        $data = [
            'title' => 'Daftar Calon Penerima Bantuan - Siskeudes Lite',
            'user'  => $this->user,
            'pendudukList' => $eligible,
        ];

        return view('demografi/blt_eligible', $data);
    }
}

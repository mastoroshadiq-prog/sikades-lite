<?php

namespace App\Controllers;

use App\Models\AsetInventarisModel;
use App\Models\AsetKategoriModel;
use App\Models\BkuModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Aset extends BaseController
{
    protected $asetModel;
    protected $kategoriModel;
    protected $bkuModel;
    protected $user;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->asetModel = new AsetInventarisModel();
        $this->kategoriModel = new AsetKategoriModel();
        $this->bkuModel = new BkuModel();
        
        // Get user data from session
        $this->user = [
            'id' => $this->session->get('user_id'),
            'username' => $this->session->get('username'),
            'role' => $this->session->get('role'),
            'kode_desa' => $this->session->get('kode_desa'),
        ];
    }

    /**
     * Dashboard Aset - Overview
     */
    public function index()
    {
        $kodeDesa = $this->user['kode_desa'];
        
        $data = [
            'title'      => 'SIPADES - Inventaris Aset Desa',
            'user'       => $this->user,
            'summary'    => $this->asetModel->getSummary($kodeDesa),
            'categories' => $this->kategoriModel->getCategoriesWithCount($kodeDesa),
            'recentAset' => $this->asetModel->getAsetWithKategori($kodeDesa),
        ];

        return view('aset/index', $data);
    }

    /**
     * List all assets with filter
     */
    public function list()
    {
        $kodeDesa = $this->user['kode_desa'];
        $kategoriId = $this->request->getGet('kategori');
        $kondisi = $this->request->getGet('kondisi');
        $tahun = $this->request->getGet('tahun');

        // Build query with filters
        $builder = $this->asetModel
                        ->select('aset_inventaris.*, aset_kategori.kode_golongan, aset_kategori.nama_golongan')
                        ->join('aset_kategori', 'aset_kategori.id = aset_inventaris.kategori_id', 'left')
                        ->where('aset_inventaris.kode_desa', $kodeDesa);

        if ($kategoriId) {
            $builder->where('aset_inventaris.kategori_id', $kategoriId);
        }
        if ($kondisi) {
            $builder->where('aset_inventaris.kondisi', $kondisi);
        }
        if ($tahun) {
            $builder->where('aset_inventaris.tahun_perolehan', $tahun);
        }

        $asetList = $builder->orderBy('aset_inventaris.kode_register', 'ASC')->findAll();

        $data = [
            'title'      => 'Daftar Inventaris Aset',
            'user'       => $this->user,
            'asetList'   => $asetList,
            'categories' => $this->kategoriModel->getActiveCategories(),
            'filters'    => [
                'kategori' => $kategoriId,
                'kondisi'  => $kondisi,
                'tahun'    => $tahun,
            ],
        ];

        return view('aset/list', $data);
    }

    /**
     * Form create new asset
     */
    public function create()
    {
        // Check if coming from BKU (auto-fill)
        $bkuId = $this->request->getGet('bku_id');
        $prefill = [];

        if ($bkuId) {
            $bku = $this->bkuModel->find($bkuId);
            if ($bku) {
                $prefill = [
                    'nama_barang'      => $bku['uraian'],
                    'harga_perolehan'  => $bku['debet'] > 0 ? $bku['debet'] : $bku['kredit'],
                    'tahun_perolehan'  => date('Y', strtotime($bku['tanggal'])),
                    'bku_id'           => $bkuId,
                    'sumber_dana'      => 'APBDes',
                ];
            }
        }

        $data = [
            'title'      => 'Tambah Aset Baru',
            'user'       => $this->user,
            'categories' => $this->kategoriModel->getDropdownOptions(),
            'prefill'    => $prefill,
            'validation' => \Config\Services::validation(),
        ];

        return view('aset/create', $data);
    }

    /**
     * Store new asset
     */
    public function store()
    {
        // Sanitize currency input - remove thousand separators
        $hargaPerolehan = $this->request->getPost('harga_perolehan');
        $hargaPerolehan = str_replace(['.', ','], ['', '.'], $hargaPerolehan);
        
        // Merge sanitized value back for validation
        $_POST['harga_perolehan'] = $hargaPerolehan;
        
        // Validation
        $rules = [
            'nama_barang'      => 'required|max_length[255]',
            'kategori_id'      => 'required|integer',
            'tahun_perolehan'  => 'required|integer',
            'harga_perolehan'  => 'required|numeric',
            'kondisi'          => 'required',
            'sumber_dana'      => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kodeDesa = $this->user['kode_desa'];
        $kategoriId = $this->request->getPost('kategori_id');
        $tahun = $this->request->getPost('tahun_perolehan');

        // Generate kode_register
        $kodeRegister = $this->asetModel->generateKodeRegister($kodeDesa, $kategoriId, $tahun);

        // Handle foto upload
        $fotoPath = null;
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $fotoName = $foto->getRandomName();
            $foto->move(WRITEPATH . 'uploads/aset', $fotoName);
            $fotoPath = 'uploads/aset/' . $fotoName;
        }

        // Prepare data
        $data = [
            'kode_desa'        => $kodeDesa,
            'kode_register'    => $kodeRegister,
            'nama_barang'      => $this->request->getPost('nama_barang'),
            'kategori_id'      => $kategoriId,
            'merk_type'        => $this->request->getPost('merk_type'),
            'ukuran'           => $this->request->getPost('ukuran'),
            'bahan'            => $this->request->getPost('bahan'),
            'tahun_perolehan'  => $tahun,
            'harga_perolehan'  => $hargaPerolehan,
            'nilai_sisa'       => $hargaPerolehan, // Initially same as acquisition
            'kondisi'          => $this->request->getPost('kondisi'),
            'status_penggunaan' => $this->request->getPost('status_penggunaan') ?? 'Digunakan',
            'lokasi'           => $this->request->getPost('lokasi'),
            'pengguna'         => $this->request->getPost('pengguna'),
            'sumber_dana'      => $this->request->getPost('sumber_dana'),
            'bku_id'           => $this->request->getPost('bku_id') ?: null,
            'lat'              => $this->request->getPost('lat') ?: null,
            'lng'              => $this->request->getPost('lng') ?: null,
            'foto'             => $fotoPath,
            'keterangan'       => $this->request->getPost('keterangan'),
            'created_by'       => $this->user['id'],
        ];

        if ($this->asetModel->insert($data)) {
            return redirect()->to('/aset')->with('success', 'Aset berhasil ditambahkan dengan kode register: ' . $kodeRegister);
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data aset');
    }

    /**
     * View asset detail
     */
    public function detail($id)
    {
        $aset = $this->asetModel
                     ->select('aset_inventaris.*, aset_kategori.kode_golongan, aset_kategori.nama_golongan, aset_kategori.masa_manfaat')
                     ->join('aset_kategori', 'aset_kategori.id = aset_inventaris.kategori_id', 'left')
                     ->where('aset_inventaris.id', $id)
                     ->first();

        if (!$aset || $aset['kode_desa'] !== $this->user['kode_desa']) {
            return redirect()->to('/aset')->with('error', 'Aset tidak ditemukan');
        }

        // Get linked BKU if exists
        $linkedBku = null;
        if ($aset['bku_id']) {
            $linkedBku = $this->bkuModel->find($aset['bku_id']);
        }

        $data = [
            'title'     => 'Detail Aset',
            'user'      => $this->user,
            'aset'      => $aset,
            'linkedBku' => $linkedBku,
        ];

        return view('aset/detail', $data);
    }

    /**
     * Edit asset form
     */
    public function edit($id)
    {
        $aset = $this->asetModel->find($id);

        if (!$aset || $aset['kode_desa'] !== $this->user['kode_desa']) {
            return redirect()->to('/aset')->with('error', 'Aset tidak ditemukan');
        }

        $data = [
            'title'      => 'Edit Aset',
            'user'       => $this->user,
            'aset'       => $aset,
            'categories' => $this->kategoriModel->getDropdownOptions(),
            'validation' => \Config\Services::validation(),
        ];

        return view('aset/edit', $data);
    }

    /**
     * Update asset
     */
    public function update($id)
    {
        $aset = $this->asetModel->find($id);

        if (!$aset || $aset['kode_desa'] !== $this->user['kode_desa']) {
            return redirect()->to('/aset')->with('error', 'Aset tidak ditemukan');
        }

        // Validation
        $rules = [
            'nama_barang' => 'required|max_length[255]',
            'kondisi'     => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle new foto upload
        $fotoPath = $aset['foto'];
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            // Delete old photo if exists
            if ($aset['foto'] && file_exists(WRITEPATH . $aset['foto'])) {
                unlink(WRITEPATH . $aset['foto']);
            }
            $fotoName = $foto->getRandomName();
            $foto->move(WRITEPATH . 'uploads/aset', $fotoName);
            $fotoPath = 'uploads/aset/' . $fotoName;
        }

        $data = [
            'nama_barang'       => $this->request->getPost('nama_barang'),
            'merk_type'         => $this->request->getPost('merk_type'),
            'ukuran'            => $this->request->getPost('ukuran'),
            'bahan'             => $this->request->getPost('bahan'),
            'kondisi'           => $this->request->getPost('kondisi'),
            'status_penggunaan' => $this->request->getPost('status_penggunaan'),
            'lokasi'            => $this->request->getPost('lokasi'),
            'pengguna'          => $this->request->getPost('pengguna'),
            'lat'               => $this->request->getPost('lat') ?: null,
            'lng'               => $this->request->getPost('lng') ?: null,
            'foto'              => $fotoPath,
            'keterangan'        => $this->request->getPost('keterangan'),
        ];

        if ($this->asetModel->update($id, $data)) {
            return redirect()->to('/aset/detail/' . $id)->with('success', 'Aset berhasil diperbarui');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data aset');
    }

    /**
     * Delete asset
     */
    public function delete($id)
    {
        $aset = $this->asetModel->find($id);

        if (!$aset || $aset['kode_desa'] !== $this->user['kode_desa']) {
            return redirect()->to('/aset')->with('error', 'Aset tidak ditemukan');
        }

        // Delete foto if exists
        if ($aset['foto'] && file_exists(WRITEPATH . $aset['foto'])) {
            unlink(WRITEPATH . $aset['foto']);
        }

        if ($this->asetModel->delete($id)) {
            return redirect()->to('/aset')->with('success', 'Aset berhasil dihapus');
        }

        return redirect()->back()->with('error', 'Gagal menghapus aset');
    }

    /**
     * Get JSON data for WebGIS
     */
    public function getJsonData()
    {
        $kodeDesa = $this->user['kode_desa'];
        $asetList = $this->asetModel->getAsetWithCoordinates($kodeDesa);

        $geoJson = [
            'type'     => 'FeatureCollection',
            'features' => [],
        ];

        foreach ($asetList as $aset) {
            $geoJson['features'][] = [
                'type'     => 'Feature',
                'geometry' => [
                    'type'        => 'Point',
                    'coordinates' => [(float) $aset['lng'], (float) $aset['lat']],
                ],
                'properties' => [
                    'id'           => $aset['id'],
                    'kode_register' => $aset['kode_register'],
                    'nama_barang'  => $aset['nama_barang'],
                    'kategori'     => $aset['nama_golongan'],
                    'kondisi'      => $aset['kondisi'],
                    'foto'         => $aset['foto'] ? base_url('writable/' . $aset['foto']) : null,
                ],
            ];
        }

        return $this->response->setJSON($geoJson);
    }

    /**
     * Print KIR (Kartu Inventaris Ruangan)
     */
    public function printKir()
    {
        $kodeDesa = $this->user['kode_desa'];
        $lokasi = $this->request->getGet('lokasi');

        $builder = $this->asetModel
                        ->select('aset_inventaris.*, aset_kategori.kode_golongan, aset_kategori.nama_golongan')
                        ->join('aset_kategori', 'aset_kategori.id = aset_inventaris.kategori_id', 'left')
                        ->where('aset_inventaris.kode_desa', $kodeDesa);

        if ($lokasi) {
            $builder->like('aset_inventaris.lokasi', $lokasi);
        }

        $asetList = $builder->orderBy('aset_inventaris.kode_register', 'ASC')->findAll();

        $data = [
            'title'    => 'Kartu Inventaris Ruangan',
            'user'     => $this->user,
            'asetList' => $asetList,
            'lokasi'   => $lokasi,
        ];

        return view('aset/print_kir', $data);
    }
}

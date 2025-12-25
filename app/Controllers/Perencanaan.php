<?php

namespace App\Controllers;

use App\Models\RpjmdesaModel;
use App\Models\RkpdesaModel;
use App\Models\KegiatanModel;
use App\Models\RefBidangModel;
use App\Models\DataUmumDesaModel;
use App\Models\ActivityLogModel;

class Perencanaan extends BaseController
{
    protected $rpjmModel;
    protected $rkpModel;
    protected $kegiatanModel;
    protected $bidangModel;
    protected $desaModel;

    public function __construct()
    {
        $this->rpjmModel = new RpjmdesaModel();
        $this->rkpModel = new RkpdesaModel();
        $this->kegiatanModel = new KegiatanModel();
        $this->bidangModel = new RefBidangModel();
        $this->desaModel = new DataUmumDesaModel();
    }

    /**
     * Dashboard Perencanaan
     */
    public function index()
    {
        $kodeDesa = session()->get('kode_desa');
        
        // Get active RPJM
        $rpjmAktif = $this->rpjmModel->getAktif($kodeDesa);
        
        // Get RKP list
        $rkpList = $this->rkpModel->getWithKegiatanCount($kodeDesa);
        
        // Get summary
        $totalRpjm = $this->rpjmModel->where('kode_desa', $kodeDesa)->countAllResults();
        $totalRkp = $this->rkpModel->where('kode_desa', $kodeDesa)->countAllResults();
        $totalKegiatan = $this->kegiatanModel->where('kode_desa', $kodeDesa)->countAllResults();
        $totalPagu = $this->kegiatanModel
            ->selectSum('pagu_anggaran')
            ->where('kode_desa', $kodeDesa)
            ->first()['pagu_anggaran'] ?? 0;

        $data = array_merge($this->data, [
            'title' => 'Modul Perencanaan',
            'rpjmAktif' => $rpjmAktif,
            'rkpList' => $rkpList,
            'totalRpjm' => $totalRpjm,
            'totalRkp' => $totalRkp,
            'totalKegiatan' => $totalKegiatan,
            'totalPagu' => $totalPagu,
        ]);

        return view('perencanaan/index', $data);
    }

    // ==========================================
    // RPJM DESA
    // ==========================================

    /**
     * List RPJM Desa
     */
    public function rpjm()
    {
        $kodeDesa = session()->get('kode_desa');
        $rpjmList = $this->rpjmModel->getWithRkpCount($kodeDesa);
        
        $data = array_merge($this->data, [
            'title' => 'RPJM Desa',
            'rpjmList' => $rpjmList,
        ]);

        return view('perencanaan/rpjm/index', $data);
    }

    /**
     * Form tambah RPJM
     */
    public function rpjmCreate()
    {
        $data = array_merge($this->data, [
            'title' => 'Tambah RPJM Desa',
            'tahunSekarang' => date('Y'),
        ]);

        return view('perencanaan/rpjm/form', $data);
    }

    /**
     * Simpan RPJM
     */
    public function rpjmSave()
    {
        $rules = [
            'tahun_awal' => 'required|numeric',
            'tahun_akhir' => 'required|numeric',
            'visi' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kode_desa' => session()->get('kode_desa'),
            'tahun_awal' => $this->request->getPost('tahun_awal'),
            'tahun_akhir' => $this->request->getPost('tahun_akhir'),
            'visi' => $this->request->getPost('visi'),
            'misi' => $this->request->getPost('misi'),
            'tujuan' => $this->request->getPost('tujuan'),
            'sasaran' => $this->request->getPost('sasaran'),
            'status' => $this->request->getPost('status') ?? 'Draft',
            'nomor_perdes' => $this->request->getPost('nomor_perdes'),
            'tanggal_perdes' => $this->request->getPost('tanggal_perdes') ?: null,
            'created_by' => session()->get('user_id'),
        ];

        $this->rpjmModel->insert($data);
        
        ActivityLogModel::log('create', 'perencanaan', 'Membuat RPJM Desa ' . $data['tahun_awal'] . '-' . $data['tahun_akhir']);

        return redirect()->to('/perencanaan/rpjm')->with('success', 'RPJM Desa berhasil ditambahkan');
    }

    /**
     * Edit RPJM
     */
    public function rpjmEdit($id)
    {
        $rpjm = $this->rpjmModel->find($id);
        
        if (!$rpjm) {
            return redirect()->to('/perencanaan/rpjm')->with('error', 'Data tidak ditemukan');
        }

        $data = array_merge($this->data, [
            'title' => 'Edit RPJM Desa',
            'rpjm' => $rpjm,
        ]);

        return view('perencanaan/rpjm/form', $data);
    }

    /**
     * Update RPJM
     */
    public function rpjmUpdate($id)
    {
        $rules = [
            'tahun_awal' => 'required|numeric',
            'tahun_akhir' => 'required|numeric',
            'visi' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $oldData = $this->rpjmModel->find($id);

        $data = [
            'tahun_awal' => $this->request->getPost('tahun_awal'),
            'tahun_akhir' => $this->request->getPost('tahun_akhir'),
            'visi' => $this->request->getPost('visi'),
            'misi' => $this->request->getPost('misi'),
            'tujuan' => $this->request->getPost('tujuan'),
            'sasaran' => $this->request->getPost('sasaran'),
            'status' => $this->request->getPost('status'),
            'nomor_perdes' => $this->request->getPost('nomor_perdes'),
            'tanggal_perdes' => $this->request->getPost('tanggal_perdes') ?: null,
        ];

        // If setting to Aktif, deactivate others
        if ($data['status'] === 'Aktif') {
            $this->rpjmModel->where('kode_desa', session()->get('kode_desa'))
                           ->where('id !=', $id)
                           ->set(['status' => 'Selesai'])
                           ->update();
        }

        $this->rpjmModel->update($id, $data);
        
        ActivityLogModel::log('update', 'perencanaan', 'Mengupdate RPJM Desa', $oldData, $data);

        return redirect()->to('/perencanaan/rpjm')->with('success', 'RPJM Desa berhasil diupdate');
    }

    /**
     * Detail RPJM
     */
    public function rpjmDetail($id)
    {
        $rpjm = $this->rpjmModel->find($id);
        
        if (!$rpjm) {
            return redirect()->to('/perencanaan/rpjm')->with('error', 'Data tidak ditemukan');
        }

        $rkpList = $this->rkpModel->where('rpjmdesa_id', $id)->orderBy('tahun', 'ASC')->findAll();

        $data = array_merge($this->data, [
            'title' => 'Detail RPJM Desa',
            'rpjm' => $rpjm,
            'rkpList' => $rkpList,
        ]);

        return view('perencanaan/rpjm/detail', $data);
    }

    /**
     * Delete RPJM
     */
    public function rpjmDelete($id)
    {
        $rpjm = $this->rpjmModel->find($id);
        
        // Check if has RKP
        $rkpCount = $this->rkpModel->where('rpjmdesa_id', $id)->countAllResults();
        if ($rkpCount > 0) {
            return redirect()->to('/perencanaan/rpjm')->with('error', 'Tidak dapat menghapus RPJM yang memiliki RKP');
        }

        $this->rpjmModel->delete($id);
        
        ActivityLogModel::log('delete', 'perencanaan', 'Menghapus RPJM Desa ' . $rpjm['tahun_awal'] . '-' . $rpjm['tahun_akhir']);

        return redirect()->to('/perencanaan/rpjm')->with('success', 'RPJM Desa berhasil dihapus');
    }

    // ==========================================
    // RKP DESA
    // ==========================================

    /**
     * List RKP Desa
     */
    public function rkp()
    {
        $kodeDesa = session()->get('kode_desa');
        $rkpList = $this->rkpModel->getWithKegiatanCount($kodeDesa);
        
        $data = array_merge($this->data, [
            'title' => 'RKP Desa',
            'rkpList' => $rkpList,
        ]);

        return view('perencanaan/rkp/index', $data);
    }

    /**
     * Form tambah RKP
     */
    public function rkpCreate()
    {
        $kodeDesa = session()->get('kode_desa');
        $rpjmList = $this->rpjmModel->where('kode_desa', $kodeDesa)
                                    ->whereIn('status', ['Draft', 'Aktif'])
                                    ->findAll();
        
        $data = array_merge($this->data, [
            'title' => 'Tambah RKP Desa',
            'rpjmList' => $rpjmList,
            'tahunSekarang' => date('Y'),
        ]);

        return view('perencanaan/rkp/form', $data);
    }

    /**
     * Simpan RKP
     */
    public function rkpSave()
    {
        $rules = [
            'rpjmdesa_id' => 'required|numeric',
            'tahun' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $rpjmId = $this->request->getPost('rpjmdesa_id');
        $tahunRkp = (int)$this->request->getPost('tahun');
        
        // Validate RKP year is within RPJM range
        $rpjm = $this->rpjmModel->find($rpjmId);
        if (!$rpjm) {
            return redirect()->back()->withInput()->with('error', 'RPJM Desa tidak ditemukan');
        }
        
        if ($tahunRkp < (int)$rpjm['tahun_awal'] || $tahunRkp > (int)$rpjm['tahun_akhir']) {
            return redirect()->back()->withInput()->with('error', 
                'Tahun RKP harus dalam rentang RPJM Desa (' . $rpjm['tahun_awal'] . ' - ' . $rpjm['tahun_akhir'] . ')');
        }

        // Check if RKP for this year already exists
        $existing = $this->rkpModel->where('kode_desa', session()->get('kode_desa'))
                                   ->where('tahun', $tahunRkp)
                                   ->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'RKP untuk tahun tersebut sudah ada');
        }

        $data = [
            'rpjmdesa_id' => $this->request->getPost('rpjmdesa_id'),
            'kode_desa' => session()->get('kode_desa'),
            'tahun' => $this->request->getPost('tahun'),
            'tema' => $this->request->getPost('tema'),
            'prioritas' => $this->request->getPost('prioritas'),
            'status' => $this->request->getPost('status') ?? 'Draft',
            'nomor_perdes' => $this->request->getPost('nomor_perdes'),
            'tanggal_perdes' => $this->request->getPost('tanggal_perdes') ?: null,
            'created_by' => session()->get('user_id'),
        ];

        $this->rkpModel->insert($data);
        
        ActivityLogModel::log('create', 'perencanaan', 'Membuat RKP Desa Tahun ' . $data['tahun']);

        return redirect()->to('/perencanaan/rkp')->with('success', 'RKP Desa berhasil ditambahkan');
    }

    /**
     * Detail RKP (List Kegiatan)
     */
    public function rkpDetail($id)
    {
        $rkp = $this->rkpModel->find($id);
        
        if (!$rkp) {
            return redirect()->to('/perencanaan/rkp')->with('error', 'Data tidak ditemukan');
        }

        $rpjm = $this->rpjmModel->find($rkp['rpjmdesa_id']);
        $kegiatanGrouped = $this->kegiatanModel->getGroupedByBidang($id);
        $summaryDana = $this->kegiatanModel->getSummaryBySumberDana($id);
        $summaryStatus = $this->kegiatanModel->getSummaryByStatus($id);
        $bidangList = $this->bidangModel->getAllOrdered();

        $data = array_merge($this->data, [
            'title' => 'RKP Desa Tahun ' . $rkp['tahun'],
            'rkp' => $rkp,
            'rpjm' => $rpjm,
            'kegiatanGrouped' => $kegiatanGrouped,
            'summaryDana' => $summaryDana,
            'summaryStatus' => $summaryStatus,
            'bidangList' => $bidangList,
        ]);

        return view('perencanaan/rkp/detail', $data);
    }

    /**
     * Edit RKP
     */
    public function rkpEdit($id)
    {
        $kodeDesa = session()->get('kode_desa');
        $rkp = $this->rkpModel->find($id);
        
        if (!$rkp) {
            return redirect()->to('/perencanaan/rkp')->with('error', 'Data tidak ditemukan');
        }

        $rpjmList = $this->rpjmModel->where('kode_desa', $kodeDesa)->findAll();

        $data = array_merge($this->data, [
            'title' => 'Edit RKP Desa',
            'rkp' => $rkp,
            'rpjmList' => $rpjmList,
        ]);

        return view('perencanaan/rkp/form', $data);
    }

    /**
     * Update RKP
     */
    public function rkpUpdate($id)
    {
        $oldData = $this->rkpModel->find($id);

        $data = [
            'rpjmdesa_id' => $this->request->getPost('rpjmdesa_id'),
            'tahun' => $this->request->getPost('tahun'),
            'tema' => $this->request->getPost('tema'),
            'prioritas' => $this->request->getPost('prioritas'),
            'status' => $this->request->getPost('status'),
            'nomor_perdes' => $this->request->getPost('nomor_perdes'),
            'tanggal_perdes' => $this->request->getPost('tanggal_perdes') ?: null,
        ];

        $this->rkpModel->update($id, $data);
        
        ActivityLogModel::log('update', 'perencanaan', 'Mengupdate RKP Desa Tahun ' . $data['tahun'], $oldData, $data);

        return redirect()->to('/perencanaan/rkp')->with('success', 'RKP Desa berhasil diupdate');
    }

    /**
     * Delete RKP
     */
    public function rkpDelete($id)
    {
        $rkp = $this->rkpModel->find($id);
        
        // Check if has kegiatan
        $kegiatanCount = $this->kegiatanModel->where('rkpdesa_id', $id)->countAllResults();
        if ($kegiatanCount > 0) {
            return redirect()->to('/perencanaan/rkp')->with('error', 'Tidak dapat menghapus RKP yang memiliki kegiatan');
        }

        $this->rkpModel->delete($id);
        
        ActivityLogModel::log('delete', 'perencanaan', 'Menghapus RKP Desa Tahun ' . $rkp['tahun']);

        return redirect()->to('/perencanaan/rkp')->with('success', 'RKP Desa berhasil dihapus');
    }

    // ==========================================
    // KEGIATAN
    // ==========================================

    /**
     * Form tambah kegiatan
     */
    public function kegiatanCreate($rkpId)
    {
        $rkp = $this->rkpModel->find($rkpId);
        if (!$rkp) {
            return redirect()->to('/perencanaan/rkp')->with('error', 'RKP tidak ditemukan');
        }

        $bidangList = $this->bidangModel->getAllOrdered();

        $data = array_merge($this->data, [
            'title' => 'Tambah Kegiatan',
            'rkp' => $rkp,
            'bidangList' => $bidangList,
        ]);

        return view('perencanaan/kegiatan/form', $data);
    }

    /**
     * Simpan kegiatan
     */
    public function kegiatanSave()
    {
        $rules = [
            'rkpdesa_id' => 'required|numeric',
            'nama_kegiatan' => 'required',
            'bidang_id' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $rkpId = $this->request->getPost('rkpdesa_id');

        $data = [
            'rkpdesa_id' => $rkpId,
            'kode_desa' => session()->get('kode_desa'),
            'bidang_id' => $this->request->getPost('bidang_id'),
            'kode_kegiatan' => $this->request->getPost('kode_kegiatan'),
            'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
            'lokasi' => $this->request->getPost('lokasi'),
            'volume' => $this->request->getPost('volume'),
            'satuan' => $this->request->getPost('satuan'),
            'sasaran_manfaat' => $this->request->getPost('sasaran_manfaat'),
            'waktu_pelaksanaan' => $this->request->getPost('waktu_pelaksanaan'),
            'pagu_anggaran' => floatval(str_replace(['.', ','], ['', '.'], $this->request->getPost('pagu_anggaran') ?? 0)),
            'sumber_dana' => $this->request->getPost('sumber_dana'),
            'status' => $this->request->getPost('status') ?? 'Usulan',
            'prioritas' => $this->request->getPost('prioritas') ?? 0,
            'keterangan' => $this->request->getPost('keterangan'),
            'created_by' => session()->get('user_id'),
        ];

        $this->kegiatanModel->insert($data);
        
        // Update RKP total pagu
        $this->rkpModel->updateTotalPagu($rkpId);
        
        ActivityLogModel::log('create', 'perencanaan', 'Menambah kegiatan: ' . $data['nama_kegiatan']);

        return redirect()->to('/perencanaan/rkp/detail/' . $rkpId)->with('success', 'Kegiatan berhasil ditambahkan');
    }

    /**
     * Edit kegiatan
     */
    public function kegiatanEdit($id)
    {
        $kegiatan = $this->kegiatanModel->find($id);
        if (!$kegiatan) {
            return redirect()->to('/perencanaan/rkp')->with('error', 'Kegiatan tidak ditemukan');
        }

        $rkp = $this->rkpModel->find($kegiatan['rkpdesa_id']);
        $bidangList = $this->bidangModel->getAllOrdered();

        $data = array_merge($this->data, [
            'title' => 'Edit Kegiatan',
            'kegiatan' => $kegiatan,
            'rkp' => $rkp,
            'bidangList' => $bidangList,
        ]);

        return view('perencanaan/kegiatan/form', $data);
    }

    /**
     * Update kegiatan
     */
    public function kegiatanUpdate($id)
    {
        $oldData = $this->kegiatanModel->find($id);
        $rkpId = $oldData['rkpdesa_id'];

        $data = [
            'bidang_id' => $this->request->getPost('bidang_id'),
            'kode_kegiatan' => $this->request->getPost('kode_kegiatan'),
            'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
            'lokasi' => $this->request->getPost('lokasi'),
            'volume' => $this->request->getPost('volume'),
            'satuan' => $this->request->getPost('satuan'),
            'sasaran_manfaat' => $this->request->getPost('sasaran_manfaat'),
            'waktu_pelaksanaan' => $this->request->getPost('waktu_pelaksanaan'),
            'pagu_anggaran' => floatval(str_replace(['.', ','], ['', '.'], $this->request->getPost('pagu_anggaran') ?? 0)),
            'sumber_dana' => $this->request->getPost('sumber_dana'),
            'status' => $this->request->getPost('status'),
            'prioritas' => $this->request->getPost('prioritas') ?? 0,
            'keterangan' => $this->request->getPost('keterangan'),
        ];

        $this->kegiatanModel->update($id, $data);
        
        // Update RKP total pagu
        $this->rkpModel->updateTotalPagu($rkpId);
        
        ActivityLogModel::log('update', 'perencanaan', 'Mengupdate kegiatan: ' . $data['nama_kegiatan'], $oldData, $data);

        return redirect()->to('/perencanaan/rkp/detail/' . $rkpId)->with('success', 'Kegiatan berhasil diupdate');
    }

    /**
     * Delete kegiatan
     */
    public function kegiatanDelete($id)
    {
        $kegiatan = $this->kegiatanModel->find($id);
        $rkpId = $kegiatan['rkpdesa_id'];

        $this->kegiatanModel->delete($id);
        
        // Update RKP total pagu
        $this->rkpModel->updateTotalPagu($rkpId);
        
        ActivityLogModel::log('delete', 'perencanaan', 'Menghapus kegiatan: ' . $kegiatan['nama_kegiatan']);

        return redirect()->to('/perencanaan/rkp/detail/' . $rkpId)->with('success', 'Kegiatan berhasil dihapus');
    }
}

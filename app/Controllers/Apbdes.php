<?php

namespace App\Controllers;

use App\Models\ApbdesModel;
use App\Models\RefRekeningModel;
use App\Models\KegiatanModel;
use App\Models\RkpdesaModel;
use App\Models\ActivityLogModel;

class Apbdes extends BaseController
{
    protected $apbdesModel;
    protected $rekeningModel;
    protected $kegiatanModel;
    protected $rkpModel;

    public function __construct()
    {
        $this->apbdesModel = new ApbdesModel();
        $this->rekeningModel = new RefRekeningModel();
        $this->kegiatanModel = new KegiatanModel();
        $this->rkpModel = new RkpdesaModel();
    }

    /**
     * List APBDes
     */
    public function index()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa', 'Kepala Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $anggaran = $this->apbdesModel->getAnggaranWithRekening($kodeDesa, $tahun);

        $data = array_merge($this->data, [
            'title' => 'APBDes - Anggaran Pendapatan dan Belanja Desa',
            'anggaran' => $anggaran,
            'tahun' => $tahun,
        ]);

        return view('apbdes/index', $data);
    }

    /**
     * Create APBDes Form
     */
    public function create()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        // Get all rekening for dropdown
        $rekening = $this->rekeningModel->orderBy('kode_akun', 'ASC')->findAll();

        $data = array_merge($this->data, [
            'title' => 'Tambah Anggaran',
            'rekening' => $rekening,
        ]);

        return view('apbdes/form', $data);
    }

    /**
     * Save APBDes
     */
    public function save()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $rules = [
            'ref_rekening_id' => 'required|integer',
            'uraian' => 'required',
            'anggaran' => 'required|decimal',
            'sumber_dana' => 'required|in_list[DDS,ADD,PAD,Bankeu]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kode_desa' => $this->session->get('kode_desa'),
            'tahun' => $this->request->getPost('tahun') ?? date('Y'),
            'ref_rekening_id' => $this->request->getPost('ref_rekening_id'),
            'uraian' => $this->request->getPost('uraian'),
            'anggaran' => $this->request->getPost('anggaran'),
            'sumber_dana' => $this->request->getPost('sumber_dana'),
        ];

        // Validasi: Anggaran tidak boleh minus
        if ($data['anggaran'] < 0) {
            return redirect()->back()->withInput()->with('error', 'Anggaran tidak boleh bernilai minus');
        }

        $this->apbdesModel->insert($data);

        return redirect()->to('/apbdes')->with('success', 'Anggaran berhasil ditambahkan');
    }

    /**
     * Edit APBDes Form
     */
    public function edit($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $anggaran = $this->apbdesModel->find($id);

        if (!$anggaran) {
            return redirect()->to('/apbdes')->with('error', 'Data tidak ditemukan');
        }

        // Check if belongs to user's desa
        if ($anggaran['kode_desa'] != $this->session->get('kode_desa')) {
            return redirect()->to('/apbdes')->with('error', 'Akses ditolak');
        }

        $rekening = $this->rekeningModel->orderBy('kode_akun', 'ASC')->findAll();

        $data = array_merge($this->data, [
            'title' => 'Edit Anggaran',
            'anggaran' => $anggaran,
            'rekening' => $rekening,
        ]);

        return view('apbdes/form', $data);
    }

    /**
     * Update APBDes
     */
    public function update($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $anggaran = $this->apbdesModel->find($id);

        if (!$anggaran || $anggaran['kode_desa'] != $this->session->get('kode_desa')) {
            return redirect()->to('/apbdes')->with('error', 'Data tidak ditemukan');
        }

        $rules = [
            'ref_rekening_id' => 'required|integer',
            'uraian' => 'required',
            'anggaran' => 'required|decimal',
            'sumber_dana' => 'required|in_list[DDS,ADD,PAD,Bankeu]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'tahun' => $this->request->getPost('tahun') ?? $anggaran['tahun'],
            'ref_rekening_id' => $this->request->getPost('ref_rekening_id'),
            'uraian' => $this->request->getPost('uraian'),
            'anggaran' => $this->request->getPost('anggaran'),
            'sumber_dana' => $this->request->getPost('sumber_dana'),
        ];

        if ($data['anggaran'] < 0) {
            return redirect()->back()->withInput()->with('error', 'Anggaran tidak boleh bernilai minus');
        }

        $this->apbdesModel->update($id, $data);

        return redirect()->to('/apbdes')->with('success', 'Anggaran berhasil diperbarui');
    }

    /**
     * Delete APBDes
     */
    public function delete($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $anggaran = $this->apbdesModel->find($id);

        if (!$anggaran || $anggaran['kode_desa'] != $this->session->get('kode_desa')) {
            return $this->respondError('Data tidak ditemukan', 404);
        }

        $this->apbdesModel->delete($id);

        return $this->respondSuccess(null, 'Anggaran berhasil dihapus');
    }

    /**
     * APBDes Report
     */
    public function report()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa', 'Kepala Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $anggaran = $this->apbdesModel->getAnggaranWithRekening($kodeDesa, $tahun);

        // Group by level for tree view
        $grouped = [
            'pendapatan' => [],
            'belanja' => [],
            'pembiayaan' => [],
        ];

        foreach ($anggaran as $item) {
            $kodeAkun = $item['kode_akun'];
            
            if (strpos($kodeAkun, '4.') === 0) {
                $grouped['pendapatan'][] = $item;
            } elseif (strpos($kodeAkun, '5.') === 0) {
                $grouped['belanja'][] = $item;
            } elseif (strpos($kodeAkun, '6.') === 0) {
                $grouped['pembiayaan'][] = $item;
            }
        }

        $data = array_merge($this->data, [
            'title' => 'Laporan APBDes',
            'grouped' => $grouped,
            'tahun' => $tahun,
        ]);

        return view('apbdes/report', $data);
    }

    /**
     * Show kegiatan from RKP to import
     */
    public function importFromKegiatan()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        // Get RKP for the year
        $rkp = $this->rkpModel->getByTahun($kodeDesa, $tahun);
        
        // Get kegiatan that haven't been linked to APBDes
        $kegiatanBelumLink = [];
        if ($rkp) {
            $kegiatanBelumLink = $this->kegiatanModel
                ->select('kegiatan.*, ref_bidang.kode_bidang, ref_bidang.nama_bidang')
                ->join('ref_bidang', 'ref_bidang.id = kegiatan.bidang_id', 'left')
                ->where('kegiatan.rkpdesa_id', $rkp['id'])
                ->where('kegiatan.apbdes_id IS NULL')
                ->whereIn('kegiatan.status', ['Prioritas', 'Disetujui'])
                ->orderBy('kegiatan.prioritas', 'ASC')
                ->findAll();
        }
        
        // Get available rekening for belanja
        $rekening = $this->rekeningModel
            ->where("kode_akun LIKE '5.%'")
            ->orderBy('kode_akun', 'ASC')
            ->findAll();
        
        $data = array_merge($this->data, [
            'title' => 'Import Kegiatan dari RKP',
            'tahun' => $tahun,
            'rkp' => $rkp,
            'kegiatan' => $kegiatanBelumLink,
            'rekening' => $rekening,
        ]);

        return view('apbdes/import_kegiatan', $data);
    }

    /**
     * Process import kegiatan to APBDes
     */
    public function processImport()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $kegiatanIds = $this->request->getPost('kegiatan_ids') ?? [];
        $rekeningIds = $this->request->getPost('rekening_ids') ?? [];
        $tahun = $this->request->getPost('tahun');
        
        if (empty($kegiatanIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu kegiatan');
        }
        
        $imported = 0;
        
        foreach ($kegiatanIds as $idx => $kegiatanId) {
            $kegiatan = $this->kegiatanModel->find($kegiatanId);
            
            if (!$kegiatan || $kegiatan['apbdes_id'] !== null) {
                continue;
            }
            
            $rekeningId = $rekeningIds[$idx] ?? null;
            
            if (!$rekeningId) {
                continue;
            }
            
            // Get rekening info
            $rekening = $this->rekeningModel->find($rekeningId);
            
            // Determine sumber dana mapping
            $sumberDanaMap = [
                'DDS' => 'DDS',
                'ADD' => 'ADD',
                'PAD' => 'PAD',
                'Bantuan Keuangan' => 'Bankeu',
                'Swadaya' => 'PAD',
                'Lainnya' => 'PAD'
            ];
            $sumberDana = $sumberDanaMap[$kegiatan['sumber_dana']] ?? 'DDS';
            
            // Create APBDes entry
            $apbdesData = [
                'kode_desa' => $kodeDesa,
                'tahun' => $tahun,
                'ref_rekening_id' => $rekeningId,
                'uraian' => $kegiatan['nama_kegiatan'] . 
                           ($kegiatan['lokasi'] ? ' - ' . $kegiatan['lokasi'] : ''),
                'anggaran' => $kegiatan['pagu_anggaran'],
                'sumber_dana' => $sumberDana,
                'kegiatan_id' => $kegiatanId,
            ];
            
            $apbdesId = $this->apbdesModel->insert($apbdesData);
            
            if ($apbdesId) {
                // Update kegiatan with apbdes_id
                $this->kegiatanModel->update($kegiatanId, [
                    'apbdes_id' => $apbdesId,
                    'ref_rekening_id' => $rekeningId,
                    'status' => 'Disetujui'
                ]);
                
                $imported++;
            }
        }
        
        if ($imported > 0) {
            ActivityLogModel::log('import', 'apbdes', "Import {$imported} kegiatan dari RKP ke APBDes");
            return redirect()->to('/apbdes')->with('success', "{$imported} kegiatan berhasil di-import ke APBDes");
        } else {
            return redirect()->back()->with('error', 'Tidak ada kegiatan yang berhasil di-import');
        }
    }

    /**
     * Get kegiatan linked to APBDes
     */
    public function linkedKegiatan()
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa', 'Kepala Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $kodeDesa = $this->session->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        // Get APBDes with linked kegiatan
        $linkedData = $this->apbdesModel
            ->select('apbdes.*, kegiatan.nama_kegiatan, kegiatan.lokasi, kegiatan.pagu_anggaran as pagu_kegiatan,
                     ref_bidang.nama_bidang, ref_rekening.kode_akun, ref_rekening.nama_akun')
            ->join('kegiatan', 'kegiatan.apbdes_id = apbdes.id', 'left')
            ->join('ref_bidang', 'ref_bidang.id = kegiatan.bidang_id', 'left')
            ->join('ref_rekening', 'ref_rekening.id = apbdes.ref_rekening_id', 'left')
            ->where('apbdes.kode_desa', $kodeDesa)
            ->where('apbdes.tahun', $tahun)
            ->whereNotNull('kegiatan.id')
            ->orderBy('apbdes.id', 'ASC')
            ->findAll();
        
        $data = array_merge($this->data, [
            'title' => 'Kegiatan Terintegrasi APBDes',
            'linkedData' => $linkedData,
            'tahun' => $tahun,
        ]);

        return view('apbdes/linked_kegiatan', $data);
    }
}

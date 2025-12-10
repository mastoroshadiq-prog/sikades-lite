<?php

namespace App\Controllers;

use App\Models\ApbdesModel;
use App\Models\BkuModel;
use App\Models\DataUmumDesaModel;
use App\Libraries\PdfExport;

class Lpj extends BaseController
{
    protected $apbdesModel;
    protected $bkuModel;
    protected $desaModel;

    public function __construct()
    {
        $this->apbdesModel = new ApbdesModel();
        $this->bkuModel = new BkuModel();
        $this->desaModel = new DataUmumDesaModel();
    }

    /**
     * Dashboard LPJ
     */
    public function index()
    {
        $kodeDesa = session()->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        // Get available years
        $db = \Config\Database::connect();
        $years = $db->table('bku')
            ->select('EXTRACT(YEAR FROM tanggal)::int as tahun')
            ->where('kode_desa', $kodeDesa)
            ->groupBy('EXTRACT(YEAR FROM tanggal)::int')
            ->orderBy('tahun', 'DESC')
            ->get()
            ->getResultArray();
        
        $data = [
            'title' => 'Laporan Pertanggungjawaban (LPJ)',
            'years' => $years,
            'currentYear' => $tahun,
        ];

        return view('lpj/index', $data);
    }

    /**
     * Laporan LPJ Semester
     */
    public function semester($semester = 1)
    {
        $kodeDesa = session()->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $semester = (int)$semester;
        
        // Get desa info
        $desa = $this->desaModel->where('kode_desa', $kodeDesa)->first();
        
        // Determine date range based on semester
        if ($semester == 1) {
            $startDate = "{$tahun}-01-01";
            $endDate = "{$tahun}-06-30";
            $semesterText = 'Semester I (Januari - Juni)';
        } else {
            $startDate = "{$tahun}-07-01";
            $endDate = "{$tahun}-12-31";
            $semesterText = 'Semester II (Juli - Desember)';
        }
        
        // Get APBDes data (anggaran)
        $pendapatan = $this->apbdesModel
            ->select('apbdes.*, ref_rekening.kode_akun, ref_rekening.nama_akun')
            ->join('ref_rekening', 'ref_rekening.id = apbdes.rekening_id')
            ->where('apbdes.kode_desa', $kodeDesa)
            ->where('apbdes.tahun', $tahun)
            ->where('apbdes.jenis', 'Pendapatan')
            ->findAll();
        
        $belanja = $this->apbdesModel
            ->select('apbdes.*, ref_rekening.kode_akun, ref_rekening.nama_akun')
            ->join('ref_rekening', 'ref_rekening.id = apbdes.rekening_id')
            ->where('apbdes.kode_desa', $kodeDesa)
            ->where('apbdes.tahun', $tahun)
            ->where('apbdes.jenis', 'Belanja')
            ->findAll();
        
        // Get realisasi from BKU for semester
        $db = \Config\Database::connect();
        
        // Calculate realisasi per rekening
        foreach ($pendapatan as &$item) {
            $realisasi = $db->table('bku')
                ->selectSum('debet', 'total')
                ->where('kode_desa', $kodeDesa)
                ->where('rekening_id', $item['rekening_id'])
                ->where('tanggal >=', $startDate)
                ->where('tanggal <=', $endDate)
                ->get()
                ->getRow();
            $item['realisasi'] = $realisasi->total ?? 0;
            $item['persentase'] = $item['anggaran'] > 0 
                ? ($item['realisasi'] / $item['anggaran']) * 100 
                : 0;
        }
        
        foreach ($belanja as &$item) {
            $realisasi = $db->table('bku')
                ->selectSum('kredit', 'total')
                ->where('kode_desa', $kodeDesa)
                ->where('rekening_id', $item['rekening_id'])
                ->where('tanggal >=', $startDate)
                ->where('tanggal <=', $endDate)
                ->get()
                ->getRow();
            $item['realisasi'] = $realisasi->total ?? 0;
            $item['persentase'] = $item['anggaran'] > 0 
                ? ($item['realisasi'] / $item['anggaran']) * 100 
                : 0;
        }
        
        // Calculate totals
        $totalAnggaranP = array_sum(array_column($pendapatan, 'anggaran'));
        $totalRealisasiP = array_sum(array_column($pendapatan, 'realisasi'));
        $totalAnggaranB = array_sum(array_column($belanja, 'anggaran'));
        $totalRealisasiB = array_sum(array_column($belanja, 'realisasi'));
        
        $data = [
            'title' => 'LPJ ' . $semesterText . ' ' . $tahun,
            'desa' => $desa,
            'tahun' => $tahun,
            'semester' => $semester,
            'semesterText' => $semesterText,
            'pendapatan' => $pendapatan,
            'belanja' => $belanja,
            'totalAnggaranP' => $totalAnggaranP,
            'totalRealisasiP' => $totalRealisasiP,
            'totalAnggaranB' => $totalAnggaranB,
            'totalRealisasiB' => $totalRealisasiB,
            'surplusAnggaran' => $totalAnggaranP - $totalAnggaranB,
            'surplusRealisasi' => $totalRealisasiP - $totalRealisasiB,
        ];

        return view('lpj/semester', $data);
    }

    /**
     * Export LPJ to PDF
     */
    public function exportPdf($semester = 1)
    {
        $kodeDesa = session()->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $semester = (int)$semester;
        
        // Get desa info
        $desa = $this->desaModel->where('kode_desa', $kodeDesa)->first();
        
        // Determine date range
        if ($semester == 1) {
            $startDate = "{$tahun}-01-01";
            $endDate = "{$tahun}-06-30";
        } else {
            $startDate = "{$tahun}-07-01";
            $endDate = "{$tahun}-12-31";
        }
        
        // Get data
        $pendapatan = $this->apbdesModel
            ->select('apbdes.*, ref_rekening.kode_akun, ref_rekening.nama_akun')
            ->join('ref_rekening', 'ref_rekening.id = apbdes.rekening_id')
            ->where('apbdes.kode_desa', $kodeDesa)
            ->where('apbdes.tahun', $tahun)
            ->where('apbdes.jenis', 'Pendapatan')
            ->findAll();
        
        $belanja = $this->apbdesModel
            ->select('apbdes.*, ref_rekening.kode_akun, ref_rekening.nama_akun')
            ->join('ref_rekening', 'ref_rekening.id = apbdes.rekening_id')
            ->where('apbdes.kode_desa', $kodeDesa)
            ->where('apbdes.tahun', $tahun)
            ->where('apbdes.jenis', 'Belanja')
            ->findAll();
        
        // Calculate realisasi
        $db = \Config\Database::connect();
        
        foreach ($pendapatan as &$item) {
            $realisasi = $db->table('bku')
                ->selectSum('debet', 'total')
                ->where('kode_desa', $kodeDesa)
                ->where('rekening_id', $item['rekening_id'])
                ->where('tanggal >=', $startDate)
                ->where('tanggal <=', $endDate)
                ->get()
                ->getRow();
            $item['realisasi'] = $realisasi->total ?? 0;
        }
        
        foreach ($belanja as &$item) {
            $realisasi = $db->table('bku')
                ->selectSum('kredit', 'total')
                ->where('kode_desa', $kodeDesa)
                ->where('rekening_id', $item['rekening_id'])
                ->where('tanggal >=', $startDate)
                ->where('tanggal <=', $endDate)
                ->get()
                ->getRow();
            $item['realisasi'] = $realisasi->total ?? 0;
        }
        
        $pdfData = [
            'desa' => $desa,
            'tahun' => $tahun,
            'semester' => $semester,
            'pendapatan' => $pendapatan,
            'belanja' => $belanja,
        ];
        
        $pdfExport = new PdfExport();
        $pdfExport->generateLpjReport($pdfData);
    }
}

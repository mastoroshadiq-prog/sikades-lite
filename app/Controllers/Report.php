<?php

namespace App\Controllers;

use App\Models\BkuModel;
use App\Models\ApbdesModel;
use App\Models\SppModel;
use App\Models\PajakModel;
use App\Models\RefRekeningModel;
use App\Models\DataUmumDesaModel;

class Report extends BaseController
{
    protected $bkuModel;
    protected $apbdesModel;
    protected $sppModel;
    protected $pajakModel;
    protected $rekeningModel;
    protected $desaModel;

    public function __construct()
    {
        $this->bkuModel = new BkuModel();
        $this->apbdesModel = new ApbdesModel();
        $this->sppModel = new SppModel();
        $this->pajakModel = new PajakModel();
        $this->rekeningModel = new RefRekeningModel();
        $this->desaModel = new DataUmumDesaModel();
    }

    /**
     * Report index - List of available reports
     */
    public function index()
    {
        $data = [
            'title' => 'Laporan',
            'user' => session()->get()
        ];

        return view('report/index', $data);
    }

    /**
     * BKU Report (Buku Kas Umum)
     */
    public function bku()
    {
        $kodeDesa = session()->get('kode_desa');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $format = $this->request->getGet('format') ?? 'html'; // html, pdf, excel

        // Get transactions
        $transactions = $this->bkuModel
            ->where('kode_desa', $kodeDesa)
            ->where('EXTRACT(MONTH FROM tanggal)::int', $bulan)
            ->where('EXTRACT(YEAR FROM tanggal)::int', $tahun)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        // Calculate running balance
        $saldoAwal = $this->getSaldoAwal($kodeDesa, $bulan, $tahun);
        $saldo = $saldoAwal;
        
        foreach ($transactions as &$trans) {
            $trans['saldo'] = $saldo + $trans['debet'] - $trans['kredit'];
            $saldo = $trans['saldo'];
        }

        // Get desa info
        $desa = $this->desaModel->where('kode_desa', $kodeDesa)->first();

        $data = [
            'title' => 'Laporan Buku Kas Umum',
            'user' => session()->get('user'),
            'desa' => $desa,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'bulanNama' => $this->getBulanIndonesia($bulan),
            'saldoAwal' => $saldoAwal,
            'transactions' => $transactions,
            'totalDebet' => array_sum(array_column($transactions, 'debet')),
            'totalKredit' => array_sum(array_column($transactions, 'kredit')),
            'saldoAkhir' => $saldo
        ];

        if ($format === 'pdf') {
            return $this->generateBkuPdf($data);
        } elseif ($format === 'excel') {
            return $this->generateBkuExcel($data);
        } else {
            return view('report/bku', $data);
        }
    }

    /**
     * APBDes Report (Anggaran)
     */
    public function apbdes()
    {
        $kodeDesa = session()->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $format = $this->request->getGet('format') ?? 'html';

        // Get budget data with rekening
        $builder = $this->apbdesModel->builder();
        $builder->select('apbdes.*, ref_rekening.kode_akun, ref_rekening.nama_akun, ref_rekening.level')
                ->join('ref_rekening', 'apbdes.ref_rekening_id = ref_rekening.id')
                ->where('apbdes.kode_desa', $kodeDesa)
                ->where('apbdes.tahun', $tahun)
                ->orderBy('ref_rekening.kode_akun', 'ASC');
        
        $budgets = $builder->get()->getResultArray();

        // Group by category
        $pendapatan = array_filter($budgets, fn($b) => strpos($b['kode_akun'], '4.') === 0);
        $belanja = array_filter($budgets, fn($b) => strpos($b['kode_akun'], '5.') === 0);

        // Get desa info
        $desa = $this->desaModel->where('kode_desa', $kodeDesa)->first();

        $data = [
            'title' => 'Laporan APBDes',
            'user' => session()->get('user'),
            'desa' => $desa,
            'tahun' => $tahun,
            'pendapatan' => $pendapatan,
            'belanja' => $belanja,
            'totalPendapatan' => array_sum(array_column($pendapatan, 'anggaran')),
            'totalBelanja' => array_sum(array_column($belanja, 'anggaran')),
            'budgets' => $budgets
        ];

        if ($format === 'pdf') {
            return $this->generateApbdesPdf($data);
        } elseif ($format === 'excel') {
            return $this->generateApbdesExcel($data);
        } else {
            return view('report/apbdes', $data);
        }
    }

    /**
     * LRA Report (Laporan Realisasi Anggaran)
     */
    public function lra()
    {
        $kodeDesa = session()->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $format = $this->request->getGet('format') ?? 'html';

        // Get budget with realization using raw query to avoid query builder escaping issues
        $db = \Config\Database::connect();
        $data_lra = $db->query("
            SELECT 
                apbdes.id,
                apbdes.kode_desa,
                apbdes.tahun,
                apbdes.ref_rekening_id,
                apbdes.uraian,
                apbdes.anggaran,
                apbdes.sumber_dana,
                ref_rekening.kode_akun,
                ref_rekening.nama_akun,
                ref_rekening.level,
                COALESCE(SUM(bku.debet), 0) as realisasi_pendapatan,
                COALESCE(SUM(bku.kredit), 0) as realisasi_belanja
            FROM apbdes
            JOIN ref_rekening ON apbdes.ref_rekening_id = ref_rekening.id
            LEFT JOIN bku ON bku.ref_rekening_id = ref_rekening.id 
                AND bku.kode_desa = apbdes.kode_desa 
                AND EXTRACT(YEAR FROM bku.tanggal)::int = apbdes.tahun
            WHERE apbdes.kode_desa = ?
            AND apbdes.tahun = ?
            GROUP BY apbdes.id, apbdes.kode_desa, apbdes.tahun, apbdes.ref_rekening_id,
                     apbdes.uraian, apbdes.anggaran, apbdes.sumber_dana,
                     ref_rekening.id, ref_rekening.kode_akun, 
                     ref_rekening.nama_akun, ref_rekening.level
            ORDER BY ref_rekening.kode_akun ASC
        ", [$kodeDesa, $tahun])->getResultArray();

        // Calculate percentages
        foreach ($data_lra as &$item) {
            $realisasi = strpos($item['kode_akun'], '4.') === 0 
                ? $item['realisasi_pendapatan'] 
                : $item['realisasi_belanja'];
            
            $item['realisasi'] = $realisasi;
            $item['sisa'] = $item['anggaran'] - $realisasi;
            $item['persentase'] = $item['anggaran'] > 0 
                ? ($realisasi / $item['anggaran']) * 100 
                : 0;
        }

        // Get desa info
        $desa = $this->desaModel->where('kode_desa', $kodeDesa)->first();

        $data = [
            'title' => 'Laporan Realisasi Anggaran',
            'user' => session()->get('user'),
            'desa' => $desa,
            'tahun' => $tahun,
            'data_lra' => $data_lra
        ];

        if ($format === 'pdf') {
            return $this->generateLraPdf($data);
        } elseif ($format === 'excel') {
            return $this->generateLraExcel($data);
        } else {
            return view('report/lra', $data);
        }
    }

    /**
     * SPP Report
     */
    public function spp($id)
    {
        $format = $this->request->getGet('format') ?? 'html';

        // Get SPP detail with rincian
        $spp = $this->sppModel->find($id);
        
        if (!$spp) {
            return redirect()->to('/spp')->with('error', 'SPP tidak ditemukan');
        }

        // Get rincian
        $rincian = $this->db->table('spp_rincian')
            ->select('spp_rincian.*, apbdes.uraian, ref_rekening.kode_akun, ref_rekening.nama_akun')
            ->join('apbdes', 'spp_rincian.apbdes_id = apbdes.id')
            ->join('ref_rekening', 'apbdes.ref_rekening_id = ref_rekening.id')
            ->where('spp_rincian.spp_id', $id)
            ->get()
            ->getResultArray();

        // Get desa info
        $desa = $this->desaModel->where('kode_desa', $spp['kode_desa'])->first();

        $data = [
            'title' => 'SPP - ' . $spp['nomor_spp'],
            'user' => session()->get('user'),
            'desa' => $desa,
            'spp' => $spp,
            'rincian' => $rincian
        ];

        if ($format === 'pdf') {
            return $this->generateSppPdf($data);
        } else {
            return view('report/spp', $data);
        }
    }

    /**
     * Tax Report (Pajak)
     */
    public function pajak()
    {
        $kodeDesa = session()->get('kode_desa');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $format = $this->request->getGet('format') ?? 'html';

        // Get tax data
        $pajak = $this->pajakModel
            ->select('pajak.*, bku.tanggal, bku.uraian, bku.no_bukti as nomor_bukti')
            ->join('bku', 'pajak.bku_id = bku.id')
            ->where('bku.kode_desa', $kodeDesa)
            ->where('EXTRACT(YEAR FROM bku.tanggal)::int', $tahun)
            ->orderBy('bku.tanggal', 'ASC')
            ->findAll();

        // Group by type
        $ppn = array_filter($pajak, fn($p) => $p['jenis_pajak'] === 'PPN');
        $pph = array_filter($pajak, fn($p) => $p['jenis_pajak'] === 'PPh');

        // Get desa info
        $desa = $this->desaModel->where('kode_desa', $kodeDesa)->first();

        $data = [
            'title' => 'Laporan Pajak',
            'user' => session()->get('user'),
            'desa' => $desa,
            'tahun' => $tahun,
            'pajak' => $pajak,
            'ppn' => $ppn,
            'pph' => $pph,
            'totalPPN' => array_sum(array_column($ppn, 'nilai')),
            'totalPPh' => array_sum(array_column($pph, 'nilai')),
            'totalPajak' => array_sum(array_column($pajak, 'nilai'))
        ];

        if ($format === 'pdf') {
            return $this->generatePajakPdf($data);
        } elseif ($format === 'excel') {
            return $this->generatePajakExcel($data);
        } else {
            return view('report/pajak', $data);
        }
    }

    // ============== PDF GENERATORS ==============

    private function generateBkuPdf($data)
    {
        $pdf = new \App\Libraries\PdfExport();
        $pdf->generateBkuReport($data);
    }

    private function generateApbdesPdf($data)
    {
        $pdf = new \App\Libraries\PdfExport();
        $pdf->generateApbdesReport($data);
    }

    private function generateLraPdf($data)
    {
        $pdf = new \App\Libraries\PdfExport();
        $pdf->generateLraReport($data);
    }

    private function generateSppPdf($data)
    {
        $pdf = new \App\Libraries\PdfExport();
        $pdf->generateSppReport($data);
    }

    private function generatePajakPdf($data)
    {
        $pdf = new \App\Libraries\PdfExport();
        $pdf->generatePajakReport($data);
    }

    // ============== EXCEL GENERATORS ==============

    private function generateBkuExcel($data)
    {
        $excel = new \App\Libraries\ExcelExport();
        $excel->generateBkuReport($data);
    }

    private function generateApbdesExcel($data)
    {
        $excel = new \App\Libraries\ExcelExport();
        $excel->generateApbdesReport($data);
    }

    private function generateLraExcel($data)
    {
        $excel = new \App\Libraries\ExcelExport();
        $excel->generateLraReport($data);
    }

    private function generatePajakExcel($data)
    {
        $excel = new \App\Libraries\ExcelExport();
        $excel->generatePajakReport($data);
    }

    // ============== HELPER METHODS ==============

    private function getSaldoAwal($kodeDesa, $bulan, $tahun)
    {
        // Calculate saldo from beginning of year until the month before
        $builder = $this->bkuModel->builder();
        $result = $builder->selectSum('debet')
                          ->selectSum('kredit')
                          ->where('kode_desa', $kodeDesa)
                          ->where('EXTRACT(YEAR FROM tanggal)::int', $tahun)
                          ->where('EXTRACT(MONTH FROM tanggal)::int <', $bulan)
                          ->get()
                          ->getRowArray();

        return ($result['debet'] ?? 0) - ($result['kredit'] ?? 0);
    }

    private function getBulanIndonesia($bulan)
    {
        $namaBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        return $namaBulan[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?? '';
    }

    private function arrayToCsv($data)
    {
        if (empty($data)) {
            return '';
        }

        $output = fopen('php://temp', 'r+');
        
        // Header
        fputcsv($output, array_keys($data[0]));
        
        // Data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}

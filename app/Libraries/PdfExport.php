<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfExport
{
    protected $dompdf;
    protected $options;

    public function __construct()
    {
        $this->options = new Options();
        $this->options->set('isHtml5ParserEnabled', true);
        $this->options->set('isRemoteEnabled', true);
        $this->options->set('defaultFont', 'Arial');
        $this->options->set('isPhpEnabled', true);
        
        $this->dompdf = new Dompdf($this->options);
    }

    /**
     * Generate PDF from HTML content
     */
    public function generate(string $html, string $filename, string $orientation = 'portrait', string $size = 'A4'): void
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper($size, $orientation);
        $this->dompdf->render();
        
        $this->dompdf->stream($filename . '.pdf', [
            'Attachment' => true
        ]);
    }

    /**
     * Generate PDF and return as string (for saving to file)
     */
    public function generateString(string $html, string $orientation = 'portrait', string $size = 'A4'): string
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper($size, $orientation);
        $this->dompdf->render();
        
        return $this->dompdf->output();
    }

    /**
     * Generate BKU PDF Report
     */
    public function generateBkuReport(array $data): void
    {
        $html = $this->getBkuTemplate($data);
        $this->generate($html, 'BKU_' . $data['bulanNama'] . '_' . $data['tahun'], 'landscape');
    }

    /**
     * Generate APBDes PDF Report
     */
    public function generateApbdesReport(array $data): void
    {
        $html = $this->getApbdesTemplate($data);
        $this->generate($html, 'APBDes_' . $data['tahun'], 'portrait');
    }

    /**
     * Generate LRA PDF Report
     */
    public function generateLraReport(array $data): void
    {
        $html = $this->getLraTemplate($data);
        $this->generate($html, 'LRA_' . $data['tahun'], 'landscape');
    }

    /**
     * Generate Tax PDF Report
     */
    public function generatePajakReport(array $data): void
    {
        $html = $this->getPajakTemplate($data);
        $this->generate($html, 'Pajak_' . $data['tahun'], 'portrait');
    }

    /**
     * Generate SPP PDF Report
     */
    public function generateSppReport(array $data): void
    {
        $html = $this->getSppTemplate($data);
        $this->generate($html, 'SPP_' . $data['spp']['nomor_spp'], 'portrait');
    }

    /**
     * Generate LPJ PDF Report
     */
    public function generateLpjReport(array $data): void
    {
        $html = $this->getLpjTemplate($data);
        $this->generate($html, 'LPJ_Semester_' . $data['semester'] . '_' . $data['tahun'], 'portrait');
    }

    /**
     * Generate Kuitansi PDF
     */
    public function generateKuitansi(array $data): void
    {
        $html = $this->getKuitansiTemplate($data);
        $this->generate($html, 'Kuitansi_' . ($data['nomor'] ?? 'X'), 'portrait', 'A5');
    }

    /**
     * BKU PDF Template
     */
    private function getBkuTemplate(array $data): string
    {
        $desa = $data['desa'] ?? [];
        $transactions = $data['transactions'] ?? [];
        
        $rows = '';
        foreach ($transactions as $idx => $trans) {
            $rows .= '<tr>
                <td style="text-align:center">' . ($idx + 1) . '</td>
                <td>' . date('d/m/Y', strtotime($trans['tanggal'])) . '</td>
                <td>' . htmlspecialchars($trans['no_bukti'] ?? '-') . '</td>
                <td>' . htmlspecialchars($trans['uraian']) . '</td>
                <td style="text-align:right">' . ($trans['debet'] > 0 ? number_format($trans['debet'], 0, ',', '.') : '-') . '</td>
                <td style="text-align:right">' . ($trans['kredit'] > 0 ? number_format($trans['kredit'], 0, ',', '.') : '-') . '</td>
                <td style="text-align:right">' . number_format($trans['saldo'], 0, ',', '.') . '</td>
            </tr>';
        }

        return '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; font-size: 10pt; }
                h1, h2, h3 { margin: 5px 0; text-align: center; }
                table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                th, td { border: 1px solid #000; padding: 5px; font-size: 9pt; }
                th { background-color: #f0f0f0; }
                .header { text-align: center; margin-bottom: 20px; }
                .summary { margin: 10px 0; }
                .signatures { margin-top: 30px; }
                .signature-box { width: 45%; display: inline-block; text-align: center; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>PEMERINTAH DESA ' . strtoupper($desa['nama_desa'] ?? 'NAMA DESA') . '</h2>
                <h3>BUKU KAS UMUM</h3>
                <p>Bulan: ' . ($data['bulanNama'] ?? '-') . ' ' . ($data['tahun'] ?? date('Y')) . '</p>
            </div>
            
            <div class="summary">
                <table style="width:100%">
                    <tr>
                        <td style="border:none;width:25%"><strong>Saldo Awal:</strong> Rp ' . number_format($data['saldoAwal'] ?? 0, 0, ',', '.') . '</td>
                        <td style="border:none;width:25%"><strong>Penerimaan:</strong> Rp ' . number_format($data['totalDebet'] ?? 0, 0, ',', '.') . '</td>
                        <td style="border:none;width:25%"><strong>Pengeluaran:</strong> Rp ' . number_format($data['totalKredit'] ?? 0, 0, ',', '.') . '</td>
                        <td style="border:none;width:25%"><strong>Saldo Akhir:</strong> Rp ' . number_format($data['saldoAkhir'] ?? 0, 0, ',', '.') . '</td>
                    </tr>
                </table>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">Tanggal</th>
                        <th width="13%">No. Bukti</th>
                        <th width="30%">Uraian</th>
                        <th width="13%">Penerimaan</th>
                        <th width="13%">Pengeluaran</th>
                        <th width="14%">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background:#f0f0f0">
                        <td colspan="6" style="text-align:center"><strong>Saldo Awal</strong></td>
                        <td style="text-align:right"><strong>Rp ' . number_format($data['saldoAwal'] ?? 0, 0, ',', '.') . '</strong></td>
                    </tr>
                    ' . $rows . '
                    <tr style="background:#f0f0f0">
                        <td colspan="4" style="text-align:right"><strong>JUMLAH</strong></td>
                        <td style="text-align:right"><strong>Rp ' . number_format($data['totalDebet'] ?? 0, 0, ',', '.') . '</strong></td>
                        <td style="text-align:right"><strong>Rp ' . number_format($data['totalKredit'] ?? 0, 0, ',', '.') . '</strong></td>
                        <td style="text-align:right"><strong>Rp ' . number_format($data['saldoAkhir'] ?? 0, 0, ',', '.') . '</strong></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="signatures" style="margin-top:50px">
                <table style="width:100%;border:none">
                    <tr>
                        <td style="width:50%;text-align:center;border:none">
                            <p>Mengetahui,<br><strong>Kepala Desa</strong></p>
                            <br><br><br><br>
                            <p><u><strong>' . ($desa['nama_kepala_desa'] ?? '.......................') . '</strong></u></p>
                        </td>
                        <td style="width:50%;text-align:center;border:none">
                            <p>' . ($desa['nama_desa'] ?? 'Nama Desa') . ', ' . date('d') . ' ' . ($data['bulanNama'] ?? '-') . ' ' . ($data['tahun'] ?? date('Y')) . '<br><strong>Bendahara Desa</strong></p>
                            <br><br><br><br>
                            <p><u><strong>' . ($desa['nama_bendahara'] ?? '.......................') . '</strong></u></p>
                        </td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';
    }

    /**
     * APBDes PDF Template
     */
    private function getApbdesTemplate(array $data): string
    {
        $desa = $data['desa'] ?? [];
        $pendapatan = $data['pendapatan'] ?? [];
        $belanja = $data['belanja'] ?? [];
        
        $pendapatanRows = '';
        foreach ($pendapatan as $idx => $item) {
            $pendapatanRows .= '<tr>
                <td style="text-align:center">' . ($idx + 1) . '</td>
                <td>' . htmlspecialchars($item['kode_akun']) . '</td>
                <td>' . htmlspecialchars($item['uraian'] ?? $item['nama_akun']) . '</td>
                <td>' . htmlspecialchars($item['sumber_dana'] ?? '-') . '</td>
                <td style="text-align:right">Rp ' . number_format($item['anggaran'] ?? 0, 0, ',', '.') . '</td>
            </tr>';
        }
        
        $belanjaRows = '';
        foreach ($belanja as $idx => $item) {
            $belanjaRows .= '<tr>
                <td style="text-align:center">' . ($idx + 1) . '</td>
                <td>' . htmlspecialchars($item['kode_akun']) . '</td>
                <td>' . htmlspecialchars($item['uraian'] ?? $item['nama_akun']) . '</td>
                <td>' . htmlspecialchars($item['sumber_dana'] ?? '-') . '</td>
                <td style="text-align:right">Rp ' . number_format($item['anggaran'] ?? 0, 0, ',', '.') . '</td>
            </tr>';
        }

        return '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; font-size: 10pt; }
                h2, h3 { margin: 5px 0; text-align: center; }
                table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                th, td { border: 1px solid #000; padding: 5px; font-size: 9pt; }
                th { background-color: #f0f0f0; }
                .section-title { background-color: #e0e0e0; font-weight: bold; }
            </style>
        </head>
        <body>
            <div style="text-align:center;margin-bottom:20px">
                <h2>PEMERINTAH DESA ' . strtoupper($desa['nama_desa'] ?? 'NAMA DESA') . '</h2>
                <h3>ANGGARAN PENDAPATAN DAN BELANJA DESA (APBDes)</h3>
                <p>Tahun Anggaran: ' . ($data['tahun'] ?? date('Y')) . '</p>
            </div>
            
            <h4>A. PENDAPATAN</h4>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kode Rekening</th>
                        <th width="45%">Uraian</th>
                        <th width="15%">Sumber Dana</th>
                        <th width="20%">Anggaran (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $pendapatanRows . '
                    <tr style="background:#d0f0d0;font-weight:bold">
                        <td colspan="4" style="text-align:right">JUMLAH PENDAPATAN</td>
                        <td style="text-align:right">Rp ' . number_format($data['totalPendapatan'] ?? 0, 0, ',', '.') . '</td>
                    </tr>
                </tbody>
            </table>
            
            <h4>B. BELANJA</h4>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kode Rekening</th>
                        <th width="45%">Uraian</th>
                        <th width="15%">Sumber Dana</th>
                        <th width="20%">Anggaran (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $belanjaRows . '
                    <tr style="background:#f0d0d0;font-weight:bold">
                        <td colspan="4" style="text-align:right">JUMLAH BELANJA</td>
                        <td style="text-align:right">Rp ' . number_format($data['totalBelanja'] ?? 0, 0, ',', '.') . '</td>
                    </tr>
                </tbody>
            </table>
            
            <table style="margin-top:20px">
                <tr style="background:#d0d0f0;font-weight:bold">
                    <td style="width:80%;text-align:right">SURPLUS / (DEFISIT)</td>
                    <td style="width:20%;text-align:right">Rp ' . number_format(($data['totalPendapatan'] ?? 0) - ($data['totalBelanja'] ?? 0), 0, ',', '.') . '</td>
                </tr>
            </table>
            
            <div style="margin-top:50px">
                <table style="width:100%;border:none">
                    <tr>
                        <td style="width:50%;text-align:center;border:none">
                            <p>Mengetahui,<br><strong>Kepala Desa</strong></p>
                            <br><br><br><br>
                            <p><u><strong>' . ($desa['nama_kepala_desa'] ?? '.......................') . '</strong></u></p>
                        </td>
                        <td style="width:50%;text-align:center;border:none">
                            <p>' . ($desa['nama_desa'] ?? 'Nama Desa') . ', ' . date('d F Y') . '<br><strong>Bendahara Desa</strong></p>
                            <br><br><br><br>
                            <p><u><strong>' . ($desa['nama_bendahara'] ?? '.......................') . '</strong></u></p>
                        </td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';
    }

    /**
     * LRA PDF Template
     */
    private function getLraTemplate(array $data): string
    {
        $desa = $data['desa'] ?? [];
        $dataLra = $data['data_lra'] ?? [];
        
        $rows = '';
        $totalAnggaran = 0;
        $totalRealisasi = 0;
        
        foreach ($dataLra as $idx => $item) {
            $persen = $item['persentase'] ?? 0;
            $sisa = $item['sisa'] ?? 0;
            
            $totalAnggaran += $item['anggaran'] ?? 0;
            $totalRealisasi += $item['realisasi'] ?? 0;
            
            $rows .= '<tr>
                <td style="text-align:center">' . ($idx + 1) . '</td>
                <td>' . htmlspecialchars($item['kode_akun']) . '</td>
                <td>' . htmlspecialchars($item['uraian'] ?? $item['nama_akun']) . '</td>
                <td style="text-align:right">Rp ' . number_format($item['anggaran'] ?? 0, 0, ',', '.') . '</td>
                <td style="text-align:right">Rp ' . number_format($item['realisasi'] ?? 0, 0, ',', '.') . '</td>
                <td style="text-align:center">' . number_format($persen, 2) . '%</td>
                <td style="text-align:right">Rp ' . number_format(abs($sisa), 0, ',', '.') . '</td>
            </tr>';
        }

        return '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; font-size: 9pt; }
                h2, h3 { margin: 5px 0; text-align: center; }
                table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                th, td { border: 1px solid #000; padding: 4px; font-size: 8pt; }
                th { background-color: #f0f0f0; }
            </style>
        </head>
        <body>
            <div style="text-align:center;margin-bottom:20px">
                <h2>PEMERINTAH DESA ' . strtoupper($desa['nama_desa'] ?? 'NAMA DESA') . '</h2>
                <h3>LAPORAN REALISASI ANGGARAN (LRA)</h3>
                <p>Tahun Anggaran: ' . ($data['tahun'] ?? date('Y')) . '</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">Kode Rekening</th>
                        <th width="28%">Uraian</th>
                        <th width="15%">Anggaran (Rp)</th>
                        <th width="15%">Realisasi (Rp)</th>
                        <th width="10%">%</th>
                        <th width="15%">Sisa (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $rows . '
                    <tr style="background:#d0d0f0;font-weight:bold">
                        <td colspan="3" style="text-align:right">TOTAL</td>
                        <td style="text-align:right">Rp ' . number_format($totalAnggaran, 0, ',', '.') . '</td>
                        <td style="text-align:right">Rp ' . number_format($totalRealisasi, 0, ',', '.') . '</td>
                        <td style="text-align:center">' . ($totalAnggaran > 0 ? number_format(($totalRealisasi / $totalAnggaran) * 100, 2) : 0) . '%</td>
                        <td style="text-align:right">Rp ' . number_format($totalAnggaran - $totalRealisasi, 0, ',', '.') . '</td>
                    </tr>
                </tbody>
            </table>
            
            <div style="margin-top:50px">
                <table style="width:100%;border:none">
                    <tr>
                        <td style="width:50%;text-align:center;border:none">
                            <p>Mengetahui,<br><strong>Kepala Desa</strong></p>
                            <br><br><br><br>
                            <p><u><strong>' . ($desa['nama_kepala_desa'] ?? '.......................') . '</strong></u></p>
                        </td>
                        <td style="width:50%;text-align:center;border:none">
                            <p>' . ($desa['nama_desa'] ?? 'Nama Desa') . ', ' . date('d F Y') . '<br><strong>Bendahara Desa</strong></p>
                            <br><br><br><br>
                            <p><u><strong>' . ($desa['nama_bendahara'] ?? '.......................') . '</strong></u></p>
                        </td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';
    }

    /**
     * Pajak PDF Template
     */
    private function getPajakTemplate(array $data): string
    {
        $desa = $data['desa'] ?? [];
        $ppn = $data['ppn'] ?? [];
        $pph = $data['pph'] ?? [];
        
        $ppnRows = '';
        foreach ($ppn as $idx => $item) {
            $ppnRows .= '<tr>
                <td style="text-align:center">' . ($idx + 1) . '</td>
                <td>' . date('d/m/Y', strtotime($item['tanggal'])) . '</td>
                <td>' . htmlspecialchars($item['nomor_bukti'] ?? '-') . '</td>
                <td>' . htmlspecialchars($item['uraian'] ?? '-') . '</td>
                <td style="text-align:right">Rp ' . number_format($item['nilai'] ?? 0, 0, ',', '.') . '</td>
                <td style="text-align:center">' . ($item['status_pembayaran'] === 'Sudah' ? '✓ Lunas' : 'Belum') . '</td>
            </tr>';
        }
        
        $pphRows = '';
        foreach ($pph as $idx => $item) {
            $pphRows .= '<tr>
                <td style="text-align:center">' . ($idx + 1) . '</td>
                <td>' . date('d/m/Y', strtotime($item['tanggal'])) . '</td>
                <td>' . htmlspecialchars($item['nomor_bukti'] ?? '-') . '</td>
                <td>' . htmlspecialchars($item['uraian'] ?? '-') . '</td>
                <td style="text-align:right">Rp ' . number_format($item['nilai'] ?? 0, 0, ',', '.') . '</td>
                <td style="text-align:center">' . ($item['status_pembayaran'] === 'Sudah' ? '✓ Lunas' : 'Belum') . '</td>
            </tr>';
        }

        return '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; font-size: 10pt; }
                h2, h3, h4 { margin: 5px 0; text-align: center; }
                table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                th, td { border: 1px solid #000; padding: 5px; font-size: 9pt; }
                th { background-color: #f0f0f0; }
            </style>
        </head>
        <body>
            <div style="text-align:center;margin-bottom:20px">
                <h2>PEMERINTAH DESA ' . strtoupper($desa['nama_desa'] ?? 'NAMA DESA') . '</h2>
                <h3>LAPORAN REKAPITULASI PAJAK</h3>
                <p>Tahun: ' . ($data['tahun'] ?? date('Y')) . '</p>
            </div>
            
            <h4 style="text-align:left">A. PPN (Pajak Pertambahan Nilai)</h4>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">Tanggal</th>
                        <th width="15%">No. Bukti</th>
                        <th width="38%">Uraian</th>
                        <th width="15%">Nilai (Rp)</th>
                        <th width="15%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    ' . ($ppnRows ?: '<tr><td colspan="6" style="text-align:center">Tidak ada data PPN</td></tr>') . '
                    <tr style="background:#d0d0f0;font-weight:bold">
                        <td colspan="4" style="text-align:right">TOTAL PPN</td>
                        <td style="text-align:right">Rp ' . number_format($data['totalPPN'] ?? 0, 0, ',', '.') . '</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            
            <h4 style="text-align:left">B. PPh (Pajak Penghasilan)</h4>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">Tanggal</th>
                        <th width="15%">No. Bukti</th>
                        <th width="38%">Uraian</th>
                        <th width="15%">Nilai (Rp)</th>
                        <th width="15%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    ' . ($pphRows ?: '<tr><td colspan="6" style="text-align:center">Tidak ada data PPh</td></tr>') . '
                    <tr style="background:#d0d0f0;font-weight:bold">
                        <td colspan="4" style="text-align:right">TOTAL PPh</td>
                        <td style="text-align:right">Rp ' . number_format($data['totalPPh'] ?? 0, 0, ',', '.') . '</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            
            <table style="margin-top:20px">
                <tr style="background:#d0f0d0;font-weight:bold">
                    <td style="width:80%;text-align:right;font-size:12pt">TOTAL PAJAK KESELURUHAN</td>
                    <td style="width:20%;text-align:right;font-size:12pt">Rp ' . number_format($data['totalPajak'] ?? 0, 0, ',', '.') . '</td>
                </tr>
            </table>
            
            <div style="margin-top:50px">
                <table style="width:100%;border:none">
                    <tr>
                        <td style="width:50%;text-align:center;border:none">
                            <p>Mengetahui,<br><strong>Kepala Desa</strong></p>
                            <br><br><br><br>
                            <p><u><strong>' . ($desa['nama_kepala_desa'] ?? '.......................') . '</strong></u></p>
                        </td>
                        <td style="width:50%;text-align:center;border:none">
                            <p>' . ($desa['nama_desa'] ?? 'Nama Desa') . ', ' . date('d F Y') . '<br><strong>Bendahara Desa</strong></p>
                            <br><br><br><br>
                            <p><u><strong>' . ($desa['nama_bendahara'] ?? '.......................') . '</strong></u></p>
                        </td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';
    }

    /**
     * SPP PDF Template
     */
    private function getSppTemplate(array $data): string
    {
        $desa = $data['desa'] ?? [];
        $spp = $data['spp'] ?? [];
        $rincian = $data['rincian'] ?? [];
        
        $rincianRows = '';
        $total = 0;
        foreach ($rincian as $idx => $item) {
            $total += $item['nilai_pencairan'] ?? 0;
            $rincianRows .= '<tr>
                <td style="text-align:center">' . ($idx + 1) . '</td>
                <td>' . htmlspecialchars($item['kode_akun'] ?? '-') . '</td>
                <td>' . htmlspecialchars($item['nama_akun'] ?? '-') . '</td>
                <td style="text-align:right">Rp ' . number_format($item['nilai_pencairan'] ?? 0, 0, ',', '.') . '</td>
            </tr>';
        }

        // Terbilang function
        $terbilang = $this->terbilang($spp['jumlah'] ?? 0);

        return '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; font-size: 10pt; }
                h2, h3 { margin: 5px 0; text-align: center; }
                table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                th, td { border: 1px solid #000; padding: 5px; font-size: 9pt; }
                th { background-color: #f0f0f0; }
                .info-table td { border: none; padding: 3px; }
            </style>
        </head>
        <body>
            <div style="text-align:center;margin-bottom:20px">
                <h2>PEMERINTAH DESA ' . strtoupper($desa['nama_desa'] ?? 'NAMA DESA') . '</h2>
                <h3>SURAT PERMINTAAN PEMBAYARAN (SPP)</h3>
                <p>Nomor: ' . htmlspecialchars($spp['nomor_spp'] ?? '-') . '</p>
            </div>
            
            <table class="info-table" style="margin-bottom:20px">
                <tr>
                    <td width="15%">Nomor SPP</td>
                    <td width="2%">:</td>
                    <td width="33%"><strong>' . htmlspecialchars($spp['nomor_spp'] ?? '-') . '</strong></td>
                    <td width="15%">Status</td>
                    <td width="2%">:</td>
                    <td width="33%"><strong>' . htmlspecialchars($spp['status'] ?? '-') . '</strong></td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td>' . (isset($spp['tanggal_spp']) ? date('d F Y', strtotime($spp['tanggal_spp'])) : '-') . '</td>
                    <td>Total Nilai</td>
                    <td>:</td>
                    <td><strong>Rp ' . number_format($spp['jumlah'] ?? 0, 0, ',', '.') . '</strong></td>
                </tr>
            </table>
            
            <p><strong>Uraian / Keperluan:</strong></p>
            <div style="border:1px solid #ccc;padding:10px;background:#f9f9f9;margin-bottom:20px">
                ' . nl2br(htmlspecialchars($spp['uraian'] ?? '-')) . '
            </div>
            
            <p><strong>Rincian Belanja:</strong></p>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kode Rekening</th>
                        <th width="55%">Uraian</th>
                        <th width="25%">Nilai (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    ' . ($rincianRows ?: '<tr><td colspan="4" style="text-align:center">Tidak ada rincian</td></tr>') . '
                    <tr style="background:#f0f0f0;font-weight:bold">
                        <td colspan="3" style="text-align:right">JUMLAH</td>
                        <td style="text-align:right">Rp ' . number_format($total, 0, ',', '.') . '</td>
                    </tr>
                </tbody>
            </table>
            
            <div style="margin:20px 0;padding:10px;background:#f0f0f0;border:1px solid #ccc">
                <strong>Terbilang:</strong> <em>' . ucwords($terbilang) . ' Rupiah</em>
            </div>
            
            <div style="margin-top:50px">
                <table style="width:100%;border:none">
                    <tr>
                        <td style="width:33%;text-align:center;border:none">
                            <p>Yang Mengajukan,<br><strong>Bendahara Desa</strong></p>
                            <br><br><br><br>
                            <p><u><strong>' . ($desa['nama_bendahara'] ?? '.......................') . '</strong></u></p>
                        </td>
                        <td style="width:33%;text-align:center;border:none">
                            <p>Mengetahui,<br><strong>Sekretaris Desa</strong></p>
                            <br><br><br><br>
                            <p><u><strong>.................................</strong></u></p>
                        </td>
                        <td style="width:33%;text-align:center;border:none">
                            <p>Menyetujui,<br><strong>Kepala Desa</strong></p>
                            <br><br><br><br>
                            <p><u><strong>' . ($desa['nama_kepala_desa'] ?? '.......................') . '</strong></u></p>
                        </td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';
    }

    /**
     * Convert number to Indonesian words
     */
    private function terbilang($x): string
    {
        $angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        
        if ($x < 12) return " " . $angka[$x];
        elseif ($x < 20) return $this->terbilang($x - 10) . " belas";
        elseif ($x < 100) return $this->terbilang($x / 10) . " puluh" . $this->terbilang($x % 10);
        elseif ($x < 200) return "seratus" . $this->terbilang($x - 100);
        elseif ($x < 1000) return $this->terbilang($x / 100) . " ratus" . $this->terbilang($x % 100);
        elseif ($x < 2000) return "seribu" . $this->terbilang($x - 1000);
        elseif ($x < 1000000) return $this->terbilang($x / 1000) . " ribu" . $this->terbilang($x % 1000);
        elseif ($x < 1000000000) return $this->terbilang($x / 1000000) . " juta" . $this->terbilang($x % 1000000);
        elseif ($x < 1000000000000) return $this->terbilang($x / 1000000000) . " milyar" . $this->terbilang($x % 1000000000);
        else return $this->terbilang($x / 1000000000000) . " trilyun" . $this->terbilang($x % 1000000000000);
    }

    /**
     * LPJ PDF Template
     */
    private function getLpjTemplate(array $data): string
    {
        $desa = $data['desa'] ?? [];
        $semester = $data['semester'] ?? 1;
        $tahun = $data['tahun'] ?? date('Y');
        
        $pendapatanData = $data['pendapatan'] ?? [];
        $belanjaData = $data['belanja'] ?? [];
        
        // Build pendapatan rows
        $pendapatanRows = '';
        $totalAnggaranP = 0;
        $totalRealisasiP = 0;
        foreach ($pendapatanData as $idx => $item) {
            $totalAnggaranP += $item['anggaran'] ?? 0;
            $totalRealisasiP += $item['realisasi'] ?? 0;
            $persen = ($item['anggaran'] > 0) ? (($item['realisasi'] / $item['anggaran']) * 100) : 0;
            $pendapatanRows .= '<tr>
                <td>' . htmlspecialchars($item['kode_akun']) . '</td>
                <td>' . htmlspecialchars($item['nama_akun']) . '</td>
                <td style="text-align:right">Rp ' . number_format($item['anggaran'] ?? 0, 0, ',', '.') . '</td>
                <td style="text-align:right">Rp ' . number_format($item['realisasi'] ?? 0, 0, ',', '.') . '</td>
                <td style="text-align:center">' . number_format($persen, 2) . '%</td>
            </tr>';
        }
        
        // Build belanja rows
        $belanjaRows = '';
        $totalAnggaranB = 0;
        $totalRealisasiB = 0;
        foreach ($belanjaData as $idx => $item) {
            $totalAnggaranB += $item['anggaran'] ?? 0;
            $totalRealisasiB += $item['realisasi'] ?? 0;
            $persen = ($item['anggaran'] > 0) ? (($item['realisasi'] / $item['anggaran']) * 100) : 0;
            $belanjaRows .= '<tr>
                <td>' . htmlspecialchars($item['kode_akun']) . '</td>
                <td>' . htmlspecialchars($item['nama_akun']) . '</td>
                <td style="text-align:right">Rp ' . number_format($item['anggaran'] ?? 0, 0, ',', '.') . '</td>
                <td style="text-align:right">Rp ' . number_format($item['realisasi'] ?? 0, 0, ',', '.') . '</td>
                <td style="text-align:center">' . number_format($persen, 2) . '%</td>
            </tr>';
        }
        
        $surplusDefisitAnggaran = $totalAnggaranP - $totalAnggaranB;
        $surplusDefisitRealisasi = $totalRealisasiP - $totalRealisasiB;
        
        $semesterText = $semester == 1 ? 'Semester I (Januari - Juni)' : 'Semester II (Juli - Desember)';

        return '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; font-size: 9pt; }
                h2, h3 { margin: 5px 0; text-align: center; }
                table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                th, td { border: 1px solid #000; padding: 4px; font-size: 8pt; }
                th { background-color: #f0f0f0; }
                .section-header { background-color: #d0d0d0; font-weight: bold; }
                .subtotal { background-color: #e8e8e8; font-weight: bold; }
            </style>
        </head>
        <body>
            <div style="text-align:center;margin-bottom:20px">
                <h2>PEMERINTAH DESA ' . strtoupper($desa['nama_desa'] ?? 'NAMA DESA') . '</h2>
                <h3>LAPORAN PERTANGGUNGJAWABAN REALISASI PELAKSANAAN APBDes</h3>
                <p>' . $semesterText . ' Tahun Anggaran ' . $tahun . '</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="12%">Kode Rekening</th>
                        <th width="40%">Uraian</th>
                        <th width="18%">Anggaran (Rp)</th>
                        <th width="18%">Realisasi (Rp)</th>
                        <th width="12%">%</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- PENDAPATAN -->
                    <tr class="section-header">
                        <td colspan="5">A. PENDAPATAN</td>
                    </tr>
                    ' . ($pendapatanRows ?: '<tr><td colspan="5" style="text-align:center">Tidak ada data</td></tr>') . '
                    <tr class="subtotal">
                        <td colspan="2" style="text-align:right">JUMLAH PENDAPATAN</td>
                        <td style="text-align:right">Rp ' . number_format($totalAnggaranP, 0, ',', '.') . '</td>
                        <td style="text-align:right">Rp ' . number_format($totalRealisasiP, 0, ',', '.') . '</td>
                        <td style="text-align:center">' . ($totalAnggaranP > 0 ? number_format(($totalRealisasiP / $totalAnggaranP) * 100, 2) : 0) . '%</td>
                    </tr>
                    
                    <!-- BELANJA -->
                    <tr class="section-header">
                        <td colspan="5">B. BELANJA</td>
                    </tr>
                    ' . ($belanjaRows ?: '<tr><td colspan="5" style="text-align:center">Tidak ada data</td></tr>') . '
                    <tr class="subtotal">
                        <td colspan="2" style="text-align:right">JUMLAH BELANJA</td>
                        <td style="text-align:right">Rp ' . number_format($totalAnggaranB, 0, ',', '.') . '</td>
                        <td style="text-align:right">Rp ' . number_format($totalRealisasiB, 0, ',', '.') . '</td>
                        <td style="text-align:center">' . ($totalAnggaranB > 0 ? number_format(($totalRealisasiB / $totalAnggaranB) * 100, 2) : 0) . '%</td>
                    </tr>
                    
                    <!-- SURPLUS/DEFISIT -->
                    <tr style="background:#d0d0f0;font-weight:bold">
                        <td colspan="2" style="text-align:right">SURPLUS / (DEFISIT)</td>
                        <td style="text-align:right">Rp ' . number_format($surplusDefisitAnggaran, 0, ',', '.') . '</td>
                        <td style="text-align:right">Rp ' . number_format($surplusDefisitRealisasi, 0, ',', '.') . '</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            
            <div style="margin-top:40px">
                <p style="margin-bottom:20px"><strong>Keterangan:</strong></p>
                <p>Demikian laporan pertanggungjawaban realisasi pelaksanaan APBDes ' . $semesterText . ' Tahun Anggaran ' . $tahun . ' yang kami sampaikan.</p>
            </div>
            
            <div style="margin-top:50px">
                <table style="width:100%;border:none">
                    <tr>
                        <td style="width:50%;text-align:center;border:none">
                            <p>Mengetahui,<br><strong>Kepala Desa</strong></p>
                            <br><br><br><br>
                            <p><u><strong>' . ($desa['nama_kepala_desa'] ?? '.......................') . '</strong></u></p>
                        </td>
                        <td style="width:50%;text-align:center;border:none">
                            <p>' . ($desa['nama_desa'] ?? 'Nama Desa') . ', ' . date('d F Y') . '<br><strong>Bendahara Desa</strong></p>
                            <br><br><br><br>
                            <p><u><strong>' . ($desa['nama_bendahara'] ?? '.......................') . '</strong></u></p>
                        </td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';
    }

    /**
     * Kuitansi PDF Template
     */
    private function getKuitansiTemplate(array $data): string
    {
        $desa = $data['desa'] ?? [];
        $nomor = $data['nomor'] ?? '-';
        $tanggal = $data['tanggal'] ?? date('d F Y');
        $penerima = $data['penerima'] ?? '-';
        $jumlah = $data['jumlah'] ?? 0;
        $uraian = $data['uraian'] ?? '-';
        $terbilang = ucwords(trim($this->terbilang($jumlah)));

        return '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; font-size: 11pt; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
                .title { font-size: 16pt; font-weight: bold; margin: 10px 0; }
                .nomor { font-size: 10pt; }
                table.info { width: 100%; margin: 15px 0; }
                table.info td { padding: 5px 0; vertical-align: top; }
                table.info td.label { width: 120px; }
                .terbilang { background: #f5f5f5; padding: 10px; border: 1px dashed #ccc; margin: 15px 0; font-style: italic; }
                .amount { font-size: 14pt; font-weight: bold; text-align: center; margin: 20px 0; padding: 15px; background: #e8f5e9; border: 2px solid #4caf50; }
                .signatures { margin-top: 40px; }
                .sig-box { width: 45%; display: inline-block; text-align: center; vertical-align: top; }
                .stamp-area { border: 1px dashed #ccc; height: 60px; margin: 10px auto; width: 60px; font-size: 8pt; color: #999; display: flex; align-items: center; justify-content: center; }
            </style>
        </head>
        <body>
            <div class="header">
                <div style="font-size:12pt">PEMERINTAH DESA ' . strtoupper($desa['nama_desa'] ?? 'NAMA DESA') . '</div>
                <div class="title">KUITANSI</div>
                <div class="nomor">Nomor: ' . htmlspecialchars($nomor) . '</div>
            </div>
            
            <table class="info">
                <tr>
                    <td class="label">Sudah terima dari</td>
                    <td>: <strong>Bendahara Desa ' . ($desa['nama_desa'] ?? 'Nama Desa') . '</strong></td>
                </tr>
                <tr>
                    <td class="label">Uang Sebesar</td>
                    <td>:</td>
                </tr>
            </table>
            
            <div class="terbilang">
                <em>' . $terbilang . ' Rupiah</em>
            </div>
            
            <div class="amount">
                Rp ' . number_format($jumlah, 0, ',', '.') . ',-
            </div>
            
            <table class="info">
                <tr>
                    <td class="label">Untuk Pembayaran</td>
                    <td>: ' . nl2br(htmlspecialchars($uraian)) . '</td>
                </tr>
            </table>
            
            <div class="signatures">
                <div class="sig-box">
                    <p>Mengetahui,<br><strong>Kepala Desa</strong></p>
                    <br><br><br>
                    <p><u><strong>' . ($desa['nama_kepala_desa'] ?? '.....................') . '</strong></u></p>
                </div>
                <div class="sig-box" style="float:right">
                    <p>' . ($desa['nama_desa'] ?? 'Desa') . ', ' . $tanggal . '<br><strong>Yang Menerima</strong></p>
                    <div class="stamp-area">Materai</div>
                    <p><u><strong>' . htmlspecialchars($penerima) . '</strong></u></p>
                </div>
            </div>
        </body>
        </html>';
    }
}

<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ExcelExport
{
    protected $spreadsheet;
    protected $sheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }

    /**
     * Download Excel file
     */
    protected function download(string $filename): void
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($this->spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Set header style
     */
    protected function setHeaderStyle(string $range): void
    {
        $this->sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '667eea'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
    }

    /**
     * Set data style with borders
     */
    protected function setDataStyle(string $range): void
    {
        $this->sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    /**
     * Set title style
     */
    protected function setTitleStyle(string $cell): void
    {
        $this->sheet->getStyle($cell)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }

    /**
     * Generate BKU Excel Report
     */
    public function generateBkuReport(array $data): void
    {
        $desa = $data['desa'] ?? [];
        $transactions = $data['transactions'] ?? [];
        
        // Title
        $this->sheet->setCellValue('A1', 'PEMERINTAH DESA ' . strtoupper($desa['nama_desa'] ?? 'NAMA DESA'));
        $this->sheet->mergeCells('A1:G1');
        $this->setTitleStyle('A1');
        
        $this->sheet->setCellValue('A2', 'BUKU KAS UMUM');
        $this->sheet->mergeCells('A2:G2');
        $this->setTitleStyle('A2');
        
        $this->sheet->setCellValue('A3', 'Periode: ' . ($data['bulanNama'] ?? '-') . ' ' . ($data['tahun'] ?? date('Y')));
        $this->sheet->mergeCells('A3:G3');
        $this->sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Summary
        $this->sheet->setCellValue('A5', 'Saldo Awal:');
        $this->sheet->setCellValue('B5', $data['saldoAwal'] ?? 0);
        $this->sheet->setCellValue('C5', 'Penerimaan:');
        $this->sheet->setCellValue('D5', $data['totalDebet'] ?? 0);
        $this->sheet->setCellValue('E5', 'Pengeluaran:');
        $this->sheet->setCellValue('F5', $data['totalKredit'] ?? 0);
        
        // Format as currency
        $this->sheet->getStyle('B5')->getNumberFormat()->setFormatCode('#,##0');
        $this->sheet->getStyle('D5')->getNumberFormat()->setFormatCode('#,##0');
        $this->sheet->getStyle('F5')->getNumberFormat()->setFormatCode('#,##0');
        
        // Headers
        $headers = ['No', 'Tanggal', 'No. Bukti', 'Uraian', 'Penerimaan', 'Pengeluaran', 'Saldo'];
        $col = 'A';
        foreach ($headers as $header) {
            $this->sheet->setCellValue($col . '7', $header);
            $col++;
        }
        $this->setHeaderStyle('A7:G7');
        
        // Data
        $row = 8;
        foreach ($transactions as $idx => $trans) {
            $this->sheet->setCellValue('A' . $row, $idx + 1);
            $this->sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($trans['tanggal'])));
            $this->sheet->setCellValue('C' . $row, $trans['no_bukti'] ?? '-');
            $this->sheet->setCellValue('D' . $row, $trans['uraian']);
            $this->sheet->setCellValue('E' . $row, $trans['debet'] ?: '');
            $this->sheet->setCellValue('F' . $row, $trans['kredit'] ?: '');
            $this->sheet->setCellValue('G' . $row, $trans['saldo']);
            $row++;
        }
        
        // Total row
        $this->sheet->setCellValue('A' . $row, 'TOTAL');
        $this->sheet->mergeCells('A' . $row . ':D' . $row);
        $this->sheet->setCellValue('E' . $row, $data['totalDebet'] ?? 0);
        $this->sheet->setCellValue('F' . $row, $data['totalKredit'] ?? 0);
        $this->sheet->setCellValue('G' . $row, $data['saldoAkhir'] ?? 0);
        $this->sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
        
        // Style data range
        $this->setDataStyle('A7:G' . $row);
        
        // Format currency columns
        $this->sheet->getStyle('E8:G' . $row)->getNumberFormat()->setFormatCode('#,##0');
        
        // Auto-width columns
        foreach (range('A', 'G') as $col) {
            $this->sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $this->download('BKU_' . ($data['bulanNama'] ?? 'Report') . '_' . ($data['tahun'] ?? date('Y')));
    }

    /**
     * Generate APBDes Excel Report
     */
    public function generateApbdesReport(array $data): void
    {
        $desa = $data['desa'] ?? [];
        $pendapatan = $data['pendapatan'] ?? [];
        $belanja = $data['belanja'] ?? [];
        
        // Title
        $this->sheet->setCellValue('A1', 'PEMERINTAH DESA ' . strtoupper($desa['nama_desa'] ?? 'NAMA DESA'));
        $this->sheet->mergeCells('A1:E1');
        $this->setTitleStyle('A1');
        
        $this->sheet->setCellValue('A2', 'ANGGARAN PENDAPATAN DAN BELANJA DESA (APBDes)');
        $this->sheet->mergeCells('A2:E2');
        $this->setTitleStyle('A2');
        
        $this->sheet->setCellValue('A3', 'Tahun Anggaran: ' . ($data['tahun'] ?? date('Y')));
        $this->sheet->mergeCells('A3:E3');
        $this->sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // PENDAPATAN Section
        $this->sheet->setCellValue('A5', 'A. PENDAPATAN');
        $this->sheet->getStyle('A5')->getFont()->setBold(true);
        
        $headers = ['No', 'Kode Rekening', 'Uraian', 'Sumber Dana', 'Anggaran (Rp)'];
        $col = 'A';
        foreach ($headers as $header) {
            $this->sheet->setCellValue($col . '6', $header);
            $col++;
        }
        $this->setHeaderStyle('A6:E6');
        
        $row = 7;
        foreach ($pendapatan as $idx => $item) {
            $this->sheet->setCellValue('A' . $row, $idx + 1);
            $this->sheet->setCellValue('B' . $row, $item['kode_akun']);
            $this->sheet->setCellValue('C' . $row, $item['uraian'] ?? $item['nama_akun']);
            $this->sheet->setCellValue('D' . $row, $item['sumber_dana'] ?? '-');
            $this->sheet->setCellValue('E' . $row, $item['anggaran'] ?? 0);
            $row++;
        }
        
        $this->sheet->setCellValue('A' . $row, 'JUMLAH PENDAPATAN');
        $this->sheet->mergeCells('A' . $row . ':D' . $row);
        $this->sheet->setCellValue('E' . $row, $data['totalPendapatan'] ?? 0);
        $this->sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
        $this->sheet->getStyle('A' . $row . ':E' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('90EE90');
        $this->setDataStyle('A6:E' . $row);
        
        $row += 2;
        $belanjaStart = $row;
        
        // BELANJA Section
        $this->sheet->setCellValue('A' . $row, 'B. BELANJA');
        $this->sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $col = 'A';
        foreach ($headers as $header) {
            $this->sheet->setCellValue($col . $row, $header);
            $col++;
        }
        $this->setHeaderStyle('A' . $row . ':E' . $row);
        $row++;
        
        $belanjaDataStart = $row;
        foreach ($belanja as $idx => $item) {
            $this->sheet->setCellValue('A' . $row, $idx + 1);
            $this->sheet->setCellValue('B' . $row, $item['kode_akun']);
            $this->sheet->setCellValue('C' . $row, $item['uraian'] ?? $item['nama_akun']);
            $this->sheet->setCellValue('D' . $row, $item['sumber_dana'] ?? '-');
            $this->sheet->setCellValue('E' . $row, $item['anggaran'] ?? 0);
            $row++;
        }
        
        $this->sheet->setCellValue('A' . $row, 'JUMLAH BELANJA');
        $this->sheet->mergeCells('A' . $row . ':D' . $row);
        $this->sheet->setCellValue('E' . $row, $data['totalBelanja'] ?? 0);
        $this->sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
        $this->sheet->getStyle('A' . $row . ':E' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFA07A');
        $this->setDataStyle('A' . ($belanjaStart + 1) . ':E' . $row);
        
        // Surplus/Defisit
        $row += 2;
        $this->sheet->setCellValue('A' . $row, 'SURPLUS / (DEFISIT)');
        $this->sheet->mergeCells('A' . $row . ':D' . $row);
        $this->sheet->setCellValue('E' . $row, ($data['totalPendapatan'] ?? 0) - ($data['totalBelanja'] ?? 0));
        $this->sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
        $this->sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
        
        // Format currency
        $this->sheet->getStyle('E7:E' . $row)->getNumberFormat()->setFormatCode('#,##0');
        
        // Auto-width columns
        foreach (range('A', 'E') as $col) {
            $this->sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $this->download('APBDes_' . ($data['tahun'] ?? date('Y')));
    }

    /**
     * Generate LRA Excel Report
     */
    public function generateLraReport(array $data): void
    {
        $desa = $data['desa'] ?? [];
        $dataLra = $data['data_lra'] ?? [];
        
        // Title
        $this->sheet->setCellValue('A1', 'PEMERINTAH DESA ' . strtoupper($desa['nama_desa'] ?? 'NAMA DESA'));
        $this->sheet->mergeCells('A1:G1');
        $this->setTitleStyle('A1');
        
        $this->sheet->setCellValue('A2', 'LAPORAN REALISASI ANGGARAN (LRA)');
        $this->sheet->mergeCells('A2:G2');
        $this->setTitleStyle('A2');
        
        $this->sheet->setCellValue('A3', 'Tahun Anggaran: ' . ($data['tahun'] ?? date('Y')));
        $this->sheet->mergeCells('A3:G3');
        $this->sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Headers
        $headers = ['No', 'Kode Rekening', 'Uraian', 'Anggaran', 'Realisasi', '%', 'Sisa'];
        $col = 'A';
        foreach ($headers as $header) {
            $this->sheet->setCellValue($col . '5', $header);
            $col++;
        }
        $this->setHeaderStyle('A5:G5');
        
        // Data
        $row = 6;
        $totalAnggaran = 0;
        $totalRealisasi = 0;
        
        foreach ($dataLra as $idx => $item) {
            $totalAnggaran += $item['anggaran'] ?? 0;
            $totalRealisasi += $item['realisasi'] ?? 0;
            
            $this->sheet->setCellValue('A' . $row, $idx + 1);
            $this->sheet->setCellValue('B' . $row, $item['kode_akun']);
            $this->sheet->setCellValue('C' . $row, $item['uraian'] ?? $item['nama_akun']);
            $this->sheet->setCellValue('D' . $row, $item['anggaran'] ?? 0);
            $this->sheet->setCellValue('E' . $row, $item['realisasi'] ?? 0);
            $this->sheet->setCellValue('F' . $row, ($item['persentase'] ?? 0) / 100);
            $this->sheet->setCellValue('G' . $row, $item['sisa'] ?? 0);
            $row++;
        }
        
        // Total row
        $this->sheet->setCellValue('A' . $row, 'TOTAL');
        $this->sheet->mergeCells('A' . $row . ':C' . $row);
        $this->sheet->setCellValue('D' . $row, $totalAnggaran);
        $this->sheet->setCellValue('E' . $row, $totalRealisasi);
        $this->sheet->setCellValue('F' . $row, $totalAnggaran > 0 ? $totalRealisasi / $totalAnggaran : 0);
        $this->sheet->setCellValue('G' . $row, $totalAnggaran - $totalRealisasi);
        $this->sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
        
        $this->setDataStyle('A5:G' . $row);
        
        // Format
        $this->sheet->getStyle('D6:E' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $this->sheet->getStyle('G6:G' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $this->sheet->getStyle('F6:F' . $row)->getNumberFormat()->setFormatCode('0.00%');
        
        // Auto-width columns
        foreach (range('A', 'G') as $col) {
            $this->sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $this->download('LRA_' . ($data['tahun'] ?? date('Y')));
    }

    /**
     * Generate Pajak Excel Report
     */
    public function generatePajakReport(array $data): void
    {
        $desa = $data['desa'] ?? [];
        $pajak = $data['pajak'] ?? [];
        
        // Title
        $this->sheet->setCellValue('A1', 'PEMERINTAH DESA ' . strtoupper($desa['nama_desa'] ?? 'NAMA DESA'));
        $this->sheet->mergeCells('A1:G1');
        $this->setTitleStyle('A1');
        
        $this->sheet->setCellValue('A2', 'LAPORAN REKAPITULASI PAJAK');
        $this->sheet->mergeCells('A2:G2');
        $this->setTitleStyle('A2');
        
        $this->sheet->setCellValue('A3', 'Tahun: ' . ($data['tahun'] ?? date('Y')));
        $this->sheet->mergeCells('A3:G3');
        $this->sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Summary
        $this->sheet->setCellValue('A5', 'Total PPN:');
        $this->sheet->setCellValue('B5', $data['totalPPN'] ?? 0);
        $this->sheet->setCellValue('C5', 'Total PPh:');
        $this->sheet->setCellValue('D5', $data['totalPPh'] ?? 0);
        $this->sheet->setCellValue('E5', 'Total Pajak:');
        $this->sheet->setCellValue('F5', $data['totalPajak'] ?? 0);
        $this->sheet->getStyle('B5')->getNumberFormat()->setFormatCode('#,##0');
        $this->sheet->getStyle('D5')->getNumberFormat()->setFormatCode('#,##0');
        $this->sheet->getStyle('F5')->getNumberFormat()->setFormatCode('#,##0');
        $this->sheet->getStyle('A5:F5')->getFont()->setBold(true);
        
        // Headers
        $headers = ['No', 'Tanggal', 'No. Bukti', 'Uraian', 'Jenis', 'Nilai', 'Status'];
        $col = 'A';
        foreach ($headers as $header) {
            $this->sheet->setCellValue($col . '7', $header);
            $col++;
        }
        $this->setHeaderStyle('A7:G7');
        
        // Data
        $row = 8;
        foreach ($pajak as $idx => $item) {
            $this->sheet->setCellValue('A' . $row, $idx + 1);
            $this->sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($item['tanggal'])));
            $this->sheet->setCellValue('C' . $row, $item['nomor_bukti'] ?? '-');
            $this->sheet->setCellValue('D' . $row, $item['uraian'] ?? '-');
            $this->sheet->setCellValue('E' . $row, $item['jenis_pajak'] ?? '-');
            $this->sheet->setCellValue('F' . $row, $item['nilai'] ?? 0);
            $this->sheet->setCellValue('G' . $row, $item['status_pembayaran'] === 'Sudah' ? 'Lunas' : 'Belum');
            $row++;
        }
        
        $this->setDataStyle('A7:G' . ($row - 1));
        $this->sheet->getStyle('F8:F' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        
        // Auto-width columns
        foreach (range('A', 'G') as $col) {
            $this->sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $this->download('Pajak_' . ($data['tahun'] ?? date('Y')));
    }
}

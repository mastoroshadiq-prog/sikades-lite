<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Header with Print Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-receipt me-2 text-warning"></i>Laporan Pajak
            </h2>
            <p class="text-muted mb-0">Rekapitulasi PPN dan PPh - Tahun <?= $tahun ?></p>
        </div>
        <div class="btn-group">
            <a href="<?= base_url('report/pajak?tahun=' . $tahun . '&format=pdf') ?>" 
               class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <a href="<?= base_url('report/pajak?tahun=' . $tahun . '&format=excel') ?>" 
               class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <!-- Report Content -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5" id="printArea">
            <!-- Header Desa -->
            <div class="text-center mb-4">
                <h4 class="mb-1">PEMERINTAH DESA <?= strtoupper($desa['nama_desa'] ?? 'NAMA DESA') ?></h4>
                <h5 class="mb-3">LAPORAN REKAPITULASI PAJAK</h5>
                <p class="mb-0">Tahun: <strong><?= $tahun ?></strong></p>
            </div>

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <small class="text-muted d-block">Total PPN</small>
                            <h6 class="mb-0 text-primary">Rp <?= number_format($totalPPN, 0, ',', '.') ?></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <small class="text-muted d-block">Total PPh</small>
                            <h6 class="mb-0 text-info">Rp <?= number_format($totalPPh, 0, ',', '.') ?></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <small class="text-muted d-block">Total Pajak</small>
                            <h6 class="mb-0 text-success">Rp <?= number_format($totalPajak, 0, ',', '.') ?></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="card-body text-center text-white">
                            <small class="d-block">Jumlah Transaksi</small>
                            <h6 class="mb-0"><?= count($pajak) ?> Transaksi</h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PPN Section -->
            <div class="mb-5">
                <h5 class="mb-3 pb-2 border-bottom">
                    <i class="fas fa-file-invoice text-primary me-2"></i>PPN (Pajak Pertambahan Nilai)
                </h5>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-primary">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="12%">Tanggal</th>
                                <th width="15%">No. Bukti</th>
                                <th width="25%">Uraian</th>
                                <th width="15%">NPWP</th>
                                <th width="13%" class="text-end">Nilai (Rp)</th>
                                <th width="15%" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ppn)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Tidak ada data PPN
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ppn as $idx => $item): ?>
                                    <tr>
                                        <td class="text-center"><?= $idx + 1 ?></td>
                                        <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                                        <td><?= esc($item['nomor_bukti']) ?></td>
                                        <td><?= esc($item['uraian']) ?></td>
                                        <td><small><?= esc($item['npwp'] ?? '-') ?></small></td>
                                        <td class="text-end fw-bold">
                                            Rp <?= number_format($item['nilai'], 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($item['status_pembayaran'] === 'Sudah'): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Sudah Bayar
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock"></i> Belum Bayar
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <!-- Subtotal PPN -->
                                <tr class="table-light fw-bold">
                                    <td colspan="5" class="text-end">TOTAL PPN</td>
                                    <td class="text-end">Rp <?= number_format($totalPPN, 0, ',', '.') ?></td>
                                    <td></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PPh Section -->
            <div class="mb-5">
                <h5 class="mb-3 pb-2 border-bottom">
                    <i class="fas fa-file-invoice-dollar text-info me-2"></i>PPh (Pajak Penghasilan)
                </h5>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-info">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="12%">Tanggal</th>
                                <th width="15%">No. Bukti</th>
                                <th width="25%">Uraian</th>
                                <th width="15%">NPWP</th>
                                <th width="13%" class="text-end">Nilai (Rp)</th>
                                <th width="15%" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pph)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Tidak ada data PPh
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pph as $idx => $item): ?>
                                    <tr>
                                        <td class="text-center"><?= $idx + 1 ?></td>
                                        <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                                        <td><?= esc($item['nomor_bukti']) ?></td>
                                        <td><?= esc($item['uraian']) ?></td>
                                        <td><small><?= esc($item['npwp'] ?? '-') ?></small></td>
                                        <td class="text-end fw-bold">
                                            Rp <?= number_format($item['nilai'], 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($item['status_pembayaran'] === 'Sudah'): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Sudah Bayar
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock"></i> Belum Bayar
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <!-- Subtotal PPh -->
                                <tr class="table-light fw-bold">
                                    <td colspan="5" class="text-end">TOTAL PPh</td>
                                    <td class="text-end">Rp <?= number_format($totalPPh, 0, ',', '.') ?></td>
                                    <td></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Grand Total -->
            <div class="table-responsive mb-5">
                <table class="table table-bordered">
                    <tbody>
                        <tr class="table-success fw-bold">
                            <td width="85%" class="text-end fs-5">TOTAL PAJAK KESELURUHAN</td>
                            <td width="15%" class="text-end fs-5">
                                Rp <?= number_format($totalPajak, 0, ',', '.') ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Signatures -->
            <div class="row mt-5">
                <div class="col-6">
                    <div class="text-center">
                        <p class="mb-5">Mengetahui,<br><strong>Kepala Desa</strong></p>
                        <div style="min-height: 80px;"></div>
                        <p class="mb-0"><u><strong><?= $desa['nama_kepala_desa'] ?? '.......................' ?></strong></u></p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center">
                        <p class="mb-5"><?= $desa['nama_desa'] ?? 'Nama Desa' ?>, <?= date('d F Y') ?><br><strong>Bendahara Desa</strong></p>
                        <div style="min-height: 80px;"></div>
                        <p class="mb-0"><u><strong><?= $desa['nama_bendahara'] ?? '.......................' ?></strong></u></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="text-center mt-4 no-print">
        <a href="<?= base_url('report') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Laporan
        </a>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    body * {
        visibility: hidden;
    }
    
    #printArea, #printArea * {
        visibility: visible;
    }
    
    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    
    .no-print {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    table {
        page-break-inside: auto;
    }
    
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
}
</style>

<?= view('layout/footer') ?>


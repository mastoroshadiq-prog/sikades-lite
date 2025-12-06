<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Header with Print Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-book me-2 text-primary"></i>Buku Kas Umum (BKU)
            </h2>
            <p class="text-muted mb-0">Periode: <?= $bulanNama ?> <?= $tahun ?></p>
        </div>
        <div class="btn-group">
            <a href="<?= base_url('report/bku?bulan=' . $bulan . '&tahun=' . $tahun . '&format=pdf') ?>" 
               class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <a href="<?= base_url('report/bku?bulan=' . $bulan . '&tahun=' . $tahun . '&format=excel') ?>" 
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
                <h5 class="mb-3">BUKU KAS UMUM</h5>
                <p class="mb-0">Bulan: <strong><?= $bulanNama ?> <?= $tahun ?></strong></p>
            </div>

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <small class="text-muted d-block">Saldo Awal</small>
                            <h6 class="mb-0 text-primary">Rp <?= number_format($saldoAwal, 0, ',', '.') ?></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <small class="text-muted d-block">Total Penerimaan</small>
                            <h6 class="mb-0 text-success">Rp <?= number_format($totalDebet, 0, ',', '.') ?></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <small class="text-muted d-block">Total Pengeluaran</small>
                            <h6 class="mb-0 text-danger">Rp <?= number_format($totalKredit, 0, ',', '.') ?></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary text-white border-0">
                        <div class="card-body text-center">
                            <small class="d-block">Saldo Akhir</small>
                            <h6 class="mb-0">Rp <?= number_format($saldoAkhir, 0, ',', '.') ?></h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="10%">Tanggal</th>
                            <th width="12%">No. Bukti</th>
                            <th width="30%">Uraian</th>
                            <th width="13%" class="text-end">Penerimaan (Rp)</th>
                            <th width="13%" class="text-end">Pengeluaran (Rp)</th>
                            <th width="17%" class="text-end">Saldo (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Saldo Awal -->
                        <tr class="table-secondary">
                            <td colspan="6" class="text-center"><strong>Saldo Awal</strong></td>
                            <td class="text-end"><strong>Rp <?= number_format($saldoAwal, 0, ',', '.') ?></strong></td>
                        </tr>

                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Tidak ada transaksi pada periode ini
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $idx => $trans): ?>
                                <tr>
                                    <td class="text-center"><?= $idx + 1 ?></td>
                                    <td><?= date('d/m/Y', strtotime($trans['tanggal'])) ?></td>
                                    <td><?= esc($trans['no_bukti']) ?></td>
                                    <td>
                                        <?= esc($trans['uraian']) ?>
                                        <?php if (!empty($trans['jenis_transaksi'])): ?>
                                            <br><small class="text-muted">
                                                <span class="badge badge-sm bg-<?= $trans['jenis_transaksi'] === 'Pendapatan' ? 'success' : ($trans['jenis_transaksi'] === 'Belanja' ? 'danger' : 'warning') ?>">
                                                    <?= $trans['jenis_transaksi'] ?>
                                                </span>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?= $trans['debet'] > 0 ? 'Rp ' . number_format($trans['debet'], 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="text-end">
                                        <?= $trans['kredit'] > 0 ? 'Rp ' . number_format($trans['kredit'], 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="text-end fw-bold">
                                        Rp <?= number_format($trans['saldo'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <!-- Total Row -->
                            <tr class="table-secondary fw-bold">
                                <td colspan="4" class="text-end">JUMLAH</td>
                                <td class="text-end">Rp <?= number_format($totalDebet, 0, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format($totalKredit, 0, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format($saldoAkhir, 0, ',', '.') ?></td>
                            </tr>
                        <?php endif; ?>
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
                        <p class="mb-5"><?= $desa['nama_desa'] ?? 'Nama Desa' ?>, <?= date('d') . ' ' . $bulanNama . ' ' . $tahun ?><br><strong>Bendahara Desa</strong></p>
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

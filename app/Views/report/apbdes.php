<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Header with Print Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-chart-pie me-2 text-success"></i>Laporan APBDes
            </h2>
            <p class="text-muted mb-0">Anggaran Pendapatan dan Belanja Desa - Tahun <?= $tahun ?></p>
        </div>
        <div class="btn-group">
            <a href="<?= base_url('report/apbdes?tahun=' . $tahun . '&format=pdf') ?>" 
               class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <a href="<?= base_url('report/apbdes?tahun=' . $tahun . '&format=excel') ?>" 
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
                <h5 class="mb-3">ANGGARAN PENDAPATAN DAN BELANJA DESA (APBDes)</h5>
                <p class="mb-0">Tahun Anggaran: <strong><?= $tahun ?></strong></p>
            </div>

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <small class="text-muted d-block">Total Pendapatan</small>
                            <h6 class="mb-0 text-success">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <small class="text-muted d-block">Total Belanja</small>
                            <h6 class="mb-0 text-danger">Rp <?= number_format($totalBelanja, 0, ',', '.') ?></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body text-center text-white">
                            <small class="d-block">Surplus/(Defisit)</small>
                            <h6 class="mb-0">Rp <?= number_format($totalPendapatan - $totalBelanja, 0, ',', '.') ?></h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PENDAPATAN Section -->
            <div class="mb-5">
                <h5 class="mb-3 pb-2 border-bottom">
                    <i class="fas fa-arrow-down text-success me-2"></i>PENDAPATAN
                </h5>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-success">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Kode Rekening</th>
                                <th width="45%">Uraian</th>
                                <th width="15%">Sumber Dana</th>
                                <th width="20%" class="text-end">Anggaran (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pendapatan)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Tidak ada data pendapatan
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pendapatan as $idx => $item): ?>
                                    <tr>
                                        <td class="text-center"><?= $idx + 1 ?></td>
                                        <td><code><?= esc($item['kode_akun']) ?></code></td>
                                        <td>
                                            <?php 
                                            $indent = str_repeat('&nbsp;&nbsp;&nbsp;', ($item['level'] ?? 1) - 1);
                                            echo $indent . esc($item['uraian'] ?? $item['nama_akun']); 
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= esc($item['sumber_dana']) ?></span>
                                        </td>
                                        <td class="text-end fw-bold">
                                            Rp <?= number_format($item['anggaran'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <!-- Subtotal Pendapatan -->
                                <tr class="table-success fw-bold">
                                    <td colspan="4" class="text-end">JUMLAH PENDAPATAN</td>
                                    <td class="text-end">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- BELANJA Section -->
            <div class="mb-5">
                <h5 class="mb-3 pb-2 border-bottom">
                    <i class="fas fa-arrow-up text-danger me-2"></i>BELANJA
                </h5>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-danger">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Kode Rekening</th>
                                <th width="45%">Uraian</th>
                                <th width="15%">Sumber Dana</th>
                                <th width="20%" class="text-end">Anggaran (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($belanja)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Tidak ada data belanja
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($belanja as $idx => $item): ?>
                                    <tr>
                                        <td class="text-center"><?= $idx + 1 ?></td>
                                        <td><code><?= esc($item['kode_akun']) ?></code></td>
                                        <td>
                                            <?php 
                                            $indent = str_repeat('&nbsp;&nbsp;&nbsp;', ($item['level'] ?? 1) - 1);
                                            echo $indent . esc($item['uraian'] ?? $item['nama_akun']); 
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning"><?= esc($item['sumber_dana']) ?></span>
                                        </td>
                                        <td class="text-end fw-bold">
                                            Rp <?= number_format($item['anggaran'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <!-- Subtotal Belanja -->
                                <tr class="table-danger fw-bold">
                                    <td colspan="4" class="text-end">JUMLAH BELANJA</td>
                                    <td class="text-end">Rp <?= number_format($totalBelanja, 0, ',', '.') ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary Total -->
            <div class="table-responsive mb-5">
                <table class="table table-bordered">
                    <tbody>
                        <tr class="table-light">
                            <td width="80%" class="text-end fw-bold">SURPLUS / (DEFISIT)</td>
                            <td width="20%" class="text-end fw-bold">
                                Rp <?= number_format($totalPendapatan - $totalBelanja, 0, ',', '.') ?>
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


<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Header with Print Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-chart-line me-2 text-info"></i>Laporan Realisasi Anggaran (LRA)
            </h2>
            <p class="text-muted mb-0">Perbandingan Anggaran vs Realisasi - Tahun <?= $tahun ?></p>
        </div>
        <div class="btn-group">
            <a href="<?= base_url('report/lra?tahun=' . $tahun . '&format=pdf') ?>" 
               class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <a href="<?= base_url('report/lra?tahun=' . $tahun . '&format=excel') ?>" 
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
                <h5 class="mb-3">LAPORAN REALISASI ANGGARAN</h5>
                <p class="mb-0">Tahun Anggaran: <strong><?= $tahun ?></strong></p>
            </div>

            <!-- Main Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th width="5%" rowspan="2">No</th>
                            <th width="15%" rowspan="2">Kode Rekening</th>
                            <th width="30%" rowspan="2">Uraian</th>
                            <th width="15%" rowspan="2">Anggaran (Rp)</th>
                            <th width="15%" rowspan="2">Realisasi (Rp)</th>
                            <th width="10%" rowspan="2">%</th>
                            <th width="10%" rowspan="2">Sisa (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data_lra)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Tidak ada data realisasi anggaran
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $currentType = '';
                            $subtotalAnggaran = 0;
                            $subtotalRealisasi = 0;
                            $no = 1;
                            
                            foreach ($data_lra as $item):
                                $isPendapatan = strpos($item['kode_akun'], '4.') === 0;
                                $itemType = $isPendapatan ? 'Pendapatan' : 'Belanja';
                                
                                // Print section header if type changes
                                if ($currentType !== $itemType):
                                    // Print subtotal for previous section
                                    if ($currentType !== ''):
                            ?>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3" class="text-end">JUMLAH <?= strtoupper($currentType) ?></td>
                                    <td class="text-end">Rp <?= number_format($subtotalAnggaran, 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($subtotalRealisasi, 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <?= $subtotalAnggaran > 0 ? number_format(($subtotalRealisasi / $subtotalAnggaran) * 100, 2) : 0 ?>%
                                    </td>
                                    <td class="text-end">Rp <?= number_format($subtotalAnggaran - $subtotalRealisasi, 0, ',', '.') ?></td>
                                </tr>
                                <?php 
                                        $subtotalAnggaran = 0;
                                        $subtotalRealisasi = 0;
                                        $no = 1;
                                    endif;
                                    $currentType = $itemType;
                                ?>
                                <tr class="table-active">
                                    <td colspan="7" class="fw-bold">
                                        <i class="fas fa-<?= $isPendapatan ? 'arrow-down text-success' : 'arrow-up text-danger' ?> me-2"></i>
                                        <?= strtoupper($itemType) ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                
                                <!-- Data Row -->
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><code><?= esc($item['kode_akun']) ?></code></td>
                                    <td>
                                        <?php 
                                        $indent = str_repeat('&nbsp;&nbsp;&nbsp;', ($item['level'] ?? 1) - 1);
                                        echo $indent . esc($item['uraian'] ?? $item['nama_akun']); 
                                        ?>
                                    </td>
                                    <td class="text-end">Rp <?= number_format($item['anggaran'], 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($item['realisasi'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <?php 
                                        $persen = $item['persentase'];
                                        $colorClass = $persen < 50 ? 'text-danger' : ($persen < 80 ? 'text-warning' : 'text-success');
                                        ?>
                                        <span class="<?= $colorClass ?> fw-bold">
                                            <?= number_format($persen, 2) ?>%
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <?php $sisa = $item['sisa']; ?>
                                        <span class="<?= $sisa < 0 ? 'text-danger' : 'text-muted' ?>">
                                            Rp <?= number_format(abs($sisa), 0, ',', '.') ?>
                                            <?= $sisa < 0 ? ' (OVER)' : '' ?>
                                        </span>
                                    </td>
                                </tr>
                                
                                <?php 
                                $subtotalAnggaran += $item['anggaran'];
                                $subtotalRealisasi += $item['realisasi'];
                                ?>
                            <?php endforeach; ?>
                            
                            <!-- Last subtotal -->
                            <?php if ($currentType !== ''): ?>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3" class="text-end">JUMLAH <?= strtoupper($currentType) ?></td>
                                    <td class="text-end">Rp <?= number_format($subtotalAnggaran, 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($subtotalRealisasi, 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <?= $subtotalAnggaran > 0 ? number_format(($subtotalRealisasi / $subtotalAnggaran) * 100, 2) : 0 ?>%
                                    </td>
                                    <td class="text-end">Rp <?= number_format($subtotalAnggaran - $subtotalRealisasi, 0, ',', '.') ?></td>
                                </tr>
                            <?php endif; ?>
                            
                            <!-- Grand Total -->
                            <?php 
                            $grandTotalAnggaran = array_sum(array_column($data_lra, 'anggaran'));
                            $grandTotalRealisasi = array_sum(array_column($data_lra, 'realisasi'));
                            $grandTotalSisa = $grandTotalAnggaran - $grandTotalRealisasi;
                            ?>
                            <tr class="table-primary fw-bold">
                                <td colspan="3" class="text-end">TOTAL KESELURUHAN</td>
                                <td class="text-end">Rp <?= number_format($grandTotalAnggaran, 0, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format($grandTotalRealisasi, 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <?= $grandTotalAnggaran > 0 ? number_format(($grandTotalRealisasi / $grandTotalAnggaran) * 100, 2) : 0 ?>%
                                </td>
                                <td class="text-end">Rp <?= number_format($grandTotalSisa, 0, ',', '.') ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Performance Summary -->
            <?php if (!empty($data_lra)): ?>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h6 class="alert-heading mb-3">
                            <i class="fas fa-chart-bar me-2"></i>Ringkasan Kinerja
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Total Anggaran</small>
                                <strong>Rp <?= number_format($grandTotalAnggaran, 0, ',', '.') ?></strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Total Realisasi</small>
                                <strong>Rp <?= number_format($grandTotalRealisasi, 0, ',', '.') ?></strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Tingkat Penyerapan</small>
                                <strong class="<?= ($grandTotalRealisasi / $grandTotalAnggaran * 100) > 80 ? 'text-success' : 'text-warning' ?>">
                                    <?= number_format(($grandTotalRealisasi / $grandTotalAnggaran) * 100, 2) ?>%
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

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


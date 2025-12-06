<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Header with Print Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-invoice me-2 text-primary"></i>Surat Permintaan Pembayaran (SPP)
            </h2>
            <p class="text-muted mb-0">Nomor: <?= $spp['nomor_spp'] ?></p>
        </div>
        <div class="btn-group">
            <a href="<?= base_url('report/spp/' . $spp['id'] . '?format=pdf') ?>" 
               class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="<?= base_url('spp') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Report Content -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5" id="printArea">
            <!-- Header Desa -->
            <div class="text-center mb-4">
                <h4 class="mb-1">PEMERINTAH DESA <?= strtoupper($desa['nama_desa'] ?? 'NAMA DESA') ?></h4>
                <h5 class="mb-3">SURAT PERMINTAAN PEMBAYARAN (SPP)</h5>
                <p class="mb-0">Nomor: <strong><?= $spp['nomor_spp'] ?></strong></p>
            </div>

            <!-- SPP Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="35%">Nomor SPP</td>
                            <td width="5%">:</td>
                            <td><strong><?= esc($spp['nomor_spp']) ?></strong></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td><?= date('d F Y', strtotime($spp['tanggal_spp'])) ?></td>
                        </tr>
                        <tr>
                            <td>Tahun Anggaran</td>
                            <td>:</td>
                            <td><?= $spp['tahun'] ?? date('Y') ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="35%">Status</td>
                            <td width="5%">:</td>
                            <td>
                                <?php if ($spp['status'] === 'Draft'): ?>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-edit"></i> Draft
                                    </span>
                                <?php elseif ($spp['status'] === 'Verified'): ?>
                                    <span class="badge bg-primary">
                                        <i class="fas fa-check"></i> Verified
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-double"></i> Approved
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Total Nilai</td>
                            <td>:</td>
                            <td><strong class="text-success">Rp <?= number_format($spp['jumlah'], 0, ',', '.') ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Uraian -->
            <div class="mb-4">
                <h6 class="mb-2">Uraian / Keperluan:</h6>
                <div class="alert alert-light border">
                    <?= nl2br(esc($spp['uraian'])) ?>
                </div>
            </div>

            <!-- Rincian Table -->
            <h6 class="mb-3">Rincian Belanja:</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="15%">Kode Rekening</th>
                            <th width="55%">Uraian</th>
                            <th width="25%" class="text-end">Nilai (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rincian)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Tidak ada rincian
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $total = 0;
                            foreach ($rincian as $idx => $item): 
                                $total += $item['nilai_pencairan'];
                            ?>
                                <tr>
                                    <td class="text-center"><?= $idx + 1 ?></td>
                                    <td><code><?= esc($item['kode_akun']) ?></code></td>
                                    <td>
                                        <strong><?= esc($item['nama_akun']) ?></strong>
                                        <?php if (!empty($item['uraian'])): ?>
                                            <br><small class="text-muted"><?= esc($item['uraian']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">Rp <?= number_format($item['nilai_pencairan'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <!-- Total Row -->
                            <tr class="table-light fw-bold">
                                <td colspan="3" class="text-end">JUMLAH</td>
                                <td class="text-end fs-5">Rp <?= number_format($total, 0, ',', '.') ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Terbilang -->
            <div class="mb-4">
                <div class="alert alert-secondary border">
                    <strong>Terbilang:</strong> 
                    <em><?= ucwords(terbilang($spp['jumlah'])) ?> Rupiah</em>
                </div>
            </div>

            <!-- Approval Timeline -->
            <?php if ($spp['status'] !== 'Draft'): ?>
            <div class="mb-5">
                <h6 class="mb-3">Status Persetujuan:</h6>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-secondary"></div>
                        <div class="timeline-content">
                            <small class="text-muted">Dibuat oleh</small>
                            <p class="mb-0"><strong><?= $spp['created_by_name'] ?? 'Operator' ?></strong></p>
                            <small><?= date('d/m/Y H:i', strtotime($spp['created_at'])) ?></small>
                        </div>
                    </div>
                    
                    <?php if ($spp['status'] === 'Verified' || $spp['status'] === 'Approved'): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <small class="text-muted">Diverifikasi oleh</small>
                            <p class="mb-0"><strong><?= $spp['verified_by_name'] ?? 'Operator' ?></strong></p>
                            <small><?= $spp['verified_at'] ? date('d/m/Y H:i', strtotime($spp['verified_at'])) : '-' ?></small>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($spp['status'] === 'Approved'): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <small class="text-muted">Disetujui oleh</small>
                            <p class="mb-0"><strong><?= $spp['approved_by_name'] ?? 'Kepala Desa' ?></strong></p>
                            <small><?= $spp['approved_at'] ? date('d/m/Y H:i', strtotime($spp['approved_at'])) : '-' ?></small>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Signatures -->
            <div class="row mt-5">
                <div class="col-4">
                    <div class="text-center">
                        <p class="mb-5">Yang Mengajukan,<br><strong>Bendahara Desa</strong></p>
                        <div style="min-height: 80px;"></div>
                        <p class="mb-0"><u><strong><?= $desa['nama_bendahara'] ?? '.......................' ?></strong></u></p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-center">
                        <p class="mb-5">Mengetahui,<br><strong>Sekretaris Desa</strong></p>
                        <div style="min-height: 80px;"></div>
                        <p class="mb-0"><u><strong>.................................</strong></u></p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-center">
                        <p class="mb-5">Menyetujui,<br><strong>Kepala Desa</strong></p>
                        <div style="min-height: 80px;"></div>
                        <p class="mb-0"><u><strong><?= $desa['nama_kepala_desa'] ?? '.......................' ?></strong></u></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Timeline Styles -->
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -26px;
    top: 5px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    padding-left: 10px;
}

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

<?php
// Helper function for terbilang (spell out numbers in Indonesian)
if (!function_exists('terbilang')) {
    function terbilang($x) {
        $angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        
        if ($x < 12) return " " . $angka[$x];
        elseif ($x < 20) return terbilang($x - 10) . " belas";
        elseif ($x < 100) return terbilang($x / 10) . " puluh" . terbilang($x % 10);
        elseif ($x < 200) return "seratus" . terbilang($x - 100);
        elseif ($x < 1000) return terbilang($x / 100) . " ratus" . terbilang($x % 100);
        elseif ($x < 2000) return "seribu" . terbilang($x - 1000);
        elseif ($x < 1000000) return terbilang($x / 1000) . " ribu" . terbilang($x % 1000);
        elseif ($x < 1000000000) return terbilang($x / 1000000) . " juta" . terbilang($x % 1000000);
        elseif ($x < 1000000000000) return terbilang($x / 1000000000) . " milyar" . terbilang($x % 1000000000);
        else return terbilang($x / 1000000000000) . " trilyun" . terbilang($x % 1000000000000);
    }
}
?>

<?= view('layout/footer') ?>


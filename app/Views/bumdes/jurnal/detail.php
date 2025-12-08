<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-invoice me-2 text-primary"></i>Detail Jurnal
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/bumdes') ?>">BUMDes</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/bumdes/jurnal/' . $unit['id']) ?>">Jurnal</a></li>
                    <li class="breadcrumb-item active"><?= esc($jurnal['no_bukti']) ?></li>
                </ol>
            </nav>
        </div>
        <a href="<?= base_url('/bumdes/jurnal/' . $unit['id']) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-book me-2"></i>Jurnal Umum</h5>
                        <span class="badge bg-light text-dark"><?= esc($jurnal['no_bukti']) ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" width="120">No. Bukti</td>
                                    <td><strong><?= esc($jurnal['no_bukti']) ?></strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal</td>
                                    <td><?= date('d F Y', strtotime($jurnal['tanggal'])) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" width="120">Unit Usaha</td>
                                    <td><?= esc($unit['nama_unit']) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Total</td>
                                    <td><strong class="text-success">Rp <?= number_format($jurnal['total'], 0, ',', '.') ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <?php if (!empty($jurnal['deskripsi'])): ?>
                    <div class="alert alert-light">
                        <strong>Deskripsi:</strong><br>
                        <?= esc($jurnal['deskripsi']) ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Detail Jurnal -->
                    <h6 class="text-muted mb-3">Detail Transaksi</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th width="120">Kode Akun</th>
                                    <th>Nama Akun</th>
                                    <th class="text-end" width="150">Debet</th>
                                    <th class="text-end" width="150">Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalDebet = 0;
                                $totalKredit = 0;
                                foreach ($jurnal['details'] as $d): 
                                    $totalDebet += $d['debet'];
                                    $totalKredit += $d['kredit'];
                                ?>
                                <tr>
                                    <td><code><?= esc($d['kode_akun']) ?></code></td>
                                    <td>
                                        <?= esc($d['nama_akun']) ?>
                                        <?php if (!empty($d['keterangan'])): ?>
                                        <br><small class="text-muted"><?= esc($d['keterangan']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?= $d['debet'] > 0 ? 'Rp ' . number_format($d['debet'], 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="text-end">
                                        <?= $d['kredit'] > 0 ? 'Rp ' . number_format($d['kredit'], 0, ',', '.') : '-' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td colspan="2" class="text-end">TOTAL</td>
                                    <td class="text-end">Rp <?= number_format($totalDebet, 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($totalKredit, 0, ',', '.') ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <?php if (abs($totalDebet - $totalKredit) < 0.01): ?>
                    <div class="text-center">
                        <span class="badge bg-success fs-6"><i class="fas fa-check me-1"></i>Balance</span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white text-muted small">
                    Dibuat: <?= date('d/m/Y H:i', strtotime($jurnal['created_at'])) ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Aksi</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= base_url('/bumdes/jurnal/' . $unit['id'] . '/create') ?>" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus me-2 text-success"></i>Tambah Jurnal Baru
                    </a>
                    <a href="javascript:window.print()" class="list-group-item list-group-item-action">
                        <i class="fas fa-print me-2 text-info"></i>Cetak Jurnal
                    </a>
                </div>
            </div>
            
            <!-- Info -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body text-center">
                    <i class="fas fa-balance-scale fa-3x text-primary mb-3"></i>
                    <h5>Double Entry</h5>
                    <p class="text-muted small mb-0">
                        Setiap transaksi dicatat dengan prinsip double-entry bookkeeping.
                        Total Debet harus sama dengan Total Kredit.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

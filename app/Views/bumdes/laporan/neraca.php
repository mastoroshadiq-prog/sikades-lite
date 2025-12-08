<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-balance-scale me-2 text-primary"></i>Laporan Neraca
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/bumdes') ?>">BUMDes</a></li>
                    <li class="breadcrumb-item"><?= esc($unit['nama_unit']) ?></li>
                    <li class="breadcrumb-item active">Neraca</li>
                </ol>
            </nav>
        </div>
        <div>
            <form method="GET" class="d-flex gap-2">
                <input type="date" name="tanggal" class="form-control" value="<?= $tanggal ?>" onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 text-center">
                    <h4 class="mb-0"><?= esc($unit['nama_unit']) ?></h4>
                    <p class="text-muted mb-0">Neraca per <?= date('d F Y', strtotime($tanggal)) ?></p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- ASET -->
                        <div class="col-md-6">
                            <h5 class="text-success border-bottom pb-2 mb-3">ASET</h5>
                            <table class="table table-sm">
                                <?php foreach ($neraca['aset'] as $a): ?>
                                <tr>
                                    <td class="ps-3"><?= esc($a['kode_akun']) ?> - <?= esc($a['nama_akun']) ?></td>
                                    <td class="text-end">Rp <?= number_format($a['saldo'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-success fw-bold">
                                    <td>TOTAL ASET</td>
                                    <td class="text-end">Rp <?= number_format($neraca['total_aset'], 0, ',', '.') ?></td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- KEWAJIBAN & EKUITAS -->
                        <div class="col-md-6">
                            <h5 class="text-warning border-bottom pb-2 mb-3">KEWAJIBAN</h5>
                            <table class="table table-sm">
                                <?php foreach ($neraca['kewajiban'] as $k): ?>
                                <tr>
                                    <td class="ps-3"><?= esc($k['kode_akun']) ?> - <?= esc($k['nama_akun']) ?></td>
                                    <td class="text-end">Rp <?= number_format($k['saldo'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-warning fw-bold">
                                    <td>Total Kewajiban</td>
                                    <td class="text-end">Rp <?= number_format($neraca['total_kewajiban'], 0, ',', '.') ?></td>
                                </tr>
                            </table>
                            
                            <h5 class="text-info border-bottom pb-2 mb-3 mt-4">EKUITAS</h5>
                            <table class="table table-sm">
                                <?php foreach ($neraca['ekuitas'] as $e): ?>
                                <tr>
                                    <td class="ps-3"><?= esc($e['kode_akun']) ?> - <?= esc($e['nama_akun']) ?></td>
                                    <td class="text-end">Rp <?= number_format($e['saldo'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-info fw-bold">
                                    <td>Total Ekuitas</td>
                                    <td class="text-end">Rp <?= number_format($neraca['total_ekuitas'], 0, ',', '.') ?></td>
                                </tr>
                            </table>
                            
                            <table class="table table-lg mt-4">
                                <tr class="table-primary fw-bold fs-5">
                                    <td>TOTAL KEWAJIBAN + EKUITAS</td>
                                    <td class="text-end">Rp <?= number_format($neraca['total_kewajiban'] + $neraca['total_ekuitas'], 0, ',', '.') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <?php 
                    $balanced = abs($neraca['total_aset'] - ($neraca['total_kewajiban'] + $neraca['total_ekuitas'])) < 0.01;
                    ?>
                    <div class="text-center mt-4">
                        <?php if ($balanced): ?>
                        <span class="badge bg-success fs-6"><i class="fas fa-check me-1"></i>Neraca Balance</span>
                        <?php else: ?>
                        <span class="badge bg-danger fs-6"><i class="fas fa-times me-1"></i>Neraca Tidak Balance</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer bg-white text-center py-3">
                    <small class="text-muted">Dicetak pada: <?= date('d/m/Y H:i') ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/tutup-buku') ?>">Tutup Buku</a></li>
            <li class="breadcrumb-item active">Detail <?= $tahun ?></li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-alt me-2 text-success"></i>Detail Tutup Buku Tahun <?= $tahun ?>
            </h2>
            <p class="text-muted mb-0">
                <span class="badge bg-success px-3 py-2"><i class="fas fa-lock me-1"></i>Closed</span>
                <?php if ($record['tanggal_tutup']): ?>
                <span class="ms-2">Ditutup pada: <?= date('d/m/Y H:i', strtotime($record['tanggal_tutup'])) ?></span>
                <?php endif; ?>
            </p>
        </div>
        <a href="<?= base_url('/tutup-buku') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <!-- Summary Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center bg-info bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Saldo Awal</h6>
                    <h4 class="text-info mb-0">Rp <?= number_format($record['saldo_awal'], 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center bg-success bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Pendapatan</h6>
                    <h4 class="text-success mb-0">Rp <?= number_format($record['total_pendapatan'], 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center bg-danger bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Belanja</h6>
                    <h4 class="text-danger mb-0">Rp <?= number_format($record['total_belanja'], 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center bg-primary bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Saldo Akhir</h6>
                    <h4 class="text-primary mb-0">Rp <?= number_format($record['saldo_akhir'], 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Summary -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Rekap per Bulan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Bulan</th>
                                    <th class="text-center">Transaksi</th>
                                    <th class="text-end">Debet</th>
                                    <th class="text-end">Kredit</th>
                                    <th class="text-end">Selisih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                                              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                $totalDebet = 0;
                                $totalKredit = 0;
                                $totalTx = 0;
                                
                                foreach ($monthlyData as $data): 
                                    $totalDebet += $data['total_debet'];
                                    $totalKredit += $data['total_kredit'];
                                    $totalTx += $data['jumlah_transaksi'];
                                    $selisih = $data['total_debet'] - $data['total_kredit'];
                                ?>
                                <tr>
                                    <td><?= $bulanNama[(int)$data['bulan']] ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary"><?= $data['jumlah_transaksi'] ?></span>
                                    </td>
                                    <td class="text-end text-success">Rp <?= number_format($data['total_debet'], 0, ',', '.') ?></td>
                                    <td class="text-end text-danger">Rp <?= number_format($data['total_kredit'], 0, ',', '.') ?></td>
                                    <td class="text-end <?= $selisih >= 0 ? 'text-success' : 'text-danger' ?>">
                                        Rp <?= number_format($selisih, 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td>TOTAL</td>
                                    <td class="text-center"><?= $totalTx ?></td>
                                    <td class="text-end text-success">Rp <?= number_format($totalDebet, 0, ',', '.') ?></td>
                                    <td class="text-end text-danger">Rp <?= number_format($totalKredit, 0, ',', '.') ?></td>
                                    <td class="text-end text-primary">Rp <?= number_format($totalDebet - $totalKredit, 0, ',', '.') ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-info"></i>Informasi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Status</td>
                            <td><span class="badge bg-success">Closed</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Tutup</td>
                            <td><?= $record['tanggal_tutup'] ? date('d/m/Y H:i', strtotime($record['tanggal_tutup'])) : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Ditutup Oleh</td>
                            <td><?= $closedByUser ? esc($closedByUser['username']) : '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <?php if ($record['catatan']): ?>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-sticky-note me-2 text-warning"></i>Catatan</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(esc($record['catatan'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-arrow-right fa-2x text-success mb-2"></i>
                    <h6 class="text-muted mb-1">Transfer ke Tahun <?= $tahun + 1 ?></h6>
                    <h4 class="text-success mb-0">Rp <?= number_format($record['saldo_akhir'], 0, ',', '.') ?></h4>
                    <small class="text-muted">Saldo awal tahun berikutnya</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

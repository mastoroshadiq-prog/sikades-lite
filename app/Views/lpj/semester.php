<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/lpj') ?>">LPJ</a></li>
            <li class="breadcrumb-item active"><?= $semesterText ?> <?= $tahun ?></li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-signature me-2 text-success"></i>Laporan Pertanggungjawaban
            </h2>
            <p class="text-muted mb-0"><?= $semesterText ?> Tahun Anggaran <?= $tahun ?></p>
        </div>
        <div>
            <a href="<?= base_url('/lpj/pdf/' . $semester . '?tahun=' . $tahun) ?>" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </a>
            <a href="<?= base_url('/lpj') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Desa Info -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-gradient text-white text-center py-3" style="background: linear-gradient(135deg, <?= $semester == 1 ? '#667eea, #764ba2' : '#11998e, #38ef7d' ?>);">
            <h4 class="mb-1">PEMERINTAH DESA <?= strtoupper($desa['nama_desa'] ?? 'NAMA DESA') ?></h4>
            <h5 class="mb-0">LAPORAN PERTANGGUNGJAWABAN REALISASI PELAKSANAAN APBDes</h5>
            <p class="mb-0 mt-2"><?= $semesterText ?> Tahun Anggaran <?= $tahun ?></p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="text-muted">Anggaran Pendapatan</h6>
                    <h4 class="text-success mb-0">Rp <?= number_format($totalAnggaranP, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="text-muted">Realisasi Pendapatan</h6>
                    <h4 class="text-info mb-0">Rp <?= number_format($totalRealisasiP, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-danger bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="text-muted">Anggaran Belanja</h6>
                    <h4 class="text-danger mb-0">Rp <?= number_format($totalAnggaranB, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="text-muted">Realisasi Belanja</h6>
                    <h4 class="text-warning mb-0">Rp <?= number_format($totalRealisasiB, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- LPJ Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
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
                        <tr class="table-primary fw-bold">
                            <td colspan="5">A. PENDAPATAN</td>
                        </tr>
                        <?php if (empty($pendapatan)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada data pendapatan</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($pendapatan as $item): ?>
                        <tr>
                            <td><?= esc($item['kode_akun']) ?></td>
                            <td><?= esc($item['nama_akun']) ?></td>
                            <td class="text-end">Rp <?= number_format($item['anggaran'], 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($item['realisasi'], 0, ',', '.') ?></td>
                            <td class="text-center">
                                <?php 
                                $persen = $item['persentase'] ?? 0;
                                $badgeClass = $persen >= 80 ? 'success' : ($persen >= 50 ? 'warning' : 'danger');
                                ?>
                                <span class="badge bg-<?= $badgeClass ?>"><?= number_format($persen, 2) ?>%</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <tr class="table-success fw-bold">
                            <td colspan="2" class="text-end">JUMLAH PENDAPATAN</td>
                            <td class="text-end">Rp <?= number_format($totalAnggaranP, 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($totalRealisasiP, 0, ',', '.') ?></td>
                            <td class="text-center">
                                <?= $totalAnggaranP > 0 ? number_format(($totalRealisasiP / $totalAnggaranP) * 100, 2) : 0 ?>%
                            </td>
                        </tr>

                        <!-- BELANJA -->
                        <tr class="table-primary fw-bold">
                            <td colspan="5">B. BELANJA</td>
                        </tr>
                        <?php if (empty($belanja)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada data belanja</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($belanja as $item): ?>
                        <tr>
                            <td><?= esc($item['kode_akun']) ?></td>
                            <td><?= esc($item['nama_akun']) ?></td>
                            <td class="text-end">Rp <?= number_format($item['anggaran'], 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($item['realisasi'], 0, ',', '.') ?></td>
                            <td class="text-center">
                                <?php 
                                $persen = $item['persentase'] ?? 0;
                                $badgeClass = $persen >= 80 ? 'success' : ($persen >= 50 ? 'warning' : 'secondary');
                                ?>
                                <span class="badge bg-<?= $badgeClass ?>"><?= number_format($persen, 2) ?>%</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <tr class="table-danger fw-bold">
                            <td colspan="2" class="text-end">JUMLAH BELANJA</td>
                            <td class="text-end">Rp <?= number_format($totalAnggaranB, 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($totalRealisasiB, 0, ',', '.') ?></td>
                            <td class="text-center">
                                <?= $totalAnggaranB > 0 ? number_format(($totalRealisasiB / $totalAnggaranB) * 100, 2) : 0 ?>%
                            </td>
                        </tr>

                        <!-- SURPLUS/DEFISIT -->
                        <tr class="table-info fw-bold">
                            <td colspan="2" class="text-end">SURPLUS / (DEFISIT)</td>
                            <td class="text-end">Rp <?= number_format($surplusAnggaran, 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($surplusRealisasi, 0, ',', '.') ?></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

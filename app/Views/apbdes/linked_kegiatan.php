<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/apbdes') ?>">APBDes</a></li>
            <li class="breadcrumb-item active">Kegiatan Terintegrasi</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-link me-2 text-primary"></i>Kegiatan Terintegrasi APBDes
            </h2>
            <p class="text-muted mb-0">Daftar kegiatan RKP yang sudah terhubung dengan APBDes tahun <?= $tahun ?></p>
        </div>
        <div>
            <a href="<?= base_url('/apbdes/import?tahun=' . $tahun) ?>" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Import Lagi
            </a>
            <a href="<?= base_url('/apbdes') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <?php if (empty($linkedData)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Belum ada kegiatan yang terintegrasi dengan APBDes tahun <?= $tahun ?>.
        <a href="<?= base_url('/apbdes/import?tahun=' . $tahun) ?>">Import sekarang</a>
    </div>
    <?php else: ?>
    
    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="text-primary mb-0"><?= count($linkedData) ?></h3>
                    <small class="text-muted">Kegiatan Terintegrasi</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h4 class="text-success mb-0">Rp <?= number_format(array_sum(array_column($linkedData, 'anggaran')), 0, ',', '.') ?></h4>
                    <small class="text-muted">Total Anggaran APBDes</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <h4 class="text-info mb-0">Rp <?= number_format(array_sum(array_column($linkedData, 'pagu_kegiatan')), 0, ',', '.') ?></h4>
                    <small class="text-muted">Total Pagu Kegiatan</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Kegiatan</th>
                            <th width="15%">Bidang</th>
                            <th width="20%">Kode Rekening</th>
                            <th width="15%" class="text-end">Pagu Kegiatan</th>
                            <th width="15%" class="text-end">Anggaran APBDes</th>
                            <th width="5%">Match</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($linkedData as $idx => $item): ?>
                        <?php 
                            $match = abs($item['pagu_kegiatan'] - $item['anggaran']) < 1;
                        ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td>
                                <strong><?= esc($item['nama_kegiatan']) ?></strong>
                                <?php if ($item['lokasi']): ?>
                                <br><small class="text-muted"><?= esc($item['lokasi']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= esc($item['nama_bidang'] ?? '-') ?></span>
                            </td>
                            <td>
                                <code><?= esc($item['kode_akun']) ?></code>
                                <br>
                                <small class="text-muted"><?= esc(substr($item['nama_akun'], 0, 30)) ?>...</small>
                            </td>
                            <td class="text-end">Rp <?= number_format($item['pagu_kegiatan'], 0, ',', '.') ?></td>
                            <td class="text-end fw-bold">Rp <?= number_format($item['anggaran'], 0, ',', '.') ?></td>
                            <td class="text-center">
                                <?php if ($match): ?>
                                <span class="badge bg-success"><i class="fas fa-check"></i></span>
                                <?php else: ?>
                                <span class="badge bg-warning"><i class="fas fa-exclamation"></i></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= view('layout/footer') ?>

<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-exchange-alt me-2 text-primary"></i>Data Mutasi Penduduk
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/demografi') ?>">Demografi</a></li>
                    <li class="breadcrumb-item active">Mutasi</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="<?= base_url('/demografi/penduduk/create') ?>" class="btn btn-success">
                <i class="fas fa-baby me-2"></i>Kelahiran
            </a>
            <a href="<?= base_url('/demografi/mutasi/kematian') ?>" class="btn btn-danger">
                <i class="fas fa-cross me-2"></i>Kematian
            </a>
            <a href="<?= base_url('/demografi/mutasi/pindah') ?>" class="btn btn-warning">
                <i class="fas fa-truck-moving me-2"></i>Pindah
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-baby fa-2x mb-2"></i>
                    <h3 class="mb-0"><?= $yearlyStats['KELAHIRAN'] ?? 0 ?></h3>
                    <small>Kelahiran</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fas fa-cross fa-2x mb-2"></i>
                    <h3 class="mb-0"><?= $yearlyStats['KEMATIAN'] ?? 0 ?></h3>
                    <small>Kematian</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-sign-in-alt fa-2x mb-2"></i>
                    <h3 class="mb-0"><?= $yearlyStats['PINDAH_MASUK'] ?? 0 ?></h3>
                    <small>Pindah Masuk</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-sign-out-alt fa-2x mb-2"></i>
                    <h3 class="mb-0"><?= $yearlyStats['PINDAH_KELUAR'] ?? 0 ?></h3>
                    <small>Pindah Keluar</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Tahun</label>
                    <select name="tahun" class="form-select">
                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Jenis Mutasi</label>
                    <select name="jenis" class="form-select">
                        <option value="">Semua</option>
                        <option value="KELAHIRAN" <?= ($filters['jenis_mutasi'] ?? '') == 'KELAHIRAN' ? 'selected' : '' ?>>Kelahiran</option>
                        <option value="KEMATIAN" <?= ($filters['jenis_mutasi'] ?? '') == 'KEMATIAN' ? 'selected' : '' ?>>Kematian</option>
                        <option value="PINDAH_MASUK" <?= ($filters['jenis_mutasi'] ?? '') == 'PINDAH_MASUK' ? 'selected' : '' ?>>Pindah Masuk</option>
                        <option value="PINDAH_KELUAR" <?= ($filters['jenis_mutasi'] ?? '') == 'PINDAH_KELUAR' ? 'selected' : '' ?>>Pindah Keluar</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="<?= base_url('/demografi/mutasi') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Daftar Mutasi Tahun <?= $tahun ?></h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>No KK</th>
                            <th>Jenis Mutasi</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($mutasiList)): ?>
                            <?php foreach ($mutasiList as $m): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($m['tanggal_peristiwa'])) ?></td>
                                <td>
                                    <a href="<?= base_url('/demografi/penduduk/detail/' . $m['penduduk_id']) ?>">
                                        <code><?= esc($m['nik']) ?></code>
                                    </a>
                                </td>
                                <td><strong><?= esc($m['nama_lengkap']) ?></strong></td>
                                <td><code><?= esc($m['no_kk']) ?></code></td>
                                <td>
                                    <?php
                                    $badgeClass = [
                                        'KELAHIRAN' => 'success',
                                        'KEMATIAN' => 'danger',
                                        'PINDAH_MASUK' => 'info',
                                        'PINDAH_KELUAR' => 'warning',
                                        'PERUBAHAN_DATA' => 'secondary',
                                    ];
                                    $icons = [
                                        'KELAHIRAN' => 'baby',
                                        'KEMATIAN' => 'cross',
                                        'PINDAH_MASUK' => 'sign-in-alt',
                                        'PINDAH_KELUAR' => 'sign-out-alt',
                                        'PERUBAHAN_DATA' => 'edit',
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $badgeClass[$m['jenis_mutasi']] ?? 'secondary' ?>">
                                        <i class="fas fa-<?= $icons[$m['jenis_mutasi']] ?? 'circle' ?> me-1"></i>
                                        <?= str_replace('_', ' ', $m['jenis_mutasi']) ?>
                                    </span>
                                </td>
                                <td><?= esc($m['keterangan'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">Tidak ada data mutasi</h5>
                                    <p class="text-muted">Belum ada mutasi tercatat untuk tahun <?= $tahun ?></p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($mutasiList)): ?>
        <div class="card-footer bg-white">
            <small class="text-muted">Total: <?= count($mutasiList) ?> mutasi</small>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= view('layout/footer') ?>

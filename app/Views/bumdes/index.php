<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-store me-2 text-success"></i>Dashboard BUMDes
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">BUMDes</li>
                </ol>
            </nav>
        </div>
        <a href="<?= base_url('/bumdes/unit/create') ?>" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Tambah Unit Usaha
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-2 text-white-50">Total Unit Usaha</h6>
                            <h2 class="mb-0"><?= $totalUnit ?></h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-building fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-2 text-white-50">Unit Aktif</h6>
                            <h2 class="mb-0"><?= $unitAktif ?></h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-2 text-white-50">Total Modal</h6>
                            <h2 class="mb-0">Rp <?= number_format($totalModal, 0, ',', '.') ?></h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-coins fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unit List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Daftar Unit Usaha</h5>
            <a href="<?= base_url('/bumdes/akun') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-book me-1"></i>Chart of Accounts
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Unit</th>
                            <th>Jenis Usaha</th>
                            <th>Penanggung Jawab</th>
                            <th>Modal Awal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($units)): ?>
                            <?php foreach ($units as $u): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($u['nama_unit']) ?></strong>
                                    <?php if ($u['jumlah_jurnal'] > 0): ?>
                                    <br><small class="text-muted"><?= $u['jumlah_jurnal'] ?> jurnal</small>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($u['jenis_usaha'] ?? '-') ?></td>
                                <td><?= esc($u['penanggung_jawab'] ?? '-') ?></td>
                                <td>Rp <?= number_format($u['modal_awal'], 0, ',', '.') ?></td>
                                <td>
                                    <?php if ($u['status'] === 'AKTIF'): ?>
                                    <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('/bumdes/unit/detail/' . $u['id']) ?>" class="btn btn-outline-primary" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('/bumdes/jurnal/' . $u['id']) ?>" class="btn btn-outline-success" title="Jurnal">
                                            <i class="fas fa-book"></i>
                                        </a>
                                        <a href="<?= base_url('/bumdes/laporan/laba-rugi/' . $u['id']) ?>" class="btn btn-outline-info" title="Laba Rugi">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-store fa-4x text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">Belum Ada Unit Usaha</h5>
                                    <p class="text-muted mb-3">Tambahkan unit usaha BUMDes pertama Anda</p>
                                    <a href="<?= base_url('/bumdes/unit/create') ?>" class="btn btn-success">
                                        <i class="fas fa-plus me-2"></i>Tambah Unit Usaha
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <?php $firstUnit = !empty($units) ? $units[0] : null; ?>
    <div class="row mt-4">
        <div class="col-md-4">
            <?php if ($firstUnit): ?>
            <a href="<?= base_url('/bumdes/jurnal/' . $firstUnit['id']) ?>" class="card border-0 shadow-sm text-center py-4 text-decoration-none hover-lift">
                <i class="fas fa-book fa-3x text-primary mb-3"></i>
                <h5 class="text-dark">Jurnal Umum</h5>
                <p class="text-muted small mb-0">Input transaksi double-entry</p>
            </a>
            <?php else: ?>
            <div class="card border-0 shadow-sm text-center py-4 opacity-50" title="Buat unit usaha terlebih dahulu">
                <i class="fas fa-book fa-3x text-primary mb-3"></i>
                <h5>Jurnal Umum</h5>
                <p class="text-muted small mb-0">Input transaksi double-entry</p>
            </div>
            <?php endif; ?>
        </div>
        <div class="col-md-4">
            <?php if ($firstUnit): ?>
            <a href="<?= base_url('/bumdes/laporan/laba-rugi/' . $firstUnit['id']) ?>" class="card border-0 shadow-sm text-center py-4 text-decoration-none hover-lift">
                <i class="fas fa-file-invoice-dollar fa-3x text-success mb-3"></i>
                <h5 class="text-dark">Laba Rugi</h5>
                <p class="text-muted small mb-0">Laporan keuangan</p>
            </a>
            <?php else: ?>
            <div class="card border-0 shadow-sm text-center py-4 opacity-50" title="Buat unit usaha terlebih dahulu">
                <i class="fas fa-file-invoice-dollar fa-3x text-success mb-3"></i>
                <h5>Laba Rugi</h5>
                <p class="text-muted small mb-0">Laporan keuangan</p>
            </div>
            <?php endif; ?>
        </div>
        <div class="col-md-4">
            <?php if ($firstUnit): ?>
            <a href="<?= base_url('/bumdes/laporan/neraca/' . $firstUnit['id']) ?>" class="card border-0 shadow-sm text-center py-4 text-decoration-none hover-lift">
                <i class="fas fa-balance-scale fa-3x text-info mb-3"></i>
                <h5 class="text-dark">Neraca</h5>
                <p class="text-muted small mb-0">Balance sheet</p>
            </a>
            <?php else: ?>
            <div class="card border-0 shadow-sm text-center py-4 opacity-50" title="Buat unit usaha terlebih dahulu">
                <i class="fas fa-balance-scale fa-3x text-info mb-3"></i>
                <h5>Neraca</h5>
                <p class="text-muted small mb-0">Balance sheet</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

<style>
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}
</style>
</div>

<?= view('layout/footer') ?>

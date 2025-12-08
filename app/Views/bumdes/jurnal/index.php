<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-book me-2 text-primary"></i>Jurnal Umum
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/bumdes') ?>">BUMDes</a></li>
                    <li class="breadcrumb-item"><?= esc($unit['nama_unit']) ?></li>
                    <li class="breadcrumb-item active">Jurnal</li>
                </ol>
            </nav>
        </div>
        <a href="<?= base_url('/bumdes/jurnal/' . $unit['id'] . '/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Jurnal
        </a>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Tahun</label>
                    <select name="tahun" class="form-select">
                        <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                        <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">Semua</option>
                        <?php 
                        $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= $bulan == $m ? 'selected' : '' ?>><?= $bulanNama[$m] ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a href="<?= base_url('/bumdes/laporan/neraca-saldo/' . $unit['id']) ?>" class="card border-0 shadow-sm text-decoration-none">
                <div class="card-body text-center py-3">
                    <i class="fas fa-list-ol text-info fa-2x mb-2"></i>
                    <h6 class="mb-0">Neraca Saldo</h6>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?= base_url('/bumdes/laporan/laba-rugi/' . $unit['id']) ?>" class="card border-0 shadow-sm text-decoration-none">
                <div class="card-body text-center py-3">
                    <i class="fas fa-chart-line text-success fa-2x mb-2"></i>
                    <h6 class="mb-0">Laba Rugi</h6>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?= base_url('/bumdes/laporan/neraca/' . $unit['id']) ?>" class="card border-0 shadow-sm text-decoration-none">
                <div class="card-body text-center py-3">
                    <i class="fas fa-balance-scale text-primary fa-2x mb-2"></i>
                    <h6 class="mb-0">Neraca</h6>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?= base_url('/bumdes/unit/detail/' . $unit['id']) ?>" class="card border-0 shadow-sm text-decoration-none">
                <div class="card-body text-center py-3">
                    <i class="fas fa-info-circle text-secondary fa-2x mb-2"></i>
                    <h6 class="mb-0">Detail Unit</h6>
                </div>
            </a>
        </div>
    </div>

    <!-- Jurnal List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Daftar Jurnal - <?= esc($unit['nama_unit']) ?></h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>No Bukti</th>
                            <th>Deskripsi</th>
                            <th class="text-end">Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($jurnalList)): ?>
                            <?php foreach ($jurnalList as $j): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($j['tanggal'])) ?></td>
                                <td><code><?= esc($j['no_bukti']) ?></code></td>
                                <td><?= esc($j['deskripsi']) ?></td>
                                <td class="text-end">Rp <?= number_format($j['total'], 0, ',', '.') ?></td>
                                <td>
                                    <a href="<?= base_url('/bumdes/jurnal/' . $unit['id'] . '/detail/' . $j['id']) ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-book fa-4x text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">Belum Ada Jurnal</h5>
                                    <a href="<?= base_url('/bumdes/jurnal/' . $unit['id'] . '/create') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Tambah Jurnal
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

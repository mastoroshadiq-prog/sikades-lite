<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-alt me-2 text-primary"></i>Laporan
            </h2>
            <p class="text-muted mb-0">Sistem Pelaporan Keuangan Desa</p>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="row g-4">
        <!-- BKU Report -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-lift">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-shape bg-gradient-primary text-white rounded-circle me-3">
                            <i class="fas fa-book fa-lg"></i>
                        </div>
                        <h5 class="card-title mb-0">Buku Kas Umum</h5>
                    </div>
                    <p class="card-text text-muted">Laporan transaksi kas masuk dan keluar dengan running balance</p>
                    
                    <form action="<?= base_url('report/bku') ?>" method="GET" class="mb-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <select name="bulan" class="form-select form-select-sm">
                                    <?php for($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>" <?= date('m') == $i ? 'selected' : '' ?>>
                                            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <select name="tahun" class="form-select form-select-sm">
                                    <?php for($i = date('Y'); $i >= 2020; $i--): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="btn-group w-100 mt-3" role="group">
                            <button type="submit" name="format" value="html" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Lihat
                            </button>
                            <button type="submit" name="format" value="pdf" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button type="submit" name="format" value="excel" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- APBDes Report -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-lift">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-shape bg-gradient-success text-white rounded-circle me-3">
                            <i class="fas fa-chart-pie fa-lg"></i>
                        </div>
                        <h5 class="card-title mb-0">APBDes</h5>
                    </div>
                    <p class="card-text text-muted">Laporan Anggaran Pendapatan dan Belanja Desa</p>
                    
                    <form action="<?= base_url('report/apbdes') ?>" method="GET" class="mb-3">
                        <div class="mb-3">
                            <select name="tahun" class="form-select form-select-sm">
                                <?php for($i = date('Y'); $i >= 2020; $i--): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="btn-group w-100" role="group">
                            <button type="submit" name="format" value="html" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Lihat
                            </button>
                            <button type="submit" name="format" value="pdf" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button type="submit" name="format" value="excel" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- LRA Report -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-lift">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-shape bg-gradient-info text-white rounded-circle me-3">
                            <i class="fas fa-chart-line fa-lg"></i>
                        </div>
                        <h5 class="card-title mb-0">Realisasi Anggaran</h5>
                    </div>
                    <p class="card-text text-muted">Laporan Realisasi Anggaran (LRA) dengan persentase pencapaian</p>
                    
                    <form action="<?= base_url('report/lra') ?>" method="GET" class="mb-3">
                        <div class="mb-3">
                            <select name="tahun" class="form-select form-select-sm">
                                <?php for($i = date('Y'); $i >= 2020; $i--): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="btn-group w-100" role="group">
                            <button type="submit" name="format" value="html" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Lihat
                            </button>
                            <button type="submit" name="format" value="pdf" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button type="submit" name="format" value="excel" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tax Report -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-lift">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-shape bg-gradient-warning text-white rounded-circle me-3">
                            <i class="fas fa-receipt fa-lg"></i>
                        </div>
                        <h5 class="card-title mb-0">Laporan Pajak</h5>
                    </div>
                    <p class="card-text text-muted">Laporan PPN dan PPh dengan status pembayaran</p>
                    
                    <form action="<?= base_url('report/pajak') ?>" method="GET" class="mb-3">
                        <div class="mb-3">
                            <select name="tahun" class="form-select form-select-sm">
                                <?php for($i = date('Y'); $i >= 2020; $i--): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="btn-group w-100" role="group">
                            <button type="submit" name="format" value="html" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Lihat
                            </button>
                            <button type="submit" name="format" value="pdf" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button type="submit" name="format" value="excel" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Reports -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-lift">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-shape bg-gradient-danger text-white rounded-circle me-3">
                            <i class="fas fa-bolt fa-lg"></i>
                        </div>
                        <h5 class="card-title mb-0">Laporan Cepat</h5>
                    </div>
                    <p class="card-text text-muted">Laporan ringkas untuk kebutuhan sehari-hari</p>
                    
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('report/bku?bulan=' . date('m') . '&tahun=' . date('Y')) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-calendar-day"></i> BKU Bulan Ini
                        </a>
                        <a href="<?= base_url('report/lra?tahun=' . date('Y')) ?>" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-calendar-alt"></i> Realisasi Tahun Ini
                        </a>
                        <a href="<?= base_url('spp?status=Pending') ?>" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-clock"></i> SPP Pending
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm bg-light">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-shape bg-gradient-secondary text-white rounded-circle me-3">
                            <i class="fas fa-info-circle fa-lg"></i>
                        </div>
                        <h5 class="card-title mb-0">Bantuan</h5>
                    </div>
                    <p class="card-text text-muted small">
                        <strong>Tips:</strong>
                    </p>
                    <ul class="small text-muted mb-0">
                        <li>Format HTML untuk preview di browser</li>
                        <li>Format PDF untuk arsip resmi</li>
                        <li>Format Excel untuk analisis data</li>
                        <li>Gunakan filter tahun/bulan sesuai kebutuhan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-shape {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>

<?= view('layout/footer') ?>

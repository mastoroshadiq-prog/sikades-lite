<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-signature me-2 text-success"></i>Laporan Pertanggungjawaban (LPJ)
            </h2>
            <p class="text-muted mb-0">Laporan Pertanggungjawaban Realisasi Pelaksanaan APBDes</p>
        </div>
    </div>

    <!-- Year Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Pilih Tahun Anggaran</label>
                    <select name="tahun" class="form-select" onchange="this.form.submit()">
                        <?php foreach ($years as $year): ?>
                        <option value="<?= $year['tahun'] ?>" <?= $year['tahun'] == $currentYear ? 'selected' : '' ?>>
                            <?= $year['tahun'] ?>
                        </option>
                        <?php endforeach; ?>
                        <?php if (empty($years)): ?>
                        <option value="<?= date('Y') ?>"><?= date('Y') ?></option>
                        <?php endif; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Semester Cards -->
    <div class="row">
        <!-- Semester I -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient text-white py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="text-center">
                        <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                        <h4 class="mb-0">Semester I</h4>
                        <p class="mb-0 opacity-75">Januari - Juni <?= $currentYear ?></p>
                    </div>
                </div>
                <div class="card-body text-center py-4">
                    <p class="text-muted mb-4">
                        Laporan Pertanggungjawaban untuk periode Semester I 
                        (1 Januari - 30 Juni <?= $currentYear ?>)
                    </p>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('/lpj/semester/1?tahun=' . $currentYear) ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-eye me-2"></i>Lihat Laporan
                        </a>
                        <a href="<?= base_url('/lpj/pdf/1?tahun=' . $currentYear) ?>" class="btn btn-outline-danger">
                            <i class="fas fa-file-pdf me-2"></i>Export PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Semester II -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient text-white py-4" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <div class="text-center">
                        <i class="fas fa-calendar-check fa-3x mb-3"></i>
                        <h4 class="mb-0">Semester II</h4>
                        <p class="mb-0 opacity-75">Juli - Desember <?= $currentYear ?></p>
                    </div>
                </div>
                <div class="card-body text-center py-4">
                    <p class="text-muted mb-4">
                        Laporan Pertanggungjawaban untuk periode Semester II 
                        (1 Juli - 31 Desember <?= $currentYear ?>)
                    </p>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('/lpj/semester/2?tahun=' . $currentYear) ?>" class="btn btn-success btn-lg">
                            <i class="fas fa-eye me-2"></i>Lihat Laporan
                        </a>
                        <a href="<?= base_url('/lpj/pdf/2?tahun=' . $currentYear) ?>" class="btn btn-outline-danger">
                            <i class="fas fa-file-pdf me-2"></i>Export PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="text-primary"><i class="fas fa-info-circle me-2"></i>Tentang LPJ</h6>
            <p class="text-muted mb-0">
                Laporan Pertanggungjawaban (LPJ) adalah laporan yang wajib dibuat oleh Pemerintah Desa 
                sebagai bentuk pertanggungjawaban atas pelaksanaan APBDes. LPJ dibuat setiap semester:
            </p>
            <ul class="text-muted">
                <li><strong>LPJ Semester I:</strong> Disampaikan paling lambat bulan Juli tahun berjalan</li>
                <li><strong>LPJ Semester II (Akhir Tahun):</strong> Disampaikan paling lambat 3 bulan setelah akhir tahun anggaran</li>
            </ul>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

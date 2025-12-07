<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-hand-holding-usd me-2 text-warning"></i>Calon Penerima Bantuan
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/demografi') ?>">Demografi</a></li>
                    <li class="breadcrumb-item active">BLT Eligible</li>
                </ol>
            </nav>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="fas fa-print me-2"></i>Cetak
            </button>
        </div>
    </div>

    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h3 class="mb-0"><?= count($pendudukList) ?></h3>
                    <small>Total Calon Penerima</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Info -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Kriteria:</strong> Penduduk dengan status <span class="badge bg-warning">DTKS/Keluarga Miskin</span> 
        dan status dasar <span class="badge bg-success">HIDUP</span>.
        Data ini dapat digunakan sebagai dasar penyaluran BLT Dana Desa atau bantuan sosial lainnya.
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Daftar Calon Penerima Bantuan (DTKS)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>No KK</th>
                            <th>L/P</th>
                            <th>Alamat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pendudukList)): ?>
                            <?php $no = 1; foreach ($pendudukList as $p): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><code><?= esc($p['nik']) ?></code></td>
                                <td>
                                    <a href="<?= base_url('/demografi/penduduk/detail/' . $p['id']) ?>">
                                        <strong><?= esc($p['nama_lengkap']) ?></strong>
                                    </a>
                                </td>
                                <td><code><?= esc($p['no_kk']) ?></code></td>
                                <td>
                                    <span class="badge bg-<?= $p['jenis_kelamin'] == 'L' ? 'info' : 'danger' ?>">
                                        <?= $p['jenis_kelamin'] ?>
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        <?= esc($p['dusun'] ?? '') ?>
                                        <?php if ($p['rt'] && $p['rw']): ?>
                                            RT <?= esc($p['rt']) ?>/RW <?= esc($p['rw']) ?>
                                        <?php endif; ?>
                                        <br><?= esc($p['alamat'] ?? '') ?>
                                    </small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-check-circle fa-4x text-success mb-3 d-block"></i>
                                    <h5 class="text-muted">Tidak ada data</h5>
                                    <p class="text-muted">Tidak ada penduduk yang terdaftar sebagai keluarga miskin (DTKS)</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($pendudukList)): ?>
        <div class="card-footer bg-white">
            <small class="text-muted">Total: <?= count($pendudukList) ?> calon penerima bantuan</small>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Back Button -->
    <div class="mt-4">
        <a href="<?= base_url('/demografi') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
    </div>
</div>

<?= view('layout/footer') ?>

<style>
@media print {
    .sidebar, .navbar, .btn, .breadcrumb, .alert {
        display: none !important;
    }
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    .container-fluid {
        padding: 0 !important;
    }
}
</style>

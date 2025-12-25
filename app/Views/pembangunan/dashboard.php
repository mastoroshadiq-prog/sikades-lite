<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-hard-hat me-2 text-warning"></i>e-Pembangunan Dashboard
            </h2>
            <p class="text-muted mb-0">Monitoring Proyek Fisik & Realisasi Anggaran <?= $tahun ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('/pembangunan/proyek/create') ?>" class="btn btn-warning">
                <i class="fas fa-plus me-2"></i>Tambah Proyek
            </a>
            <a href="<?= base_url('/pembangunan/monitoring') ?>" class="btn btn-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>Monitoring Deviasi
            </a>
        </div>
    </div>

    <!-- Alert Projects -->
    <?php if (!empty($alertProjects)): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <strong>⚠️ PERHATIAN!</strong> Terdapat <strong><?= count($alertProjects) ?></strong> proyek dengan deviasi tinggi 
                    (realisasi keuangan jauh melebihi progres fisik).
                    <a href="<?= base_url('/pembangunan/monitoring') ?>" class="alert-link">Lihat Detail →</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Proyek -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow h-100" style="background: #6f42c1 !important;">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white mb-2" style="opacity: 0.9;">Total Proyek</h6>
                            <h2 class="mb-0 fw-bold text-white"><?= $stats['total_proyek'] ?></h2>
                            <small class="text-white" style="opacity: 0.8;">Tahun <?= $tahun ?></small>
                        </div>
                        <div class="rounded-circle p-3" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-project-diagram fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dalam Proses -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow h-100" style="background: #fd7e14 !important;">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white mb-2" style="opacity: 0.9;">Dalam Proses</h6>
                            <h2 class="mb-0 fw-bold text-white"><?= $stats['proses'] ?></h2>
                            <small class="text-white" style="opacity: 0.8;">Rata-rata <?= $stats['avg_fisik'] ?>% selesai</small>
                        </div>
                        <div class="rounded-circle p-3" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-spinner fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selesai -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow h-100" style="background: #28a745 !important;">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white mb-2" style="opacity: 0.9;">Selesai</h6>
                            <h2 class="mb-0 fw-bold text-white"><?= $stats['selesai'] ?></h2>
                            <small class="text-white" style="opacity: 0.8;">100% terealisasi</small>
                        </div>
                        <div class="rounded-circle p-3" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-check-circle fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mangkrak/Alert -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow h-100" style="background: #dc3545 !important;">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white mb-2" style="opacity: 0.9;">Perlu Perhatian</h6>
                            <h2 class="mb-0 fw-bold text-white"><?= $stats['mangkrak'] + $stats['proyek_alert'] ?></h2>
                            <small class="text-white" style="opacity: 0.8;"><?= $stats['mangkrak'] ?> mangkrak, <?= $stats['proyek_alert'] ?> deviasi</small>
                        </div>
                        <div class="rounded-circle p-3" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-exclamation-circle fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Realization Summary -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Perbandingan Fisik vs Keuangan</h5>
                    <select class="form-select form-select-sm w-auto" onchange="location.href='?tahun='+this.value">
                        <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                            <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="comparisonChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2 text-success"></i>Realisasi Anggaran</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="display-4 fw-bold text-primary">
                            <?= $stats['total_anggaran'] > 0 ? round(($stats['total_realisasi'] / $stats['total_anggaran']) * 100, 1) : 0 ?>%
                        </div>
                        <p class="text-muted mb-0">Realisasi Keuangan</p>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1 small">
                            <span>Total Anggaran</span>
                            <strong>Rp <?= number_format($stats['total_anggaran'], 0, ',', '.') ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span>Terealisasi</span>
                            <strong class="text-success">Rp <?= number_format($stats['total_realisasi'], 0, ',', '.') ?></strong>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span>Sisa</span>
                            <strong class="text-warning">Rp <?= number_format($stats['total_anggaran'] - $stats['total_realisasi'], 0, ',', '.') ?></strong>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <div class="text-center">
                            <div class="h4 text-primary mb-0"><?= $stats['avg_fisik'] ?>%</div>
                            <small class="text-muted">Rata-rata Fisik</small>
                        </div>
                        <div class="text-center">
                            <div class="h4 text-success mb-0"><?= $stats['avg_keuangan'] ?>%</div>
                            <small class="text-muted">Rata-rata Keuangan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Projects -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2 text-info"></i>Daftar Proyek Terbaru</h5>
            <a href="<?= base_url('/pembangunan/proyek') ?>" class="btn btn-outline-primary btn-sm">
                Lihat Semua
            </a>
        </div>
        <div class="card-body p-0">
            <?php if (empty($projects)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-hard-hat fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum Ada Proyek</h5>
                    <a href="<?= base_url('/pembangunan/proyek/create') ?>" class="btn btn-warning mt-3">
                        <i class="fas fa-plus me-2"></i>Tambah Proyek
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Proyek</th>
                                <th>Lokasi</th>
                                <th>Anggaran</th>
                                <th>Fisik</th>
                                <th>Keuangan</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($projects, 0, 10) as $p): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($p['nama_proyek']) ?></strong>
                                        <?php if ($p['deviation']['is_alert'] ?? false): ?>
                                            <span class="badge bg-danger ms-1"><i class="fas fa-exclamation-triangle"></i></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small class="text-muted"><?= esc($p['lokasi_detail'] ?: '-') ?></small></td>
                                    <td>Rp <?= number_format($p['anggaran'], 0, ',', '.') ?></td>
                                    <td>
                                        <div class="progress" style="height: 20px; width: 100px;">
                                            <div class="progress-bar bg-info" style="width: <?= $p['persentase_fisik'] ?>%">
                                                <?= $p['persentase_fisik'] ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px; width: 100px;">
                                            <div class="progress-bar bg-success" style="width: <?= $p['persentase_keuangan'] ?>%">
                                                <?= number_format($p['persentase_keuangan'], 0) ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = match($p['status']) {
                                            'RENCANA' => 'secondary',
                                            'PROSES' => 'warning',
                                            'SELESAI' => 'success',
                                            'MANGKRAK' => 'danger',
                                            default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?>"><?= $p['status'] ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('/pembangunan/proyek/detail/' . $p['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const projects = <?= json_encode(array_slice($projects, 0, 10)) ?>;

if (projects.length > 0) {
    const labels = projects.map(p => p.nama_proyek.substring(0, 20) + (p.nama_proyek.length > 20 ? '...' : ''));
    const fisikData = projects.map(p => parseInt(p.persentase_fisik));
    const keuanganData = projects.map(p => parseFloat(p.persentase_keuangan));

    const ctx = document.getElementById('comparisonChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Progres Fisik (%)',
                    data: fisikData,
                    backgroundColor: 'rgba(23, 162, 184, 0.8)',
                    borderColor: '#17a2b8',
                    borderWidth: 1,
                },
                {
                    label: 'Realisasi Keuangan (%)',
                    data: keuanganData,
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: '#28a745',
                    borderWidth: 1,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: value => value + '%'
                    }
                }
            }
        }
    });
}
</script>

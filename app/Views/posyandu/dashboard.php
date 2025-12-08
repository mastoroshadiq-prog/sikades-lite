<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-heartbeat me-2 text-danger"></i>e-Posyandu Dashboard
            </h2>
            <p class="text-muted mb-0">Monitoring Kesehatan Balita & Ibu Hamil</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('/posyandu/stunting') ?>" class="btn btn-danger">
                <i class="fas fa-child me-2"></i>Monitoring Stunting
            </a>
            <a href="<?= base_url('/posyandu/bumil/risti') ?>" class="btn btn-warning">
                <i class="fas fa-user-nurse me-2"></i>Bumil Risti
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Stunting Stats -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient" style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 mb-2">Kasus Stunting</h6>
                            <h2 class="mb-0 fw-bold"><?= $stuntingStats['stunting'] ?? 0 ?></h2>
                            <small class="text-white-50">dari <?= $stuntingStats['total_balita'] ?? 0 ?> balita</small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-child fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress bg-white bg-opacity-25" style="height: 6px;">
                            <div class="progress-bar bg-white" style="width: <?= $stuntingStats['percentage'] ?? 0 ?>%"></div>
                        </div>
                        <small class="text-white-50 mt-1 d-block"><?= $stuntingStats['percentage'] ?? 0 ?>% prevalensi</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Balita -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2">Total Balita Terpantau</h6>
                            <h2 class="mb-0 fw-bold text-primary"><?= $stuntingStats['total_balita'] ?? 0 ?></h2>
                            <small class="text-success"><i class="fas fa-check-circle me-1"></i><?= $stuntingStats['normal'] ?? 0 ?> normal</small>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-baby fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ibu Hamil -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2">Ibu Hamil Aktif</h6>
                            <h2 class="mb-0 fw-bold text-info"><?= $bumpilStats['total_hamil'] ?? 0 ?></h2>
                            <small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i><?= $bumpilStats['resiko_tinggi'] ?? 0 ?> resiko tinggi</small>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-user-nurse fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- HPL dalam 30 hari -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2">Akan Melahirkan</h6>
                            <h2 class="mb-0 fw-bold text-success"><?= $bumpilStats['akan_melahirkan_30_hari'] ?? 0 ?></h2>
                            <small class="text-muted">dalam 30 hari ke depan</small>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-calendar-check fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Monthly Trend Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Tren Pemeriksaan Bulanan <?= $tahun ?></h5>
                    <select class="form-select form-select-sm w-auto" onchange="location.href='?tahun='+this.value">
                        <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                            <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="trendChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Posyandu List -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-clinic-medical me-2 text-success"></i>Posyandu</h5>
                    <a href="<?= base_url('/posyandu/posyandu/create') ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($posyanduList)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-clinic-medical fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data posyandu</p>
                            <a href="<?= base_url('/posyandu/posyandu/create') ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah Posyandu
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($posyanduList as $p): ?>
                                <a href="<?= base_url('/posyandu/posyandu/detail/' . $p['id']) ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?= esc($p['nama_posyandu']) ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i><?= esc($p['alamat_dusun'] ?: 'Desa') ?>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-primary"><?= $p['jumlah_balita'] ?> balita</span>
                                            <?php if ($p['jumlah_stunting'] > 0): ?>
                                                <span class="badge bg-danger"><?= $p['jumlah_stunting'] ?> stunting</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- K1-K4 Completion -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2 text-info"></i>Capaian Pemeriksaan K1-K4 Ibu Hamil</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <div class="display-4 fw-bold text-info"><?= $bumpilStats['k4_percentage'] ?? 0 ?>%</div>
                            <p class="text-muted mb-0">K4 Complete</p>
                        </div>
                        <div class="col-md-9">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Pemeriksaan K4 Lengkap</span>
                                    <span><?= $bumpilStats['k4_complete'] ?? 0 ?> dari <?= $bumpilStats['total_hamil'] ?? 0 ?> ibu hamil</span>
                                </div>
                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar bg-info" style="width: <?= $bumpilStats['k4_percentage'] ?? 0 ?>%"></div>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Target nasional: minimal 4x pemeriksaan selama kehamilan (K1, K2, K3, K4)
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const monthlyData = <?= json_encode($monthlyTrend) ?>;
const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

const ctx = document.getElementById('trendChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: monthNames,
        datasets: [
            {
                label: 'Total Pemeriksaan',
                data: monthlyData.map(d => d.total_periksa),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4,
            },
            {
                label: 'Kasus Stunting',
                data: monthlyData.map(d => d.stunting),
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                fill: true,
                tension: 0.4,
            },
            {
                label: 'Gizi Buruk',
                data: monthlyData.map(d => d.gizi_buruk),
                borderColor: '#fd7e14',
                backgroundColor: 'rgba(253, 126, 20, 0.1)',
                fill: true,
                tension: 0.4,
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
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

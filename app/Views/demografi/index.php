<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-users me-2 text-primary"></i>Demografi Desa
            </h2>
            <p class="text-muted mb-0">Statistik dan Data Kependudukan</p>
        </div>
        <div>
            <a href="<?= base_url('/demografi/penduduk/create') ?>" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>Tambah Penduduk
            </a>
            <a href="<?= base_url('/demografi/import') ?>" class="btn btn-outline-success ms-2">
                <i class="fas fa-file-import me-2"></i>Import Data
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Penduduk -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Total Penduduk</p>
                            <h2 class="mb-0 fw-bold"><?= number_format($summary['total_penduduk']) ?></h2>
                            <small class="opacity-75">Jiwa terdaftar</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total KK -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Kartu Keluarga</p>
                            <h2 class="mb-0 fw-bold"><?= number_format($summary['total_kk']) ?></h2>
                            <small class="opacity-75">KK terdaftar</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-home"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Laki-laki -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Laki-laki</p>
                            <h2 class="mb-0 fw-bold"><?= number_format($summary['laki_laki']) ?></h2>
                            <small class="opacity-75">
                                <?= $summary['total_penduduk'] > 0 ? round(($summary['laki_laki'] / $summary['total_penduduk']) * 100, 1) : 0 ?>%
                            </small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-male"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Perempuan -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Perempuan</p>
                            <h2 class="mb-0 fw-bold"><?= number_format($summary['perempuan']) ?></h2>
                            <small class="opacity-75">
                                <?= $summary['total_penduduk'] > 0 ? round(($summary['perempuan'] / $summary['total_penduduk']) * 100, 1) : 0 ?>%
                            </small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-female"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Warga Miskin -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-hand-holding-heart fa-2x text-warning"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Warga Miskin (DTKS)</p>
                            <h4 class="mb-0"><?= number_format($summary['warga_miskin']) ?> <small class="text-muted fs-6">jiwa</small></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Penyandang Disabilitas -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-wheelchair fa-2x text-info"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Penyandang Disabilitas</p>
                            <h4 class="mb-0"><?= number_format($summary['penyandang_disabilitas']) ?> <small class="text-muted fs-6">jiwa</small></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mutasi Tahun Ini -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-exchange-alt fa-2x text-success"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Mutasi <?= $tahun ?></p>
                            <h4 class="mb-0">
                                <span class="text-success">+<?= $mutasiStats['KELAHIRAN'] + $mutasiStats['PINDAH_MASUK'] ?></span>
                                <span class="mx-1">/</span>
                                <span class="text-danger">-<?= $mutasiStats['KEMATIAN'] + $mutasiStats['PINDAH_KELUAR'] ?></span>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Piramida Penduduk -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Piramida Penduduk</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($agePyramid)): ?>
                    <div style="height: 400px; position: relative;">
                        <canvas id="pyramidChart"></canvas>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-chart-bar fa-4x mb-3 d-block opacity-50"></i>
                        <p>Belum ada data penduduk</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Pendidikan -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-graduation-cap me-2 text-info"></i>Tingkat Pendidikan</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($educationStats)): ?>
                    <div style="height: 400px; position: relative;">
                        <canvas id="educationChart"></canvas>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-graduation-cap fa-4x mb-3 d-block opacity-50"></i>
                        <p>Belum ada data pendidikan</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- More Charts -->
    <div class="row g-4 mb-4">
        <!-- Pekerjaan -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-briefcase me-2 text-warning"></i>Jenis Pekerjaan</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($occupationStats)): ?>
                    <div style="height: 350px; position: relative;">
                        <canvas id="occupationChart"></canvas>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-briefcase fa-4x mb-3 d-block opacity-50"></i>
                        <p>Belum ada data pekerjaan</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Agama & Status Kawin -->
        <div class="col-lg-6">
            <div class="row g-4">
                <!-- Agama -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="fas fa-pray me-2 text-success"></i>Agama</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($religionStats)): ?>
                            <div class="row">
                                <?php foreach ($religionStats as $stat): ?>
                                <div class="col-6 col-md-4 mb-3">
                                    <div class="text-center">
                                        <h5 class="mb-0"><?= number_format($stat['jumlah']) ?></h5>
                                        <small class="text-muted"><?= esc($stat['agama']) ?></small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <p class="text-muted text-center mb-0">Belum ada data</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Status Perkawinan -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="fas fa-ring me-2 text-danger"></i>Status Perkawinan</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($maritalStats)): ?>
                            <div class="row">
                                <?php foreach ($maritalStats as $stat): ?>
                                <div class="col-6 mb-2">
                                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                        <span><?= esc($stat['status_perkawinan']) ?></span>
                                        <span class="badge bg-secondary"><?= number_format($stat['jumlah']) ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <p class="text-muted text-center mb-0">Belum ada data</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Per Wilayah & Recent Mutasi -->
    <div class="row g-4">
        <!-- Statistik per Wilayah -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Per Wilayah</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($wilayahStats)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Dusun</th>
                                    <th class="text-end">Jumlah KK</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($wilayahStats as $stat): ?>
                                <tr>
                                    <td><?= esc($stat['dusun']) ?></td>
                                    <td class="text-end">
                                        <span class="badge bg-primary"><?= number_format($stat['jumlah_kk']) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        <p class="mb-0">Belum ada data wilayah</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Mutasi Terakhir -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-exchange-alt me-2 text-info"></i>Mutasi Terakhir <?= $tahun ?></h5>
                    <a href="<?= base_url('/demografi/mutasi') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recentMutasi)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Jenis</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentMutasi as $mutasi): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($mutasi['tanggal_peristiwa'])) ?></td>
                                    <td><code><?= esc($mutasi['nik']) ?></code></td>
                                    <td><?= esc($mutasi['nama_lengkap']) ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = [
                                            'KELAHIRAN' => 'success',
                                            'KEMATIAN' => 'danger',
                                            'PINDAH_MASUK' => 'info',
                                            'PINDAH_KELUAR' => 'warning',
                                            'PERUBAHAN_DATA' => 'secondary',
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $badgeClass[$mutasi['jenis_mutasi']] ?? 'secondary' ?>">
                                            <?= str_replace('_', ' ', $mutasi['jenis_mutasi']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                        <p class="mb-0">Belum ada data mutasi tahun <?= $tahun ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="mb-3"><i class="fas fa-link me-2"></i>Akses Cepat</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= base_url('/demografi/keluarga') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-home me-1"></i>Data Keluarga
                        </a>
                        <a href="<?= base_url('/demografi/penduduk') ?>" class="btn btn-outline-success">
                            <i class="fas fa-users me-1"></i>Data Penduduk
                        </a>
                        <a href="<?= base_url('/demografi/mutasi') ?>" class="btn btn-outline-info">
                            <i class="fas fa-exchange-alt me-1"></i>Data Mutasi
                        </a>
                        <a href="<?= base_url('/demografi/blt-eligible') ?>" class="btn btn-outline-warning">
                            <i class="fas fa-hand-holding-usd me-1"></i>Calon Penerima Bantuan
                        </a>
                        <a href="<?= base_url('/demografi/import') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-file-import me-1"></i>Import Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

<?php if (!empty($agePyramid)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Piramida Penduduk (Horizontal Bar Chart)
    const pyramidData = <?= json_encode($agePyramid) ?>;
    const labels = pyramidData.map(d => d.kelompok_umur);
    const lakiLaki = pyramidData.map(d => -parseInt(d.laki_laki)); // Negative for left side
    const perempuan = pyramidData.map(d => parseInt(d.perempuan));
    
    new Chart(document.getElementById('pyramidChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Laki-laki',
                    data: lakiLaki,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Perempuan',
                    data: perempuan,
                    backgroundColor: 'rgba(255, 99, 132, 0.8)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + Math.abs(context.raw) + ' jiwa';
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    ticks: {
                        callback: function(value) {
                            return Math.abs(value);
                        }
                    }
                },
                y: {
                    stacked: true
                }
            }
        }
    });
});
</script>
<?php endif; ?>

<?php if (!empty($educationStats)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pendidikan Doughnut Chart
    const eduData = <?= json_encode($educationStats) ?>;
    const eduLabels = eduData.map(d => d.pendidikan_terakhir);
    const eduValues = eduData.map(d => parseInt(d.jumlah));
    
    new Chart(document.getElementById('educationChart'), {
        type: 'doughnut',
        data: {
            labels: eduLabels,
            datasets: [{
                data: eduValues,
                backgroundColor: [
                    '#667eea', '#764ba2', '#f093fb', '#f5576c',
                    '#4facfe', '#00f2fe', '#43e97b', '#38f9d7',
                    '#fa709a', '#fee140'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: { boxWidth: 12, font: { size: 11 } }
                }
            }
        }
    });
});
</script>
<?php endif; ?>

<?php if (!empty($occupationStats)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pekerjaan Horizontal Bar Chart
    const jobData = <?= json_encode($occupationStats) ?>;
    const jobLabels = jobData.map(d => d.pekerjaan.length > 20 ? d.pekerjaan.substring(0, 20) + '...' : d.pekerjaan);
    const jobValues = jobData.map(d => parseInt(d.jumlah));
    
    new Chart(document.getElementById('occupationChart'), {
        type: 'bar',
        data: {
            labels: jobLabels,
            datasets: [{
                label: 'Jumlah',
                data: jobValues,
                backgroundColor: 'rgba(255, 193, 7, 0.8)',
                borderColor: 'rgba(255, 193, 7, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { beginAtZero: true }
            }
        }
    });
});
</script>
<?php endif; ?>

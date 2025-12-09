<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-hard-hat me-2 text-warning"></i><?= esc($project['nama_proyek']) ?>
            </h2>
            <p class="text-muted mb-0">
                <i class="fas fa-map-marker-alt me-1"></i><?= esc($project['lokasi_detail'] ?: 'Lokasi belum ditentukan') ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <?php if ($project['status'] == 'PROSES' || $project['status'] == 'RENCANA'): ?>
                <a href="<?= base_url('/pembangunan/progress/' . $project['id']) ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Input Progres
                </a>
            <?php endif; ?>
            <a href="<?= base_url('/pembangunan/proyek') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('warning') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Deviation Alert -->
    <?php if ($project['deviation']['is_alert']): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <strong>⚠️ PERINGATAN DEVIASI!</strong><br>
                    <?= $project['deviation']['message'] ?><br>
                    <small>Deviasi: <?= $project['deviation']['value'] ?>% (Keuangan <?= $project['persentase_keuangan'] ?>% vs Fisik <?= $project['persentase_fisik'] ?>%)</small>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Status & Progress Card -->
            <div class="card border-0 shadow-sm mb-4">
                <?php
                $headerClass = match($project['status']) {
                    'RENCANA' => 'bg-secondary',
                    'PROSES' => 'bg-warning',
                    'SELESAI' => 'bg-success',
                    'MANGKRAK' => 'bg-danger',
                    default => 'bg-secondary'
                };
                ?>
                <div class="card-header <?= $headerClass ?> text-white py-3">
                    <h5 class="mb-0">
                        <span class="badge bg-white text-dark me-2"><?= $project['status'] ?></span>
                        Status Proyek
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Progres Fisik</h6>
                            <div class="d-flex align-items-center mb-2">
                                <div class="display-5 fw-bold text-info me-3"><?= $project['persentase_fisik'] ?>%</div>
                            </div>
                            <div class="progress" style="height: 15px;">
                                <div class="progress-bar bg-info" style="width: <?= $project['persentase_fisik'] ?>%"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Realisasi Keuangan</h6>
                            <div class="d-flex align-items-center mb-2">
                                <div class="display-5 fw-bold text-success me-3"><?= number_format($project['persentase_keuangan'], 0) ?>%</div>
                            </div>
                            <div class="progress" style="height: 15px;">
                                <div class="progress-bar bg-success" style="width: <?= $project['persentase_keuangan'] ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Chart -->
                    <?php if (!empty($timeline)): ?>
                        <canvas id="progressChart" height="80"></canvas>
                    <?php else: ?>
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                            <p>Belum ada data progres</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Photo Comparison -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-camera me-2 text-info"></i>Dokumentasi Progres</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="mb-2"><strong>0% (Awal)</strong></div>
                            <?php if ($project['foto_0']): ?>
                                <img src="<?= base_url($project['foto_0']) ?>" class="img-fluid rounded" style="max-height: 200px;">
                            <?php else: ?>
                                <div class="bg-light rounded p-4">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                    <p class="text-muted mt-2 mb-0 small">Belum ada foto</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-2"><strong>50% (Pertengahan)</strong></div>
                            <?php if ($project['foto_50']): ?>
                                <img src="<?= base_url($project['foto_50']) ?>" class="img-fluid rounded" style="max-height: 200px;">
                            <?php else: ?>
                                <div class="bg-light rounded p-4">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                    <p class="text-muted mt-2 mb-0 small">Belum ada foto</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-2"><strong>100% (Selesai)</strong></div>
                            <?php if ($project['foto_100']): ?>
                                <img src="<?= base_url($project['foto_100']) ?>" class="img-fluid rounded" style="max-height: 200px;">
                            <?php else: ?>
                                <div class="bg-light rounded p-4">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                    <p class="text-muted mt-2 mb-0 small">Belum ada foto</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Logs -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Riwayat Progres</h5>
                    <?php if ($project['status'] == 'PROSES' || $project['status'] == 'RENCANA'): ?>
                        <a href="<?= base_url('/pembangunan/progress/' . $project['id']) ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Input Progres
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($project['logs'])): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada riwayat progres</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Progres</th>
                                        <th>Volume</th>
                                        <th>Pelapor</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($project['logs'] as $log): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($log['tanggal_laporan'])) ?></td>
                                            <td>
                                                <div class="progress" style="height: 20px; width: 80px;">
                                                    <div class="progress-bar bg-info" style="width: <?= $log['persentase_fisik'] ?>%">
                                                        <?= $log['persentase_fisik'] ?>%
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= $log['volume_terealisasi'] ? $log['volume_terealisasi'] . ' ' . $project['satuan'] : '-' ?></td>
                                            <td><?= esc($log['pelapor'] ?: '-') ?></td>
                                            <td>
                                                <?php if ($log['foto']): ?>
                                                    <a href="<?= base_url($log['foto']) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-image"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($log['kendala']): ?>
                                                    <span class="badge bg-warning" title="<?= esc($log['kendala']) ?>">Kendala</span>
                                                <?php endif; ?>
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

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Project Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Detail Proyek</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless small mb-0">
                        <tr>
                            <td class="text-muted">Anggaran</td>
                            <td class="text-end fw-bold">Rp <?= number_format($project['anggaran'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Volume Target</td>
                            <td class="text-end"><?= $project['volume_target'] ? $project['volume_target'] . ' ' . $project['satuan'] : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Mulai</td>
                            <td class="text-end"><?= $project['tgl_mulai'] ? date('d/m/Y', strtotime($project['tgl_mulai'])) : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Target Selesai</td>
                            <td class="text-end"><?= $project['tgl_selesai_target'] ? date('d/m/Y', strtotime($project['tgl_selesai_target'])) : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pelaksana (TPK)</td>
                            <td class="text-end"><?= esc($project['pelaksana_kegiatan'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kontraktor</td>
                            <td class="text-end"><?= esc($project['kontraktor'] ?: '-') ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Map -->
            <?php if ($project['lat'] && $project['lng']): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Lokasi</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="map" style="height: 250px;"></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Actions -->
            <?php if ($project['status'] == 'PROSES'): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-cog me-2 text-secondary"></i>Aksi</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('/pembangunan/progress/' . $project['id']) ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Input Progres Baru
                            </a>
                            <a href="<?= base_url('/pembangunan/proyek/mangkrak/' . $project['id']) ?>" 
                               class="btn btn-outline-danger"
                               onclick="return confirm('Apakah Anda yakin proyek ini mangkrak/tertunda?')">
                                <i class="fas fa-pause-circle me-2"></i>Tandai Mangkrak
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

<?php if ($project['lat'] && $project['lng']): ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('map').setView([<?= $project['lat'] ?>, <?= $project['lng'] ?>], 16);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
L.marker([<?= $project['lat'] ?>, <?= $project['lng'] ?>])
    .addTo(map)
    .bindPopup('<strong><?= esc($project['nama_proyek']) ?></strong>');
</script>
<?php endif; ?>

<?php if (!empty($timeline)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const timeline = <?= json_encode($timeline) ?>;
const ctx = document.getElementById('progressChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: timeline.map(t => t.tanggal_laporan),
        datasets: [{
            label: 'Progres Fisik (%)',
            data: timeline.map(t => t.persentase_fisik),
            borderColor: '#17a2b8',
            backgroundColor: 'rgba(23, 162, 184, 0.1)',
            fill: true,
            tension: 0.4,
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, max: 100 }
        }
    }
});
</script>
<?php endif; ?>

<?php 
// Use partial layout for HTMX requests
$headerView = ($isHtmxRequest ?? false) ? 'layout/partial_header' : 'layout/header';
$sidebarView = ($isHtmxRequest ?? false) ? 'layout/partial_sidebar' : 'layout/sidebar';
?>
<?= view($headerView) ?>
<?= view($sidebarView) ?>


<!-- Page Title -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="fas fa-home text-primary"></i> Dashboard</h2>
        <p class="text-muted mb-0">Ringkasan Keuangan Desa</p>
    </div>
    <div class="text-end">
        <small class="text-muted d-block">Tahun Anggaran</small>
        <h5 class="mb-0"><?= $tahun ?? date('Y') ?></h5>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <!-- Total Anggaran -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card border-0 bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75">Total Anggaran</p>
                        <h3 class="mb-0 fw-bold" id="totalAnggaran">
                            <?= number_format($stats['total_anggaran'] ?? 0, 0, ',', '.') ?>
                        </h3>
                    </div>
                    <div class="fs-1 opacity-50">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total Realisasi -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card border-0 bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75">Total Realisasi</p>
                        <h3 class="mb-0 fw-bold" id="totalRealisasi">
                            <?= number_format($stats['total_realisasi'] ?? 0, 0, ',', '.') ?>
                        </h3>
                        <small class="opacity-75"><?= $stats['persentase_realisasi'] ?? 0 ?>% dari anggaran</small>
                    </div>
                    <div class="fs-1 opacity-50">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Saldo Kas -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card border-0 bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75">Saldo Kas</p>
                        <h3 class="mb-0 fw-bold" id="saldoKas">
                            <?= number_format($stats['saldo_kas'] ?? 0, 0, ',', '.') ?>
                        </h3>
                    </div>
                    <div class="fs-1 opacity-50">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- SPP Pending -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card border-0 bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75">SPP Pending</p>
                        <h3 class="mb-0 fw-bold" id="sppPending">
                            <?= $stats['spp_pending'] ?? 0 ?> <small>dokumen</small>
                        </h3>
                    </div>
                    <div class="fs-1 opacity-50">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Pendapatan vs Belanja Chart -->
    <div class="col-lg-8">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Pendapatan vs Belanja per Bulan</h5>
            </div>
            <div class="card-body">
                <canvas id="pendapatanBelanjaChart" height="80"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Realisasi Anggaran Chart -->
    <div class="col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-success"></i>Realisasi Anggaran</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="max-width: 250px;">
                    <canvas id="realisasiChart"></canvas>
                    <div class="text-center mt-3">
                        <h4 class="text-success mb-0"><?= $stats['persentase_realisasi'] ?? 0 ?>%</h4>
                        <small class="text-muted">Terrealisasi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Budget Progress by Sumber Dana -->
<?php if (!empty($budgetProgress)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-coins me-2 text-warning"></i>Anggaran per Sumber Dana</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php 
                    $colors = ['DDS' => 'success', 'ADD' => 'primary', 'PAD' => 'info', 'Bankeu' => 'warning'];
                    foreach ($budgetProgress as $item): 
                    $color = $colors[$item['sumber_dana']] ?? 'secondary';
                    ?>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="border rounded p-3 text-center">
                            <span class="badge bg-<?= $color ?> mb-2"><?= $item['sumber_dana'] ?></span>
                            <h5 class="text-<?= $color ?> mb-0">Rp <?= number_format($item['anggaran'], 0, ',', '.') ?></h5>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Recent Activities & Quick Actions -->
<div class="row g-4">
    <!-- Recent Transactions -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-history me-2 text-info"></i>Transaksi Terakhir</h5>
                <a href="<?= base_url('/bku') ?>" class="btn btn-sm btn-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Bukti</th>
                                <th>Keterangan</th>
                                <th class="text-end">Debet</th>
                                <th class="text-end">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentTransactions)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fs-2 mb-2"></i>
                                    <p class="mb-0">Belum ada transaksi</p>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($recentTransactions as $tx): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($tx['tanggal'])) ?></td>
                                <td><code><?= esc($tx['no_bukti'] ?? '-') ?></code></td>
                                <td><?= esc(substr($tx['uraian'], 0, 40)) ?><?= strlen($tx['uraian']) > 40 ? '...' : '' ?></td>
                                <td class="text-end text-success">
                                    <?= $tx['debet'] > 0 ? 'Rp ' . number_format($tx['debet'], 0, ',', '.') : '-' ?>
                                </td>
                                <td class="text-end text-danger">
                                    <?= $tx['kredit'] > 0 ? 'Rp ' . number_format($tx['kredit'], 0, ',', '.') : '-' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Pending SPP List -->
        <?php if (!empty($pendingSpp)): ?>
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-clock me-2 text-warning"></i>SPP Menunggu Persetujuan</h5>
                <a href="<?= base_url('/spp') ?>" class="btn btn-sm btn-warning">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nomor SPP</th>
                                <th>Tanggal</th>
                                <th>Uraian</th>
                                <th>Status</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingSpp as $spp): ?>
                            <tr>
                                <td><a href="<?= base_url('/spp/detail/' . $spp['id']) ?>"><?= esc($spp['nomor_spp']) ?></a></td>
                                <td><?= date('d/m/Y', strtotime($spp['tanggal_spp'])) ?></td>
                                <td><?= esc(substr($spp['uraian'], 0, 30)) ?>...</td>
                                <td><span class="badge bg-<?= $spp['status'] == 'Verified' ? 'info' : 'secondary' ?>"><?= $spp['status'] ?></span></td>
                                <td class="text-end fw-bold">Rp <?= number_format($spp['jumlah'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if (isset($role) && in_array($role, ['Administrator', 'Operator Desa'])): ?>
                    <a href="<?= base_url('/apbdes/create') ?>" class="btn btn-outline-primary text-start">
                        <i class="fas fa-file-invoice-dollar me-2"></i> Input APBDes
                    </a>
                    <a href="<?= base_url('/spp/create') ?>" class="btn btn-outline-success text-start">
                        <i class="fas fa-file-invoice me-2"></i> Buat SPP Baru
                    </a>
                    <a href="<?= base_url('/bku/create') ?>" class="btn btn-outline-info text-start">
                        <i class="fas fa-book me-2"></i> Input BKU
                    </a>
                    <a href="<?= base_url('/perencanaan/rkp/create') ?>" class="btn btn-outline-warning text-start">
                        <i class="fas fa-calendar-alt me-2"></i> Buat RKP Desa
                    </a>
                    <?php endif; ?>
                    
                    <hr>
                    <a href="<?= base_url('/report/bku') ?>" class="btn btn-outline-secondary text-start">
                        <i class="fas fa-file-alt me-2"></i> Laporan BKU
                    </a>
                    <a href="<?= base_url('/report/lra') ?>" class="btn btn-outline-secondary text-start">
                        <i class="fas fa-chart-bar me-2"></i> Laporan Realisasi
                    </a>
                    <a href="<?= base_url('/lpj') ?>" class="btn btn-outline-secondary text-start">
                        <i class="fas fa-file-signature me-2"></i> Laporan LPJ
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Info Card -->
        <div class="card mt-4 border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white">
                <h6 class="text-white-50"><i class="fas fa-info-circle me-2"></i>Informasi Login</h6>
                <p class="small mb-2">
                    <strong>User:</strong> <?= esc($user['username'] ?? 'User') ?>
                </p>
                <p class="small mb-2">
                    <strong>Role:</strong> 
                    <span class="badge bg-light text-dark"><?= esc($user['role'] ?? '-') ?></span>
                </p>
                <p class="small mb-0">
                    <strong>Kode Desa:</strong> <?= esc($user['kode_desa'] ?? '-') ?>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    // Monthly data from controller
    const monthlyData = <?= json_encode($monthlyData ?? ['labels' => [], 'pendapatan' => [], 'belanja' => []]) ?>;
    
    // Pendapatan vs Belanja Bar Chart
    const ctxBar = document.getElementById('pendapatanBelanjaChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: monthlyData.labels,
            datasets: [{
                label: 'Pendapatan',
                data: monthlyData.pendapatan,
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 1,
                borderRadius: 4
            }, {
                label: 'Belanja',
                data: monthlyData.belanja,
                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(0) + ' Rb';
                            }
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
    
    // Realisasi Anggaran Doughnut Chart
    const totalAnggaran = <?= $stats['total_anggaran'] ?? 0 ?>;
    const totalRealisasi = <?= $stats['total_realisasi'] ?? 0 ?>;
    const sisaAnggaran = Math.max(0, totalAnggaran - totalRealisasi);
    
    const ctxDoughnut = document.getElementById('realisasiChart').getContext('2d');
    new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: ['Terealisasi', 'Sisa Anggaran'],
            datasets: [{
                data: [totalRealisasi, sisaAnggaran],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.9)',
                    'rgba(229, 231, 235, 0.9)'
                ],
                borderColor: [
                    'rgba(16, 185, 129, 1)',
                    'rgba(229, 231, 235, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += 'Rp ' + context.parsed.toLocaleString('id-ID');
                            
                            if (totalAnggaran > 0) {
                                const percentage = ((context.parsed / totalAnggaran) * 100).toFixed(1);
                                label += ' (' + percentage + '%)';
                            }
                            
                            return label;
                        }
                    }
                }
            }
        }
    });
    
    // Format currency in stat cards
    document.getElementById('totalAnggaran').innerHTML = formatRupiah(<?= $stats['total_anggaran'] ?? 0 ?>);
    document.getElementById('totalRealisasi').innerHTML = formatRupiah(<?= $stats['total_realisasi'] ?? 0 ?>);
    document.getElementById('saldoKas').innerHTML = formatRupiah(<?= $stats['saldo_kas'] ?? 0 ?>);
</script>

<?php 
$footerView = ($isHtmxRequest ?? false) ? 'layout/partial_footer' : 'layout/footer';
?>
<?= view($footerView) ?>


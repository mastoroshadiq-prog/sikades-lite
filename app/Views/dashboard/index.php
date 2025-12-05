<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Page Title -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="fas fa-home text-primary"></i> Dashboard</h2>
        <p class="text-muted mb-0">Ringkasan Keuangan Desa</p>
    </div>
    <div class="text-end">
        <small class="text-muted d-block">Tahun Anggaran</small>
        <h5 class="mb-0"><?= date('Y') ?></h5>
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
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Pendapatan vs Belanja</h5>
            </div>
            <div class="card-body">
                <canvas id="pendapatanBelanjaChart" height="80"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Realisasi Anggaran Chart -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Realisasi Anggaran</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="realisasiChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities & Quick Actions -->
<div class="row g-4">
    <!-- Recent Transactions -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Transaksi Terakhir</h5>
                <a href="<?= base_url('/penatausahaan/bku') ?>" class="btn btn-sm btn-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Bukti</th>
                                <th>Keterangan</th>
                                <th>Jenis</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fs-2 mb-2"></i>
                                    <p class="mb-0">Belum ada transaksi</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if (isset($role) && in_array($role, ['Administrator', 'Operator Desa'])): ?>
                    <a href="<?= base_url('/apbdes/create') ?>" class="btn btn-outline-primary text-start">
                        <i class="fas fa-file-invoice-dollar me-2"></i> Input APBDes
                    </a>
                    <a href="<?= base_url('/penatausahaan/spp/create') ?>" class="btn btn-outline-success text-start">
                        <i class="fas fa-file-invoice me-2"></i> Buat SPP Baru
                    </a>
                    <a href="<?= base_url('/penatausahaan/bku/create') ?>" class="btn btn-outline-info text-start">
                        <i class="fas fa-book me-2"></i> Input BKU
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('/laporan/bku') ?>" class="btn btn-outline-secondary text-start">
                        <i class="fas fa-file-alt me-2"></i> Laporan BKU
                    </a>
                    <a href="<?= base_url('/laporan/realisasi') ?>" class="btn btn-outline-secondary text-start">
                        <i class="fas fa-chart-bar me-2"></i> Laporan Realisasi
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Info Card -->
        <div class="card mt-4 border-primary">
            <div class="card-body">
                <h6 class="text-primary"><i class="fas fa-info-circle me-2"></i>Informasi</h6>
                <p class="small mb-2">
                    <strong>Logged in as:</strong> <?= esc($user['username'] ?? 'User') ?>
                </p>
                <p class="small mb-2">
                    <strong>Role:</strong> 
                    <span class="badge bg-primary"><?= esc($user['role'] ?? '-') ?></span>
                </p>
                <p class="small mb-0">
                    <strong>Kode Desa:</strong> <?= esc($user['kode_desa'] ?? '-') ?>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    // Pendapatan vs Belanja Bar Chart
    const ctxBar = document.getElementById('pendapatanBelanjaChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Pendapatan',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 1
            }, {
                label: 'Belanja',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 1
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
    const sisaAnggaran = totalAnggaran - totalRealisasi;
    
    const ctxDoughnut = document.getElementById('realisasiChart').getContext('2d');
    new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: ['Terealisasi', 'Sisa Anggaran'],
            datasets: [{
                data: [totalRealisasi, sisaAnggaran],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(229, 231, 235, 0.8)'
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
                            
                            const percentage = ((context.parsed / totalAnggaran) * 100).toFixed(1);
                            label += ' (' + percentage + '%)';
                            
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

<?= view('layout/footer') ?>

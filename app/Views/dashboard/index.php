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
        <form method="get" action="<?= base_url('/dashboard') ?>" class="d-flex align-items-center gap-2">
            <label for="tahunSelect" class="text-muted mb-0 text-nowrap">Tahun Anggaran:</label>
            <select name="tahun" id="tahunSelect" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <?php 
                $years = $availableYears ?? [date('Y')];
                foreach ($years as $year): 
                ?>
                <option value="<?= $year ?>" <?= ($tahun ?? date('Y')) == $year ? 'selected' : '' ?>><?= $year ?></option>
                <?php endforeach; ?>
            </select>
            <noscript><button type="submit" class="btn btn-sm btn-primary">Tampilkan</button></noscript>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <!-- Total Anggaran -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card border-0 bg-primary text-white drilldown-card" 
             data-drilldown="anggaran" 
             role="button" 
             title="Klik untuk melihat detail anggaran">
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
                <small class="opacity-75 mt-2 d-block"><i class="fas fa-search me-1"></i>Klik untuk detail</small>
            </div>
        </div>
    </div>
    
    <!-- Total Realisasi -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card border-0 bg-success text-white drilldown-card" 
             data-drilldown="realisasi" 
             role="button" 
             title="Klik untuk melihat detail realisasi">
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
                <small class="opacity-75 mt-2 d-block"><i class="fas fa-search me-1"></i>Klik untuk detail</small>
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
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Pendapatan vs Belanja per Bulan</h5>
            </div>
            <div class="card-body">
                <?php 
                $hasMonthlyData = !empty($monthlyData['pendapatan']) && array_sum($monthlyData['pendapatan']) > 0;
                ?>
                <?php if ($hasMonthlyData): ?>
                <div style="height: 300px; position: relative;">
                    <canvas id="pendapatanBelanjaChart"></canvas>
                </div>
                <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-chart-bar fa-4x mb-3 d-block opacity-50"></i>
                    <h6>Belum Ada Data Transaksi</h6>
                    <p class="mb-0">Chart akan muncul setelah ada transaksi BKU</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Realisasi Anggaran Chart -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm drilldown-card" 
             data-drilldown="pie-chart" 
             role="button" 
             title="Klik untuk melihat perbandingan detail anggaran vs realisasi">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-success"></i>Realisasi Anggaran</h5>
                <small class="text-muted"><i class="fas fa-search"></i> Klik untuk detail</small>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="max-width: 250px;">
                    <?php if (($stats['total_anggaran'] ?? 0) > 0): ?>
                    <div style="height: 200px; position: relative;">
                        <canvas id="realisasiChart"></canvas>
                    </div>
                    <?php endif; ?>
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
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-coins me-2 text-warning"></i>Anggaran per Sumber Dana</h5>
                <small class="text-muted"><i class="fas fa-hand-pointer"></i> Klik untuk detail</small>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php 
                    $colors = ['DDS' => 'success', 'ADD' => 'primary', 'PAD' => 'info', 'Bankeu' => 'warning'];
                    foreach ($budgetProgress as $item): 
                    $color = $colors[$item['sumber_dana']] ?? 'secondary';
                    ?>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="border rounded p-3 text-center drilldown-sumber-dana" 
                             data-sumber-dana="<?= $item['sumber_dana'] ?>"
                             role="button"
                             title="Klik untuk detail <?= $item['sumber_dana'] ?>"
                             style="cursor: pointer; transition: all 0.3s ease;">
                            <span class="badge bg-<?= $color ?> mb-2"><?= $item['sumber_dana'] ?></span>
                            <h5 class="text-<?= $color ?> mb-0">Rp <?= number_format($item['anggaran'], 0, ',', '.') ?></h5>
                            <small class="text-muted"><i class="fas fa-search fa-xs"></i> Detail</small>
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

<!-- Drilldown Modal -->
<div class="modal fade" id="drilldownModal" tabindex="-1" aria-labelledby="drilldownModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="drilldownModalLabel">
                    <i class="fas fa-search me-2"></i>Detail Data
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="drilldownLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </div>
                <div id="drilldownContent" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<?php 
$footerView = ($isHtmxRequest ?? false) ? 'layout/partial_footer' : 'layout/footer';
?>
<?= view($footerView) ?>

<style>
/* Drilldown Card Styles */
.drilldown-card {
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.drilldown-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.drilldown-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.1);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.drilldown-card:hover::after {
    opacity: 1;
}

.drilldown-sumber-dana:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    background-color: rgba(0, 0, 0, 0.02);
}

/* Drilldown Data Table */
.drilldown-table {
    font-size: 0.9rem;
}

.drilldown-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.drilldown-table .sumber-badge {
    font-size: 0.75rem;
}

/* Summary Cards in Drilldown */
.drilldown-summary-card {
    border-radius: 10px;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s ease;
}

.drilldown-summary-card:hover {
    transform: translateY(-3px);
}

/* Comparison Bar */
.comparison-bar {
    height: 25px;
    border-radius: 5px;
    overflow: hidden;
    background: #e9ecef;
}

.comparison-bar-fill {
    height: 100%;
    transition: width 0.5s ease;
}

/* Animation for modal content */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#drilldownContent > * {
    animation: fadeInUp 0.3s ease;
}
</style>

<script>
// Dashboard Charts - Run after footer loads Chart.js
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js not loaded');
        return;
    }
    
    // Monthly data from controller
    const monthlyData = <?= json_encode($monthlyData ?? ['labels' => [], 'pendapatan' => [], 'belanja' => []]) ?>;
    
    // Pendapatan vs Belanja Bar Chart
    const barCanvas = document.getElementById('pendapatanBelanjaChart');
    if (barCanvas) {
        new Chart(barCanvas, {
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
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + ' Rb';
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Realisasi Anggaran Doughnut Chart
    const doughnutCanvas = document.getElementById('realisasiChart');
    if (doughnutCanvas) {
        const totalAnggaran = <?= $stats['total_anggaran'] ?? 0 ?>;
        const totalRealisasi = <?= $stats['total_realisasi'] ?? 0 ?>;
        const sisaAnggaran = Math.max(0, totalAnggaran - totalRealisasi);
        
        new Chart(doughnutCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Terealisasi', 'Sisa Anggaran'],
                datasets: [{
                    data: [totalRealisasi, sisaAnggaran],
                    backgroundColor: ['rgba(16, 185, 129, 0.9)', 'rgba(229, 231, 235, 0.9)'],
                    borderColor: ['rgba(16, 185, 129, 1)', 'rgba(229, 231, 235, 1)'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label + ': Rp ' + context.parsed.toLocaleString('id-ID');
                                if (totalAnggaran > 0) {
                                    label += ' (' + ((context.parsed / totalAnggaran) * 100).toFixed(1) + '%)';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Initialize Drilldown functionality
    initDrilldown();
});

// Drilldown functionality
function initDrilldown() {
    const baseUrl = '<?= base_url() ?>';
    const tahun = <?= $tahun ?? date('Y') ?>;
    
    // Format currency helper
    function formatCurrency(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
    }
    
    // Format date helper
    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }
    
    // Month names
    const bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    // Get color for sumber dana
    function getSumberDanaColor(sumber) {
        const colors = {
            'DDS': 'success',
            'ADD': 'primary', 
            'PAD': 'info',
            'Bankeu': 'warning'
        };
        return colors[sumber] || 'secondary';
    }
    
    // Show modal with loading
    function showDrilldownModal(title) {
        const modal = new bootstrap.Modal(document.getElementById('drilldownModal'));
        document.getElementById('drilldownModalLabel').innerHTML = '<i class="fas fa-search me-2"></i>' + title;
        document.getElementById('drilldownLoading').style.display = 'block';
        document.getElementById('drilldownContent').style.display = 'none';
        document.getElementById('drilldownContent').innerHTML = '';
        modal.show();
    }
    
    // Show content in modal
    function showDrilldownContent(html) {
        document.getElementById('drilldownLoading').style.display = 'none';
        document.getElementById('drilldownContent').innerHTML = html;
        document.getElementById('drilldownContent').style.display = 'block';
    }
    
    // Drilldown: Total Anggaran
    function loadAnggaranDrilldown() {
        showDrilldownModal('Detail Total Anggaran ' + tahun);
        
        fetch(`${baseUrl}/dashboard/drilldown/anggaran?tahun=${tahun}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    showDrilldownContent('<div class="alert alert-danger">' + (data.message || 'Gagal memuat data') + '</div>');
                    return;
                }
                
                let html = `
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-primary">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Total Anggaran Tahun ${data.tahun}:</strong> ${formatCurrency(data.summary.grand_total)}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Summary by Sumber Dana -->
                    <div class="row mb-4">
                        ${Object.entries(data.summary.total_by_sumber).map(([sumber, total]) => `
                            <div class="col-md-3 col-6 mb-3">
                                <div class="drilldown-summary-card bg-${getSumberDanaColor(sumber)} bg-opacity-10 border border-${getSumberDanaColor(sumber)}">
                                    <span class="badge bg-${getSumberDanaColor(sumber)} mb-2">${sumber}</span>
                                    <h5 class="text-${getSumberDanaColor(sumber)} mb-0">${formatCurrency(total)}</h5>
                                    <small class="text-muted">${data.summary.grand_total > 0 ? ((total / data.summary.grand_total) * 100).toFixed(1) : 0}%</small>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    
                    <!-- Detailed Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover drilldown-table">
                            <thead>
                                <tr>
                                    <th>Kode Akun</th>
                                    <th>Nama Akun</th>
                                    <th>Uraian</th>
                                    <th>Sumber Dana</th>
                                    <th class="text-end">Anggaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.data.length > 0 ? data.data.map(item => `
                                    <tr>
                                        <td><code>${item.kode_akun || '-'}</code></td>
                                        <td>${item.nama_akun || '-'}</td>
                                        <td>${item.uraian}</td>
                                        <td><span class="badge sumber-badge bg-${getSumberDanaColor(item.sumber_dana)}">${item.sumber_dana}</span></td>
                                        <td class="text-end fw-bold">${formatCurrency(item.anggaran)}</td>
                                    </tr>
                                `).join('') : '<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data anggaran</td></tr>'}
                            </tbody>
                            ${data.data.length > 0 ? `
                            <tfoot class="table-primary">
                                <tr>
                                    <th colspan="4" class="text-end">Total</th>
                                    <th class="text-end">${formatCurrency(data.summary.grand_total)}</th>
                                </tr>
                            </tfoot>
                            ` : ''}
                        </table>
                    </div>
                `;
                
                showDrilldownContent(html);
            })
            .catch(error => {
                console.error('Error:', error);
                showDrilldownContent('<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>');
            });
    }
    
    // Drilldown: Total Realisasi
    function loadRealisasiDrilldown() {
        showDrilldownModal('Detail Total Realisasi ' + tahun);
        
        fetch(`${baseUrl}/dashboard/drilldown/realisasi?tahun=${tahun}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    showDrilldownContent('<div class="alert alert-danger">' + (data.message || 'Gagal memuat data') + '</div>');
                    return;
                }
                
                let html = `
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <i class="fas fa-chart-line me-2"></i>
                                <strong>Total Realisasi Tahun ${data.tahun}:</strong> ${formatCurrency(data.summary.grand_total)}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <i class="fas fa-receipt me-2"></i>
                                <strong>Total Transaksi:</strong> ${data.summary.total_transactions} transaksi
                            </div>
                        </div>
                    </div>
                    
                    <!-- Monthly Summary -->
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Realisasi per Bulan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                ${data.data.monthly.map(item => `
                                    <div class="col-md-4 col-6 mb-3">
                                        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                            <span class="fw-medium">${bulanNama[item.bulan]}</span>
                                            <span class="badge bg-success">${formatCurrency(item.total_realisasi)}</span>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                    
                    <!-- By Rekening -->
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-book me-2 text-info"></i>Realisasi per Rekening</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover drilldown-table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kode Akun</th>
                                            <th>Nama Akun</th>
                                            <th class="text-center">Jml Transaksi</th>
                                            <th class="text-end">Total Realisasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${data.data.by_rekening.length > 0 ? data.data.by_rekening.map(item => `
                                            <tr>
                                                <td><code>${item.kode_akun || '-'}</code></td>
                                                <td>${item.nama_akun || '-'}</td>
                                                <td class="text-center"><span class="badge bg-info">${item.jumlah_transaksi}</span></td>
                                                <td class="text-end fw-bold text-success">${formatCurrency(item.total_realisasi)}</td>
                                            </tr>
                                        `).join('') : '<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data</td></tr>'}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Transactions -->
                    <div class="card">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-history me-2 text-warning"></i>20 Transaksi Terakhir</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover drilldown-table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>No. Bukti</th>
                                            <th>Kode Akun</th>
                                            <th>Uraian</th>
                                            <th class="text-end">Belanja</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${data.data.recent_transactions.length > 0 ? data.data.recent_transactions.map(item => `
                                            <tr>
                                                <td>${formatDate(item.tanggal)}</td>
                                                <td><code>${item.no_bukti || '-'}</code></td>
                                                <td><code>${item.kode_akun || '-'}</code></td>
                                                <td>${(item.uraian || '').substring(0, 50)}${(item.uraian || '').length > 50 ? '...' : ''}</td>
                                                <td class="text-end text-danger fw-bold">${formatCurrency(item.kredit)}</td>
                                            </tr>
                                        `).join('') : '<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada transaksi</td></tr>'}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
                
                showDrilldownContent(html);
            })
            .catch(error => {
                console.error('Error:', error);
                showDrilldownContent('<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>');
            });
    }
    
    // Drilldown: Sumber Dana
    function loadSumberDanaDrilldown(sumberDana) {
        showDrilldownModal('Detail Anggaran ' + sumberDana + ' - ' + tahun);
        
        fetch(`${baseUrl}/dashboard/drilldown/sumber-dana?tahun=${tahun}&sumber_dana=${encodeURIComponent(sumberDana)}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    showDrilldownContent('<div class="alert alert-danger">' + (data.message || 'Gagal memuat data') + '</div>');
                    return;
                }
                
                const totalAnggaran = data.data.reduce((sum, item) => sum + parseFloat(item.anggaran), 0);
                const realisasiSummary = data.realisasi_summary.find(r => r.sumber_dana === sumberDana) || { total_anggaran: 0, total_realisasi: 0 };
                const persentaseRealisasi = realisasiSummary.total_anggaran > 0 ? ((realisasiSummary.total_realisasi / realisasiSummary.total_anggaran) * 100).toFixed(1) : 0;
                
                let html = `
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="drilldown-summary-card bg-${getSumberDanaColor(sumberDana)} bg-opacity-10 border border-${getSumberDanaColor(sumberDana)}">
                                <h6 class="text-${getSumberDanaColor(sumberDana)}"><i class="fas fa-wallet me-2"></i>Total Anggaran</h6>
                                <h4 class="mb-0">${formatCurrency(realisasiSummary.total_anggaran)}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="drilldown-summary-card bg-success bg-opacity-10 border border-success">
                                <h6 class="text-success"><i class="fas fa-check me-2"></i>Total Realisasi</h6>
                                <h4 class="mb-0">${formatCurrency(realisasiSummary.total_realisasi)}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="drilldown-summary-card bg-info bg-opacity-10 border border-info">
                                <h6 class="text-info"><i class="fas fa-percentage me-2"></i>Persentase</h6>
                                <h4 class="mb-0">${persentaseRealisasi}%</h4>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Progress Realisasi</span>
                            <span class="fw-bold">${persentaseRealisasi}%</span>
                        </div>
                        <div class="comparison-bar">
                            <div class="comparison-bar-fill bg-${getSumberDanaColor(sumberDana)}" style="width: ${Math.min(100, persentaseRealisasi)}%"></div>
                        </div>
                    </div>
                    
                    <!-- Detailed Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover drilldown-table">
                            <thead>
                                <tr>
                                    <th>Kode Akun</th>
                                    <th>Nama Akun</th>
                                    <th>Uraian</th>
                                    <th class="text-end">Anggaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.data.length > 0 ? data.data.map(item => `
                                    <tr>
                                        <td><code>${item.kode_akun || '-'}</code></td>
                                        <td>${item.nama_akun || '-'}</td>
                                        <td>${item.uraian}</td>
                                        <td class="text-end fw-bold">${formatCurrency(item.anggaran)}</td>
                                    </tr>
                                `).join('') : '<tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data</td></tr>'}
                            </tbody>
                            ${data.data.length > 0 ? `
                            <tfoot class="table-${getSumberDanaColor(sumberDana)}">
                                <tr>
                                    <th colspan="3" class="text-end">Total</th>
                                    <th class="text-end">${formatCurrency(totalAnggaran)}</th>
                                </tr>
                            </tfoot>
                            ` : ''}
                        </table>
                    </div>
                `;
                
                showDrilldownContent(html);
            })
            .catch(error => {
                console.error('Error:', error);
                showDrilldownContent('<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>');
            });
    }
    
    // Drilldown: Pie Chart (Anggaran vs Realisasi Comparison)
    function loadPieChartDrilldown() {
        showDrilldownModal('Perbandingan Anggaran vs Realisasi ' + tahun);
        
        fetch(`${baseUrl}/dashboard/drilldown/pie-chart?tahun=${tahun}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    showDrilldownContent('<div class="alert alert-danger">' + (data.message || 'Gagal memuat data') + '</div>');
                    return;
                }
                
                const summary = data.data.summary;
                
                let html = `
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="drilldown-summary-card bg-primary bg-opacity-10 border border-primary">
                                <h6 class="text-primary"><i class="fas fa-wallet me-2"></i>Total Anggaran</h6>
                                <h4 class="mb-0">${formatCurrency(summary.total_anggaran)}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="drilldown-summary-card bg-success bg-opacity-10 border border-success">
                                <h6 class="text-success"><i class="fas fa-check-circle me-2"></i>Total Realisasi</h6>
                                <h4 class="mb-0">${formatCurrency(summary.total_realisasi)}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="drilldown-summary-card bg-warning bg-opacity-10 border border-warning">
                                <h6 class="text-warning"><i class="fas fa-coins me-2"></i>Sisa Anggaran</h6>
                                <h4 class="mb-0">${formatCurrency(summary.sisa_anggaran)}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="drilldown-summary-card bg-info bg-opacity-10 border border-info">
                                <h6 class="text-info"><i class="fas fa-percentage me-2"></i>Persentase</h6>
                                <h4 class="mb-0">${summary.persentase_realisasi}%</h4>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Comparison by Sumber Dana -->
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Perbandingan per Sumber Dana</h6>
                        </div>
                        <div class="card-body">
                            ${data.data.by_sumber_dana.length > 0 ? data.data.by_sumber_dana.map(item => `
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-${getSumberDanaColor(item.sumber_dana)}">${item.sumber_dana}</span>
                                        <span class="small text-muted">
                                            ${formatCurrency(item.total_realisasi)} / ${formatCurrency(item.total_anggaran)} 
                                            <span class="fw-bold">(${item.persentase}%)</span>
                                        </span>
                                    </div>
                                    <div class="comparison-bar">
                                        <div class="comparison-bar-fill bg-${getSumberDanaColor(item.sumber_dana)}" style="width: ${Math.min(100, item.persentase)}%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-success">Realisasi: ${formatCurrency(item.total_realisasi)}</small>
                                        <small class="text-warning">Sisa: ${formatCurrency(item.sisa)}</small>
                                    </div>
                                </div>
                            `).join('') : '<div class="text-center text-muted py-3">Tidak ada data</div>'}
                        </div>
                    </div>
                    
                    <!-- Comparison Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover drilldown-table">
                            <thead>
                                <tr>
                                    <th>Sumber Dana</th>
                                    <th class="text-end">Anggaran</th>
                                    <th class="text-end">Realisasi</th>
                                    <th class="text-end">Sisa</th>
                                    <th class="text-center">Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.data.by_sumber_dana.length > 0 ? data.data.by_sumber_dana.map(item => `
                                    <tr>
                                        <td><span class="badge bg-${getSumberDanaColor(item.sumber_dana)}">${item.sumber_dana}</span></td>
                                        <td class="text-end">${formatCurrency(item.total_anggaran)}</td>
                                        <td class="text-end text-success">${formatCurrency(item.total_realisasi)}</td>
                                        <td class="text-end text-warning">${formatCurrency(item.sisa)}</td>
                                        <td class="text-center">
                                            <span class="badge ${item.persentase >= 80 ? 'bg-success' : item.persentase >= 50 ? 'bg-warning' : 'bg-danger'}">
                                                ${item.persentase}%
                                            </span>
                                        </td>
                                    </tr>
                                `).join('') : '<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data</td></tr>'}
                            </tbody>
                            ${data.data.by_sumber_dana.length > 0 ? `
                            <tfoot class="table-primary">
                                <tr>
                                    <th>TOTAL</th>
                                    <th class="text-end">${formatCurrency(summary.total_anggaran)}</th>
                                    <th class="text-end text-success">${formatCurrency(summary.total_realisasi)}</th>
                                    <th class="text-end text-warning">${formatCurrency(summary.sisa_anggaran)}</th>
                                    <th class="text-center"><span class="badge bg-${summary.persentase_realisasi >= 80 ? 'success' : summary.persentase_realisasi >= 50 ? 'warning' : 'danger'}">${summary.persentase_realisasi}%</span></th>
                                </tr>
                            </tfoot>
                            ` : ''}
                        </table>
                    </div>
                `;
                
                showDrilldownContent(html);
            })
            .catch(error => {
                console.error('Error:', error);
                showDrilldownContent('<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>');
            });
    }
    
    // Event Listeners - Drilldown Cards
    document.querySelectorAll('.drilldown-card').forEach(card => {
        card.addEventListener('click', function() {
            const drilldownType = this.dataset.drilldown;
            
            switch(drilldownType) {
                case 'anggaran':
                    loadAnggaranDrilldown();
                    break;
                case 'realisasi':
                    loadRealisasiDrilldown();
                    break;
                case 'pie-chart':
                    loadPieChartDrilldown();
                    break;
            }
        });
    });
    
    // Event Listeners - Sumber Dana Cards
    document.querySelectorAll('.drilldown-sumber-dana').forEach(card => {
        card.addEventListener('click', function() {
            const sumberDana = this.dataset.sumberDana;
            loadSumberDanaDrilldown(sumberDana);
        });
    });
}
</script>

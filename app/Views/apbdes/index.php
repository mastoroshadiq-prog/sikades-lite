<?= view('layout/htmx_layout_start', get_defined_vars()) ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="fas fa-file-invoice-dollar text-primary"></i> APBDes</h2>
        <p class="text-muted mb-0">Anggaran Pendapatan dan Belanja Desa</p>
    </div>
    <div>
        <?php if (isset($user['role']) && in_array($user['role'], ['Administrator', 'Operator Desa'])): ?>
        <a href="<?= base_url('/apbdes/create?tahun=' . $tahun) ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Anggaran
        </a>
        <?php endif; ?>
        <a href="<?= base_url('/apbdes/report?tahun=' . $tahun) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-print me-2"></i>Cetak Laporan
        </a>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('/apbdes') ?>" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="tahun" class="form-label">Tahun Anggaran</label>
                <select name="tahun" id="tahun" class="form-select" onchange="this.form.submit()">
                    <?php for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++): ?>
                        <option value="<?= $y ?>" <?= ($y == $tahun) ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-9 text-end">
                <div class="badge bg-primary fs-6">
                    Total Anggaran: <strong>Rp <?= number_format(array_sum(array_column($anggaran, 'anggaran')), 0, ',', '.') ?></strong>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards (moved above the table) -->
<?php if (!empty($anggaran)): ?>
<?php
$totalPendapatan = 0;
$totalBelanja = 0;
$totalPembiayaan = 0;

foreach ($anggaran as $item) {
    if (strpos($item['kode_akun'], '4.') === 0) {
        $totalPendapatan += $item['anggaran'];
    } elseif (strpos($item['kode_akun'], '5.') === 0) {
        $totalBelanja += $item['anggaran'];
    } elseif (strpos($item['kode_akun'], '6.') === 0) {
        $totalPembiayaan += $item['anggaran'];
    }
}

$surplus = $totalPendapatan - $totalBelanja;
?>
<div class="row g-3 mb-4" id="summaryCards">
    <div class="col-md-3">
        <div class="card bg-success text-white h-100 summary-card" 
             data-filter="4." 
             data-label="Pendapatan"
             role="button" 
             style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;"
             title="Klik untuk filter Pendapatan">
            <div class="card-body py-3">
                <h6 class="opacity-75 mb-1"><i class="fas fa-arrow-down me-1"></i>Total Pendapatan</h6>
                <h4 class="mb-0">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h4>
                <small class="opacity-50"><i class="fas fa-filter me-1"></i>Klik untuk filter</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-danger text-white h-100 summary-card" 
             data-filter="5." 
             data-label="Belanja"
             role="button" 
             style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;"
             title="Klik untuk filter Belanja">
            <div class="card-body py-3">
                <h6 class="opacity-75 mb-1"><i class="fas fa-arrow-up me-1"></i>Total Belanja</h6>
                <h4 class="mb-0">Rp <?= number_format($totalBelanja, 0, ',', '.') ?></h4>
                <small class="opacity-50"><i class="fas fa-filter me-1"></i>Klik untuk filter</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white h-100 summary-card" 
             data-filter="6." 
             data-label="Pembiayaan"
             role="button" 
             style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;"
             title="Klik untuk filter Pembiayaan">
            <div class="card-body py-3">
                <h6 class="opacity-75 mb-1"><i class="fas fa-exchange-alt me-1"></i>Total Pembiayaan</h6>
                <h4 class="mb-0">Rp <?= number_format($totalPembiayaan, 0, ',', '.') ?></h4>
                <small class="opacity-50"><i class="fas fa-filter me-1"></i>Klik untuk filter</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card <?= $surplus >= 0 ? 'bg-primary' : 'bg-warning' ?> text-white h-100 summary-card" 
             data-filter="all" 
             data-label="Semua"
             role="button" 
             style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;"
             title="Klik untuk tampilkan semua">
            <div class="card-body py-3">
                <h6 class="opacity-75 mb-1"><i class="fas fa-balance-scale me-1"></i><?= $surplus >= 0 ? 'Surplus' : 'Defisit' ?></h6>
                <h4 class="mb-0">Rp <?= number_format(abs($surplus), 0, ',', '.') ?></h4>
                <small class="opacity-50"><i class="fas fa-list me-1"></i>Tampilkan semua</small>
            </div>
        </div>
    </div>
</div>

<!-- Active Filter Indicator -->
<div id="activeFilterIndicator" class="alert alert-info py-2 mb-3" style="display: none;">
    <div class="d-flex justify-content-between align-items-center">
        <span>
            <i class="fas fa-filter me-2"></i>
            Menampilkan: <strong id="filterLabel">Semua</strong>
        </span>
        <button type="button" class="btn btn-sm btn-outline-info" onclick="clearFilter()">
            <i class="fas fa-times me-1"></i>Hapus Filter
        </button>
    </div>
</div>
<?php endif; ?>

<!-- APBDes Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Anggaran Tahun <?= $tahun ?></h5>
    </div>
    <div class="card-body">
        <?php $isAdmin = isset($user['role']) && in_array($user['role'], ['Administrator', 'Operator Desa']); ?>
        
        <?php if (empty($anggaran)): ?>
        <!-- Empty State - shown outside DataTable -->
        <div class="text-center text-muted py-5">
            <i class="fas fa-inbox fs-1 mb-3 d-block"></i>
            <h5>Belum ada data anggaran untuk tahun <?= $tahun ?></h5>
            <p class="mb-3">Silakan tambah anggaran baru atau pilih tahun lain.</p>
            <?php if ($isAdmin): ?>
            <a href="<?= base_url('/apbdes/create?tahun=' . $tahun) ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Anggaran
            </a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <!-- Table with data - use DataTable -->
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 12%">Kode Rekening</th>
                        <th style="width: 25%">Nama Rekening</th>
                        <th style="width: 23%">Uraian</th>
                        <th style="width: 10%">Sumber Dana</th>
                        <th style="width: 15%" class="text-end">Anggaran</th>
                        <th style="width: 10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($anggaran as $item): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><code><?= esc($item['kode_akun']) ?></code></td>
                        <td>
                            <strong><?= esc($item['nama_akun']) ?></strong>
                            <br><small class="text-muted">Level <?= $item['level'] ?></small>
                        </td>
                        <td>
                            <small><?= esc($item['uraian']) ?></small>
                        </td>
                        <td>
                            <?php
                            $badgeClass = [
                                'DDS' => 'bg-primary',
                                'ADD' => 'bg-success',
                                'PAD' => 'bg-info',
                                'Bankeu' => 'bg-warning'
                            ];
                            ?>
                            <span class="badge <?= $badgeClass[$item['sumber_dana']] ?? 'bg-secondary' ?>">
                                <?= esc($item['sumber_dana']) ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <strong>Rp <?= number_format($item['anggaran'], 0, ',', '.') ?></strong>
                        </td>
                        <td class="text-center">
                            <?php if ($isAdmin): ?>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('/apbdes/edit/' . $item['id']) ?>" 
                                   class="btn btn-outline-primary" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-outline-danger" 
                                        onclick="confirmDelete('<?= base_url('/apbdes/delete/' . $item['id']) ?>', 'anggaran ini')"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <?php else: ?>
                            <span class="text-muted">-</span>
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

<!-- Summary Card Filter JavaScript -->
<?php if (!empty($anggaran)): ?>
<style>
.summary-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
}
.summary-card:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 6px 20px rgba(0,0,0,0.25) !important;
}
.summary-card.active-filter {
    transform: scale(1.02) !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.35) !important;
    opacity: 1 !important;
}
.summary-card.inactive-filter {
    opacity: 0.7;
}
</style>

<script>
// Wait for DataTables to be fully initialized
setTimeout(function() {
    initSummaryCardFilters();
}, 500);

function initSummaryCardFilters() {
    // Get DataTable instance
    var $table = $('.data-table');
    if (!$table.length || !$.fn.DataTable.isDataTable($table)) {
        console.log('DataTable not ready, retrying...');
        setTimeout(initSummaryCardFilters, 300);
        return;
    }
    
    var table = $table.DataTable();
    var currentFilter = 'all';
    
    console.log('Summary card filters initialized');
    
    // Summary card click handler
    $('.summary-card').off('click').on('click', function() {
        var filterValue = $(this).data('filter');
        var label = $(this).data('label');
        
        console.log('Filtering by:', filterValue);
        
        // Update active state visuals
        $('.summary-card').removeClass('active-filter').addClass('inactive-filter');
        $(this).removeClass('inactive-filter').addClass('active-filter');
        
        // Apply filter
        if (filterValue === 'all') {
            table.column(1).search('').draw();
            $('#activeFilterIndicator').slideUp(200);
            $('.summary-card').removeClass('inactive-filter active-filter');
            currentFilter = 'all';
        } else {
            // Search in column 1 (Kode Rekening) - simpler approach
            table.column(1).search(filterValue).draw();
            $('#filterLabel').text(label);
            $('#activeFilterIndicator').slideDown(200);
            currentFilter = filterValue;
        }
        
        // Scroll to filter indicator
        if ($('#activeFilterIndicator').is(':visible')) {
            $('html, body').animate({
                scrollTop: $('#activeFilterIndicator').offset().top - 100
            }, 300);
        }
    });
}

// Clear filter function (global)
function clearFilter() {
    var $table = $('.data-table');
    if ($.fn.DataTable.isDataTable($table)) {
        var table = $table.DataTable();
        table.column(1).search('').draw();
    }
    
    $('#activeFilterIndicator').slideUp(200);
    
    // Reset all cards
    $('.summary-card').removeClass('inactive-filter active-filter');
}
</script>
<?php endif; ?>

<?= view('layout/htmx_layout_end', get_defined_vars()) ?>


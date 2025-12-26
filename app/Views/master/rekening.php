<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Page Header -->
<div class="mb-4">
    <h2 class="mb-1"><i class="fas fa-sitemap text-primary"></i> Referensi Rekening</h2>
    <p class="text-muted mb-0">Daftar Kode Rekening (Chart of Accounts) - Permendagri No. 20 Tahun 2018</p>
</div>

<!-- Filter & Info Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 3px solid rgba(255,255,255,0.3);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-1 fw-bold">Total Rekening</h6>
                        <h2 class="mb-0 fw-bold"><?= count($rekening) ?></h2>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-list-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-body">
                <h6 class="text-muted mb-2">Filter Level:</h6>
                <select class="form-select" id="levelFilter">
                    <option value="">Semua Level</option>
                    <option value="1">Level 1 - Akun</option>
                    <option value="2">Level 2 - Kelompok</option>
                    <option value="3">Level 3 - Jenis</option>
                    <option value="4">Level 4 - Objek</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-info">
            <div class="card-body">
                <h6 class="text-muted mb-2">Filter Jenis:</h6>
                <select class="form-select" id="jenisFilter">
                    <option value="">Semua Jenis</option>
                    <option value="4">4.x - Pendapatan</option>
                    <option value="5">5.x - Belanja</option>
                    <option value="6">6.x - Pembiayaan</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Rekening</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th style="width: 15%">Kode</th>
                        <th style="width: 55%">Uraian</th>
                        <th style="width: 10%" class="text-center">Level</th>
                        <th style="width: 20%">Parent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rekening)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fs-2 mb-2 d-block"></i>
                                Data rekening tidak ditemukan
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rekening as $rek): ?>
                        <tr data-level="<?= $rek['level'] ?>" data-kode="<?= substr($rek['kode_akun'], 0, 1) ?>">
                            <td>
                                <code class="fs-6"><?= esc($rek['kode_akun']) ?></code>
                            </td>
                            <td>
                                <?php
                                // Indentation based on level
                                $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $rek['level'] - 1);
                                $icon = [
                                    1 => 'fa-folder',
                                    2 => 'fa-folder-open',
                                    3 => 'fa-file-invoice',
                                    4 => 'fa-file'
                                ];
                                ?>
                                <?= $indent ?>
                                <i class="fas <?= $icon[$rek['level']] ?> me-1 text-primary"></i>
                                <strong><?= esc($rek['nama_akun']) ?></strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">Level <?= $rek['level'] ?></span>
                            </td>
                            <td>
                                <?php if ($rek['parent_id']): ?>
                                    <small class="text-muted">ID: <?= $rek['parent_id'] ?></small>
                                <?php else: ?>
                                    <small class="text-muted">-</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Legend -->
<div class="card mt-3">
    <div class="card-body">
        <h6 class="mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Keterangan Struktur Rekening</h6>
        <div class="row">
            <div class="col-md-3">
                <p class="mb-0">
                    <span class="badge bg-secondary me-2">Level 1</span>
                    <strong>Akun</strong> - Kategori tertinggi
                </p>
            </div>
            <div class="col-md-3">
                <p class="mb-0">
                    <span class="badge bg-secondary me-2">Level 2</span>
                    <strong>Kelompok</strong> - Sub kategori akun
                </p>
            </div>
            <div class="col-md-3">
                <p class="mb-0">
                    <span class="badge bg-secondary me-2">Level 3</span>
                    <strong>Jenis</strong> - Detail kelompok
                </p>
            </div>
            <div class="col-md-3">
                <p class="mb-0">
                    <span class="badge bg-secondary me-2">Level 4</span>
                    <strong>Objek</strong> - Rekening terendah
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Filter functionality
document.getElementById('levelFilter').addEventListener('change', function() {
    filterTable();
});

document.getElementById('jenisFilter').addEventListener('change', function() {
    filterTable();
});

function filterTable() {
    const levelFilter = document.getElementById('levelFilter').value;
    const jenisFilter = document.getElementById('jenisFilter').value;
    const rows = document.querySelectorAll('tbody tr[data-level]');
    
    rows.forEach(row => {
        let showRow = true;
        
        if (levelFilter && row.getAttribute('data-level') !== levelFilter) {
            showRow = false;
        }
        
        if (jenisFilter && row.getAttribute('data-kode') !== jenisFilter) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}
</script>

<?= view('layout/footer') ?>

<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="fas fa-sitemap text-primary"></i> Struktur Organisasi</h2>
        <p class="text-muted mb-0">Manajemen Perangkat Desa dan Struktur Organisasi</p>
    </div>
    <a href="<?= base_url('/master/organisasi/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Perangkat
    </a>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-white shadow-lg filter-card active" 
             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); cursor: pointer;"
             data-filter="aktif"
             onclick="filterTable('aktif')"
             role="button">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-1 fw-bold">Perangkat Aktif</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['total_aktif'] ?></h2>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white shadow-lg filter-card" 
             style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); cursor: pointer;"
             data-filter="struktural"
             onclick="filterTable('struktural')"
             role="button">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-1 fw-bold">Struktural</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['struktural'] ?></h2>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white shadow-lg filter-card" 
             style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); cursor: pointer;"
             data-filter="kadus"
             onclick="filterTable('kadus')"
             role="button">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-1 fw-bold">Kepala Dusun</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['kadus'] ?></h2>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white shadow-lg filter-card" 
             style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); cursor: pointer;"
             data-filter="nonaktif"
             onclick="filterTable('nonaktif')"
             role="button">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-1 fw-bold">Non-Aktif</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['total_non_aktif'] ?></h2>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-user-slash"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Perangkat Desa</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 20%">Nama</th>
                        <th style="width: 15%">Jabatan</th>
                        <th style="width: 12%">NIP</th>
                        <th style="width: 15%">Pangkat/Gol</th>
                        <th style="width: 10%">Pendidikan</th>
                        <th style="width: 8%" class="text-center">Status</th>
                        <th style="width: 15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($perangkat)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fs-2 mb-2 d-block"></i>
                                Data perangkat belum ada
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($perangkat as $index => $p): ?>
                        <?php
                        // Determine category
                        $isStruktural = (
                            stripos($p['jabatan'], 'Kepala') !== false ||
                            stripos($p['jabatan'], 'Sekretaris') !== false ||
                            stripos($p['jabatan'], 'Kaur') !== false ||
                            stripos($p['jabatan'], 'Kasi') !== false
                        );
                        $isKadus = stripos($p['jabatan'], 'Dusun') !== false;
                        $category = '';
                        if ($isKadus) $category = 'kadus';
                        elseif ($isStruktural) $category = 'struktural';
                        
                        // Ensure boolean is properly converted (PostgreSQL returns 't'/'f' or true/false)
                        $isAktif = ($p['aktif'] === true || $p['aktif'] === 't' || $p['aktif'] === '1' || $p['aktif'] === 1);
                        ?>
                        <tr data-status="<?= $isAktif ? 'aktif' : 'nonaktif' ?>" 
                            data-category="<?= $category ?>">
                            <td><?= $index + 1 ?></td>
                            <td>
                                <strong><?= esc($p['nama']) ?></strong>
                                <?php if ($p['no_sk']): ?>
                                <br><small class="text-muted">SK: <?= esc($p['no_sk']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($p['jabatan']) ?></td>
                            <td>
                                <?php if ($p['nip']): ?>
                                    <code><?= esc($p['nip']) ?></code>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><small><?= esc($p['pangkat_golongan'] ?: '-') ?></small></td>
                            <td><small><?= esc($p['pendidikan'] ?: '-') ?></small></td>
                            <td class="text-center">
                                <?php if ($isAktif): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('/master/organisasi/edit/' . $p['id']) ?>" 
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="confirmDelete(<?= $p['id'] ?>, '<?= esc($p['nama']) ?>')" 
                                            class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.filter-card {
    transition: all 0.3s ease;
    opacity: 0.7;
}

.filter-card:hover {
    opacity: 1;
    transform: translateY(-5px);
}

.filter-card.active {
    opacity: 1;
    border: 3px solid rgba(255,255,255,0.5) !important;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3) !important;
}
</style>

<script>
function filterTable(filter) {
    // Update active card
    document.querySelectorAll('.filter-card').forEach(card => {
        card.classList.remove('active');
    });
    document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
    
    // Filter table
    const rows = document.querySelectorAll('tbody tr[data-status]');
    let visibleCount = 0;
    
    rows.forEach(row => {
        let show = false;
        
        switch(filter) {
            case 'aktif':
                show = row.dataset.status === 'aktif';
                break;
            case 'nonaktif':
                show = row.dataset.status === 'nonaktif';
                break;
            case 'struktural':
                show = row.dataset.status === 'aktif' && row.dataset.category === 'struktural';
                break;
            case 'kadus':
                show = row.dataset.status === 'aktif' && row.dataset.category === 'kadus';
                break;
        }
        
        if (show) {
            row.style.display = '';
            visibleCount++;
            // Update row number
            row.querySelector('td:first-child').textContent = visibleCount;
        } else {
            row.style.display = 'none';
        }
    });
}

function confirmDelete(id, nama) {
    Swal.fire({
        title: 'Hapus Perangkat?',
        text: `Yakin ingin menghapus ${nama}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `<?= base_url('/master/organisasi/delete/') ?>${id}`;
        }
    });
}

// Initialize on page load (show Aktif by default)
document.addEventListener('DOMContentLoaded', function() {
    filterTable('aktif');
});
</script>

<?= view('layout/footer') ?>

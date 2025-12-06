<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/perencanaan') ?>">Perencanaan</a></li>
            <li class="breadcrumb-item active">RPJM Desa</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-map me-2 text-primary"></i>RPJM Desa
            </h2>
            <p class="text-muted mb-0">Rencana Pembangunan Jangka Menengah Desa (6 Tahun)</p>
        </div>
        <a href="<?= base_url('/perencanaan/rpjm/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah RPJM
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- RPJM List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (empty($rpjmList)): ?>
            <div class="text-center py-5">
                <i class="fas fa-map fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada RPJM Desa</h5>
                <p class="text-muted">Buat RPJM Desa untuk memulai perencanaan pembangunan</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="rpjmTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Periode</th>
                            <th>Visi</th>
                            <th>RKP</th>
                            <th>Total Pagu</th>
                            <th>Status</th>
                            <th>Perdes</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rpjmList as $idx => $rpjm): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td>
                                <strong class="text-primary"><?= $rpjm['tahun_awal'] ?> - <?= $rpjm['tahun_akhir'] ?></strong>
                            </td>
                            <td>
                                <span title="<?= esc($rpjm['visi']) ?>">
                                    <?= esc(substr($rpjm['visi'] ?? '', 0, 80)) ?><?= strlen($rpjm['visi'] ?? '') > 80 ? '...' : '' ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info"><?= $rpjm['jumlah_rkp'] ?? 0 ?> RKP</span>
                            </td>
                            <td>Rp <?= number_format($rpjm['total_pagu'] ?? 0, 0, ',', '.') ?></td>
                            <td>
                                <?php
                                $statusColors = ['Draft' => 'secondary', 'Aktif' => 'success', 'Selesai' => 'dark'];
                                $color = $statusColors[$rpjm['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= $rpjm['status'] ?></span>
                            </td>
                            <td>
                                <?= esc($rpjm['nomor_perdes'] ?? '-') ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('/perencanaan/rpjm/detail/' . $rpjm['id']) ?>" 
                                       class="btn btn-outline-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('/perencanaan/rpjm/edit/' . $rpjm['id']) ?>" 
                                       class="btn btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="confirmDelete(<?= $rpjm['id'] ?>)" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
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

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Yakin hapus RPJM ini?',
        text: 'Data akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('/perencanaan/rpjm/delete/') ?>' + id, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(() => window.location.reload());
        }
    });
}
</script>

<?= view('layout/footer') ?>

<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-edit me-2 text-warning"></i>Perubahan Anggaran (PAK)
            </h2>
            <p class="text-muted mb-0">Daftar perubahan anggaran tahun <?= $tahun ?></p>
        </div>
        <?php if (isset($user['role']) && in_array($user['role'], ['Administrator', 'Operator Desa'])): ?>
        <a href="<?= base_url('/pak/create?tahun=' . $tahun) ?>" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Buat PAK Baru
        </a>
        <?php endif; ?>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Info Card -->
    <div class="card border-0 shadow-sm mb-4 bg-warning bg-opacity-10">
        <div class="card-body">
            <h6 class="text-warning"><i class="fas fa-info-circle me-2"></i>Tentang PAK</h6>
            <p class="text-muted mb-0">
                Perubahan Anggaran Kabupaten (PAK) adalah dokumen perubahan APBDes yang dibuat 
                ketika terjadi perubahan anggaran di tengah tahun berjalan. PAK harus disetujui 
                oleh Kepala Desa sebelum diterapkan ke APBDes.
            </p>
        </div>
    </div>

    <!-- PAK List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (empty($pakList)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-edit fs-1 mb-3"></i>
                <p class="mb-0">Belum ada perubahan anggaran untuk tahun <?= $tahun ?></p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Nomor PAK</th>
                            <th width="15%">Tanggal</th>
                            <th width="25%">Keterangan</th>
                            <th width="15%">Status</th>
                            <th width="10%">Dibuat</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pakList as $idx => $pak): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td>
                                <a href="<?= base_url('/pak/detail/' . $pak['id']) ?>">
                                    <strong><?= esc($pak['nomor_pak']) ?></strong>
                                </a>
                            </td>
                            <td><?= date('d/m/Y', strtotime($pak['tanggal_pak'])) ?></td>
                            <td><?= esc(substr($pak['keterangan'] ?? '-', 0, 50)) ?><?= strlen($pak['keterangan'] ?? '') > 50 ? '...' : '' ?></td>
                            <td>
                                <?php
                                $badgeClass = [
                                    'Draft' => 'secondary',
                                    'Disetujui' => 'success',
                                    'Ditolak' => 'danger'
                                ][$pak['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $badgeClass ?>"><?= $pak['status'] ?></span>
                            </td>
                            <td><?= esc($pak['created_by_name'] ?? '-') ?></td>
                            <td>
                                <a href="<?= base_url('/pak/detail/' . $pak['id']) ?>" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($pak['status'] == 'Draft' && isset($user['role']) && $user['role'] == 'Administrator'): ?>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="deletePak(<?= $pak['id'] ?>)" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
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

<script>
function deletePak(id) {
    Swal.fire({
        title: 'Hapus PAK?',
        text: 'Data PAK akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('/pak/delete') ?>/' + id, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Terhapus!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
}
</script>

<?= view('layout/footer') ?>

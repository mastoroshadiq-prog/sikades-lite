<?= view('layout/htmx_layout_start', get_defined_vars()) ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="fas fa-file-invoice text-primary"></i> SPP - Surat Permintaan Pembayaran</h2>
        <p class="text-muted mb-0">Kelola SPP dan pencairan anggaran</p>
    </div>
    <?php if (in_array($user['role'], ['Administrator', 'Operator Desa'])): ?>
    <div>
        <a href="<?= base_url('/spp/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Buat SPP Baru
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Filter Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <h6 class="text-muted mb-2">Filter Status:</h6>
                <select class="form-select" id="statusFilter" onchange="window.location.href='<?= base_url('/spp') ?>?status='+this.value+'&tahun=<?= $tahun ?>'">
                    <option value="">Semua Status</option>
                    <option value="Draft" <?= $status_filter == 'Draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="Verified" <?= $status_filter == 'Verified' ? 'selected' : '' ?>>Verified</option>
                    <option value="Approved" <?= $status_filter == 'Approved' ? 'selected' : '' ?>>Approved</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <h6 class="text-muted mb-2">Filter Tahun:</h6>
                <select class="form-select" id="tahunFilter" onchange="window.location.href='<?= base_url('/spp') ?>?status=<?= $status_filter ?>&tahun='+this.value">
                    <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                        <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body py-3">
                        <h6 class="text-white-50 mb-1">Total SPP</h6>
                        <h4 class="mb-0"><?= count($spp_list) ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body py-3">
                        <h6 class="text-white-50 mb-1">Draft</h6>
                        <h4 class="mb-0"><?= count(array_filter($spp_list, fn($s) => $s['status'] == 'Draft')) ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body py-3">
                        <h6 class="text-white-50 mb-1">Approved</h6>
                        <h4 class="mb-0"><?= count(array_filter($spp_list, fn($s) => $s['status'] == 'Approved')) ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SPP Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar SPP</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 15%">Nomor SPP</th>
                        <th style="width: 10%">Tanggal</th>
                        <th style="width: 30%">Uraian</th>
                        <th style="width: 15%" class="text-end">Jumlah</th>
                        <th style="width: 10%" class="text-center">Status</th>
                        <th style="width: 15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($spp_list)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fs-2 mb-2 d-block"></i>
                                Belum ada data SPP
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($spp_list as $spp): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= esc($spp['nomor_spp']) ?></strong>
                            </td>
                            <td>
                                <small><?= date('d/m/Y', strtotime($spp['tanggal_spp'])) ?></small>
                            </td>
                            <td>
                                <?= esc(substr($spp['uraian'], 0, 50)) ?>
                                <?= strlen($spp['uraian']) > 50 ? '...' : '' ?>
                            </td>
                            <td class="text-end">
                                <strong>Rp <?= number_format($spp['jumlah'], 0, ',', '.') ?></strong>
                            </td>
                            <td class="text-center">
                                <?php
                                $badgeClass = [
                                    'Draft' => 'bg-secondary',
                                    'Verified' => 'bg-primary',
                                    'Approved' => 'bg-success'
                                ];
                                ?>
                                <span class="badge <?= $badgeClass[$spp['status']] ?? 'bg-secondary' ?>">
                                    <?= $spp['status'] ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('/spp/detail/' . $spp['id']) ?>" 
                                       class="btn btn-outline-info" 
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <?php if (in_array($user['role'], ['Administrator', 'Operator Desa']) && $spp['status'] == 'Draft'): ?>
                                    <a href="<?= base_url('/spp/edit/' . $spp['id']) ?>" 
                                       class="btn btn-outline-primary" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if (in_array($user['role'], ['Administrator', 'Operator Desa']) && $spp['status'] == 'Draft'): ?>
                                    <button type="button" 
                                            class="btn btn-outline-success" 
                                            onclick="verifySpp(<?= $spp['id'] ?>)"
                                            title="Verifikasi">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if (in_array($user['role'], ['Administrator', 'Kepala Desa']) && $spp['status'] == 'Verified'): ?>
                                    <button type="button" 
                                            class="btn btn-outline-success" 
                                            onclick="approveSpp(<?= $spp['id'] ?>)"
                                            title="Setujui">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($user['role'] == 'Administrator' && $spp['status'] == 'Draft'): ?>
                                    <button type="button" 
                                            class="btn btn-outline-danger" 
                                            onclick="confirmDelete('<?= base_url('/spp/delete/' . $spp['id']) ?>', 'SPP <?= esc($spp['nomor_spp']) ?>')"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
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

<script>
// Verify SPP
function verifySpp(id) {
    Swal.fire({
        title: 'Verifikasi SPP?',
        text: 'SPP akan berstatus Verified setelah diverifikasi',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Verifikasi',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('/spp/verify/') ?>' + id, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal verifikasi SPP', 'error');
            });
        }
    });
}

// Approve SPP
function approveSpp(id) {
    Swal.fire({
        title: 'Setujui SPP?',
        text: 'SPP akan berstatus Approved dan dapat dicairkan',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Setujui',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('/spp/approve/') ?>' + id, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal menyetujui SPP', 'error');
            });
        }
    });
}
</script>

<?= view('layout/htmx_layout_end', get_defined_vars()) ?>

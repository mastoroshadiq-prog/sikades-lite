<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/pak') ?>">PAK</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-alt me-2 text-warning"></i><?= esc($pak['nomor_pak']) ?>
            </h2>
            <p class="text-muted mb-0">Detail Perubahan Anggaran</p>
        </div>
        <div>
            <?php if ($pak['status'] == 'Draft' && isset($user['role']) && in_array($user['role'], ['Administrator', 'Kepala Desa'])): ?>
            <button type="button" class="btn btn-success" onclick="approvePak(<?= $pak['id'] ?>)">
                <i class="fas fa-check me-2"></i>Setujui
            </button>
            <button type="button" class="btn btn-danger" onclick="rejectPak(<?= $pak['id'] ?>)">
                <i class="fas fa-times me-2"></i>Tolak
            </button>
            <?php endif; ?>
            <a href="<?= base_url('/pak') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Status Banner -->
    <?php
    $statusBanner = [
        'Draft' => ['bg-secondary', 'Menunggu Persetujuan'],
        'Disetujui' => ['bg-success', 'Disetujui - Sudah Diterapkan ke APBDes'],
        'Ditolak' => ['bg-danger', 'Ditolak'],
    ];
    $banner = $statusBanner[$pak['status']] ?? ['bg-secondary', $pak['status']];
    ?>
    <div class="alert <?= $banner[0] ?> text-white mb-4">
        <i class="fas fa-info-circle me-2"></i><strong>Status:</strong> <?= $banner[1] ?>
    </div>

    <!-- Info Card -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-3"><i class="fas fa-file-alt me-2"></i>Informasi PAK</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="40%">Nomor PAK</td>
                            <td>: <strong><?= esc($pak['nomor_pak']) ?></strong></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>: <?= date('d F Y', strtotime($pak['tanggal_pak'])) ?></td>
                        </tr>
                        <tr>
                            <td>Tahun Anggaran</td>
                            <td>: <?= $pak['tahun'] ?></td>
                        </tr>
                        <tr>
                            <td>Dibuat Oleh</td>
                            <td>: <?= esc($pak['created_by_name'] ?? '-') ?></td>
                        </tr>
                        <?php if ($pak['approved_by']): ?>
                        <tr>
                            <td>Disetujui/Ditolak Oleh</td>
                            <td>: <?= esc($pak['approved_by_name'] ?? '-') ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-3"><i class="fas fa-calculator me-2"></i>Ringkasan Perubahan</h6>
                    <div class="row text-center">
                        <div class="col-4">
                            <h5 class="text-muted mb-1">Anggaran Semula</h5>
                            <h4 class="text-primary">Rp <?= number_format($pak['total_sebelum'] ?? 0, 0, ',', '.') ?></h4>
                        </div>
                        <div class="col-4">
                            <h5 class="text-muted mb-1">Anggaran Menjadi</h5>
                            <h4 class="text-success">Rp <?= number_format($pak['total_sesudah'] ?? 0, 0, ',', '.') ?></h4>
                        </div>
                        <div class="col-4">
                            <h5 class="text-muted mb-1">Selisih</h5>
                            <?php $selisih = $pak['total_selisih'] ?? 0; ?>
                            <h4 class="<?= $selisih >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $selisih >= 0 ? '+' : '' ?>Rp <?= number_format($selisih, 0, ',', '.') ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Keterangan -->
    <?php if (!empty($pak['keterangan'])): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="text-muted"><i class="fas fa-comment-alt me-2"></i>Keterangan</h6>
            <p class="mb-0"><?= nl2br(esc($pak['keterangan'])) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Detail Items -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Detail Perubahan (<?= count($pak['items'] ?? []) ?> item)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Kode Rekening</th>
                            <th width="25%">Uraian</th>
                            <th width="10%">Sumber</th>
                            <th width="15%" class="text-end">Semula</th>
                            <th width="15%" class="text-end">Menjadi</th>
                            <th width="13%" class="text-end">Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pak['items'])): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada data perubahan</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($pak['items'] as $idx => $item): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><code><?= esc($item['kode_akun']) ?></code></td>
                            <td>
                                <?= esc($item['nama_anggaran'] ?? $item['nama_akun']) ?>
                                <?php if (!empty($item['keterangan'])): ?>
                                <br><small class="text-muted"><?= esc($item['keterangan']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= ['DDS' => 'success', 'ADD' => 'primary', 'PAD' => 'info', 'Bankeu' => 'warning'][$item['sumber_dana']] ?? 'secondary' ?>">
                                    <?= esc($item['sumber_dana']) ?>
                                </span>
                            </td>
                            <td class="text-end">Rp <?= number_format($item['anggaran_sebelum'], 0, ',', '.') ?></td>
                            <td class="text-end fw-bold">Rp <?= number_format($item['anggaran_sesudah'], 0, ',', '.') ?></td>
                            <td class="text-end <?= $item['selisih'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $item['selisih'] >= 0 ? '+' : '' ?>Rp <?= number_format($item['selisih'], 0, ',', '.') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="4" class="text-end">TOTAL</td>
                            <td class="text-end">Rp <?= number_format($pak['total_sebelum'] ?? 0, 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($pak['total_sesudah'] ?? 0, 0, ',', '.') ?></td>
                            <td class="text-end <?= ($pak['total_selisih'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= ($pak['total_selisih'] ?? 0) >= 0 ? '+' : '' ?>Rp <?= number_format($pak['total_selisih'] ?? 0, 0, ',', '.') ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function approvePak(id) {
    Swal.fire({
        title: 'Setujui PAK?',
        html: '<p>PAK akan disetujui dan perubahan akan langsung diterapkan ke APBDes.</p><p class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Tindakan ini tidak dapat dibatalkan!</p>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Setujui!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('/pak/approve') ?>/' + id, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Disetujui!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
}

function rejectPak(id) {
    Swal.fire({
        title: 'Tolak PAK?',
        text: 'PAK akan ditolak dan tidak akan diterapkan ke APBDes.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Tolak!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('/pak/reject') ?>/' + id, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Ditolak!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
}
</script>

<?= view('layout/footer') ?>

<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-list me-2 text-primary"></i>Daftar Inventaris Aset
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/aset') ?>">SIPADES</a></li>
                    <li class="breadcrumb-item active">Daftar Aset</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('/aset/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Aset
            </a>
            <a href="<?= base_url('/aset/print-kir') ?>" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-print me-2"></i>Cetak KIR
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $filters['kategori'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= esc($cat['kode_golongan']) ?> - <?= esc($cat['nama_golongan']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kondisi</label>
                    <select name="kondisi" class="form-select">
                        <option value="">Semua Kondisi</option>
                        <option value="Baik" <?= $filters['kondisi'] == 'Baik' ? 'selected' : '' ?>>Baik</option>
                        <option value="Rusak Ringan" <?= $filters['kondisi'] == 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
                        <option value="Rusak Berat" <?= $filters['kondisi'] == 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="tahun" class="form-control" 
                           value="<?= $filters['tahun'] ?>" 
                           placeholder="Semua">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="<?= base_url('/aset/list') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Asset List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="asetTable">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Register</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Tahun</th>
                            <th>Kondisi</th>
                            <th class="text-end">Nilai (Rp)</th>
                            <th>Lokasi</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($asetList)): ?>
                            <?php foreach ($asetList as $aset): ?>
                            <tr>
                                <td>
                                    <code class="text-primary fw-bold"><?= esc($aset['kode_register']) ?></code>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if ($aset['foto']): ?>
                                            <?php 
                                            // Extract just the filename from the path
                                            $fotoFilename = basename($aset['foto']);
                                            ?>
                                            <img src="<?= base_url('/assets/image/' . $fotoFilename) ?>" 
                                                 class="rounded me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;"
                                                 onerror="this.onerror=null; this.src=''; this.parentElement.innerHTML='<div class=\'bg-secondary bg-opacity-10 rounded me-2 d-flex align-items-center justify-content-center\' style=\'width: 40px; height: 40px;\'><i class=\'fas fa-box text-secondary\'></i></div>' + this.parentElement.innerHTML.split(this.outerHTML)[1];">
                                        <?php else: ?>
                                            <div class="bg-secondary bg-opacity-10 rounded me-2 d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-box text-secondary"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?= esc($aset['nama_barang']) ?></strong>
                                            <?php if ($aset['merk_type']): ?>
                                                <br><small class="text-muted"><?= esc($aset['merk_type']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= esc($aset['kode_golongan']) ?>
                                    </span>
                                    <br><small><?= esc($aset['nama_golongan']) ?></small>
                                </td>
                                <td><?= $aset['tahun_perolehan'] ?></td>
                                <td>
                                    <?php
                                    $kondisiClass = [
                                        'Baik' => 'success',
                                        'Rusak Ringan' => 'warning',
                                        'Rusak Berat' => 'danger',
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $kondisiClass[$aset['kondisi']] ?? 'secondary' ?>">
                                        <?= $aset['kondisi'] ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <?= number_format($aset['harga_perolehan'], 0, ',', '.') ?>
                                </td>
                                <td>
                                    <?php if ($aset['lokasi']): ?>
                                        <small><?= esc($aset['lokasi']) ?></small>
                                    <?php else: ?>
                                        <small class="text-muted">-</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('/aset/detail/' . $aset['id']) ?>" 
                                           class="btn btn-outline-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('/aset/edit/' . $aset['id']) ?>" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirmDelete(<?= $aset['id'] ?>, '<?= esc($aset['nama_barang']) ?>')" 
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">Tidak ada data aset</h5>
                                    <p class="text-muted">Belum ada aset yang sesuai dengan filter</p>
                                    <a href="<?= base_url('/aset/create') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Tambah Aset
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($asetList)): ?>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5" class="text-end">Total <?= count($asetList) ?> Aset</th>
                            <th class="text-end">
                                <?php 
                                $totalNilai = array_sum(array_column($asetList, 'harga_perolehan'));
                                ?>
                                Rp <?= number_format($totalNilai, 0, ',', '.') ?>
                            </th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus aset:</p>
                <p class="fw-bold" id="deleteAssetName"></p>
                <p class="text-danger">
                    <small><i class="fas fa-warning me-1"></i>Tindakan ini tidak dapat dibatalkan!</small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, nama) {
    document.getElementById('deleteAssetName').textContent = nama;
    document.getElementById('deleteForm').action = '<?= base_url('/aset/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?= view('layout/footer') ?>

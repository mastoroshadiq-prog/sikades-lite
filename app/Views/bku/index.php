<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="fas fa-book text-primary"></i> BKU - Buku Kas Umum</h2>
        <p class="text-muted mb-0">Pencatatan transaksi kas masuk dan keluar</p>
    </div>
    <?php if ($this->hasRole(['Administrator', 'Operator Desa'])): ?>
    <div>
        <a href="<?= base_url('/bku/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Transaksi
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-1">Total Debet</h6>
                <h4 class="mb-0">Rp <?= number_format($total_debet, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-1">Total Kredit</h6>
                <h4 class="mb-0">Rp <?= number_format($total_kredit, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body">
                <h6 class="text-white-50 mb-1">Saldo</h6>
                <h4 class="mb-0">Rp <?= number_format($saldo, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <h6 class="text-muted mb-2">Filter:</h6>
                <select class="form-select form-select-sm" onchange="window.location.href='<?= base_url('/bku') ?>?tahun='+this.value">
                    <?php for ($y = date('Y') - 2; $y <= date('Y') +1; $y++): ?>
                        <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- BKU Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Transaksi BKU</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered data-table">
                <thead class="table-light">
                    <tr>
                        <th width="3%">No</th>
                        <th width="8%">Tanggal</th>
                        <th width="10%">No. Bukti</th>
                        <th width="25%">Uraian</th>
                        <th width="15%">Rekening</th>
                        <th width="8%">Jenis</th>
                        <th width="11%" class="text-end">Debet</th>
                        <th width="11%" class="text-end">Kredit</th>
                        <th width="11%" class="text-end">Saldo</th>
                        <th width="8%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bku_entries)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fs-2 mb-2 d-block"></i>
                                Belum ada transaksi BKU
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($bku_entries as $bku): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($bku['tanggal'])) ?></td>
                            <td><small><?= esc($bku['no_bukti']) ?></small></td>
                            <td><?= esc($bku['uraian']) ?></td>
                            <td>
                                <small class="text-muted"><?= $bku['kode_akun'] ?? '-' ?></small><br>
                                <small><?= $bku['nama_akun'] ?? '-' ?></small>
                            </td>
                            <td>
                                <?php
                                $badgeClass = [
                                    'Pendapatan' => 'bg-success',
                                    'Belanja' => 'bg-danger',
                                    'Mutasi' => 'bg-warning'
                                ];
                                ?>
                                <span class="badge <?= $badgeClass[$bku['jenis_transaksi']] ?? 'bg-secondary' ?>">
                                    <?= $bku['jenis_transaksi'] ?>
                                </span>
                            </td>
                            <td class="text-end"><?= $bku['debet'] > 0 ? number_format($bku['debet'], 0, ',', '.') : '-' ?></td>
                            <td class="text-end"><?= $bku['kredit'] > 0 ? number_format($bku['kredit'], 0, ',', '.') : '-' ?></td>
                            <td class="text-end"><strong>Rp <?= number_format($bku['saldo_kumulatif'], 0, ',', '.') ?></strong></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if ($this->hasRole(['Administrator', 'Operator Desa'])): ?>
                                    <a href="<?= base_url('/bku/edit/' . $bku['id']) ?>" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if ($this->hasRole(['Administrator'])): ?>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="confirmDelete('<?= base_url('/bku/delete/' . $bku['id']) ?>', 'transaksi BKU')"
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

<?= view('layout/footer') ?>

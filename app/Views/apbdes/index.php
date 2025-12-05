<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="fas fa-file-invoice-dollar text-primary"></i> APBDes</h2>
        <p class="text-muted mb-0">Anggaran Pendapatan dan Belanja Desa</p>
    </div>
    <div>
        <?php if ($user['role'] == 'Administrator' || $user['role'] == 'Operator Desa'): ?>
        <a href="<?= base_url('/apbdes/create') ?>" class="btn btn-primary">
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

<!-- APBDes Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Anggaran Tahun <?= $tahun ?></h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 15%">Kode Rekening</th>
                        <th style="width: 30%">Nama Rekening</th>
                        <th style="width: 25%">Uraian</th>
                        <th style="width: 10%">Sumber Dana</th>
                        <th style="width: 15%" class="text-end">Anggaran</th>
                        <?php if ($user['role'] == 'Administrator' || $user['role'] == 'Operator Desa'): ?>
                        <th style="width: 10%" class="text-center">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($anggaran)): ?>
                        <tr>
                            <td colspan="<?= ($user['role'] == 'Administrator' || $user['role'] == 'Operator Desa') ? '7' : '6' ?>" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fs-2 mb-2 d-block"></i>
                                Belum ada data anggaran untuk tahun <?= $tahun ?>
                            </td>
                        </tr>
                    <?php else: ?>
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
                            <?php if ($user['role'] == 'Administrator' || $user['role'] == 'Operator Desa'): ?>
                            <td class="text-center">
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
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<?php if (!empty($anggaran)): ?>
<div class="row g-4 mt-4">
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
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="opacity-75">Total Pendapatan</h6>
                <h4 class="mb-0">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="opacity-75">Total Belanja</h6>
                <h4 class="mb-0">Rp <?= number_format($totalBelanja, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="opacity-75">Total Pembiayaan</h6>
                <h4 class="mb-0">Rp <?= number_format($totalPembiayaan, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card <?= $surplus >= 0 ? 'bg-primary' : 'bg-warning' ?> text-white">
            <div class="card-body">
                <h6 class="opacity-75"><?= $surplus >= 0 ? 'Surplus' : 'Defisit' ?></h6>
                <h4 class="mb-0">Rp <?= number_format(abs($surplus), 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= view('layout/footer') ?>

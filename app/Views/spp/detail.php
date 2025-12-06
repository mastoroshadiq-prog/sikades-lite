<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="fas fa-file-invoice text-primary"></i> Detail SPP</h2>
        <p class="text-muted mb-0"><?= esc($spp['nomor_spp']) ?></p>
    </div>
    <div>
        <a href="<?= base_url('/spp') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
        <button onclick="window.print()" class="btn btn-info">
            <i class="fas fa-print me-2"></i>Cetak
        </button>
    </div>
</div>

<!-- SPP Detail -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informasi SPP</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Nomor SPP:</strong></td>
                        <td><?= esc($spp['nomor_spp']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal:</strong></td>
                        <td><?= date('d F Y', strtotime($spp['tanggal_spp'])) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Uraian:</strong></td>
                        <td><?= esc($spp['uraian']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <?php
                            $badgeClass = [
                                'Draft' => 'bg-secondary',
                                'Verified' => 'bg-primary',
                                'Approved' => 'bg-success'
                            ];
                            ?>
                            <span class="badge <?= $badgeClass[$spp['status']] ?>">
                                <?= $spp['status'] ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Total:</strong></td>
                        <td><h4 class="text-primary mb-0">Rp <?= number_format($spp['jumlah'], 0, ',', '.') ?></h4></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Rincian SPP -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Rincian SPP</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="45%">Anggaran</th>
                                <th width="30%">Uraian</th>
                                <th width="20%" class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($spp['rincian'])): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($spp['rincian'] as $item): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <small class="text-muted"><?= $item['kode_akun'] ?></small><br>
                                            <strong><?= $item['nama_akun'] ?></strong>
                                        </td>
                                        <td><?= esc($item['uraian']) ?></td>
                                        <td class="text-end">Rp <?= number_format($item['jumlah'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="table-light">
                                    <td colspan="3" class="text-end"><strong class="reference">Total:</strong></td>
                                    <td class="text-end"><strong>Rp <?= number_format($spp['jumlah'], 0, ',', '.') ?></strong></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Approval Timeline -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Status Persetujuan</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <!-- Created -->
                    <div class="mb-3">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="fas fa-circle text-success"></i>
                            </div>
                            <div class="flex-grow-1">
                                <strong>Dibuat</strong><br>
                                <small class="text-muted">
                                    <?= date('d M Y, H:i', strtotime($spp['created_at'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Verified -->
                    <?php if ($spp['status'] == 'Verified' || $spp['status'] == 'Approved'): ?>
                    <div class="mb-3">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="fas fa-circle text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <strong>Diverifikasi</strong><br>
                                <small class="text-muted">Operator</small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Approved -->
                    <?php if ($spp['status'] == 'Approved'): ?>
                    <div>
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="fas fa-circle text-success"></i>
                            </div>
                            <div class="flex-grow-1">
                                <strong>Disetujui</strong><br>
                                <small class="text-muted">Kepala Desa</small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

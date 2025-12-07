<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/tutup-buku') ?>">Tutup Buku</a></li>
            <li class="breadcrumb-item active">Preview <?= $tahun ?></li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-search me-2 text-warning"></i>Preview Tutup Buku Tahun <?= $tahun ?>
            </h2>
            <p class="text-muted mb-0">Periksa data sebelum melakukan tutup buku</p>
        </div>
        <a href="<?= base_url('/tutup-buku') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <!-- Warnings -->
    <?php if (!empty($warnings)): ?>
    <div class="alert alert-warning">
        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Peringatan</h6>
        <ul class="mb-0">
            <?php foreach ($warnings as $warning): ?>
            <li><?= $warning ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Summary Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Ringkasan Keuangan Tahun <?= $tahun ?></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">Saldo Awal (1 Januari <?= $tahun ?>)</td>
                            <td class="text-end fw-bold fs-5">Rp <?= number_format($summary['saldo_awal'], 0, ',', '.') ?></td>
                        </tr>
                        <tr class="text-success">
                            <td><i class="fas fa-plus-circle me-2"></i>Total Pendapatan</td>
                            <td class="text-end fw-bold fs-5">Rp <?= number_format($summary['total_pendapatan'], 0, ',', '.') ?></td>
                        </tr>
                        <tr class="text-danger">
                            <td><i class="fas fa-minus-circle me-2"></i>Total Belanja</td>
                            <td class="text-end fw-bold fs-5">Rp <?= number_format($summary['total_belanja'], 0, ',', '.') ?></td>
                        </tr>
                        <tr class="table-primary">
                            <td class="fw-bold">Saldo Akhir (31 Desember <?= $tahun ?>)</td>
                            <td class="text-end fw-bold fs-4 text-primary">Rp <?= number_format($summary['saldo_akhir'], 0, ',', '.') ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="bg-light rounded p-4 h-100">
                        <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Statistik</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Jumlah Transaksi BKU:</span>
                            <strong><?= $txCount ?> transaksi</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Jumlah SPP:</span>
                            <strong><?= $sppCount ?> dokumen</strong>
                        </div>
                        <?php if ($pendingSpp > 0): ?>
                        <div class="d-flex justify-content-between text-warning">
                            <span>SPP Belum Approved:</span>
                            <strong><?= $pendingSpp ?> dokumen</strong>
                        </div>
                        <?php endif; ?>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Tahun Sebelumnya:</span>
                            <strong>
                                <?php if ($prevYearClosed): ?>
                                <span class="text-success"><i class="fas fa-check-circle"></i> Closed</span>
                                <?php else: ?>
                                <span class="text-warning"><i class="fas fa-exclamation-circle"></i> Belum Closed</span>
                                <?php endif; ?>
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Konfirmasi Tutup Buku</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Perhatian!</h6>
                <p>Setelah tutup buku dilakukan:</p>
                <ul>
                    <li>Semua transaksi BKU tahun <?= $tahun ?> akan <strong>dikunci</strong> dan tidak dapat diubah</li>
                    <li>Semua SPP tahun <?= $tahun ?> akan <strong>dikunci</strong></li>
                    <li>Anggaran (APBDes) tahun <?= $tahun ?> akan <strong>dikunci</strong></li>
                    <li>Saldo akhir <strong>Rp <?= number_format($summary['saldo_akhir'], 0, ',', '.') ?></strong> akan menjadi saldo awal tahun <?= $tahun + 1 ?></li>
                </ul>
                <p class="mb-0"><strong>Tindakan ini tidak dapat dibatalkan dengan mudah!</strong></p>
            </div>

            <form action="<?= base_url('/tutup-buku/process') ?>" method="POST" id="closingForm">
                <?= csrf_field() ?>
                <input type="hidden" name="tahun" value="<?= $tahun ?>">
                
                <div class="mb-3">
                    <label class="form-label">Catatan (opsional)</label>
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan untuk tutup buku ini..."></textarea>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="confirmCheck" required>
                    <label class="form-check-label" for="confirmCheck">
                        Saya memahami bahwa tindakan ini akan mengunci semua data tahun <?= $tahun ?> dan tidak dapat dibatalkan dengan mudah.
                    </label>
                </div>

                <div class="d-flex gap-2">
                    <a href="<?= base_url('/tutup-buku') ?>" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-danger" id="submitBtn" disabled>
                        <i class="fas fa-lock me-2"></i>Proses Tutup Buku Tahun <?= $tahun ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('confirmCheck').addEventListener('change', function() {
    document.getElementById('submitBtn').disabled = !this.checked;
});

document.getElementById('closingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Konfirmasi Final',
        text: 'Apakah Anda yakin ingin menutup buku tahun <?= $tahun ?>?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Tutup Buku!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
    });
});
</script>

<?= view('layout/footer') ?>

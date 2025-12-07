<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-book-reader me-2 text-danger"></i>Tutup Buku Akhir Tahun
            </h2>
            <p class="text-muted mb-0">Proses penutupan buku kas dan transfer saldo antar tahun anggaran</p>
        </div>
    </div>

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

    <!-- Current Year Summary -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Summary Tahun <?= $currentYear ?></h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="border-end">
                        <h6 class="text-muted mb-2">Saldo Awal</h6>
                        <h4 class="text-info mb-0">Rp <?= number_format($currentSummary['saldo_awal'], 0, ',', '.') ?></h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-end">
                        <h6 class="text-muted mb-2">Total Pendapatan</h6>
                        <h4 class="text-success mb-0">Rp <?= number_format($currentSummary['total_pendapatan'], 0, ',', '.') ?></h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-end">
                        <h6 class="text-muted mb-2">Total Belanja</h6>
                        <h4 class="text-danger mb-0">Rp <?= number_format($currentSummary['total_belanja'], 0, ',', '.') ?></h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-2">Saldo Akhir</h6>
                    <h4 class="text-primary mb-0">Rp <?= number_format($currentSummary['saldo_akhir'], 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Year List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Daftar Tahun Anggaran</h5>
        </div>
        <div class="card-body">
            <?php if (empty($years)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada data transaksi</h5>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="15%">Tahun</th>
                            <th>Status</th>
                            <th class="text-end">Saldo Awal</th>
                            <th class="text-end">Pendapatan</th>
                            <th class="text-end">Belanja</th>
                            <th class="text-end">Saldo Akhir</th>
                            <th width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($years as $year): ?>
                        <?php 
                            $record = $year['record'];
                            $summary = $record ? [
                                'saldo_awal' => $record['saldo_awal'],
                                'total_pendapatan' => $record['total_pendapatan'],
                                'total_belanja' => $record['total_belanja'],
                                'saldo_akhir' => $record['saldo_akhir']
                            ] : null;
                        ?>
                        <tr>
                            <td>
                                <strong class="fs-5"><?= $year['tahun'] ?></strong>
                                <?php if ($year['tahun'] == $currentYear): ?>
                                <span class="badge bg-info ms-1">Berjalan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($year['status'] == 'Closed'): ?>
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-lock me-1"></i>Closed
                                </span>
                                <?php elseif ($year['status'] == 'Proses'): ?>
                                <span class="badge bg-warning px-3 py-2">
                                    <i class="fas fa-spinner fa-spin me-1"></i>Proses
                                </span>
                                <?php else: ?>
                                <span class="badge bg-secondary px-3 py-2">
                                    <i class="fas fa-unlock me-1"></i>Open
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?= $summary ? 'Rp ' . number_format($summary['saldo_awal'], 0, ',', '.') : '-' ?>
                            </td>
                            <td class="text-end text-success">
                                <?= $summary ? 'Rp ' . number_format($summary['total_pendapatan'], 0, ',', '.') : '-' ?>
                            </td>
                            <td class="text-end text-danger">
                                <?= $summary ? 'Rp ' . number_format($summary['total_belanja'], 0, ',', '.') : '-' ?>
                            </td>
                            <td class="text-end fw-bold">
                                <?= $summary ? 'Rp ' . number_format($summary['saldo_akhir'], 0, ',', '.') : '-' ?>
                            </td>
                            <td>
                                <?php if ($year['status'] == 'Closed'): ?>
                                <a href="<?= base_url('/tutup-buku/detail/' . $year['tahun']) ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye me-1"></i>Detail
                                </a>
                                <?php if (session()->get('role') == 'Administrator'): ?>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="confirmReopen(<?= $year['tahun'] ?>)">
                                    <i class="fas fa-unlock"></i>
                                </button>
                                <?php endif; ?>
                                <?php else: ?>
                                <a href="<?= base_url('/tutup-buku/preview/' . $year['tahun']) ?>" class="btn btn-sm btn-danger">
                                    <i class="fas fa-book me-1"></i>Tutup Buku
                                </a>
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

    <!-- Info Card -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body">
            <h6 class="text-primary"><i class="fas fa-info-circle me-2"></i>Tentang Tutup Buku</h6>
            <ul class="text-muted mb-0">
                <li><strong>Open:</strong> Tahun masih bisa dimodifikasi (tambah/edit/hapus transaksi)</li>
                <li><strong>Closed:</strong> Tahun sudah ditutup, semua data terkunci dan tidak bisa diubah</li>
                <li>Saldo akhir tahun yang ditutup akan menjadi saldo awal tahun berikutnya</li>
                <li>Sebaiknya tutup buku dilakukan setelah semua transaksi tahun tersebut selesai (akhir Desember)</li>
            </ul>
        </div>
    </div>
</div>

<!-- Reopen Form (hidden) -->
<form id="reopenForm" action="<?= base_url('/tutup-buku/reopen') ?>" method="POST" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="tahun" id="reopenTahun">
</form>

<script>
function confirmReopen(tahun) {
    Swal.fire({
        title: 'Buka Kembali Tahun ' + tahun + '?',
        html: '<p class="text-danger"><strong>PERINGATAN!</strong></p>' +
              '<p>Membuka kembali tahun yang sudah ditutup akan:</p>' +
              '<ul class="text-start"><li>Mengijinkan perubahan data tahun tersebut</li>' +
              '<li>Mempengaruhi saldo awal tahun berikutnya</li></ul>' +
              '<p>Pastikan Anda yakin dengan tindakan ini.</p>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Buka Kembali',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('reopenTahun').value = tahun;
            document.getElementById('reopenForm').submit();
        }
    });
}
</script>

<?= view('layout/footer') ?>

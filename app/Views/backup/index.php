<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-database me-2 text-primary"></i>Backup & Restore Database
            </h2>
            <p class="text-muted mb-0">Kelola backup database aplikasi</p>
        </div>
        <form action="<?= base_url('/backup/create') ?>" method="POST" class="d-inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-success" onclick="return confirm('Buat backup database sekarang?')">
                <i class="fas fa-plus me-2"></i>Buat Backup Baru
            </button>
        </form>
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
    <div class="card border-0 shadow-sm mb-4 bg-info bg-opacity-10">
        <div class="card-body">
            <h6 class="text-info"><i class="fas fa-info-circle me-2"></i>Informasi Backup</h6>
            <p class="text-muted mb-0">
                Backup database secara berkala untuk mencegah kehilangan data. File backup disimpan di server 
                dan dapat diunduh untuk penyimpanan eksternal. Pastikan untuk melakukan backup sebelum 
                melakukan perubahan besar pada sistem.
            </p>
        </div>
    </div>

    <!-- Backup List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Backup (<?= count($backups) ?> file)</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($backups)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-folder-open fs-1 mb-3"></i>
                <p class="mb-0">Belum ada file backup. Klik "Buat Backup Baru" untuk membuat backup.</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="40%">Nama File</th>
                            <th width="15%">Ukuran</th>
                            <th width="20%">Tanggal</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($backups as $idx => $backup): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td>
                                <i class="fas fa-file-code text-success me-2"></i>
                                <strong><?= esc($backup['filename']) ?></strong>
                            </td>
                            <td>
                                <?php 
                                $size = $backup['size'];
                                if ($size >= 1048576) {
                                    echo number_format($size / 1048576, 2) . ' MB';
                                } else {
                                    echo number_format($size / 1024, 2) . ' KB';
                                }
                                ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($backup['date'])) ?></td>
                            <td>
                                <a href="<?= base_url('/backup/download/' . $backup['filename']) ?>" 
                                   class="btn btn-sm btn-primary" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-warning" title="Restore"
                                        onclick="confirmRestore('<?= esc($backup['filename']) ?>')">
                                    <i class="fas fa-undo"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus"
                                        onclick="deleteBackup('<?= esc($backup['filename']) ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
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

<!-- Restore Form (Hidden) -->
<form id="restoreForm" action="<?= base_url('/backup/restore') ?>" method="POST" style="display:none">
    <?= csrf_field() ?>
    <input type="hidden" name="filename" id="restoreFilename">
</form>

<script>
function confirmRestore(filename) {
    Swal.fire({
        title: 'Restore Database?',
        html: `<p>Anda akan me-restore database dari backup:</p><strong>${filename}</strong><p class="text-danger mt-2"><i class="fas fa-exclamation-triangle me-2"></i>PERINGATAN: Data saat ini akan diganti dengan data dari backup ini!</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f0ad4e',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Restore!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('restoreFilename').value = filename;
            document.getElementById('restoreForm').submit();
        }
    });
}

function deleteBackup(filename) {
    Swal.fire({
        title: 'Hapus Backup?',
        text: 'File backup: ' + filename + ' akan dihapus!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('/backup/delete') ?>/' + filename, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Terhapus!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Gagal menghapus backup', 'error'));
        }
    });
}
</script>

<?= view('layout/footer') ?>

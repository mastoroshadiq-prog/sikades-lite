<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-truck-moving me-2 text-warning"></i>Pencatatan Pindah Keluar
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/demografi') ?>">Demografi</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/demografi/mutasi') ?>">Mutasi</a></li>
                    <li class="breadcrumb-item active">Pindah Keluar</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning py-3">
                    <h5 class="mb-0"><i class="fas fa-truck-moving me-2"></i>Form Pencatatan Pindah Keluar</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('/demografi/mutasi/pindah/save') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <?php if (isset($penduduk)): ?>
                        <!-- Pre-filled penduduk -->
                        <input type="hidden" name="penduduk_id" value="<?= $penduduk['id'] ?>">
                        
                        <div class="alert alert-secondary">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="text-muted small">NIK</label>
                                    <p class="mb-0 fw-bold"><?= esc($penduduk['nik']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Nama Lengkap</label>
                                    <p class="mb-0 fw-bold"><?= esc($penduduk['nama_lengkap']) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <!-- Search penduduk -->
                        <div class="mb-4">
                            <label class="form-label">Cari Penduduk <span class="text-danger">*</span></label>
                            <select name="penduduk_id" class="form-select select2-search" required 
                                    data-placeholder="Ketik NIK atau Nama...">
                                <option value=""></option>
                            </select>
                            <small class="text-muted">Ketik minimal 3 karakter untuk mencari</small>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Pindah <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_peristiwa" class="form-control" 
                                       value="<?= date('Y-m-d') ?>" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Keterangan / Alamat Tujuan</label>
                                <textarea name="keterangan" class="form-control" rows="3" 
                                          placeholder="Alamat tujuan pindah, alasan pindah, dll"></textarea>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('/demografi/mutasi') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Simpan Data Pindah
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-info"></i>Informasi</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Catatan:</strong>
                        <p class="mb-0 mt-2">Pencatatan pindah keluar akan mengubah status penduduk menjadi <strong>PINDAH</strong>.</p>
                    </div>
                    
                    <h6>Yang akan terjadi:</h6>
                    <ul class="mb-0">
                        <li>Status penduduk berubah menjadi <span class="badge bg-warning text-dark">PINDAH</span></li>
                        <li>Penduduk tidak lagi masuk statistik aktif</li>
                        <li>Dikeluarkan dari daftar penerima bantuan</li>
                        <li>Tercatat dalam riwayat mutasi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

<?php if (!isset($penduduk)): ?>
<script>
// Initialize Select2 with AJAX search
$(document).ready(function() {
    $('select[name="penduduk_id"]').select2({
        ajax: {
            url: '<?= base_url('/demografi/api/search') ?>',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return { q: params.term };
            },
            processResults: function(data) {
                return { results: data };
            },
            cache: true
        },
        minimumInputLength: 3,
        placeholder: 'Ketik NIK atau Nama...',
        allowClear: true,
        theme: 'bootstrap-5'
    });
});
</script>
<?php endif; ?>

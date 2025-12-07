<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-import me-2 text-primary"></i>Import Data Penduduk
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/demografi') ?>">Demografi</a></li>
                    <li class="breadcrumb-item active">Import Data</li>
                </ol>
            </nav>
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-upload me-2 text-success"></i>Upload File</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('/demografi/import/process') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="mb-4">
                            <label class="form-label">Pilih File <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control form-control-lg" 
                                   accept=".csv,.xlsx,.xls" required>
                            <small class="text-muted">Format yang didukung: CSV, Excel (.xlsx, .xls). Maksimal 5MB.</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-lightbulb me-2"></i>Tips Import:</h6>
                            <ul class="mb-0">
                                <li>Download template terlebih dahulu untuk memastikan format benar</li>
                                <li>NIK dan No KK harus 16 digit</li>
                                <li>Tanggal lahir dalam format YYYY-MM-DD (contoh: 1990-01-15)</li>
                                <li>Jenis kelamin diisi dengan L (Laki-laki) atau P (Perempuan)</li>
                                <li>Jika No KK sudah ada, penduduk akan ditambahkan ke KK tersebut</li>
                            </ul>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('/demografi') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-upload me-2"></i>Import Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Download Template -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-download me-2"></i>Template</h5>
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-file-csv fa-4x text-success mb-3"></i>
                    <h5>Download Template CSV</h5>
                    <p class="text-muted mb-3">Gunakan template ini untuk memastikan format data sesuai</p>
                    <a href="<?= base_url('/demografi/import/template') ?>" class="btn btn-success btn-lg">
                        <i class="fas fa-download me-2"></i>Download Template
                    </a>
                </div>
            </div>
            
            <!-- Kolom yang Diperlukan -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-list me-2 text-info"></i>Kolom yang Diperlukan</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kolom</th>
                                <th>Wajib</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>NO_KK</td>
                                <td><span class="badge bg-danger">Ya</span></td>
                            </tr>
                            <tr>
                                <td>NIK</td>
                                <td><span class="badge bg-danger">Ya</span></td>
                            </tr>
                            <tr>
                                <td>NAMA_LENGKAP</td>
                                <td><span class="badge bg-danger">Ya</span></td>
                            </tr>
                            <tr>
                                <td>JENIS_KELAMIN</td>
                                <td><span class="badge bg-danger">Ya</span></td>
                            </tr>
                            <tr>
                                <td>TEMPAT_LAHIR</td>
                                <td><span class="badge bg-secondary">Tidak</span></td>
                            </tr>
                            <tr>
                                <td>TANGGAL_LAHIR</td>
                                <td><span class="badge bg-secondary">Tidak</span></td>
                            </tr>
                            <tr>
                                <td>AGAMA</td>
                                <td><span class="badge bg-secondary">Tidak</span></td>
                            </tr>
                            <tr>
                                <td>PENDIDIKAN</td>
                                <td><span class="badge bg-secondary">Tidak</span></td>
                            </tr>
                            <tr>
                                <td>PEKERJAAN</td>
                                <td><span class="badge bg-secondary">Tidak</span></td>
                            </tr>
                            <tr>
                                <td>ALAMAT, RT, RW, DUSUN</td>
                                <td><span class="badge bg-secondary">Tidak</span></td>
                            </tr>
                            <tr>
                                <td>IS_MISKIN (0/1)</td>
                                <td><span class="badge bg-secondary">Tidak</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

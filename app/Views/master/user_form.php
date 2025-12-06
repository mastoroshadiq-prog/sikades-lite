<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-user-<?= isset($user) ? 'edit' : 'plus' ?> text-primary"></i>
            <?= isset($user) ? 'Edit User' : 'Tambah User' ?>
        </h2>
        <p class="text-muted mb-0">Form manajemen user</p>
    </div>
    <div>
        <a href="<?= base_url('/master/users') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<!-- Form Card -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Form <?= isset($user) ? 'Edit' : 'Input' ?> User
                </h5>
            </div>
            <div class="card-body">
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Terjadi Kesalahan:</h6>
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="<?= isset($user) ? base_url('/master/users/update/' . $user['id']) : base_url('/master/users/save') ?>" 
                      method="POST" 
                      id="formUser">
                    <?= csrf_field() ?>
                    
                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            Username <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="username" 
                               id="username" 
                               class="form-control" 
                               required 
                               <?= isset($user) ? 'readonly' : '' ?>
                               placeholder="Username untuk login"
                               value="<?= isset($user) ? esc($user['username']) : old('username') ?>">
                        <?php if (isset($user)): ?>
                        <small class="form-text text-muted">Username tidak dapat diubah</small>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Password -->
                    <?php if (!isset($user)): ?>
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control" 
                               required 
                               placeholder="Minimum 6 karakter">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">
                            Konfirmasi Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" 
                               name="password_confirm" 
                               id="password_confirm" 
                               class="form-control" 
                               required 
                               placeholder="Ulangi password">
                    </div>
                    <?php else: ?>
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password Baru <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small>
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control" 
                               placeholder="Password baru (opsional)">
                    </div>
                    <?php endif; ?>
                    
                    <!-- Role -->
                    <div class="mb-3">
                        <label for="role" class="form-label">
                            Role <span class="text-danger">*</span>
                        </label>
                        <select name="role" 
                                id="role" 
                                class="form-select" 
                                required>
                            <option value="">-- Pilih Role --</option>
                            <option value="Administrator" <?= (isset($user) && $user['role'] == 'Administrator') ? 'selected' : '' ?>>
                                Administrator
                            </option>
                            <option value="Operator Desa" <?= (isset($user) && $user['role'] == 'Operator Desa') ? 'selected' : '' ?>>
                                Operator Desa
                            </option>
                            <option value="Kepala Desa" <?= (isset($user) && $user['role'] == 'Kepala Desa') ? 'selected' : '' ?>>
                                Kepala Desa
                            </option>
                        </select>
                    </div>
                    
                    <!-- Kode Desa -->
                    <div class="mb-4">
                        <label for="kode_desa" class="form-label">
                            Kode Desa <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="kode_desa" 
                               id="kode_desa" 
                               class="form-control" 
                               required 
                               placeholder="Contoh: 3201012001"
                               value="<?= isset($user) ? esc($user['kode_desa']) : old('kode_desa', '3201012001') ?>">
                        <small class="form-text text-muted">
                            Kode desa 10 digit sesuai Kemendagri
                        </small>
                    </div>
                    
                    <hr>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('/master/users') ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            <?= isset($user) ? 'Update' : 'Simpan' ?> User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Info Sidebar -->
    <div class="col-lg-4">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Role</h6>
            </div>
            <div class="card-body">
                <h6 class="text-primary">Administrator:</h6>
                <ul class="small mb-3">
                    <li>Akses penuh sistem</li>
                    <li>Kelola user</li>
                    <li>Kelola master data</li>
                    <li>Input & approve transaksi</li>
                </ul>
                
                <h6 class="text-primary">Operator Desa:</h6>
                <ul class="small mb-3">
                    <li>Input APBDes</li>
                    <li>Buat SPP</li>
                    <li>Input BKU</li>
                    <li>Tidak bisa approve</li>
                </ul>
                
                <h6 class="text-primary">Kepala Desa:</h6>
                <ul class="small mb-0">
                    <li>View dashboard & laporan</li>
                    <li>Approve SPP</li>
                    <li>Tidak bisa input data</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Password confirmation validation
    document.getElementById('formUser').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirm');
        
        if (passwordConfirm && password !== passwordConfirm.value) {
            e.preventDefault();
            showToast('error', 'Validasi Gagal', 'Password dan konfirmasi password tidak sama');
            return false;
        }
        
        if (password && password.length < 6) {
            e.preventDefault();
            showToast('error', 'Validasi Gagal', 'Password minimal 6 karakter');
            return false;
        }
    });
</script>

<?= view('layout/footer') ?>

<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="fas fa-users text-primary"></i> Manajemen User</h2>
        <p class="text-muted mb-0">Kelola pengguna sistem</p>
    </div>
    <div>
        <a href="<?= base_url('/master/users/create') ?>" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>Tambah User
        </a>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar User</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 20%">Username</th>
                        <th style="width: 25%">Role</th>
                        <th style="width: 20%">Kode Desa</th>
                        <th style="width: 20%">Dibuat</th>
                        <th style="width: 10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fs-2 mb-2 d-block"></i>
                                Belum ada data user
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= esc($user['username']) ?></strong>
                            </td>
                            <td>
                                <?php
                                $badgeClass = [
                                    'Administrator' => 'bg-danger',
                                    'Operator Desa' => 'bg-primary',
                                    'Kepala Desa' => 'bg-success'
                                ];
                                ?>
                                <span class="badge <?= $badgeClass[$user['role']] ?? 'bg-secondary' ?>">
                                    <?= esc($user['role']) ?>
                                </span>
                            </td>
                            <td><?= esc($user['kode_desa']) ?></td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d M Y H:i', strtotime($user['created_at'])) ?>
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('/master/users/edit/' . $user['id']) ?>" 
                                       class="btn btn-outline-primary" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($user['id'] != session()->get('user_id')): ?>
                                    <button type="button" 
                                            class="btn btn-outline-danger" 
                                            onclick="confirmDelete('<?= base_url('/master/users/delete/' . $user['id']) ?>', 'user <?= esc($user['username']) ?>')"
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

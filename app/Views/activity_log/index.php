<?= view('layout/header') ?>
<?= view('layout/sidebar') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-history me-2 text-primary"></i>Activity Log
            </h2>
            <p class="text-muted mb-0">Riwayat aktivitas pengguna sistem</p>
        </div>
        <div>
            <span class="badge bg-primary fs-6">
                <i class="fas fa-list me-1"></i>Total: <?= number_format($total) ?> aktivitas
            </span>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= base_url('activity-log') ?>" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Filter</label>
                    <select name="filter" class="form-select" onchange="this.form.submit()">
                        <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Semua</option>
                        <option value="today" <?= $filter === 'today' ? 'selected' : '' ?>>Hari Ini</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Module</label>
                    <select name="module" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Module</option>
                        <?php foreach ($modules as $mod): ?>
                            <option value="<?= $mod ?>" <?= $selectedModule === $mod ? 'selected' : '' ?>>
                                <?= ucfirst($mod) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="<?= $selectedDate ?>" onchange="this.form.submit()">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <a href="<?= base_url('activity-log') ?>" class="btn btn-outline-secondary d-block">
                        <i class="fas fa-sync"></i> Reset Filter
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Log Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (empty($logs)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada aktivitas ditemukan</h5>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">Waktu</th>
                                <th width="15%">User</th>
                                <th width="10%">Module</th>
                                <th width="10%">Action</th>
                                <th width="35%">Description</th>
                                <th width="10%">IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $idx => $log): ?>
                                <tr>
                                    <td class="text-muted">
                                        <?= (($currentPage - 1) * $perPage) + $idx + 1 ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('d/m/Y', strtotime($log['created_at'])) ?>
                                        </small>
                                        <br>
                                        <small class="fw-bold">
                                            <?= date('H:i:s', strtotime($log['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <i class="fas fa-user text-muted me-1"></i>
                                        <?= esc($log['user_name'] ?? $log['username'] ?? 'System') ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= esc($log['module']) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $actionColors = [
                                            'create' => 'success',
                                            'update' => 'primary',
                                            'delete' => 'danger',
                                            'login' => 'info',
                                            'logout' => 'secondary',
                                            'view' => 'light',
                                            'export' => 'warning',
                                            'verify' => 'info',
                                            'approve' => 'success'
                                        ];
                                        $color = $actionColors[$log['action']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= ucfirst($log['action']) ?></span>
                                    </td>
                                    <td>
                                        <?= esc($log['description'] ?? '-') ?>
                                        <?php if ($log['data_before'] || $log['data_after']): ?>
                                            <br>
                                            <button class="btn btn-sm btn-link p-0" type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#detail-<?= $log['id'] ?>">
                                                <small>Lihat Detail</small>
                                            </button>
                                            <div class="collapse mt-2" id="detail-<?= $log['id'] ?>">
                                                <?php if ($log['data_before']): ?>
                                                    <small class="d-block text-muted">
                                                        <strong>Before:</strong>
                                                        <code class="d-block" style="font-size:10px;max-height:100px;overflow:auto">
                                                            <?= esc($log['data_before']) ?>
                                                        </code>
                                                    </small>
                                                <?php endif; ?>
                                                <?php if ($log['data_after']): ?>
                                                    <small class="d-block text-muted">
                                                        <strong>After:</strong>
                                                        <code class="d-block" style="font-size:10px;max-height:100px;overflow:auto">
                                                            <?= esc($log['data_after']) ?>
                                                        </code>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= esc($log['ip_address']) ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?>&filter=<?= $filter ?>&module=<?= $selectedModule ?>&date=<?= $selectedDate ?>">
                                    Previous
                                </a>
                            </li>
                            <?php 
                            $start = max(1, $currentPage - 2);
                            $end = min($totalPages, $currentPage + 2);
                            for ($i = $start; $i <= $end; $i++): 
                            ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&filter=<?= $filter ?>&module=<?= $selectedModule ?>&date=<?= $selectedDate ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>&filter=<?= $filter ?>&module=<?= $selectedModule ?>&date=<?= $selectedDate ?>">
                                    Next
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>

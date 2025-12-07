    <!-- Sidebar -->
    <div class="sidebar position-fixed" style="width: 250px; top: 56px; left: 0;">
        <nav class="nav flex-column py-3">
            <a class="nav-link <?= (uri_string() == 'dashboard') ? 'active' : '' ?>" href="<?= base_url('/dashboard') ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            
            <div class="nav-item">
                <div class="px-3 py-2 text-muted small fw-bold">PERENCANAAN</div>
            </div>
            
            <a class="nav-link <?= (strpos(uri_string(), 'perencanaan') !== false && strpos(uri_string(), 'rpjm') === false && strpos(uri_string(), 'rkp') === false) ? 'active' : '' ?>" href="<?= base_url('/perencanaan') ?>">
                <i class="fas fa-project-diagram"></i> Dashboard Perencanaan
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'perencanaan/rpjm') !== false) ? 'active' : '' ?>" href="<?= base_url('/perencanaan/rpjm') ?>">
                <i class="fas fa-map"></i> RPJM Desa
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'perencanaan/rkp') !== false) ? 'active' : '' ?>" href="<?= base_url('/perencanaan/rkp') ?>">
                <i class="fas fa-calendar-alt"></i> RKP Desa
            </a>
            
            <div class="nav-item">
                <div class="px-3 py-2 text-muted small fw-bold">DATA ENTRI</div>
            </div>
            
            <a class="nav-link <?= (strpos(uri_string(), 'apbdes') !== false) ? 'active' : '' ?>" href="<?= base_url('/apbdes') ?>">
                <i class="fas fa-file-invoice-dollar"></i> Penganggaran (APBDes)
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'spp') !== false) ? 'active' : '' ?>" href="<?= base_url('/spp') ?>">
                <i class="fas fa-file-invoice"></i> SPP
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'bku') !== false) ? 'active' : '' ?>" href="<?= base_url('/bku') ?>">
                <i class="fas fa-book"></i> Buku Kas Umum (BKU)
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'pajak') !== false) ? 'active' : '' ?>" href="<?= base_url('/pajak') ?>">
                <i class="fas fa-receipt"></i> Pajak
            </a>
            
            <div class="nav-item">
                <div class="px-3 py-2 text-muted small fw-bold">LAPORAN</div>
            </div>
            
            <a class="nav-link <?= (strpos(uri_string(), 'report') !== false) ? 'active' : '' ?>" href="<?= base_url('/report') ?>">
                <i class="fas fa-file-alt"></i> Semua Laporan
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'report/bku') !== false) ? 'active' : '' ?>" href="<?= base_url('/report/bku') ?>">
                <i class="fas fa-book"></i> Laporan BKU
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'report/lra') !== false) ? 'active' : '' ?>" href="<?= base_url('/report/lra') ?>">
                <i class="fas fa-chart-line"></i> Realisasi Anggaran
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'lpj') !== false) ? 'active' : '' ?>" href="<?= base_url('/lpj') ?>">
                <i class="fas fa-file-signature"></i> Laporan LPJ
            </a>
            
            <?php if (isset($user['role']) && in_array($user['role'], ['Administrator', 'Operator Desa'])): ?>
            <div class="nav-item">
                <div class="px-3 py-2 text-muted small fw-bold">PENGATURAN</div>
            </div>
            
            <a class="nav-link <?= (strpos(uri_string(), 'master/desa') !== false) ? 'active' : '' ?>" href="<?= base_url('/master/desa') ?>">
                <i class="fas fa-building"></i> Data Desa
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'master/rekening') !== false) ? 'active' : '' ?>" href="<?= base_url('/master/rekening') ?>">
                <i class="fas fa-list-alt"></i> Referensi Rekening
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'tutup-buku') !== false) ? 'active' : '' ?>" href="<?= base_url('/tutup-buku') ?>">
                <i class="fas fa-book-reader"></i> Tutup Buku
            </a>
            <?php endif; ?>
            
            <?php if (isset($user['role']) && $user['role'] == 'Administrator'): ?>
            <a class="nav-link <?= (strpos(uri_string(), 'master/users') !== false) ? 'active' : '' ?>" href="<?= base_url('/master/users') ?>">
                <i class="fas fa-users"></i> Manajemen User
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'activity-log') !== false) ? 'active' : '' ?>" href="<?= base_url('/activity-log') ?>">
                <i class="fas fa-history"></i> Activity Log
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'backup') !== false) ? 'active' : '' ?>" href="<?= base_url('/backup') ?>">
                <i class="fas fa-database"></i> Backup Database
            </a>
            <?php endif; ?>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="content-wrapper">

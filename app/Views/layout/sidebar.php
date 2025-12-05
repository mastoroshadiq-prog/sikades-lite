    <!-- Sidebar -->
    <div class="sidebar position-fixed" style="width: 250px; top: 56px; left: 0;">
        <nav class="nav flex-column py-3">
            <a class="nav-link <?= (uri_string() == 'dashboard') ? 'active' : '' ?>" href="<?= base_url('/dashboard') ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            
            <div class="nav-item">
                <div class="px-3 py-2 text-muted small fw-bold">DATA ENTRI</div>
            </div>
            
            <a class="nav-link <?= (strpos(uri_string(), 'apbdes') !== false) ? 'active' : '' ?>" href="<?= base_url('/apbdes') ?>">
                <i class="fas fa-file-invoice-dollar"></i> Penganggaran (APBDes)
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'penatausahaan/spp') !== false) ? 'active' : '' ?>" href="<?= base_url('/penatausahaan/spp') ?>">
                <i class="fas fa-file-invoice"></i> SPP
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'penatausahaan/bku') !== false) ? 'active' : '' ?>" href="<?= base_url('/penatausahaan/bku') ?>">
                <i class="fas fa-book"></i> Buku Kas Umum (BKU)
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'penatausahaan/pajak') !== false) ? 'active' : '' ?>" href="<?= base_url('/penatausahaan/pajak') ?>">
                <i class="fas fa-receipt"></i> Pajak
            </a>
            
            <div class="nav-item">
                <div class="px-3 py-2 text-muted small fw-bold">LAPORAN</div>
            </div>
            
            <a class="nav-link <?= (strpos(uri_string(), 'laporan/bku') !== false) ? 'active' : '' ?>" href="<?= base_url('/laporan/bku') ?>">
                <i class="fas fa-file-alt"></i> Laporan BKU
            </a>
            
            <a class="nav-link <?= (strpos(uri_string(), 'laporan/realisasi') !== false) ? 'active' : '' ?>" href="<?= base_url('/laporan/realisasi') ?>">
                <i class="fas fa-chart-bar"></i> Laporan Realisasi
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
            <?php endif; ?>
            
            <?php if (isset($user['role']) && $user['role'] == 'Administrator'): ?>
            <a class="nav-link <?= (strpos(uri_string(), 'master/users') !== false) ? 'active' : '' ?>" href="<?= base_url('/master/users') ?>">
                <i class="fas fa-users"></i> Manajemen User
            </a>
            <?php endif; ?>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="content-wrapper">

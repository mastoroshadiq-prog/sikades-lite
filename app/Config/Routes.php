<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Public Routes
$routes->get('/', 'Home::index');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attemptLogin');
$routes->get('/logout', 'Auth::logout');

// Asset Images (serve from writable folder)
$routes->get('/assets/image/(:any)', 'Assets::image/$1');

// Protected Routes (Require Authentication)
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('/dashboard', 'Dashboard::index');
    
    // Perencanaan Routes (NEW - Planning Module)
    $routes->group('perencanaan', function($routes) {
        $routes->get('/', 'Perencanaan::index');
        
        // RPJM Desa
        $routes->get('rpjm', 'Perencanaan::rpjm');
        $routes->get('rpjm/create', 'Perencanaan::rpjmCreate');
        $routes->post('rpjm/save', 'Perencanaan::rpjmSave');
        $routes->get('rpjm/edit/(:num)', 'Perencanaan::rpjmEdit/$1');
        $routes->post('rpjm/update/(:num)', 'Perencanaan::rpjmUpdate/$1');
        $routes->get('rpjm/detail/(:num)', 'Perencanaan::rpjmDetail/$1');
        $routes->delete('rpjm/delete/(:num)', 'Perencanaan::rpjmDelete/$1');
        
        // RKP Desa
        $routes->get('rkp', 'Perencanaan::rkp');
        $routes->get('rkp/create', 'Perencanaan::rkpCreate');
        $routes->post('rkp/save', 'Perencanaan::rkpSave');
        $routes->get('rkp/edit/(:num)', 'Perencanaan::rkpEdit/$1');
        $routes->post('rkp/update/(:num)', 'Perencanaan::rkpUpdate/$1');
        $routes->get('rkp/detail/(:num)', 'Perencanaan::rkpDetail/$1');
        $routes->delete('rkp/delete/(:num)', 'Perencanaan::rkpDelete/$1');
        
        // Kegiatan
        $routes->get('kegiatan/create/(:num)', 'Perencanaan::kegiatanCreate/$1');
        $routes->post('kegiatan/save', 'Perencanaan::kegiatanSave');
        $routes->get('kegiatan/edit/(:num)', 'Perencanaan::kegiatanEdit/$1');
        $routes->post('kegiatan/update/(:num)', 'Perencanaan::kegiatanUpdate/$1');
        $routes->delete('kegiatan/delete/(:num)', 'Perencanaan::kegiatanDelete/$1');
    });
    
    // Master Data Routes
    $routes->group('master', function($routes) {
        // Data Desa
        $routes->get('desa', 'Master::desa');
        $routes->post('desa/save', 'Master::saveDesa');
        
        // Users Management
        $routes->get('users', 'Master::users');
        $routes->get('users/create', 'Master::createUser');
        $routes->post('users/save', 'Master::saveUser');
        $routes->get('users/edit/(:num)', 'Master::editUser/$1');
        $routes->post('users/update/(:num)', 'Master::updateUser/$1');
        $routes->delete('users/delete/(:num)', 'Master::deleteUser/$1');
        
        // Rekening (Chart of Accounts)
        $routes->get('rekening', 'Master::rekening');
        $routes->post('rekening/import', 'Master::importRekening');
    });
    
    // APBDes Routes (Budgeting)
    $routes->group('apbdes', function($routes) {
        $routes->get('/', 'Apbdes::index');
        $routes->get('create', 'Apbdes::create');
        $routes->post('save', 'Apbdes::save');
        $routes->get('edit/(:num)', 'Apbdes::edit/$1');
        $routes->post('update/(:num)', 'Apbdes::update/$1');
        $routes->delete('delete/(:num)', 'Apbdes::delete/$1');
        $routes->get('report', 'Apbdes::report');
        
        // Import from RKP Kegiatan
        $routes->get('import', 'Apbdes::importFromKegiatan');
        $routes->post('import/process', 'Apbdes::processImport');
        $routes->get('linked', 'Apbdes::linkedKegiatan');
    });
    
    // PAK Routes (Perubahan Anggaran)
    $routes->group('pak', function($routes) {
        $routes->get('/', 'Pak::index');
        $routes->get('create', 'Pak::create');
        $routes->post('save', 'Pak::save');
        $routes->get('detail/(:num)', 'Pak::detail/$1');
        $routes->post('approve/(:num)', 'Pak::approve/$1');
        $routes->post('reject/(:num)', 'Pak::reject/$1');
        $routes->delete('delete/(:num)', 'Pak::delete/$1');
    });
    
    // SPP Routes (Payment Request)
    $routes->group('spp', function($routes) {
        $routes->get('/', 'Spp::index');
        $routes->get('create', 'Spp::create');
        $routes->post('save', 'Spp::save');
        $routes->get('edit/(:num)', 'Spp::edit/$1');
        $routes->post('update/(:num)', 'Spp::update/$1');
        $routes->get('detail/(:num)', 'Spp::detail/$1');
        $routes->post('verify/(:num)', 'Spp::verify/$1');
        $routes->post('approve/(:num)', 'Spp::approve/$1');
        $routes->delete('delete/(:num)', 'Spp::delete/$1');
        $routes->get('kuitansi/(:num)', 'Spp::kuitansi/$1');
    });
    
    // BKU Routes (Buku Kas Umum)
    $routes->group('bku', function($routes) {
        $routes->get('/', 'Bku::index');
        $routes->get('create', 'Bku::create');
        $routes->post('save', 'Bku::save');
        $routes->get('edit/(:num)', 'Bku::edit/$1');
        $routes->post('update/(:num)', 'Bku::update/$1');
        $routes->delete('delete/(:num)', 'Bku::delete/$1');
        $routes->get('report', 'Bku::report');
    });
    
    // Pajak Routes (Tax Recording)
    $routes->group('pajak', function($routes) {
        $routes->get('/', 'Pajak::index');
        $routes->get('create', 'Pajak::create');
        $routes->post('save', 'Pajak::save');
        $routes->get('edit/(:num)', 'Pajak::edit/$1');
        $routes->post('update/(:num)', 'Pajak::update/$1');
        $routes->delete('delete/(:num)', 'Pajak::delete/$1');
        $routes->post('bayar/(:num)', 'Pajak::bayar/$1');
    });
    
    // Penatausahaan Routes (Administration)
    $routes->group('penatausahaan', function($routes) {
        // SPP (Payment Request)
        $routes->get('spp', 'Penatausahaan::spp');
        $routes->get('spp/create', 'Penatausahaan::createSpp');
        $routes->post('spp/save', 'Penatausahaan::saveSpp');
        $routes->get('spp/detail/(:num)', 'Penatausahaan::detailSpp/$1');
        $routes->post('spp/approve/(:num)', 'Penatausahaan::approveSpp/$1');
        $routes->delete('spp/delete/(:num)', 'Penatausahaan::deleteSpp/$1');
        
        // BKU (General Cash Book)
        $routes->get('bku', 'Penatausahaan::bku');
        $routes->get('bku/create', 'Penatausahaan::createBku');
        $routes->post('bku/save', 'Penatausahaan::saveBku');
        $routes->get('bku/edit/(:num)', 'Penatausahaan::editBku/$1');
        $routes->post('bku/update/(:num)', 'Penatausahaan::updateBku/$1');
        $routes->delete('bku/delete/(:num)', 'Penatausahaan::deleteBku/$1');
        
        // Pajak (Tax)
        $routes->get('pajak', 'Penatausahaan::pajak');
        $routes->post('pajak/save', 'Penatausahaan::savePajak');
    });
    
    // Upload Routes (Bukti Transaksi)
    $routes->group('upload', function($routes) {
        $routes->post('bku/(:num)', 'Upload::bku/$1');
        $routes->post('spp/(:num)', 'Upload::spp/$1');
        $routes->get('view/(:segment)/(:any)', 'Upload::view/$1/$2');
        $routes->delete('(:segment)/(:num)', 'Upload::delete/$1/$2');
    });
    
    // Modern Report Routes (Phase 4)
    $routes->group('report', function($routes) {
        $routes->get('/', 'Report::index');
        $routes->get('bku', 'Report::bku');
        $routes->get('apbdes', 'Report::apbdes');
        $routes->get('lra', 'Report::lra');
        $routes->get('spp/(:num)', 'Report::spp/$1');
        $routes->get('pajak', 'Report::pajak');
    });
    
    // Legacy Laporan Routes (Compatibility)
    $routes->group('laporan', function($routes) {
        $routes->get('bku', 'Laporan::bku');
        $routes->get('bku/pdf', 'Laporan::bkuPdf');
        $routes->get('realisasi', 'Laporan::realisasi');
        $routes->get('realisasi/pdf', 'Laporan::realisasiPdf');
    });
    
    // Activity Log Routes
    $routes->get('activity-log', 'ActivityLog::index');
    $routes->get('activity-log/summary', 'ActivityLog::summary');
    $routes->post('activity-log/clear', 'ActivityLog::clearOld');
    
    // Tutup Buku Routes (Year-End Closing)
    $routes->get('tutup-buku', 'TutupBuku::index');
    $routes->get('tutup-buku/preview/(:num)', 'TutupBuku::preview/$1');
    $routes->post('tutup-buku/process', 'TutupBuku::process');
    $routes->get('tutup-buku/detail/(:num)', 'TutupBuku::detail/$1');
    $routes->post('tutup-buku/reopen', 'TutupBuku::reopen');
    $routes->get('tutup-buku/summary/(:num)', 'TutupBuku::getSummary/$1');
    
    // LPJ Routes (Laporan Pertanggungjawaban)
    $routes->get('lpj', 'Lpj::index');
    $routes->get('lpj/semester/(:num)', 'Lpj::semester/$1');
    $routes->get('lpj/pdf/(:num)', 'Lpj::exportPdf/$1');
    
    // ===========================================
    // SIPADES - Sistem Pengelolaan Aset Desa
    // ===========================================
    $routes->group('aset', function($routes) {
        $routes->get('/', 'Aset::index');
        $routes->get('list', 'Aset::list');
        $routes->get('create', 'Aset::create');
        $routes->post('store', 'Aset::store');
        $routes->get('detail/(:num)', 'Aset::detail/$1');
        $routes->get('edit/(:num)', 'Aset::edit/$1');
        $routes->post('update/(:num)', 'Aset::update/$1');
        $routes->delete('delete/(:num)', 'Aset::delete/$1');
        $routes->get('json', 'Aset::getJsonData'); // For WebGIS
        $routes->get('print-kir', 'Aset::printKir');
    });
    
    // Backup Routes (Database Backup & Restore)
    $routes->get('backup', 'Backup::index');
    $routes->post('backup/create', 'Backup::create');
    $routes->get('backup/download/(:any)', 'Backup::download/$1');
    $routes->delete('backup/delete/(:any)', 'Backup::delete/$1');
    $routes->post('backup/restore', 'Backup::restore');
    
    // Settings
    $routes->get('/profile', 'User::profile');
    $routes->post('/profile/update', 'User::updateProfile');
    
    // ===========================================
    // DEMOGRAFI - Sistem Data Kependudukan
    // ===========================================
    $routes->group('demografi', function($routes) {
        // Dashboard
        $routes->get('/', 'Demografi::index');
        
        // Keluarga (KK)
        $routes->get('keluarga', 'Demografi::keluarga');
        $routes->get('keluarga/create', 'Demografi::createKeluarga');
        $routes->post('keluarga/save', 'Demografi::saveKeluarga');
        $routes->get('keluarga/detail/(:num)', 'Demografi::detailKeluarga/$1');
        $routes->get('keluarga/edit/(:num)', 'Demografi::editKeluarga/$1');
        $routes->post('keluarga/update/(:num)', 'Demografi::updateKeluarga/$1');
        $routes->delete('keluarga/delete/(:num)', 'Demografi::deleteKeluarga/$1');
        
        // Penduduk
        $routes->get('penduduk', 'Demografi::penduduk');
        $routes->get('penduduk/create', 'Demografi::createPenduduk');
        $routes->get('penduduk/create/(:num)', 'Demografi::createPenduduk/$1');
        $routes->post('penduduk/save', 'Demografi::savePenduduk');
        $routes->get('penduduk/detail/(:num)', 'Demografi::detailPenduduk/$1');
        $routes->get('penduduk/edit/(:num)', 'Demografi::editPenduduk/$1');
        $routes->post('penduduk/update/(:num)', 'Demografi::updatePenduduk/$1');
        
        // Mutasi
        $routes->get('mutasi', 'Demografi::mutasi');
        $routes->get('mutasi/kematian', 'Demografi::catatKematian');
        $routes->get('mutasi/kematian/(:num)', 'Demografi::catatKematian/$1');
        $routes->post('mutasi/kematian/save', 'Demografi::saveKematian');
        $routes->get('mutasi/pindah', 'Demografi::catatPindah');
        $routes->get('mutasi/pindah/(:num)', 'Demografi::catatPindah/$1');
        $routes->post('mutasi/pindah/save', 'Demografi::savePindah');
        
        // Import/Export
        $routes->get('import', 'Demografi::import');
        $routes->post('import/process', 'Demografi::processImport');
        $routes->get('import/template', 'Demografi::downloadTemplate');
        
        // API
        $routes->get('api/search', 'Demografi::searchPenduduk');
        $routes->get('blt-eligible', 'Demografi::bltEligible');
    });
    
    // ===========================================
    // BUMDES - Sistem Akuntansi BUMDes
    // ===========================================
    $routes->group('bumdes', function($routes) {
        // Dashboard
        $routes->get('/', 'Bumdes::index');
        
        // Unit Usaha
        $routes->get('unit', 'Bumdes::units');
        $routes->get('unit/create', 'Bumdes::createUnit');
        $routes->post('unit/save', 'Bumdes::saveUnit');
        $routes->get('unit/detail/(:num)', 'Bumdes::detailUnit/$1');
        $routes->get('unit/edit/(:num)', 'Bumdes::editUnit/$1');
        $routes->post('unit/update/(:num)', 'Bumdes::updateUnit/$1');
        
        // Jurnal
        $routes->get('jurnal/(:num)', 'Bumdes::jurnal/$1');
        $routes->get('jurnal/(:num)/create', 'Bumdes::createJurnal/$1');
        $routes->post('jurnal/(:num)/save', 'Bumdes::saveJurnal/$1');
        $routes->get('jurnal/(:num)/detail/(:num)', 'Bumdes::detailJurnal/$1/$2');
        
        // Laporan
        $routes->get('laporan/laba-rugi/(:num)', 'Bumdes::laporanLabaRugi/$1');
        $routes->get('laporan/neraca/(:num)', 'Bumdes::laporanNeraca/$1');
        $routes->get('laporan/neraca-saldo/(:num)', 'Bumdes::laporanNeracaSaldo/$1');
        
        // Chart of Accounts
        $routes->get('akun', 'Bumdes::akun');
    });
    
    // ===========================================
    // WebGIS - Peta Aset Desa
    // ===========================================
    $routes->group('gis', function($routes) {
        $routes->get('/', 'Gis::index');
        $routes->get('json', 'Gis::getJsonData');
        $routes->get('population', 'Gis::getPopulationData');
        $routes->get('detail/(:num)', 'Gis::getAsetDetail/$1');
        $routes->get('fullscreen', 'Gis::fullscreen');
        
        // Wilayah Management
        $routes->get('wilayah', 'Gis::wilayahSettings');
        $routes->post('wilayah/coordinates', 'Gis::updateWilayahCoordinates');
        $routes->post('wilayah/upload', 'Gis::uploadBoundary');
        $routes->get('wilayah/boundaries', 'Gis::getWilayahBoundaries');
        $routes->get('wilayah/markers', 'Gis::getWilayahMarkers');
    });
    
    // ===========================================
    // e-Posyandu - Kesehatan Masyarakat
    // ===========================================
    $routes->group('posyandu', function($routes) {
        // Dashboard
        $routes->get('/', 'Posyandu::index');
        
        // Posyandu Management
        $routes->get('posyandu', 'Posyandu::posyandu');
        $routes->get('posyandu/create', 'Posyandu::createPosyandu');
        $routes->post('posyandu/save', 'Posyandu::savePosyandu');
        $routes->get('posyandu/detail/(:num)', 'Posyandu::detailPosyandu/$1');
        
        // Pemeriksaan Balita
        $routes->get('pemeriksaan/(:num)/create', 'Posyandu::createPemeriksaan/$1');
        $routes->post('pemeriksaan/save', 'Posyandu::savePemeriksaan');
        $routes->get('pemeriksaan/riwayat/(:num)', 'Posyandu::riwayatBalita/$1');
        
        // Stunting Monitoring
        $routes->get('stunting', 'Posyandu::stunting');
        $routes->get('stunting/gis', 'Posyandu::getStuntingGis');
        
        // Ibu Hamil
        $routes->get('bumil/(:num)/create', 'Posyandu::createBumil/$1');
        $routes->post('bumil/save', 'Posyandu::saveBumil');
        $routes->get('bumil/risti', 'Posyandu::bumilRisti');
        
        // Kader Management
        $routes->get('kader/(:num)/create', 'Posyandu::createKader/$1');
        $routes->post('kader/save', 'Posyandu::saveKader');
        $routes->get('kader/edit/(:num)', 'Posyandu::editKader/$1');
        $routes->post('kader/update/(:num)', 'Posyandu::updateKader/$1');
        $routes->get('kader/delete/(:num)', 'Posyandu::deleteKader/$1');
    });
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

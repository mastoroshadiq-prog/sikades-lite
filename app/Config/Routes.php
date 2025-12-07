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
    
    // Backup Routes (Database Backup & Restore)
    $routes->get('backup', 'Backup::index');
    $routes->post('backup/create', 'Backup::create');
    $routes->get('backup/download/(:any)', 'Backup::download/$1');
    $routes->delete('backup/delete/(:any)', 'Backup::delete/$1');
    $routes->post('backup/restore', 'Backup::restore');
    
    // Settings
    $routes->get('/profile', 'User::profile');
    $routes->post('/profile/update', 'User::updateProfile');
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

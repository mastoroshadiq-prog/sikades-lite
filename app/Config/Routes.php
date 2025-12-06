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
    
    // Laporan Routes (Reporting)
    $routes->group('laporan', function($routes) {
        $routes->get('bku', 'Laporan::bku');
        $routes->get('bku/pdf', 'Laporan::bkuPdf');
        $routes->get('realisasi', 'Laporan::realisasi');
        $routes->get('realisasi/pdf', 'Laporan::realisasiPdf');
    });
    
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

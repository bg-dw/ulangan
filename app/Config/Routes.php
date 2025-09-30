<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Login');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Login::index');
$routes->post('/' . bin2hex('auth'), 'Login::auth');
$routes->get('/' . bin2hex('logout'), 'Login::logout');


//DATA MASTER
//SISWA
$routes->get('/' . bin2hex('home'), 'Home::index');
$routes->post('/' . bin2hex('siswa') . '/' . bin2hex('add'), 'Home::ac_add');
$routes->post('/' . bin2hex('siswa') . '/' . bin2hex('update'), 'Home::ac_update');
$routes->post('/' . bin2hex('siswa') . '/' . bin2hex('delete'), 'Home::ac_delete');

//JUDUL
$routes->get('/' . bin2hex('data-judul'), 'Judul::index');
$routes->post('/' . bin2hex('data-judul') . '/' . bin2hex('add'), 'Judul::ac_add');
$routes->post('/' . bin2hex('data-judul') . '/' . bin2hex('update'), 'Judul::ac_update');
$routes->post('/' . bin2hex('data-judul') . '/' . bin2hex('delete'), 'Judul::ac_delete');

//MAPEL
$routes->get('/' . bin2hex('data-mapel'), 'Mapel::index');
$routes->post('/' . bin2hex('mapel') . '/' . bin2hex('add'), 'Mapel::ac_add');
$routes->post('/' . bin2hex('mapel') . '/' . bin2hex('update'), 'Mapel::ac_update');
$routes->post('/' . bin2hex('mapel') . '/' . bin2hex('delete'), 'Mapel::ac_delete');

//UJIAN
$routes->get('/' . bin2hex('data-ujian'), 'Ujian::index');
$routes->post('/' . bin2hex('data-ujian') . '/' . bin2hex('add'), 'Ujian::ac_add');
$routes->post('/' . bin2hex('data-ujian') . '/' . bin2hex('update'), 'Ujian::ac_update');
$routes->post('/' . bin2hex('data-ujian') . '/' . bin2hex('delete'), 'Ujian::ac_delete');

//SOAL
$routes->get('/' . bin2hex('data-soal'), 'Soal::index');
$routes->get('/' . bin2hex('data-soal') . '/' . bin2hex('add'), 'Soal::add_soal');
$routes->post('/' . bin2hex('data-soal') . '/' . bin2hex('save'), 'Soal::saveSoal');
$routes->post('/' . bin2hex('data-soal') . '/' . bin2hex('delete'), 'Soal::ac_delete');

// DRAFT
$routes->post('/' . bin2hex('data-draft') . '/' . bin2hex('save'), 'Soal::saveDraft');
$routes->get('/' . bin2hex('data-draft') . '/' . bin2hex('edit') . '/(:num)', 'Soal::editDraft/$1');
$routes->post('/' . bin2hex('data-draft') . '/' . bin2hex('update'), 'Soal::updateDraft');
$routes->post('/' . bin2hex('data-draft') . '/' . bin2hex('final'), 'Soal::finalDraft');
$routes->post('/' . bin2hex('data-draft') . '/' . bin2hex('delete'), 'Soal::ac_delete');

$routes->get('/' . bin2hex('daftar-login'), 'ulangan::index');
$routes->get('/' . bin2hex('reset-login'), 'ulangan::reset');
$routes->get('/' . bin2hex('status-tes'), 'ulangan::status');
$routes->get('/' . bin2hex('hasil-ulangan'), 'Hasil::index');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

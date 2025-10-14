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
$routes->setDefaultController('Auth_siswa');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/' . bin2hex('login'), 'Login::index');
$routes->get('/login', 'Login::index');
$routes->post('/' . bin2hex('auth'), 'Login::auth');
$routes->get('/' . bin2hex('logout'), 'Login::logout');


//UJIAN siswa
$routes->get('/', 'Auth_siswa::index');
$routes->post('/' . bin2hex('pilih-siswa'), 'Auth_siswa::pilih_siswa');
$routes->post('/' . bin2hex('ujian-cek'), 'Auth_siswa::cek');
$routes->get('/' . bin2hex('ujian-token'), 'Auth_siswa::token');
$routes->get('/' . bin2hex('ujian-get-token'), 'Auth_siswa::getToken');
$routes->post('/' . bin2hex('ujian-cek-token'), 'Auth_siswa::cekToken');
// $routes->get('/cek', 'Auth_siswa::cekToken');

//START UJIAN
$routes->get('/' . bin2hex('ujian-start'), 'Start_ujian::start');
$routes->post('/' . bin2hex('ujian-simpan-jawaban'), 'Start_ujian::simpan_jawaban');
$routes->post('/' . bin2hex('ujian-selesai'), 'Start_ujian::selesai');
$routes->post('/' . bin2hex('ujian-inactive'), 'Start_ujian::inactive');

$routes->group('', ['filter' => 'auth'], function ($routes) {

	//DATA MASTER
	//SISWA
	$routes->get('/' . bin2hex('home'), 'Home::index');
	$routes->post('/' . bin2hex('siswa') . '/' . bin2hex('add'), 'Home::ac_add');
	$routes->post('/' . bin2hex('siswa') . '/' . bin2hex('update'), 'Home::ac_update');
	$routes->post('/' . bin2hex('siswa') . '/' . bin2hex('delete'), 'Home::ac_delete');
	$routes->post('/' . bin2hex('siswa') . '/' . bin2hex('update-status'), 'Home::updateStatus');

	//JUDUL
	$routes->get('/' . bin2hex('data-judul'), 'Data_judul::index');
	$routes->post('/' . bin2hex('data-judul') . '/' . bin2hex('add'), 'Data_judul::ac_add');
	$routes->post('/' . bin2hex('data-judul') . '/' . bin2hex('update'), 'Data_judul::ac_update');
	$routes->post('/' . bin2hex('data-judul') . '/' . bin2hex('delete'), 'Data_judul::ac_delete');

	//MAPEL
	$routes->get('/' . bin2hex('data-mapel'), 'Data_mapel::index');
	$routes->post('/' . bin2hex('mapel') . '/' . bin2hex('add'), 'Data_mapel::ac_add');
	$routes->post('/' . bin2hex('mapel') . '/' . bin2hex('update'), 'Data_mapel::ac_update');
	$routes->post('/' . bin2hex('mapel') . '/' . bin2hex('delete'), 'Data_mapel::ac_delete');

	//SOAL
	$routes->get('/' . bin2hex('data-soal'), 'Data_soal::index');
	$routes->get('/' . bin2hex('data-soal') . '/' . bin2hex('add'), 'Data_soal::add_soal');
	$routes->post('/' . bin2hex('data-soal') . '/' . bin2hex('upload'), 'Data_soal::uploadGambar');
	$routes->post('/' . bin2hex('data-soal') . '/' . bin2hex('hapus'), 'Data_soal::hapusGambar');
	$routes->post('/' . bin2hex('data-soal') . '/' . bin2hex('save'), 'Data_soal::saveSoal');
	$routes->post('/' . bin2hex('data-soal') . '/' . bin2hex('delete'), 'Data_soal::ac_delete');

	// DRAFT
	$routes->post('/' . bin2hex('data-draft') . '/' . bin2hex('save'), 'Data_soal::saveDraft');
	$routes->get('/' . bin2hex('data-draft') . '/' . bin2hex('edit') . '/(:num)', 'Data_soal::editDraft/$1');
	$routes->post('/' . bin2hex('data-draft') . '/' . bin2hex('update'), 'Data_soal::updateDraft');
	$routes->post('/' . bin2hex('data-draft') . '/' . bin2hex('final'), 'Data_soal::finalDraft');
	$routes->post('/' . bin2hex('data-draft') . '/' . bin2hex('delete'), 'Data_soal::ac_delete');

	//DATA UJIAN
	$routes->get('/' . bin2hex('data-ujian'), 'Data_ujian::index');
	$routes->post('/' . bin2hex('data-ujian') . '/' . bin2hex('add'), 'Data_ujian::ac_add');
	$routes->post('/' . bin2hex('data-ujian') . '/' . bin2hex('update'), 'Data_ujian::ac_update');
	$routes->post('/' . bin2hex('data-ujian') . '/' . bin2hex('delete'), 'Data_ujian::ac_delete');

	//SOAL UJIAN
	$routes->get('/' . bin2hex('soal-ujian'), 'Soal_ujian::index');
	$routes->post('/' . bin2hex('soal-ujian') . '/' . bin2hex('add'), 'Soal_ujian::ac_add');
	$routes->post('/' . bin2hex('soal-ujian') . '/' . bin2hex('update'), 'Soal_ujian::ac_update');
	$routes->post('/' . bin2hex('soal-ujian') . '/' . bin2hex('delete'), 'Soal_ujian::ac_delete');
	$routes->post('/' . bin2hex('soal-ujian') . '/' . bin2hex('update-status'), 'Soal_ujian::updateStatus');

	//UJIAN
	$routes->get('/' . bin2hex('daftar-login'), 'Ulangan::index');
	$routes->post('/' . bin2hex('get-ujian'), 'Ulangan::get_ujian');
	$routes->get('/' . bin2hex('cek-last-update'), 'Ulangan::get_last_update');


	$routes->get('/' . bin2hex('reset-login'), 'Ulangan::reset');
	$routes->post('/' . bin2hex('reset-act'), 'Ulangan::ac_reset');
	$routes->get('/' . bin2hex('status-tes'), 'Ulangan::status');
	$routes->post('/' . bin2hex('data-ulangan') . '/' . bin2hex('rilis-token'), 'Ulangan::rilisToken');
	$routes->post('/' . bin2hex('data-ulangan') . '/' . bin2hex('hapus-token'), 'Ulangan::hapusToken');

	//REKAP
	$routes->get('/' . bin2hex('rekap'), 'Rekap::index');
	$routes->post('/' . bin2hex('rekap-init'), 'Rekap::init_nilai');
	$routes->post('/' . bin2hex('rekap-update-nilai'), 'Rekap::update_nilai');
	$routes->get('/' . bin2hex('rekap-get-hasil') . "/(:any)", 'Rekap::get_data_by/$1');
});





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

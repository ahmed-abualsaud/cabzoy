<?php

use Config\Services;

try {
	helper('db');
	$db     = db();
	$routes = Services::routes();

	if ($db->tableExists('auth_groups') && $db->tableExists('auth_permissions') && $db->tableExists('auth_groups_permissions')) {
		$routes->get('/dashboard', 'HomeController::index');
		$routes->get('/mail', 'MailController::index');
		$routes->addRedirect('/', '/dashboard');
		$routes->get('/dispatch', 'HomeController::dispatch');
		$routes->post('dispatch', 'HomeController::processDispatch');
		$routes->get('/bird-eye', 'HomeController::birdEye', ['filter' => 'role:creators, super-admins, admins, managers']);

		if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'auth.php')) require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
		if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'list.php')) require_once __DIR__ . DIRECTORY_SEPARATOR . 'list.php';
		if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'add.php')) require_once __DIR__ . DIRECTORY_SEPARATOR . 'add.php';
		if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'update.php')) require_once __DIR__ . DIRECTORY_SEPARATOR . 'update.php';
		if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'delete.php')) require_once __DIR__ . DIRECTORY_SEPARATOR . 'delete.php';
		if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'spacial.php')) require_once __DIR__ . DIRECTORY_SEPARATOR . 'spacial.php';
		if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'api.php')) require_once __DIR__ . DIRECTORY_SEPARATOR . 'api.php';

		foreach (glob(ROOTPATH . 'modules/*', GLOB_ONLYDIR) as $item_dir) {
			if (file_exists($item_dir . '/routes/web.php')) require_once($item_dir . '/routes/web.php');
		}
	} else {
		$routes->get('/', 'CliController::check');
		$routes->get('/install', 'CliController::requestData');
		$routes->post('/install', 'CliController::init');
		$routes->get('tools/server/reset/all', 'CliController::reset', ['as' => 'resetAll']);
	}
} catch (\Throwable $th) {
	$routes->get('/', 'CliController::check');
	$routes->get('/install', 'CliController::requestData');
	$routes->post('/install', 'CliController::init');
	$routes->get('tools/server/reset/all', 'CliController::reset', ['as' => 'resetAll']);
}

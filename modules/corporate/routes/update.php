<?php

use Config\Services;

/** @var \CodeIgniter\Router\RouteCollection */
$routes = Services::routes();

$routes->group('update', ['namespace' => 'Modules\Corporate\Controllers'], static function ($routes) {
	$routes->get('company/(:num)', 'CompanyController::edit/$1', [
		'as' => 'update_company', 'filter' => 'permission:companies.update',
	]);
	$routes->post('company/(:num)', 'CompanyController::update/$1', ['filter' => 'permission:companies.update']);
});

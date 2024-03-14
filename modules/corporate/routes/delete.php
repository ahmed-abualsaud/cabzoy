<?php

use Config\Services;

/** @var \CodeIgniter\Router\RouteCollection */
$routes = Services::routes();

$routes->group('delete', ['namespace' => 'Modules\Corporate\Controllers'], static function ($routes) {
	$routes->get('companies/(:num)', 'CompanyController::delete/$1', [
		'as' => 'delete_companies', 'filter' => 'permission:companies.delete'
	]);
});

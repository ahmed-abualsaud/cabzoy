<?php

use Config\Services;

/** @var \CodeIgniter\Router\RouteCollection */
$routes = Services::routes();

$routes->group('list', ['namespace' => 'Modules\Corporate\Controllers'], static function ($routes) {
	$routes->get('companies', 'CompanyController::index', ['as' => 'companies']);
});

<?php

use Config\Services;

/** @var \CodeIgniter\Router\RouteCollection */
$routes = Services::routes();

$routes->group('add', ['namespace' => 'Modules\Corporate\Controllers'], static function ($routes) {
	$routes->get('company', 'CompanyController::add', ['as' => 'add_company', 'filter' => 'permission:companies.add']);
	$routes->post('company', 'CompanyController::save', ['filter' => 'permission:companies.add']);
});

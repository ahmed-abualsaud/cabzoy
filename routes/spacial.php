<?php

use Config\Services;

helper(['inflector', 'role']);

/** @var \CodeIgniter\Router\RouteCollection */
$routes = Services::routes();

$routes->group('payment', ['namespace' => 'App\Controllers'], static function ($routes) {
	$routes->get('init', 'PaymentController::index');
	$routes->get('redirect/(:num)', 'PaymentController::responseRedirect/$1', ['as' => 'payment_redirect']);
});

$routes->group('cli', ['namespace' => 'App\Controllers'], static function ($routes) {
	$routes->get('init', 'CliController::init');
});

$routes->group('assign', ['namespace' => 'App\Controllers'], static function ($routes) {
	$routes->get('vehicle', 'VehicleRelationController::index', ['as' => 'assign_vehicle', 'filter' => 'permission:vehicles.assign']);
	$routes->post('vehicle', 'VehicleRelationController::save', ['filter' => 'permission:vehicles.assign']);

	$routes->get('ticket', 'TicketController::assign', ['as' => 'assign_ticket', 'filter' => 'permission:tickets.assign']);
	$routes->post('ticket', 'TicketController::updateAssign', ['filter' => 'permission:tickets.assign']);
});

$routes->group('update', ['namespace' => 'App\Controllers'], static function ($routes) {
	$routes->group('assign', ['namespace' => 'App\Controllers'], static function ($routes) {
		$routes->get('vehicle/(:num)', 'VehicleRelationController::update/$1', [
			'as' => 'update_assign_vehicle', 'filter' => 'permission:vehicles.assign'
		]);
	});
});

$routes->group('delete', ['namespace' => 'App\Controllers'], static function ($routes) {
	$routes->group('assign', ['namespace' => 'App\Controllers'], static function ($routes) {
		$routes->get('vehicle/(:num)', 'VehicleRelationController::delete/$1', [
			'as' => 'delete_assign_vehicle', 'filter' => 'permission:vehicles.assign'
		]);
	});
});


$routes->group('list', ['namespace' => 'App\Controllers\Fares'], static function ($routes) {
	$routes->get('base-fares', 'FareController::index', ['as' => 'base_fares', 'filter' => 'permission:fares.read']);
	$routes->get('zone-fares', 'ZoneController::index', ['as' => 'zone_fares', 'filter' => 'permission:fares.read']);
	$routes->get('hourly-fares', 'HourlyController::index', ['as' => 'hourly_fares', 'filter' => 'permission:fares.read']);
	$routes->get('category-fares', 'CategoryController::index', ['as' => 'category_fares', 'filter' => 'permission:fares.read']);
});


$routes->group('add', ['namespace' => 'App\Controllers\Fares'], static function ($routes) {
	$routes->get('category-fare', 'CategoryController::add', ['as' => 'add_category_fare', 'filter' => 'permission:fares.add']);
	$routes->post('category-fare', 'CategoryController::save', ['filter' => 'permission:fares.add']);

	$routes->get('hourly-fare', 'HourlyController::add', ['as' => 'add_hourly_fare', 'filter' => 'permission:fares.add']);
	$routes->post('hourly-fare', 'HourlyController::save', ['filter' => 'permission:fares.add']);

	$routes->get('base-fare', 'FareController::add', ['as' => 'add_base_fare', 'filter' => 'permission:fares.add']);
	$routes->post('base-fare', 'FareController::save', ['filter' => 'permission:fares.add']);
});


$routes->group('update', ['namespace' => 'App\Controllers\Fares'], static function ($routes) {
	$routes->get('hourly-fare/(:num)', 'HourlyController::edit/$1', [
		'as' => 'update_hourly_fare', 'filter' => 'permission:fares.update'
	]);
	$routes->post('hourly-fare/(:num)', 'HourlyController::update/$1', ['filter' => 'permission:fares.update']);

	$routes->get('category-fare/(:num)', 'CategoryController::edit/$1', [
		'as' => 'update_category_fare', 'filter' => 'permission:fares.update'
	]);
	$routes->post('category-fare/(:num)', 'CategoryController::update/$1', ['filter' => 'permission:fares.update']);

	$routes->get('base-fare/(:num)', 'FareController::edit/$1', ['as' => 'update_base_fare', 'filter' => 'permission:fares.update']);
	$routes->post('base-fare/(:num)', 'FareController::update/$1', ['filter' => 'permission:fares.update']);
});


$routes->group('delete', ['namespace' => 'App\Controllers\Fares'], static function ($routes) {
	$routes->get('base-fare/(:num)', 'FareController::delete/$1', ['as' => 'delete_base_fare', 'filter' => 'permission:fares.delete']);
	$routes->get('hourly-fare/(:num)', 'HourlyController::delete/$1', [
		'as' => 'delete_hourly_fare', 'filter' => 'permission:fares.delete'
	]);
	$routes->get('category-fare/(:num)', 'CategoryController::delete/$1', [
		'as' => 'delete_category_fare', 'filter' => 'permission:fares.delete'
	]);
});

<?php

use Config\Services;

$routes = Services::routes();

$routes->group('api', ['namespace' => 'App\Apis'], function ($routes) {
	$routes->post('files', 'Files::create');
	$routes->post('wallets/check', 'Wallets::check');
	$routes->get('active-vehicle/(:segment)', 'Vehicles::activeVehicle/$1');

	$routes->get('user/me', 'User::me');
	$routes->post('user/login', 'User::login');
	$routes->get('user/logout', 'User::logout');
	$routes->post('user/register', 'User::register');
	$routes->patch('user/me', 'User::updateProfile');
	$routes->post('user/forgot-password', 'User::forgotPassword');
	$routes->get('locations/live', 'Locations::getAllDriverLocation');

	$routes->resource('fares');
	$routes->resource('cards');
	$routes->resource('orders');
	$routes->resource('promos');
	$routes->resource('refers');
	$routes->resource('reviews');
	$routes->resource('wallets');
	$routes->resource('messages');
	$routes->resource('accounts');
	$routes->resource('locations');
	$routes->resource('documents');
	$routes->resource('transactions');

	$routes->resource('driver-orders', ['controller' => 'OrderDrivers']);
	$routes->resource('emergency-contacts', ['controller' => 'EmergencyContacts']);

	$routes->resource('ping', ['only' => ['index']]);
	$routes->resource('settings', ['only' => ['index']]);
	$routes->resource('categories', ['only' => ['index']]);
	$routes->resource('tips', ['only' => ['index', 'create']]);
	$routes->resource('notifications', ['only' => ['index', 'update', 'create']]);
	$routes->resource('vehicles', ['only' => ['index', 'create', 'delete']]);
	$routes->resource('families', ['only' => ['index', 'create', 'show', 'delete']]);
	$routes->resource('withdraws', ['only' => ['index', 'create', 'show', 'delete']]);
});

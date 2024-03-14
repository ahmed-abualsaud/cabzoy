<?php

use Config\Services;

/** @var \CodeIgniter\Router\RouteCollection */
$routes = Services::routes();

helper(['inflector', 'role']);

$routes->group('list', ['namespace' => 'App\Controllers'], static function ($routes) {
	$routes->get('cards', 'CardController::index', ['as' => 'cards']);
	$routes->get('promos', 'PromoController::index', ['as' => 'promos']);
	$routes->get('orders', 'OrderController::index', ['as' => 'orders']);
	$routes->get('reports', 'ReportController::index', ['as' => 'reports']);
	$routes->get('reviews', 'ReviewController::index', ['as' => 'reviews']);
	$routes->get('wallets', 'WalletController::index', ['as' => 'wallets']);
	$routes->get('settings', 'SettingController::index', ['as' => 'settings']);
	$routes->get('vehicles', 'VehicleController::index', ['as' => 'vehicles']);
	$routes->get('accounts', 'AccountController::index', ['as' => 'accounts']);
	$routes->get('messages', 'MessageController::index', ['as' => 'messages']);
	$routes->get('withdraws', 'WithdrawController::index', ['as' => 'withdraws']);
	$routes->get('documents', 'DocumentController::index', ['as' => 'documents']);
	$routes->get('categories', 'CategoryController::index', ['as' => 'categories']);
	$routes->get('complaints', 'ComplaintController::index', ['as' => 'complaints']);
	$routes->get('commissions', 'CommissionController::index', ['as' => 'commissions']);
	$routes->get('transactions', 'TransactionController::index', ['as' => 'transactions']);
	$routes->get('notifications', 'NotificationController::index', ['as' => 'notifications']);

	if (function_usable('user_groups') && is_array(user_groups())) foreach (user_groups() as $group) {
		$routes->get($group->name, 'UserController::index', ['as' => $group->name]);
	}
});

$routes->group('show', ['namespace' => 'App\Controllers'], static function ($routes) {
	if (function_usable('user_groups') && is_array(user_groups())) foreach (user_groups() as $group) {
		$group_name = singular($group->name);
		$routes->get("{$group_name}/(:num)", 'UserController::show/$1', ['as' => "show_{$group_name}"]);
	}
});

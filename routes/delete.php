<?php

use Config\Services;

helper(['inflector', 'role']);

/** @var \CodeIgniter\Router\RouteCollection */
$routes = Services::routes();

$routes->group('delete', ['namespace' => 'App\Controllers'], static function ($routes) {
	$routes->get('order/(:num)', 'OrderController::delete/$1', ['as' => 'delete_order', 'filter' => 'permission:orders.delete']);
	$routes->get('fare/(:num)', 'FareController::delete/$1', [
		'as' => 'delete_fare', 'filter' => 'permission:fares.delete',
	]);
	$routes->get('report/(:num)', 'ReportController::delete/$1', [
		'as' => 'delete_report', 'filter' => 'permission:reports.delete',
	]);
	$routes->get('setting/(:num)', 'SettingController::delete/$1', [
		'as' => 'delete_setting', 'filter' => 'permission:settings.delete'
	]);
	$routes->get('vehicle/(:num)', 'VehicleController::delete/$1', [
		'as' => 'delete_vehicle', 'filter' => 'permission:vehicles.delete',
	]);
	$routes->get('complaint/(:num)', 'ComplaintController::delete/$1', [
		'as' => 'delete_complaint', 'filter' => 'permission:complaints.delete',
	]);
	$routes->get('commission/(:num)', 'CommissionController::delete/$1', [
		'as' => 'delete_commission', 'filter' => 'permission:commissions.delete',
	]);

	$routes->get('cards/(:num)', 'CardController::delete/$1', ['as' => 'delete_card', 'filter' => 'permission:cards.delete']);
	$routes->get('documents/(:num)', 'DocumentController::delete/$1', ['as' => 'delete_document', 'filter' => 'permission:documents.delete']);
	$routes->get('withdraws/(:num)', 'WithdrawController::delete/$1', ['as' => 'delete_withdraw', 'filter' => 'permission:withdraws.delete']);
	$routes->get('promos/(:num)', 'PromoController::delete/$1', ['as' => 'delete_promo', 'filter' => 'permission:promos.delete']);
	$routes->get('zones/(:num)', 'ZoneController::delete/$1', ['as' => 'delete_zones', 'filter' => 'permission:zones.delete']);
	$routes->get('reviews/(:num)', 'ReviewController::delete/$1', ['as' => 'delete_reviews', 'filter' => 'permission:reviews.delete']);
	$routes->get('accounts/(:num)', 'AccountController::delete/$1', [
		'as' => 'delete_account', 'filter' => 'permission:accounts.delete'
	]);
	$routes->get('messages/(:num)', 'MessageController::delete/$1', [
		'as' => 'delete_messages', 'filter' => 'permission:messages.delete'
	]);
	$routes->get('transactions/(:num)', 'TransactionController::delete/$1', [
		'as' => 'delete_transactions', 'filter' => 'permission:transactions.delete'
	]);
	$routes->get('wallets/(:num)', 'WalletController::delete/$1', [
		'as' => 'delete_wallets', 'filter' => 'permission:wallets.delete'
	]);
	if (function_usable('user_groups') && is_array(user_groups())) foreach (user_groups() as $group) {
		$group_name = singular($group->name);
		$routes->get("{$group_name}/(:num)", 'UserController::delete/$1', [
			'as' => "delete_{$group_name}", 'filter' => "permission:{$group->name}.delete",
		]);
	}
});

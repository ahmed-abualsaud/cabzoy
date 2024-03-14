<?php

use Config\Services;

helper(['inflector', 'role']);

/** @var \CodeIgniter\Router\RouteCollection */
$routes = Services::routes();

$routes->group('update', ['namespace' => 'App\Controllers'], static function ($routes) {
	$routes->get('fare/(:num)', 'FareController::edit/$1', ['as' => 'update_fare', 'filter' => 'permission:fares.update']);
	$routes->post('fare/(:num)', 'FareController::update/$1', ['filter' => 'permission:fares.update']);

	$routes->post('order/(:num)', 'OrderController::update/$1', ['filter' => 'permission:orders.update']);
	$routes->get('order/(:num)', 'OrderController::edit/$1', ['as' => 'update_order', 'filter' => 'permission:orders.update']);

	$routes->post('vehicle/(:num)', 'VehicleController::update/$1', ['filter' => 'permission:vehicles.update']);
	$routes->get('vehicle/(:num)', 'VehicleController::edit/$1', ['as' => 'update_vehicle', 'filter' => 'permission:vehicles.update']);

	$routes->get('report/(:num)', 'ReportController::edit/$1', ['as' => 'update_report', 'filter' => 'permission:reports.update']);
	$routes->post('report/(:num)', 'ReportController::update/$1', ['filter' => 'permission:reports.update']);

	$routes->get('setting/(:num)', 'SettingController::edit/$1', ['as' => 'update_setting', 'filter' => 'permission:settings.update']);
	$routes->post('setting/(:num)', 'SettingController::update/$1', ['filter' => 'permission:settings.update']);

	$routes->get('category/(:num)', 'CategoryController::edit/$1', [
		'as' => 'update_category', 'filter' => 'permission:categories.update',
	]);
	$routes->post('category/(:num)', 'CategoryController::update/$1', ['filter' => 'permission:categories.update']);

	$routes->get('complaint/(:num)', 'ComplaintController::edit/$1', [
		'as' => 'update_complaint', 'filter' => 'permission:complaints.update',
	]);
	$routes->post('complaint/(:num)', 'ComplaintController::update/$1', ['filter' => 'permission:complaints.update']);

	$routes->get('commission/(:num)', 'CommissionController::edit/$1', [
		'as' => 'update_commission', 'filter' => 'permission:commissions.update',
	]);
	$routes->post('commission/(:num)', 'CommissionController::update/$1', ['filter' => 'permission:commissions.update']);

	$routes->get('card/(:num)', 'CardController::edit/$1', ['as' => 'update_card', 'filter' => 'permission:cards.update']);
	$routes->post('card/(:num)', 'CardController::update/$1', ['filter' => 'permission:cards.update']);

	$routes->get('document/(:num)', 'DocumentController::edit/$1', ['as' => 'update_document', 'filter' => 'permission:documents.update']);
	$routes->post('document/(:num)', 'DocumentController::update/$1', ['filter' => 'permission:documents.update']);

	$routes->get('withdraw/(:num)', 'WithdrawController::edit/$1', ['as' => 'update_withdraw', 'filter' => 'permission:withdraws.update']);
	$routes->post('withdraw/(:num)', 'WithdrawController::update/$1', ['filter' => 'permission:withdraws.update']);

	$routes->get('promo/(:num)', 'PromoController::edit/$1', ['as' => 'update_promo', 'filter' => 'permission:promos.update']);
	$routes->post('promo/(:num)', 'PromoController::update/$1', ['filter' => 'permission:promos.update']);

	$routes->get('account/(:num)', 'AccountController::edit/$1', ['as' => 'update_account', 'filter' => 'permission:accounts.update']);
	$routes->post('account/(:num)', 'AccountController::update/$1', ['filter' => 'permission:accounts.update']);

	$routes->get('review/(:num)', 'ReviewController::edit/$1', ['as' => 'update_review', 'filter' => 'permission:reviews.update']);
	$routes->post('review/(:num)', 'ReviewController::update/$1', ['filter' => 'permission:reviews.update']);

	$routes->get('message/(:num)', 'MessageController::edit/$1', ['as' => 'update_message', 'filter' => 'permission:messages.update']);
	$routes->post('message/(:num)', 'MessageController::update/$1', ['filter' => 'permission:messages.update']);

	$routes->get('transaction/(:num)', 'TransactionController::edit/$1', [
		'as' => 'update_transaction', 'filter' => 'permission:transactions.update',
	]);
	$routes->post('transaction/(:num)', 'TransactionController::update/$1', ['filter' => 'permission:transactions.update']);

	$routes->get('wallet/(:num)', 'TransactionController::edit/$1', [
		'as' => 'update_wallet', 'filter' => 'permission:wallets.update',
	]);
	$routes->post('wallet/(:num)', 'TransactionController::update/$1', ['filter' => 'permission:wallets.update']);


	if (function_usable('user_groups') && is_array(user_groups())) foreach (user_groups() as $group) {
		$group_name = singular($group->name);
		$routes->get("{$group_name}/(:num)", 'UserController::edit/$1', [
			'as' => "update_{$group_name}", 'filter' => "permission:{$group->name}.update",
		]);
		$routes->post("{$group_name}/(:num)", 'UserController::update/$1', ['filter' => "permission:{$group->name}.update"]);
	}
});

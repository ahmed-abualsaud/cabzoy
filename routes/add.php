<?php

use Config\Services;

helper(['inflector', 'role']);

/** @var \CodeIgniter\Router\RouteCollection */
$routes = Services::routes();

$routes->group('add', ['namespace' => 'App\Controllers'], static function ($routes) {
	$routes->get('fare', 'FareController::add', ['as' => 'add_fare', 'filter' => 'permission:fares.add']);
	$routes->post('fare', 'FareController::save', ['filter' => 'permission:fares.add']);


	$routes->get('order', 'OrderController::add', ['as' => 'add_order', 'filter' => 'permission:orders.add']);
	$routes->post('order', 'OrderController::save', ['filter' => 'permission:orders.add']);

	$routes->get('notification', 'NotificationController::add', ['as' => 'add_notification', 'filter' => 'permission:notifications.add']);
	$routes->post('notification', 'NotificationController::save', ['filter' => 'permission:notifications.add']);

	$routes->get('refer', 'ReferController::add', ['as' => 'add_refer', 'filter' => 'permission:refers.add']);
	$routes->post('refer', 'ReferController::save', ['filter' => 'permission:refers.add']);

	$routes->get('report', 'ReportController::add', ['as' => 'add_report', 'filter' => 'permission:reports.add']);
	$routes->post('report', 'ReportController::save', ['filter' => 'permission:reports.add']);

	$routes->get('setting', 'SettingController::add', ['as' => 'add_setting', 'filter' => 'permission:settings.add']);
	$routes->post('setting', 'SettingController::save', ['filter' => 'permission:settings.add']);

	$routes->get('vehicle', 'VehicleController::add', ['as' => 'add_vehicle', 'filter' => 'permission:vehicles.add']);
	$routes->post('vehicle', 'VehicleController::save', ['filter' => 'permission:vehicles.add']);

	$routes->get('category', 'CategoryController::add', ['as' => 'add_category', 'filter' => 'permission:categories.add']);
	$routes->post('category', 'CategoryController::save', ['filter' => 'permission:categories.add']);

	$routes->get('complaint', 'ComplaintController::add', ['as' => 'add_complaint', 'filter' => 'permission:complaints.add']);
	$routes->post('complaint', 'ComplaintController::save', ['filter' => 'permission:complaints.add']);

	$routes->get('commission', 'CommissionController::add', ['as' => 'add_commission', 'filter' => 'permission:commissions.add']);
	$routes->post('commission', 'CommissionController::save', ['filter' => 'permission:commissions.add']);

	$routes->get('card', 'CardController::add', ['as' => 'add_card', 'filter' => 'permission:cards.add']);
	$routes->post('card', 'CardController::save', ['filter' => 'permission:cards.add']);

	$routes->get('document', 'DocumentController::add', ['as' => 'add_document', 'filter' => 'permission:documents.add']);
	$routes->post('document', 'DocumentController::save', ['filter' => 'permission:documents.add']);

	$routes->get('withdraw', 'WithdrawController::add', ['as' => 'add_withdraw', 'filter' => 'permission:withdraws.add']);
	$routes->post('withdraw', 'WithdrawController::save', ['filter' => 'permission:withdraws.add']);

	$routes->get('promo', 'PromoController::add', ['as' => 'add_promo', 'filter' => 'permission:promos.add']);
	$routes->post('promo', 'PromoController::save', ['filter' => 'permission:promos.add']);

	$routes->get('account', 'AccountController::add', ['as' => 'add_account', 'filter' => 'permission:accounts.add']);
	$routes->post('account', 'AccountController::save', ['filter' => 'permission:accounts.add']);

	$routes->get('review', 'ReviewController::add', ['as' => 'add_review', 'filter' => 'permission:reviews.add']);
	$routes->post('review', 'ReviewController::save', ['filter' => 'permission:reviews.add']);

	$routes->get('message', 'MessageController::add', ['as' => 'add_message', 'filter' => 'permission:messages.add']);
	$routes->post('message', 'MessageController::save', ['filter' => 'permission:messages.add']);

	$routes->get('transaction', 'TransactionController::add', ['as' => 'add_transaction', 'filter' => 'permission:transactions.add']);
	$routes->post('transaction', 'TransactionController::save', ['filter' => 'permission:transactions.add']);

	$routes->get('wallet', 'WalletController::add', ['as' => 'add_wallet', 'filter' => 'permission:wallets.add']);
	$routes->post('wallet', 'WalletController::save', ['filter' => 'permission:wallets.add']);

	if (function_usable('user_groups') && is_array(user_groups())) foreach (user_groups() as $group) {
		$group_name = singular($group->name);
		$routes->get($group_name, 'UserController::add', [
			'as' => "add_{$group_name}", 'filter' => "permission:{$group->name}.read",
		]);
		$routes->post($group_name, 'UserController::save', ['filter' => "permission:{$group->name}.add"]);
	}
});

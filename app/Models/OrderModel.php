<?php

namespace App\Models;

use App\Entities\Order;

class OrderModel extends BaseModel
{
	protected $useSoftDeletes = true;
	protected $table          = 'orders';
	protected $returnType     = Order::class;
	protected $with           = [
		'users', 'order_fares', 'order_locations', 'order_drivers', 'order_users', 'order_promos', 'order_commissions', 'order_tips'
	];
	protected $allowedFields  = [
		'order_vehicle', 'order_price', 'order_kms', 'order_otp', 'order_comment', 'wait_time', 'is_paid', 'order_status', 'order_type', 'payment_mode', 'booking_from', 'booking_at', 'created_by',
	];
	protected $validationRules = [
		'id'           => 'permit_empty|is_natural_no_zero',
		'booking_from' => 'in_list[web, app]',
		'is_paid'      => 'in_list[paid, not-paid]',
		'payment_mode' => 'in_list[online, corporate, cod]',
		'order_type'   => 'in_list[normal, outdoor, advanced]',
		'order_status' => 'in_list[new, booked, dispatched, arrived, picked, ongoing, complete, cancel]',
	];


	public function notStatus(array $status = null)
	{
		$builder = $this->builder();
		$builder->whereNotIn('order_status', $status);

		return $this;
	}

	public function isStatus(string $status = 'new')
	{
		$builder = $this->builder();
		$builder->where('order_status', $status);

		return $this;
	}

	public function paymentType(string $paymentMode = 'online')
	{
		$builder = $this->builder();
		$builder->where('payment_mode', $paymentMode);

		return $this;
	}


	/** Order Type
	 *
	 * @param string $type `normal | advanced | outdoor`
	 * @return self */
	public function isType(string $type = 'normal')
	{
		$builder = $this->builder();
		$builder->where('order_type', $type);

		return $this;
	}

	public function checkRecentOrder($userId = null)
	{
		if (!config('Settings')->enableSingleOrder) return false;
		$recentOrderCount = $this->builder()->join('order_users', 'order_users.order_id = orders.id')->where('order_users.user_id', $userId ?? user_id())->whereNotIn('order_status', ['complete', 'cancel'])->get()->getResult();
		if (is_countable($recentOrderCount) && count($recentOrderCount) > 0) return true;
		return false;
	}
}

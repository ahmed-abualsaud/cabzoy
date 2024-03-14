<?php

namespace App\Entities;

class Wallet extends BaseEntity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	public function getAmount($formatted = false)
	{
		if ($formatted) return number_to_currency($this->attributes['amount'], config('Settings')->defaultCurrencyUnit ?? 'USD');

		return $this->attributes['amount'];
	}
}

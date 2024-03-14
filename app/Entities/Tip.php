<?php

namespace App\Entities;

class Tip extends BaseEntity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	public function getAmount($formatted = false)
	{
		if ($formatted) return number_to_currency($this->attributes['tip_amount'], config('Settings')->defaultCurrencyUnit ?? 'USD');

		return $this->attributes['tip_amount'];
	}
}

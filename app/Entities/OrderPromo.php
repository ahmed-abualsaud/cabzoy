<?php

namespace App\Entities;

class OrderPromo extends BaseEntity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	public function getDiscount($formatted = false)
	{
		if ($formatted) return number_to_currency($this->attributes['discount'], config('Settings')->defaultCurrencyUnit ?? 'USD');

		return $this->attributes['discount'];
	}
}

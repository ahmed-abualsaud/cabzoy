<?php

namespace App\Entities;

class Promo extends BaseEntity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	public function setPromoCode($promoCode = null)
	{
		helper('url');
		$this->attributes['promo_code'] = url_title($promoCode, '-', true);

		return $this;
	}

	public function getPromoDiscount($formatted = false)
	{
		if ($formatted) {
			if ($this->attributes['promo_discount_type'] === 'flat')
				return number_to_currency($this->attributes['promo_discount'], config('Settings')->defaultCurrencyUnit ?? 'USD');
			else return $this->attributes['promo_discount'] . '%';
		}

		return $this->attributes['promo_discount'];
	}

	public function getPromoMinAmount($formatted = false)
	{
		if ($formatted) return number_to_currency($this->attributes['promo_min_amount'], config('Settings')->defaultCurrencyUnit ?? 'USD');

		return $this->attributes['promo_min_amount'];
	}

	public function getPromoMaxAmount($formatted = false)
	{
		if ($formatted) return number_to_currency($this->attributes['promo_max_amount'], config('Settings')->defaultCurrencyUnit ?? 'USD');

		return $this->attributes['promo_max_amount'];
	}
}

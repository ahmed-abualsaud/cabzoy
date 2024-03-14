<?php

namespace App\Entities;

class Commission extends BaseEntity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	public function setCommissionName(string $commission_name = null)
	{
		$name = str_safe($commission_name);
		$this->attributes['commission_name'] = strpos($name, ' fee') === false ? "$name fee" : $name;

		return $this;
	}

	public function getCommission($formatted = false)
	{
		if ($formatted) {
			if ($this->attributes['commission_type'] === 'flat')
				return number_to_currency($this->attributes['commission'], config('Settings')->defaultCurrencyUnit ?? 'USD');
			else return $this->attributes['commission'] . '%';
		}

		return $this->attributes['commission'];
	}
}

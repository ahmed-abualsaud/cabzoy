<?php

namespace App\Entities;

class Fare extends BaseEntity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	public function setFareName(string $fare_name = null)
	{
		$name = str_safe($fare_name);
		$this->attributes['fare_name'] = strpos($name, ' fare') === false ? "$name fare" : $name;

		return $this;
	}

	public function getFare($formatted = false)
	{
		if ($formatted) return number_to_currency($this->attributes['fare'], config('Settings')->defaultCurrencyUnit ?? 'USD');

		return $this->attributes['fare'];
	}

	public function getMinFare($formatted = false)
	{
		if ($formatted) return number_to_currency($this->attributes['min_fare'], config('Settings')->defaultCurrencyUnit ?? 'USD');

		return $this->attributes['min_fare'];
	}

	public function getFareFrom($formatted = false)
	{
		if ($formatted) return $this->attributes['fare_from'] . " " . config('Settings')->defaultLengthUnit;

		return $this->attributes['fare_from'];
	}

	public function getFareTo($formatted = false)
	{
		if ($formatted) return $this->attributes['fare_to'] . " " . config('Settings')->defaultLengthUnit;

		return $this->attributes['fare_to'];
	}

	public function getStartTime($formatted = false)
	{
		if ($formatted) return date('h:i A', strtotime($this->attributes['start_time'] ?? '00:00:00'));

		return $this->attributes['start_time'];
	}

	public function getEndTime($formatted = false)
	{
		if ($formatted) return date('h:i A', strtotime($this->attributes['end_time'] ?? '00:00:00'));

		return $this->attributes['end_time'];
	}
}

<?php

namespace App\Entities;

class Order extends BaseEntity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at', 'booking_at'];

	public function getOrderPrice($formatted = false)
	{
		if ($formatted) return number_to_currency($this->attributes['order_price'], config('Settings')->defaultCurrencyUnit ?? 'USD');

		return $this->attributes['order_price'];
	}

	public function getOrderKms($formatted = false)
	{
		if ($formatted) {
			$number        = $this->attributes['order_kms'];
			$lengthUnit    = strtolower(config('Settings')->defaultLengthUnit);
			$defaultLength = ($lengthUnit === 'km' || $lengthUnit === 'kms') ? 0.001 : 0.000621372;
			return format_number($number * $defaultLength, 2) . $lengthUnit;
		}

		return $this->attributes['order_kms'];
	}

	public function getBookingAt($formatted = true)
	{
		if (is_null($this->attributes['booking_at'])) return 'not booking yet.';

		$timezone                       = config('Settings')->timezone ?? $this->timezone ?? app_timezone();
		$this->attributes['booking_at'] = $this->mutateDate($this->attributes['booking_at']);
		$this->attributes['booking_at']->setTimezone($timezone);
		if ($formatted) return $this->attributes['booking_at']->format('M d, Y h:i A');

		return $this->attributes['booking_at']->format('Y-m-d h:i:s');
	}
}

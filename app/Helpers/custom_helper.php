<?php

use CodeIgniter\I18n\Time;

if (!function_exists('is')) {

	/** Check Param exists or not
	 *
	 * @param mixed  $param
	 * @param string $action `number | array | string | object | json | show | showCapital`
	 *
	 * @return bool */
	function is(&$param, ?string $action = null)
	{
		if (null !== $action) {
			if ($action === 'number') return isset($param) && is_numeric($param);
			if ($action === 'array') return isset($param) && !empty($param) && is_array($param);
			if ($action === 'string') return isset($param) && !empty($param) && is_string($param);
			if ($action === 'object' || $action === 'json') return isset($param) && !empty($param) && is_object($param);
			if ($action === 'show' || $action === 'print') return isset($param) && !empty($param) && print $param;
			if ($action === 'showCapital') return isset($param) && !empty($param) && print ucwords($param);
			if ($action === 'price') return isset($param) && is_numeric($param) && print number_to_currency($param ?? 0, 'usd');
		}

		return isset($param) && !empty($param);
	}
}


if (!function_exists('datetime')) {
	/** Return Current Date Time String
	 *
	 * @return string */
	function datetime()
	{
		$time     = new Time;
		$timezone = getDefaultConfig('timezone', app_timezone() ?? 'America/Chicago');
		$time->setTimezone($timezone);
		return $time->toDateTimeString();
	}
}


if (!function_exists('str_safe')) {
	/** Best & safest way to insert string
	 *
	 * @param string|null $string
	 * @param boolean $isLower
	 * @return string */
	function str_safe(string $string = null, bool $isLower = true)
	{
		if ($isLower) $string = strtolower($string);
		return trim($string);
	}
}

if (!function_exists('unique_multi_array')) {
	function unique_multi_array($array, $key)
	{
		$i = 0; // start index
		$temp_array = []; // temporary array
		$key_array = []; // keys array

		foreach ($array as $val) {
			if (!in_array($val[$key], $key_array)) {
				$temp_array[$i] = $val;
				$key_array[$i]  = $val[$key];
			}
			$i++;
		}
		return $temp_array;
	}
}


if (!function_exists('formatCurrency')) {
	function formatCurrency($amount = 0)
	{
		helper('number');
		return number_to_currency($amount, getDefaultConfig('defaultCurrencyUnit', 'USD'));
	}
}

if (!function_exists('camelCase')) {
	function camelCase($string)
	{
		return lcfirst(str_replace('-', '', ucwords(strtolower($string), '-')));
	}
}
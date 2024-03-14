<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

use Config\Services;

defined('PUBLICPATH') || define('PUBLICPATH', realpath(ROOTPATH . 'public') . DIRECTORY_SEPARATOR);
defined('UPLOADPATH') || define('UPLOADPATH', realpath(PUBLICPATH . 'uploads') . DIRECTORY_SEPARATOR);
defined('MODULESPATH') || define('MODULESPATH', realpath(ROOTPATH . 'modules') . DIRECTORY_SEPARATOR);
defined('WEEKDAY_ARRAY') || define('WEEKDAY_ARRAY', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
defined('VALID_PHONE') || define('VALID_PHONE', '/^((?:[1-9][0-9]{5,28}[0-9])|(?:(00|0)( ){0,1}[1-9][0-9]{3,26}[0-9])|(?:( ){0,1}[1-9][0-9]{4,27}[0-9]))$/m');

if (!function_exists('str_contains')) {
	function str_contains(string $haystack, string $needle): bool
	{
		return '' === $needle || false !== strpos($haystack, $needle);
	}
}

/**
 * Given a controller/method string and any params,
 * will attempt to build the relative URL to the
 * matching route.
 *
 * NOTE: This requires the controller/method to
 * have a route defined in the routes Config file.
 *
 * @param mixed ...$params
 *
 * @return false|string
 */
function route_to(string $method, ...$params)
{
	$to = Services::routes()->reverseRoute($method, ...$params);

	return $to ? site_url($to) : $to;
}

/** Check Directory Exists or not
 *
 * @param string|null $path              Directory Path
 * @param bool        $createIfNotExists Create Directory if not exists
 *
 * @return bool */
function checkDir(?string $path = null, bool $createIfNotExists = true)
{
	if (file_exists($path) && is_dir($path)) return true;
	if ($createIfNotExists) return mkdir($path, 0644, true);

	return false;
}

function isRoute(string $route, bool $allRoute = true)
{
	helper('inflector');
	$currentPath = trim(uri_string(true), '/ ');
	$route = str_replace(site_url(), '', $route);

	if ($allRoute) {
		$route = str_replace(['add/', 'update/', 'list/', 'assign/', 'show/'], '', $route);
		$currentPath = str_replace(['add/', 'update/', 'list/', 'assign/', 'show/'], '', $currentPath);
		if (strpos($currentPath, '/') !== false) $currentPath = substr($currentPath, 0, strrpos($currentPath, '/'));
		return singular($route) === singular($currentPath);
	}

	return singular($route) === singular($currentPath);
}

/** Replace String with Spacial Character
 *
 * @param string $string
 * @param integer $nonHideCount
 * @param boolean $isEmail
 * @return string */
function encrypt($string = null, $nonHideCount = 2, $isEmail = false)
{
	if ($isEmail) $emailString = substr($string, 0, strpos($string, "@"));
	$total = strlen($emailString ?? $string) - $nonHideCount * ($isEmail ? 1 : 2);

	return substr($string, 0, $nonHideCount) . str_pad('', $total, "*") . substr($string, $total + $nonHideCount, $isEmail ? strlen($string) - strlen($emailString) : $nonHideCount);
}

/**
 * Handle & Get Default Configuration Setting from Database
 *
 * @param string $configName `siteName`, `siteLogo`
 * @param string $default A string when setting not found or throw error
 * @return string */
function getDefaultConfig($configName = 'siteName', string $default = '')
{
	if (!empty($configName)) {
		try {
			$default = config('Settings')->$configName;
		} catch (\Throwable $th) {
			log_message('error', $th);
		}
	}

	return $default;
}

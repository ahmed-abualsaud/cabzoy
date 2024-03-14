<?php defined('COLORS') || define('COLORS', [
	'app-black' => '#000000',
	'app-white' => '#ffffff',
	'app-transparent' => '#0000',

	'app-pink100' => '#ffe6ef',
	'app-pink200' => '#ffb3d0',
	'app-pink300' => '#ff80b1',
	'app-pink400' => '#ff4d91',
	'app-pink500' => '#ff0062',
	'app-pink600' => '#cc004e',
	'app-pink700' => '#99003b',
	'app-pink800' => '#800031',
	'app-pink900' => '#4c001d',

	'app-red100' => '#feeded',
	'app-red200' => '#fcc8c8',
	'app-red300' => '#faa3a4',
	'app-red400' => '#f87e7f',
	'app-red500' => '#f54748',
	'app-red600' => '#c4393a',
	'app-red700' => '#932b2b',
	'app-red800' => '#621c1d',
	'app-red900' => '#110606',

	'app-orange100' => '#fff1e6',
	'app-orange200' => '#ffd6b3',
	'app-orange300' => '#ffbb80',
	'app-orange400' => '#ff9f4d',
	'app-orange500' => '#ff7600',
	'app-orange600' => '#cc5e00',
	'app-orange700' => '#994700',
	'app-orange800' => '#662f00',
	'app-orange900' => '#331800',

	'app-blue100' => '#f7f7ff',
	'app-blue200' => '#b6d4fe',
	'app-blue300' => '#7CA9FF',
	'app-blue400' => '#569afe',
	'app-blue500' => '#0d6efd',
	'app-blue600' => '#0a58ca',
	'app-blue700' => '#084298',
	'app-blue800' => '#052c65',
	'app-blue900' => '#031633',

	'app-aqua100' => '#e7fcff',
	'app-aqua200' => '#b6f5fe',
	'app-aqua300' => '#86eefe',
	'app-aqua400' => '#56e7fe',
	'app-aqua500' => '#0ddcfd',
	'app-aqua600' => '#0ab0ca',
	'app-aqua700' => '#088498',
	'app-aqua800' => '#055865',
	'app-aqua900' => '#032c33',

	'app-purple100' => '#f0e7fe',
	'app-purple200' => '#d1b7fb',
	'app-purple300' => '#b388f9',
	'app-purple400' => '#9458f6',
	'app-purple500' => '#6610f2',
	'app-purple600' => '#520dc2',
	'app-purple700' => '#3d0a91',
	'app-purple800' => '#290661',
	'app-purple900' => '#140330',

	'app-yellow100' => '#fff9e6',
	'app-yellow200' => '#ffecb5',
	'app-yellow300' => '#ffe083',
	'app-yellow400' => '#ffd451',
	'app-yellow500' => '#ffc107',
	'app-yellow600' => '#cc9a06',
	'app-yellow700' => '#997404',
	'app-yellow800' => '#664d03',
	'app-yellow900' => '#332701',

	'app-green100' => '#e7fff5',
	'app-green200' => '#b8ffe2',
	'app-green300' => '#89ffce',
	'app-green400' => '#5affba',
	'app-green500' => '#13ff9d',
	'app-green600' => '#0fcc7e',
	'app-green700' => '#0b995e',
	'app-green800' => '#08663f',
	'app-green900' => '#04331f',

	'app-gray100' => '#f2f2f2',
	'app-gray200' => '#dedee6',
	'app-gray300' => '#c6c6cc',
	'app-gray400' => '#adadb3',
	'app-gray500' => '#7c7c80',
	'app-gray600' => '#4d4d4d',
	'app-gray700' => '#3e3e3e',
	'app-gray800' => '#1a1a1a',
	'app-gray900' => '#010003',
]);

if (!function_exists('hexToRGB')) {
	function hexToRGB($colorName)
	{
		$hex = COLORS["app-$colorName"];
		return implode(', ', sscanf($hex, "#%02x%02x%02x")) ?? '255, 255, 255';
	}
}


if (!function_exists('initDynamicColors')) {
	function initDynamicColors()
	{
		$colors = '';
		foreach (COLORS as $key => $value) {
			$colors .= "--$key: $value;";
		}

		$color = getDefaultConfig('webColor', 'red') ?? 'red';
		return print("
		<style>
			:root {
				$colors
				--app-danger: var(--app-red400);
				--app-success: var(--app-green600);
				--app-danger-dark: var(--app-red700);
				--app-success-dark: var(--app-green700);
				--app-dark: var(--app-" . $color . "900);
				--app-border: var(--app-" . $color . "300);
				--app-primary: var(--app-" . $color . "500);
				--app-dark-light: var(--app-" . $color . "700);
				--app-border-light: var(--app-" . $color . "200);
				--app-primary-dark: var(--app-" . $color . "700);
				--app-primary-light: var(--app-" . $color . "100);
				--app-gray: rgba(" . hexToRGB($color . '900') . ", 90%);
				--app-shadow: rgba(" . hexToRGB($color . '500') . ", 50%);
				--app-gray-light: rgba(" . hexToRGB($color . '900') . ", 60%);
				--app-shadow-light: rgba(" . hexToRGB($color . '500') . ", 25%);
			}
		</style>
	");
	}
}

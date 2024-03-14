<?php

if (!function_exists('opt_selected')) {
	/** Option is selected or not
	 *
	 * @param string $first
	 * @param string $sec
	 *
	 * @return string|null */
	function opt_selected(?string $first = null, ?string $sec = null, bool $print = true)
	{
		if (is_numeric($first)) $first = (int) $first;
		if (is_numeric($sec)) $sec     = (int) $sec;
		if ($print && $first === $sec) return print 'selected';
		if ($first === $sec) return 'selected';

		return null;
	}
}

if (!function_exists('select_box')) {
	function select_box($name = null, $title = null, $options = [], $validation = null, $selected = null, $readonly = false, $multiple = false)
	{
		$error     = is($validation, 'object') ? $validation->getError($name) : '';
		$className = is($validation, 'object') && $validation->hasError($name) ? 'is-invalid' : '';

		$option = implode('', array_map(static fn ($key, $value) => '<option value="' . esc($key) . '" ' . opt_selected($key, old($name) ?? $selected, false) . ">{$value}</option>", array_keys($options), array_values($options)));

		$selectDisabled = $multiple ? '' : 'selected';
		$readonly = $readonly ? 'readonly' : '';
		$multiple = $multiple ? 'multiple' : '';

		return "<div class='form-group'><label class='mb-2' for='{$name}'>{$title}</label><div class='form-control p-0 {$className}'><select class='form-control choices' name='{$name}' id='{$name}' {$readonly} {$multiple}><option value='' {$selectDisabled} disabled>Select {$title}</option>{$option}</select></div><div class='invalid-feedback'>{$error}</div></div>";
	}
}

if (!function_exists('badge')) {
	/** Span Status Badge
	 *
	 * @param array  $status ['complete'=> 'success']
	 * @param string $active Active Key
	 *
	 * @return string */
	function badge(array $status = [], string $active = 'complete', bool $light = true)
	{
		$lightClassName = $light ? 'light-' : '';
		$title          = ucwords(trim($active));
		$className      = array_deep_search($active, $status);

		return "<span class='badge bg-{$lightClassName}{$className}'>{$title}</span>";
	}
}

if (!function_exists('image')) {
	function image($image, $name = 'K', $last = 'G')
	{
		helper('text');
		if (!empty($image) && !str_contains($image, 'http')) $image = site_url($image);
		$html = '<div class="avatar bg-warning avatar-xl border border-primary">';
		if ($image) $html .= "<img src='{$image}'>";
		else $html        .= '<span class="avatar-content text-uppercase">' . substr($name, 0, 1) . substr($last, 0, 1) . '</span>';
		$html             .= '</div>';

		return $html;
	}
}

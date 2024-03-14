<?php

namespace App\Entities;

class Category extends BaseEntity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	public function getCategoryName($formatted = true)
	{
		if (!$formatted) return $this->attributes['category_name'];
		return strtoupper($this->attributes['category_name']);
	}

	public function getCategoryImage($formatted = true)
	{
		if (null !== $this->attributes['category_image'] && $formatted) return site_url($this->attributes['category_image']);

		return $this->attributes['category_image'];
	}

	public function getCategoryIcon($formatted = true)
	{
		if (null !== $this->attributes['category_icon'] && $formatted) return site_url($this->attributes['category_icon']);

		return $this->attributes['category_icon'];
	}

	public function setCategoryName(?string $name = null)
	{
		if ($name) $this->attributes['category_name'] = str_safe($name);

		return $this;
	}

	public function setCategoryImage(?string $image = null)
	{
		$this->attributes['category_image'] = !empty($image) && strpos($image, 'uploads/') === false && strpos($image, 'http://') === false && strpos($image, 'https://') === false ? "uploads/{$image}" : $image;
		return $this;
	}

	public function setCategoryIcon(?string $icon = null)
	{
		$this->attributes['category_icon'] = !empty($icon) && strpos($icon, 'uploads/') === false && strpos($icon, 'http://') === false && strpos($icon, 'https://') === false ? "uploads/{$icon}" : $icon;

		return $this;
	}
}

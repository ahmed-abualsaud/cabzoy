<?php

namespace App\Entities;

class Notification extends BaseEntity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	public function getNotificationImage($formatted = true)
	{
		if (!empty($this->attributes['notification_image']) && !str_contains($this->attributes['notification_image'], 'http://')  && !str_contains($this->attributes['notification_image'], 'https://') && $formatted) return site_url($this->attributes['notification_image']);

		return $this->attributes['notification_image'];
	}

	public function setNotificationImage(?string $image = null)
	{
		$this->attributes['notification_image'] = !empty($image) && !str_contains($image, 'uploads/') && strpos($image, 'http://') === false && strpos($image, 'https://') === false ? "uploads/{$image}" : $image;

		return $this;
	}
}

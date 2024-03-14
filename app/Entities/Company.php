<?php

namespace App\Entities;

class Company extends BaseEntity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];
	protected $casts = ['is_default' => 'boolean'];

	public function getCompanyName($formatted = false)
	{
		if ($formatted) return ucwords($this->attributes['company_name']);

		return $this->attributes['company_name'];
	}

	public function getCompanyImage($formatted = true)
	{
		if (null !== $this->attributes['company_image']  && strpos($this->attributes['company_image'], 'http://') !== false && strpos($this->attributes['company_image'], 'https://') !== false && $formatted) return site_url($this->attributes['company_image']);

		return $this->attributes['company_image'];
	}

	public function setCompanyImage(?string $image = null)
	{
		$this->attributes['company_image'] = !empty($image) && strpos($image, 'uploads/') === false && strpos($image, 'http://') === false && strpos($image, 'https://') === false ? "uploads/{$image}" : $image;

		return $this;
	}

	public function getCompanyDocument($formatted = true)
	{
		if (null !== $this->attributes['company_document'] && $formatted) return site_url($this->attributes['company_document']);

		return $this->attributes['company_document'];
	}

	public function setCompanyDocument(?string $document = null)
	{
		$this->attributes['company_document'] = !empty($document) && strpos($document, 'uploads/') === false ? "uploads/{$document}" : $document;

		return $this;
	}
}

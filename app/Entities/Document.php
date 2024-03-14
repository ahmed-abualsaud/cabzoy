<?php

namespace App\Entities;

class Document extends BaseEntity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	public function getDocumentFrontImage($formatted = true)
	{
		if (!empty($this->attributes['document_front_image']) && !str_contains($this->attributes['document_front_image'], 'http://')  && !str_contains($this->attributes['document_front_image'], 'https://') && $formatted) return site_url($this->attributes['document_front_image']);

		return $this->attributes['document_front_image'];
	}

	public function setDocumentFrontImage(?string $image = null)
	{
		$this->attributes['document_front_image'] = !empty($image) && strpos($image, 'uploads/') === false && strpos($image, 'http://') === false && strpos($image, 'https://') === false ? "uploads/{$image}" : $image;

		return $this;
	}

	public function getDocumentBackImage($formatted = true)
	{
		if (!empty($this->attributes['document_back_image']) && !str_contains($this->attributes['document_back_image'], 'http://')  && !str_contains($this->attributes['document_back_image'], 'https://') && $formatted) return site_url($this->attributes['document_back_image']);

		return $this->attributes['document_back_image'];
	}

	public function setDocumentBackImage(?string $image = null)
	{
		$this->attributes['document_back_image'] = !empty($image) && strpos($image, 'uploads/') === false && strpos($image, 'http://') === false && strpos($image, 'https://') === false ? "uploads/{$image}" : $image;

		return $this;
	}
}

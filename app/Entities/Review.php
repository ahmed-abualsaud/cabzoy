<?php

namespace App\Entities;

class Review extends BaseEntity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	public function setReview(?string $name = null)
	{
		if ($name) $this->attributes['review'] = str_safe($name);

		return $this;
	}
}

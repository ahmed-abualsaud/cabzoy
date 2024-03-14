<?php

namespace App\Entities;

class Message extends BaseEntity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	public function setMessage(string $message = null)
	{
		$this->attributes['message'] = str_safe($message);

		return $this;
	}
}

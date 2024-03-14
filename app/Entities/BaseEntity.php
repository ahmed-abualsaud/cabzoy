<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

helper('custom');
class BaseEntity extends Entity
{
	use \Tatter\Relations\Traits\ModelTrait;

	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	public function getCreatedAt($formatted = true)
	{
		if (is_null($this->attributes['created_at'])) return 'not created yet.';

		$timezone                       = config('Settings')->timezone ?? $this->timezone ?? app_timezone();
		$this->attributes['created_at'] = $this->mutateDate($this->attributes['created_at']);
		$this->attributes['created_at']->setTimezone($timezone);
		if ($formatted) return $this->attributes['created_at']->format('M d, Y h:i A');

		return $this->attributes['created_at']->format('Y-m-d h:i:s');
	}
}

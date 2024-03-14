<?php

namespace App\Entities;

class VehicleRelation extends BaseEntity
{
	public function getStartedAt($formatted = true)
	{
		if (is_null($this->attributes['started_at']) || empty($this->attributes['started_at'])) return 'not started yet.';

		$timezone                       = config('Settings')->timezone ?? $this->timezone ?? app_timezone();
		$this->attributes['started_at'] = $this->mutateDate($this->attributes['started_at']);
		$this->attributes['started_at']->setTimezone($timezone);
		if ($formatted) return $this->attributes['started_at']->format('M d, Y h:i A');

		return $this->attributes['started_at']->format('Y-m-d h:i:s');
	}

	public function getEndedAt($formatted = true)
	{
		if (is_null($this->attributes['ended_at']) || empty($this->attributes['ended_at'])) return 'not ended yet.';

		$timezone                       = config('Settings')->timezone ?? $this->timezone ?? app_timezone();
		$this->attributes['ended_at'] = $this->mutateDate($this->attributes['ended_at']);
		$this->attributes['ended_at']->setTimezone($timezone);
		if ($formatted) return $this->attributes['ended_at']->format('M d, Y h:i A');

		return $this->attributes['ended_at']->format('Y-m-d h:i:s');
	}
}

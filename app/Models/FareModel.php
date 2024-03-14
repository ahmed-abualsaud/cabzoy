<?php

namespace App\Models;

use App\Entities\Fare;

class FareModel extends BaseModel
{
	protected $useSoftDeletes  = true;
	protected $table           = 'fares';
	protected $with            = 'users';
	protected $returnType      = Fare::class;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $allowedFields   = ['fare_name', 'fare', 'min_fare', 'fare_day', 'start_time', 'end_time', 'fare_from', 'fare_to', 'fare_type', 'fare_status', 'created_by'];

	/** Filter rows with Fare Type
	 *
	 * @param string $type `hourly | base` default `hourly`
	 * @return self */
	public function isType(string $type = 'hourly')
	{
		$builder = $this->builder();
		if ($type === 'hourly') $builder->where('fare_day!=', null)->where('start_time!=', null)->where('end_time!=', null);
		elseif ($type === 'base') $builder->where('fare_from!=', null)->where('fare_to!=', null);
		return $this;
	}

	/** Filter rows with FareDay, StartTime & EndTime already exists
	 *
	 * @param string $fareDay Day of the fare eg. `Monday`
	 * @param string $startTime Start Time of the Fare eg. `10:00:00`
	 * @param string $endTime End Time of the Fare eg. `11:00:00`
	 * @return self */
	public function isHourExists(string $fareDay = null, string $startTime = null, string $endTime = null)
	{
		$builder = $this->builder();
		$builder->where('fare_day', $fareDay)->where('start_time', $startTime)->where('end_time', $endTime);

		return $this;
	}

	/** Filter rows within the given distance
	 *
	 * @param float $distance eg. `1000`
	 * @return self */
	public function isBetween(float $distance = 0)
	{
		$builder = $this->builder();
		$builder->where('fare_from <=', $distance)->where('fare_to >=', $distance);

		return $this;
	}

	/** Filter rows within the given datetime
	 *
	 * @param string $datetime  eg. `2019-01-01 10:00:00`
	 * @return self */
	public function timeBetween(string $datetime = null)
	{
		$builder = $this->builder();

		$dt = \DateTime::createFromFormat('Y-m-d H:i:s', !empty($datetime) ? $datetime : date('Y-m-d H:i:s'));

		if (!($dt !== false && !array_sum($dt::getLastErrors())))
			$dt = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));

		$date = $dt->format('Y-m-d ');
		$fullDateTime = $dt->format('Y-m-d H:i:s');

		$builder->where("start_time IS NOT NULL AND end_time IS NOT NULL AND UNIX_TIMESTAMP(CONCAT('$date', start_time)) < UNIX_TIMESTAMP('$fullDateTime') AND UNIX_TIMESTAMP(CONCAT('$date', end_time)) > UNIX_TIMESTAMP('$fullDateTime') AND fare_day = DAYNAME('$fullDateTime')");

		return $this;
	}
}

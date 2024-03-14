<?php

namespace App\Models;

class FareRelationModel extends BaseModel
{
	protected $useSoftDeletes  = false;
	protected $useTimestamps   = false;
	protected $returnType      = 'object';
	protected $table           = 'fare_relations';
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $with            = ['categories', 'vehicles', 'zones', 'fares'];
	protected $allowedFields   = ['category_id', 'vehicle_id', 'zone_id', 'fare_id'];


	/** Get Type of data
	 *
	 * @param string|null $relationType `zone|category|vehicle`
	 * @return self */
	public function type(?string $relationType = null)
	{
		if (!is_null($relationType)) $this->builder()->where($relationType . "_id!=", null);
		return $this;
	}

	/**
	 * Get Zone by Latitude & Longitude
	 *
	 * @param float $lat
	 * @param float $long
	 * @return object|null */
	public function getZonesFare(?float $lat = null, ?float $long = null)
	{
		helper('geo');

		$zone  = null;
		$query = $this->builder()
			->join('zones', 'zones.id = fare_relations.zone_id', 'left')
			->where('zones.zone_type', 'plot')->where('zones.deleted_at', null)
			->orderBy('zones.id', 'DESC')
			->get();

		foreach ($query->getResultObject() as $value) {
			if (checkPointInsidePolygon($lat, $long, $value->zone)) {
				$zone = $value;
				break;
			}
		}

		return $zone;
	}

	/** Relate Fare to Other Tables
	 *
	 * @param integer $zone_id
	 * @param integer $category_id
	 * @param integer $vehicle_id
	 * @return self */
	public function relate(int $zone_id = null, int $category_id = null, int $vehicle_id = null)
	{
		$builder = $this->builder();
		if (!is_null($zone_id)) $builder->where('zone_id', $zone_id);
		if (!is_null($vehicle_id)) $builder->where('vehicle_id', $vehicle_id);
		if (!is_null($category_id)) $builder->where('category_id', $category_id);

		return $this;
	}
}

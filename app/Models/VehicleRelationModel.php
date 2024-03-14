<?php

namespace App\Models;

use App\Entities\VehicleRelation;

class VehicleRelationModel extends BaseModel
{
	protected $useSoftDeletes  = true;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $returnType      = VehicleRelation::class;
	protected $table           = 'categories_users_vehicles';
	protected $with            = ['categories', 'users', 'vehicles'];
	protected $allowedFields   = ['category_id', 'user_id', 'vehicle_id', 'started_at', 'ended_at', 'status'];

	/** Filter Vehicle Service is not ended and available
	 *
	 * @param int $userId
	 * @param int $vehicleId
	 * @return self */
	public function notEnded(?int $userId = null, ?int $vehicleId = null)
	{
		$builder = $this->builder()->where('ended_at', null)
			->where('status', 'available');
		if (!is_null($userId)) $builder->where('user_id', $userId);
		if (!is_null($vehicleId)) $builder->where('vehicle_id', $vehicleId);

		return $this;
	}

	/** Filter via Status
	 *
	 * @param string $type `available|not-available|busy`
	 * @return self */
	public function statusIs(string $type = 'available')
	{
		$this->builder()->where('status', $type);
		return $this;
	}

	public function getVehicleCategoryArray()
	{
		$categoryArray = [];
		$categoryArray = array_map(static function ($relationObject) use ($categoryArray) {
			return $categoryArray[$relationObject->category_id] = $relationObject->category_id;
		}, $this->notEnded()->findAll());
		return $categoryArray;
	}

	/** Get Category, Vehicle & Driver Relation form Category Name
	 *
	 * @param bool $driverAvailable if it's true then get only available drivers
	 * @param string $categoryName Vehicle Category Name
	 * @return self */
	public function getRelationFromCategoryName(bool $driverAvailable = true, string $categoryName = null)
	{
		$builder = $this->builder();
		if ($driverAvailable)
			$builder->where('ended_at', null)->where('status', 'available');

		if (!is_null($categoryName))
			$builder->join('categories', 'categories.id = categories_users_vehicles.category_id')
				->where('categories.category_name', strtolower($categoryName))
				->where('categories.category_type', 'vehicle');

		return $this;
	}
}

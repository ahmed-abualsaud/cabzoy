<?php

namespace App\Models;

use App\Entities\Vehicle;

class VehicleModel extends BaseModel
{
	protected $useSoftDeletes  = true;
	protected $with            = 'users';
	protected $table           = 'vehicles';
	protected $returnType      = Vehicle::class;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $allowedFields   = [
		'vehicle_number', 'vehicle_brand', 'vehicle_modal', 'vehicle_color', 'vehicle_image', 'vehicle_seats', 'vehicle_status', 'created_by'
	];

	/** Status of Vehicle
	 *
	 * @param string $status `approved|rejected|pending`
	 * @return self */
	public function is(string $status = 'approved')
	{
		$this->builder()->where('vehicle_status', $status);
		return $this;
	}

	public function relate(?int $category_id = null, ?int $user_id = null, ?int $vehicle_id = null)
	{
		return $this->builder('categories_users_vehicles')->insert([
			'user_id'     => $user_id,
			'vehicle_id'  => $vehicle_id,
			'category_id' => $category_id,
		]);
	}
}

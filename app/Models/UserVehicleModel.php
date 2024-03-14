<?php

namespace App\Models;

use App\Entities\UserVehicle;

class UserVehicleModel extends BaseModel
{
	protected $useSoftDeletes  = false;
	protected $useTimestamps   = false;
	protected $table           = 'user_vehicles';
	protected $returnType      = UserVehicle::class;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $with            = ['users', 'vehicles'];
	protected $allowedFields   = ['user_id', 'vehicle_id'];
}

<?php

namespace App\Models;

use App\Entities\OrderDriver;

class OrderDriverModel extends BaseModel
{
	protected $useSoftDeletes  = false;
	protected $useTimestamps   = false;
	protected $table           = 'order_drivers';
	protected $returnType      = OrderDriver::class;
	protected $with            = ['users', 'orders'];
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $allowedFields   = ['driver_id', 'order_id', 'action'];
}

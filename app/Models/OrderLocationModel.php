<?php

namespace App\Models;

use App\Entities\OrderLocation;

class OrderLocationModel extends BaseModel
{
	protected $useSoftDeletes  = true;
	protected $with            = 'orders';
	protected $table           = 'order_locations';
	protected $returnType      = OrderLocation::class;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $allowedFields   = ['order_id', 'order_location_type', 'order_location_text', 'order_location_lat', 'order_location_long'];
}

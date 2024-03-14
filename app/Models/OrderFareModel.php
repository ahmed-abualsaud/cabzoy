<?php

namespace App\Models;

use App\Entities\OrderFare;

class OrderFareModel extends BaseModel
{
	protected $useSoftDeletes  = false;
	protected $useTimestamps   = false;
	protected $table           = 'order_fares';
	protected $returnType      = OrderFare::class;
	protected $with            = ['fares', 'orders'];
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $allowedFields   = ['fare_id', 'order_id'];
}

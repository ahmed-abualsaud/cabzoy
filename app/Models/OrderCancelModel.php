<?php

namespace App\Models;

use App\Entities\OrderCancel;

class OrderCancelModel extends BaseModel
{
	protected $useSoftDeletes  = false;
	protected $useTimestamps   = false;
	protected $table           = 'order_cancels';
	protected $returnType      = OrderCancel::class;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $with            = ['users', 'orders', 'categories'];
	protected $allowedFields   = ['user_id', 'order_id', 'category_id', 'comment'];
}

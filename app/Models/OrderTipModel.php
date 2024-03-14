<?php

namespace App\Models;

use App\Entities\OrderTip;

class OrderTipModel extends BaseModel
{
	protected $useSoftDeletes  = true;
	protected $useTimestamps   = true;
	protected $table           = 'order_tips';
	protected $returnType      = OrderTip::class;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $with            = ['tips', 'orders', 'users'];
	protected $allowedFields   = ['tip_id', 'order_id', 'user_id', 'amount'];
}

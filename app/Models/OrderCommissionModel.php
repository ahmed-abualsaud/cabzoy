<?php

namespace App\Models;

use App\Entities\OrderCommission;

class OrderCommissionModel extends BaseModel
{
	protected $useTimestamps   = false;
	protected $table           = 'order_commissions';
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $returnType      = OrderCommission::class;
	protected $with            = ['orders', 'commissions'];
	protected $allowedFields   = ['order_id', 'commission_id', 'commission_amount'];
}

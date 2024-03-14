<?php

namespace App\Models;

use App\Entities\OrderPromo;

class OrderPromoModel extends BaseModel
{
	protected $useSoftDeletes  = true;
	protected $useTimestamps   = true;
	protected $table           = 'order_promos';
	protected $returnType      = OrderPromo::class;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $with            = ['promos', 'orders', 'users'];
	protected $allowedFields   = ['promo_id', 'order_id', 'user_id', 'discount', 'created_at', 'updated_at', 'deleted_at'];
}

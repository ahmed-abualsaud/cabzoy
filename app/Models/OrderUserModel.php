<?php

namespace App\Models;

use App\Entities\OrderUser;

class OrderUserModel extends BaseModel
{
	protected $useSoftDeletes  = false;
	protected $useTimestamps   = false;
	protected $table           = 'order_users';
	protected $returnType      = OrderUser::class;
	protected $with            = ['users', 'orders'];
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $allowedFields   = ['user_id', 'order_id'];
}

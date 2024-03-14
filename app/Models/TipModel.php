<?php

namespace App\Models;

use App\Entities\Tip;

class TipModel extends BaseModel
{
	protected $useSoftDeletes  = true;
	protected $table           = 'tips';
	protected $returnType      = Tip::class;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $with            = ['users', 'order_tips'];
	protected $allowedFields   = [
		'user_id', 'tip_amount', 'tip_comment',
	];
}

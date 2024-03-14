<?php

namespace App\Models;

use App\Entities\ReferUser;

class ReferUserModel extends BaseModel
{
	protected $useSoftDeletes  = true;
	protected $table           = 'refer_users';
	protected $returnType      = ReferUser::class;
	protected $with            = ['users', 'refers'];
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $allowedFields   = ['user_id', 'refer_id'];
}

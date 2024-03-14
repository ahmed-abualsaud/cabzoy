<?php

namespace App\Models;

use App\Entities\Refer;

class ReferModel extends BaseModel
{
	protected $useSoftDeletes  = true;
	protected $with            = 'users';
	protected $table           = 'refers';
	protected $returnType      = Refer::class;
	protected $allowedFields   = ['user_id', 'refer'];
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
}

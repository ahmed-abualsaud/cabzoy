<?php

namespace App\Models;

use App\Entities\ReviewDriver;

class ReviewDriverModel extends BaseModel
{
	protected $useSoftDeletes  = false;
	protected $useTimestamps   = false;
	protected $table           = 'review_drivers';
	protected $returnType      = ReviewDriver::class;
	protected $with            = ['users', 'reviews'];
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $allowedFields   = ['review_id', 'user_id', 'rating'];
}

<?php

namespace App\Models;

use App\Entities\Review;

class ReviewModel extends BaseModel
{
	protected $useSoftDeletes  = true;
	protected $with            = 'users';
	protected $table           = 'reviews';
	protected $returnType      = Review::class;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $allowedFields   = ['user_id', 'order_id', 'review', 'rating'];
}

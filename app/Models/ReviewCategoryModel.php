<?php

namespace App\Models;

use App\Entities\ReviewCategory;

class ReviewCategoryModel extends BaseModel
{
	protected $useSoftDeletes  = false;
	protected $useTimestamps   = false;
	protected $table           = 'review_categories';
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $returnType      = ReviewCategory::class;
	protected $with            = ['categories', 'reviews'];
	protected $allowedFields   = ['review_id', 'category_id', 'rating'];
}

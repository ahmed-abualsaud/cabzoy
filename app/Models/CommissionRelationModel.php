<?php

namespace App\Models;

use App\Entities\CommissionRelation;

class CommissionRelationModel extends BaseModel
{
	protected $useSoftDeletes  = false;
	protected $useTimestamps   = false;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $table           = 'commission_relations';
	protected $returnType      = CommissionRelation::class;
	protected $with            = ['companies', 'users', 'commissions'];
	protected $allowedFields   = ['company_id', 'user_id', 'commission_id'];

	/** Get Type of data
	 *
	 * @param string|null $relationType `user|company`
	 * @return self */
	public function type(?string $relationType = null)
	{
		if (!is_null($relationType)) $this->builder()->where($relationType . "_id!=", null);
		return $this;
	}
}

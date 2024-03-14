<?php

namespace App\Models;

use App\Entities\Commission;

class CommissionModel extends BaseModel
{
	protected $useSoftDeletes  = true;
	protected $table           = 'commissions';
	protected $returnType      = Commission::class;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $with            = ['users', 'categories', 'vehicles', 'companies'];
	protected $allowedFields   = ['category_id', 'vehicle_id', 'company_id', 'commission_name', 'commission', 'commission_type', 'commission_status', 'created_by'];

	/** Get Type of data
	 *
	 * @param string|null $category_id
	 * @param string|null $vehicle_id
	 * @param string|null $company_id
	 * @return self */
	public function relate($category_id = null, $vehicle_id = null, $company_id = null)
	{
		$builder = $this->builder();

		if (!is_null($category_id)) $builder->where('category_id', $category_id);
		if (!is_null($vehicle_id)) $builder->where('vehicle_id', $vehicle_id);
		if (!is_null($company_id)) $builder->where('company_id', $company_id);

		return $this;
	}
}

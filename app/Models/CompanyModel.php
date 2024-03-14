<?php

namespace App\Models;

use App\Entities\Company;

class CompanyModel extends BaseModel
{
	protected $useSoftDeletes = true;
	protected $with           = 'users';
	protected $table          = 'companies';
	protected $returnType     = Company::class;
	protected $allowedFields  = ['company_name', 'company_email', 'company_mobile', 'company_image', 'company_address', 'company_document', 'company_status', 'created_by', 'is_default'];

	protected $validationRules = [
		'is_default'  => 'in_list[0, 1]',
	];



	/** Default of Company
	 *
	 * @return self */
	public function isDefault()
	{
		$this->builder()->where('is_default', '1');
		return $this;
	}
}

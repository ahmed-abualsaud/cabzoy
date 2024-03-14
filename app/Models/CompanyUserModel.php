<?php

namespace App\Models;

use App\Entities\CompanyUser;

class CompanyUserModel extends BaseModel
{
	protected $useSoftDeletes = false;
	protected $useTimestamps  = false;
	protected $returnType     = CompanyUser::class;
	protected $with           = ['users', 'companies'];
	protected $allowedFields  = ['user_id', 'company_id'];
	protected $table          = 'companies_users_relations';
}

<?php

namespace App\Models;

use App\Entities\Account;

class AccountModel extends BaseModel
{
	protected $useSoftDeletes = true;
	protected $with           = 'users';
	protected $table          = 'accounts';
	protected $returnType     = Account::class;
	protected $allowedFields  = [
		'account_number', 'account_holdername', 'bank_name', 'branch_number', 'branch_address', 'account_code', 'account_status', 'created_by', 'is_default'
	];
	protected $validationRules = [
		'id'             => 'permit_empty|is_natural_no_zero',
		'is_default'     => 'in_list[0, 1]',
		'account_status' => 'in_list[approved, pending, rejected]',
	];


	/** Status of Account
	 *
	 * @param string $status `approved|rejected|pending`
	 * @return self */
	public function statusIs(string $status = 'approved')
	{
		$this->builder()->where('account_status', $status);
		return $this;
	}

	/** Default of Account
	 *
	 * @return self */
	public function isDefault()
	{
		$this->builder()->where('is_default', '1');
		return $this;
	}
}

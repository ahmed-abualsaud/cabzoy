<?php

namespace App\Models;

use App\Entities\Withdraw;

class WithdrawModel extends BaseModel
{
	protected $useSoftDeletes  = true;
	protected $with            = 'users';
	protected $table           = 'withdraws';
	protected $returnType      = Withdraw::class;
	protected $allowedFields   = ['user_id', 'amount', 'comment', 'status'];
	protected $validationRules = [
		'id'     => 'permit_empty|is_natural_no_zero',
		'status' => 'in_list[approved, pending, rejected]'
	];


	/** Status of Withdraw
	 *
	 * @param string $status `approved|rejected|pending`
	 * @return self */
	public function statusIs(string $status = 'approved')
	{
		$this->builder()->where('status', $status);
		return $this;
	}
}

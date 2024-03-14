<?php

namespace App\Models;

use App\Entities\Transaction;

class TransactionModel extends BaseModel
{
	protected $useSoftDeletes  = true;
	protected $table           = 'transactions';
	protected $returnType      = Transaction::class;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $with            = ['users', 'companies'];
	protected $allowedFields   = ['user_id', 'company_id', 'txn', 'summary', 'amount', 'action', 'status', 'transaction_type'];

	/** @param string $status `success`, `pending` or `failed`
	 * @return self */
	public function is(string $status = 'success')
	{
		$this->builder()->where('status', $status);
		return $this;
	}

	public function getDetails($user_id = null, $company_id = null)
	{
		$builder = $this->builder();
		$builder->select([
			'id', 'user_id', 'company_id', "SUM(COALESCE(CASE WHEN action = 'debit' THEN amount END,0)) total_debits", "SUM(COALESCE(CASE WHEN action = 'credit' THEN amount END,0)) total_credits", "SUM(COALESCE(CASE WHEN action = 'credit' THEN amount END,0)) - SUM(COALESCE(CASE WHEN action = 'debit' THEN amount END,0)) balance"
		])->where('status', 'success');

		if (!empty($user_id)) $builder->where('user_id', $user_id);
		if (!empty($company_id)) $builder->where('company_id', $company_id);

		$builder->groupBy('status')->having('balance <>', 0);

		return $this;
	}
}

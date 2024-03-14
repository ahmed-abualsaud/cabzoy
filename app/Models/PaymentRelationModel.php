<?php

namespace App\Models;

use App\Entities\PaymentRelation;

class PaymentRelationModel extends BaseModel
{
	protected $useSoftDeletes = false;
	protected $useTimestamps  = false;
	protected $table          = 'payment_relations';
	protected $returnType     = PaymentRelation::class;
	protected $with           = ['users', 'companies', 'cards', 'accounts'];
	protected $allowedFields  = ['company_id', 'user_id', 'card_id', 'account_id', 'relation_type'];
	protected $validationRules = [
		'id'            => 'permit_empty|is_natural_no_zero',
		'relation_type' => 'in_list[card, account]',
	];


	/** Type of Relation
	 *
	 * @param string $type `card|account`
	 * @return self */
	public function typeIs(string $type = 'card')
	{
		$this->builder()->where('relation_type', $type);
		return $this;
	}
}

<?php

namespace App\Models;

use App\Entities\Card;

class CardModel extends BaseModel
{
	protected $useSoftDeletes = true;
	protected $with           = 'users';
	protected $table          = 'cards';
	protected $returnType     = Card::class;
	protected $allowedFields  = [
		'card_number', 'card_holdername', 'card_month', 'card_year', 'card_cvv', 'card_status', 'card_type', 'created_by', 'is_default'
	];
	protected $validationRules = [
		'id'          => 'permit_empty|is_natural_no_zero',
		'is_default'  => 'in_list[0, 1]',
		'card_type'   => 'in_list[credit, debit]',
		'card_status' => 'in_list[approved, pending, rejected]',
	];


	/** Status of Card
	 *
	 * @param string $status `approved|rejected|pending`
	 * @return self */
	public function statusIs(string $status = 'approved')
	{
		$this->builder()->where('card_status', $status);
		return $this;
	}

	/** Type of Card
	 *
	 * @param string $type `credit|debit`
	 * @return self */
	public function typeIs(string $type = 'credit')
	{
		$this->builder()->where('card_type', $type);
		return $this;
	}

	/** Default of Card
	 *
	 * @return self */
	public function isDefault()
	{
		$this->builder()->where('is_default', '1');
		return $this;
	}
}

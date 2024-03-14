<?php

namespace App\Models;

use App\Entities\Promo;

class PromoModel extends BaseModel
{
	protected $useSoftDeletes = true;
	protected $with           = 'users';
	protected $table          = 'promos';
	protected $returnType     = Promo::class;
	protected $allowedFields  = [
		'user_id', 'promo_code', 'promo_discount', 'promo_min_amount', 'promo_max_amount', 'promo_count', 'promo_discount_type', 'promo_status',
	];
	protected $validationRules = [
		'id'                  => 'permit_empty|is_natural_no_zero',
		'promo_discount_type' => 'in_list[percentage, flat]',
		'promo_status'        => 'in_list[approved, pending, rejected]',
	];


	/** Status of Promo
	 *
	 * @param string $status `approved|rejected|pending`
	 * @return self */
	public function statusIs(string $status = 'approved')
	{
		$this->builder()->where('promo_status', $status);
		return $this;
	}

	public function valid($amount = 0, $useCount = 0)
	{
		$this->builder()
			->where("`promo_status` = 'approved' AND (`promo_count` > '$useCount' OR `promo_count` = '0') AND (`promo_min_amount` < '$amount' OR `promo_min_amount` = '0') AND (`promo_max_amount` > '$amount' OR `promo_max_amount` = '0')");

		return $this;
	}
}

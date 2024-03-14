<?php

namespace App\Entities;

class OrderCommission extends BaseEntity
{
	protected $casts = [
		'commission_amount' => 'float',
	];
}

<?php

namespace App\Entities;

class Card extends BaseEntity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];
	protected $casts = ['is_default' => 'boolean'];
}

<?php

namespace App\Models;

use App\Entities\EmergencyContact;

class EmergencyContactModel extends BaseModel
{
	protected $useSoftDeletes = true;
	protected $with           = 'users';
	protected $table          = 'emergency_contacts';
	protected $returnType     = EmergencyContact::class;
	protected $allowedFields  = [
		'user_id', 'name', 'phone', 'email'
	];
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
}

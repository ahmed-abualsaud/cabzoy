<?php

namespace App\Models;

use App\Entities\AuthLogin;

class AuthLoginModel extends BaseModel
{
	protected $primaryKey       = 'id';
	protected $useAutoIncrement = true;
	protected $useSoftDeletes   = false;
	protected $protectFields    = true;
	protected $useTimestamps    = true;
	protected $table            = 'auth_logins';
	protected $with             = 'users';
	protected $DBGroup          = 'default';
	protected $returnType       = AuthLogin::class;
	protected $allowedFields = [
		'ip_address', 'email', 'user_id', 'date', 'success'
	];
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
}

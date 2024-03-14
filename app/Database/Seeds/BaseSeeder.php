<?php

namespace App\Database\Seeds;

use App\{Entities\User, Models\GroupModel, Models\UserModel};
use CodeIgniter\Database\Seeder;
use Myth\Auth\Config\Services;

class BaseSeeder extends Seeder
{
	/** Authorize permissions & roles
	 *
	 * @var \Myth\Auth\Authorization\FlatAuthorization */
	protected $authorize;

	protected $user;
	protected $userModel;

	public function __construct()
	{
		helper('db');
		$this->db        = db();
		$this->user      = new User();
		$this->userModel = new UserModel();
		$this->authorize = Services::authorization(new GroupModel(), null, new UserModel());
	}
}

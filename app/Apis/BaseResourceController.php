<?php

namespace App\Apis;

use App\Models\{GroupModel, UserModel};
use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use Myth\Auth\Config\Services as AuthServices;

class BaseResourceController extends ResourceController
{
	protected $pager;

	/**
	 * @var \Myth\Auth\Config\Auth
	 */
	protected $config;

	/** Authorize permissions & roles
	 *
	 * @var \Myth\Auth\Authorization\FlatAuthorization */
	protected $authorize;

	/** Authenticate users
	 *
	 * @var \App\Libraries\AppAuthenticator */
	protected $authenticate;

	protected $helpers = ['custom', 'notification', 'geo', 'auth', 'docs'];

	/** User Model
	 *
	 * @var \App\Models\UserModel */
	protected $userModel;

	public function __construct()
	{
		$this->config       = config('Auth');
		$this->userModel    = new UserModel();
		$this->pager        = Services::pager();
		$this->authenticate = AuthServices::authentication('app', new UserModel());
		$this->authorize    = AuthServices::authorization(new GroupModel(), null, new UserModel());
	}

	/** Response format success
	 * @param mixed $data
	 * @param string $status `success|updated|created|deleted|no-content`
	 * @param string $message
	 *
	 * @return \CodeIgniter\HTTP\Response */
	public function success($data = null, $status = 'success', $message = 'success')
	{
		switch ($status):
			case 'created':
				$status = $this->codes['created'];
				break;

			case 'updated':
				$status = $this->codes['updated'];
				break;

			case 'no-content':
				$status = $this->codes['no_content'];
				break;

			case 'deleted':
				$status = $this->codes['deleted'];
				break;

			default:
				$status = 200;
				break;
		endswitch;

		$output = ['status' => $status, 'error'  => null, 'messages' => ['success' => $message]];

		if (!is_null($data)) $output['data'] = $data;
		return $this->respond($output, $status, $message);
	}
}

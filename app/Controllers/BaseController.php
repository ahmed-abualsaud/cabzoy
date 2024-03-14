<?php

namespace App\Controllers;

use App\Models\{GroupModel, UserModel};
use CodeIgniter\{Controller, HTTP\CLIRequest, HTTP\IncomingRequest, HTTP\RequestInterface, HTTP\ResponseInterface};
use Config\Services;
use Myth\Auth\Config\Services as AuthServices;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
	/**
	 * Instance of the main Request object.
	 *
	 * @var CLIRequest|IncomingRequest
	 */
	protected $request;

	/**
	 * Demo Message
	 *
	 * @var array<string>
	 */
	protected array $demoMessage;

	/** Instance of Session
	 *
	 * @var \CodeIgniter\Session\Session */
	protected $session;

	/** Instance of Validation
	 *
	 * @var \CodeIgniter\Validation\Validation */
	protected $validation;

	/** Authorize permissions & roles
	 *
	 * @var \Myth\Auth\Authorization\FlatAuthorization */
	protected $authorize;

	/** Authenticate users
	 *
	 * @var \Myth\Auth\Authentication\LocalAuthenticator */
	protected $authenticate;

	/** Type of Environment
	 *
	 * @var boolean */
	protected $isDemo;

	/** User Model Instance
	 *
	 * @var \App\Models\UserModel */
	protected $userModel;

	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = ['html', 'db', 'role', 'custom', 'theme', 'array', 'auth', 'number', 'filesystem', 'inflector'];

	/**
	 * Be sure to declare properties for any property fetch you initialized.
	 * The creation of dynamic property is deprecated in PHP 8.2.
	 */
	// protected $session;

	/**
	 * @return void
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		// Preload any models, libraries, etc, here.
		$this->userModel    = new UserModel();
		$this->session      = Services::session();
		$this->validation   = Services::validation();
		$this->isDemo       = env('ENV_TYPE', 'dev') === 'demo';
		$this->authenticate = AuthServices::authentication('app', new UserModel());
		$this->authorize    = AuthServices::authorization(new GroupModel(), null, new UserModel());
		$this->demoMessage  = env('ENV_TYPE', 'dev') === 'demo' ? [
			'Few actions are blocked in this version, which is mainly for demonstration purposes.',
			'The demo version does not allow you to make any changes.'
		] : [];
	}
}

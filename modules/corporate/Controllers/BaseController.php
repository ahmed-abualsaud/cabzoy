<?php

namespace Modules\Corporate\Controllers;

use App\Controllers\BaseController as ControllersBaseController;
use CodeIgniter\{HTTP\RequestInterface, HTTP\ResponseInterface};
use Modules\Corporate\Models\CompanyModel;
use Psr\Log\LoggerInterface;

abstract class BaseController extends ControllersBaseController
{

	/** Company Model Instance
	 *
	 * @var \Modules\Corporate\Models\CompanyModel */
	protected $companyModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		$this->companyModel = new CompanyModel();
		helper('Modules\Corporate\Helpers\corporate');
	}
}

if (!function_exists('corporateView')) {
	function corporateView($name, $data)
	{
		return view("Modules\Corporate\Views/$name", $data);
	}
}

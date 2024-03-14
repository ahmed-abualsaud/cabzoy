<?php

namespace App\Controllers;

use App\Models\ReferUserModel;
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class ReferController extends BaseController
{
	protected $referUserModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->referUserModel = new ReferUserModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('refers', 'read')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to see refers',
		]);

		$refers = $this->referUserModel->orderBy('id', 'desc')->findAll();
		return view('pages/refer/list', ['refers' => $refers]);
	}
}

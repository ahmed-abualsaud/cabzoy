<?php

namespace App\Controllers;

use App\Models\AuthLoginModel;
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class ReportController extends BaseController
{
	protected $authLoginModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->authLoginModel = new AuthLoginModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('reports', 'assign')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to assign report.',
		]);

		$reports = $this->authLoginModel->orderBy('id', 'desc')->findAll();

		return view('pages/report/list', [
			'reports' => $reports, 'validation' => $this->validation,
		]);
	}
}

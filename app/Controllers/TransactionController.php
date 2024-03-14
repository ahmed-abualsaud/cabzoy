<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class TransactionController extends BaseController
{
	protected $transactionModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->transactionModel = new TransactionModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('transactions', 'read, mine', true)) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to see transactions',
		]);
		// Get all transactions
		$transactions = $this->transactionModel->orderBy('id', 'desc')->findAll();
		return view('pages/transaction/list', ['transactions' => $transactions]);
	}
}

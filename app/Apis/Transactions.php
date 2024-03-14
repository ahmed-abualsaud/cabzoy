<?php

namespace App\Apis;

use App\Models\TransactionModel;
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Transactions extends BaseResourceController
{
	protected $modelName = TransactionModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$sort    = $this->request->getVar('sort') ?? 'desc';
		$perPage = $this->request->getVar('perPage') ?? null;

		$transactions = $this->model->where('user_id', $this->authenticate->id())->orderBy('id', $sort)->paginate($perPage);
		return $this->success($transactions, 'success', 'Transactions fetched successfully.');
	}

	public function show($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$transactions = $this->model->getDetails($this->authenticate->id())->first();
		if (!is($transactions, 'object')) $transactions = ["total_debits" => 0, "total_credits" => 0, "balance" => 0];
		return $this->success($transactions, 'success', 'Transactions fetched successfully.');
	}
}

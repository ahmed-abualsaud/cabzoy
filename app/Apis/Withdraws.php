<?php

namespace App\Apis;

use App\{Entities\Withdraw, Models\WalletModel, Models\WithdrawModel};
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Withdraws extends BaseResourceController
{
	protected $walletModel;
	protected $modelName = WithdrawModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
		$this->walletModel = new WalletModel();
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$sort    = $this->request->getVar('sort') ?? 'desc';
		$perPage = $this->request->getVar('perPage') ?? null;

		$withdraws = $this->model->where('user_id', $this->authenticate->id())->orderBy('id', $sort)->paginate($perPage);

		return $this->success($withdraws, 'success', 'Withdraws fetched successfully.');
	}

	public function show($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$withdraw = $this->model->find($id);
		if (!is($withdraw, 'object')) return $this->fail('Withdraw not found.');

		return $this->success($withdraw, 'success', 'Withdraws fetched successfully.');
	}

	public function create()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$rules = ['amount' => 'required|is_natural_no_zero'];
		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);

		$user_id = $this->authenticate->id();
		$amount  = $this->request->getVar('amount');

		$wallet = $this->walletModel->getDetails($user_id)->first();
		if (!is($wallet, 'object')) $wallet = ["total_debits" => 0, "total_credits" => 0, "balance" => 0];
		if (!(isset($wallet->balance) && $wallet->balance > $amount)) return $this->fail(
			'Insufficient balance in your wallet.'
		);

		$withdraw_id = $this->model->insert(new Withdraw([
			'status' => 'pending', 'amount' => $amount, 'user_id' => $user_id,
		]));
		if (!$withdraw_id) return $this->fail('Something went wrong while saving withdraw.');

		return $this->success(null, 'created', 'Withdraw saved successfully.');
	}

	public function delete($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$withdraw = $this->model->find($id);
		if (!is($withdraw, 'object')) return $this->fail('Withdraw not found.');

		$withdraw = $this->model->delete($id);
		if (!$withdraw) return $this->fail('Something went wrong while deleting withdraw.');

		return $this->success(null, 'deleted', 'Withdraw deleted successfully.');
	}
}

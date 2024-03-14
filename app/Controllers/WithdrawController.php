<?php

namespace App\Controllers;

use App\Entities\{Transaction, Wallet, WalletTransaction, Withdraw};
use App\Models\{PaymentRelationModel, TransactionModel, WalletModel, WalletTransactionModel, WithdrawModel};
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Config\Services;
use Psr\Log\LoggerInterface;

class WithdrawController extends BaseController
{
	protected $walletModel;
	protected $withdrawModel;
	protected $transactionModel;
	protected $paymentRelationModel;
	protected $walletTransactionModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->walletModel            = new WalletModel();
		$this->withdrawModel          = new WithdrawModel();
		$this->transactionModel       = new TransactionModel();
		$this->paymentRelationModel   = new PaymentRelationModel();
		$this->walletTransactionModel = new WalletTransactionModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('withdraws', 'read')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to see withdraws',
		]);
		$withdraws = $this->withdrawModel->orderBy('id', 'desc')->findAll();

		return view('pages/withdraw/list', ['withdraws' => $withdraws]);
	}

	public function add()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('withdraws', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create withdraw',
		]);

		$users = $this->userModel->findAll();
		if (is($users, 'array')) foreach ($users as $key => $value) {
			$wallet = $this->walletModel->getDetails($value->id)->first();
			if (is($wallet, 'object')) $users[$key]->balance = $wallet->balance;
			else $users[$key]->balance = 0;
		}

		return view('pages/withdraw/add', ['validation' => $this->validation, 'users' => $users]);
	}

	public function save(): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('withdraws', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create withdraw',
		]);

		$rules = [
			"amount"  => "required|integer",
			"user_id" => "required|integer",
			"status"  => "required|in_list[pending, rejected]",
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$status  = $this->request->getPost('status');
		$amount  = $this->request->getPost('amount');
		$user_id = $this->request->getPost('user_id');
		$comment = $this->request->getPost('comment');

		if (!perm('withdraws', 'add-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to create withdraw'
		]);

		$withdraw_id = $this->withdrawModel->insert(new Withdraw([
			'amount' => $amount, 'status' => $status, 'user_id' => $user_id, 'comment' => $comment,
		]));

		if (!$withdraw_id) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in save withdraw, please try sometime later.',
		]);

		return redirect()->to(route_to('withdraws'))->with('success', ['Withdraw Added Successfully']);
	}

	public function edit(?int $id = null)
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('withdraws', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update withdraw',
		]);

		$withdraw = $this->withdrawModel->find($id);
		if (!is($withdraw, 'object')) return redirect()->back()->with('errors', ['Withdraw not found']);

		$user = $this->userModel->find($withdraw->user_id);
		$accounts = $this->paymentRelationModel->where('user_id', $withdraw->user_id)->typeIs('account')->without(['users', 'companies', 'cards'])->findAll();
		$account = null;

		foreach ($accounts as $value) {
			if ($value->account->is_default) $account = $value->account;
		}

		return view('pages/withdraw/edit', [
			'withdraw' => $withdraw, 'user' => $user, 'account' => $account, 'validation' => $this->validation,
		]);
	}

	public function update(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('withdraws', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update withdraw',
		]);

		$withdraw = $this->withdrawModel->find($id);
		if (!is($withdraw, 'object')) return redirect()->back()->with('errors', ['Withdraw not found']);

		$rules = ["amount" => "required|integer", "status" => "required|in_list[approved, pending, rejected]"];
		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$status         = $this->request->getPost('status');
		$amount         = $this->request->getPost('amount');
		$comment        = $this->request->getPost('comment');
		$bank_code      = $this->request->getPost('bank_code');
		$account_number = $this->request->getPost('account_number');

		if (!perm('withdraws', 'update-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to edit withdraw'
		]);

		if ($status === 'approved' && config('Settings')->verifyBankAccount) {
			$secretKey = config('Settings')->flutterwaveSecretKey;

			$curl = Services::curlrequest(['baseURI' => 'https://api.flutterwave.com/v3/', 'Content-Type' => 'application/json']);
			$response = $curl->post('transfers', [
				'json' => [
					"amount"         => $amount,
					"narration"      => $comment,
					"account_bank"   => $bank_code,
					"reference"      => 'ANT-' . time(),
					"account_number" => $account_number,
					"callback_url"   => route_to('update_withdraw', $id),
					"currency"       => config('Settings')->defaultCurrencyUnit,
					"debit_currency" => config('Settings')->defaultCurrencyUnit,
				],
				'headers' => ['Authorization' => "Bearer $secretKey"],
				'http_errors' => false
			]);

			$body = $response->getBody();
			if ($response->getStatusCode() === 200 && !empty($body)) {
				$body   = json_decode($body);
				$status = 'approved';

				$transactionId = $this->transactionModel->insert(new Transaction([
					'amount'           => $amount,
					'action'           => 'debit',
					'summary'          => $comment,
					'status'           => 'success',
					'txn'              => $body->data->id,
					'transaction_type' => 'payment-gateway',
					'user_id'          => $withdraw->user_id,
				]), true);

				if (!$transactionId) return redirect()->back()->withInput()->with('errors', ['Failed to create transaction.']);

				$walletId = $this->walletModel->insert(new Wallet([
					'amount'      => $amount,
					'action'      => 'debit',
					'wallet_type' => 'payout',
					'status'      => 'success',
					'user_id'     => $withdraw->user_id,
				]), true);

				if (!$walletId) return redirect()->back()->withInput()->with('errors', ['Failed to creating wallet payment']);

				$txnId = $this->walletTransactionModel->insert(new WalletTransaction([
					'wallet_id'      => $walletId,
					'transaction_id' => $transactionId,
				]), true);

				if (!$txnId) return redirect()->back()->withInput()->with('errors', ['Failed to creating wallet transaction']);
			} else {
				$status = 'rejected';
				return redirect()->back()->withInput()->with('errors', ['Something went wrong']);
			}
		}

		$withdraw_id = $this->withdrawModel->update($id, new Withdraw([
			'amount' => $amount, 'status' => $status, 'comment' => $comment,
		]));

		if (!$withdraw_id) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in save withdraw, please try sometime later.',
		]);

		return redirect()->to(route_to('withdraws'))->with('success', ['Withdraw Updated Successfully']);
	}

	public function delete(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('withdraws', 'delete')) return redirect()->to(route_to('dashboard'))->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to delete withdraw',
		]);

		$withdraw = $this->withdrawModel->find($id);
		if (is($withdraw, 'object')) {
			if ($this->withdrawModel->delete($id, true))
				return redirect()->back()->with('success', ['Withdraw deleted successfully']);

			return redirect()->back()->with('errors', ['Withdraw not deleted']);
		}

		return redirect()->back()->with('errors', ['Withdraw not found']);
	}
}

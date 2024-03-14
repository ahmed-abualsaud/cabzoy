<?php

namespace App\Controllers;

use App\Entities\{Transaction, Wallet, WalletTransaction};
use App\Models\{TransactionModel, WalletModel, WalletTransactionModel};
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class WalletController extends BaseController
{
	protected $walletModel;
	protected $transactionModel;
	protected $walletTransactionModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->walletModel            = new WalletModel();
		$this->transactionModel       = new TransactionModel();
		$this->walletTransactionModel = new WalletTransactionModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('wallets', 'read')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to see wallets',
		]);
		// Get all wallets
		$wallets = $this->walletModel->orderBy('id', 'desc')->findAll();

		return view('pages/wallet/list', ['wallets' => $wallets]);
	}


	public function add()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('wallets', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create wallet',
		]);

		$users = $this->userModel->findAll();
		return view('pages/wallet/add', ['validation' => $this->validation, 'users' => $users]);
	}

	public function save(): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('wallets', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create wallet',
		]);

		$rules = ["amount" => "required|integer", "user_id" => "required|integer", "comment" => "required"];
		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$user_id = $this->request->getVar('user_id');
		$comment = $this->request->getVar('comment');
		$amount  = $this->request->getVar('amount') ?? 0;

		if (!perm('wallets', 'add-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to create wallet'
		]);

		if ($amount > 0) {
			$transactionId = $this->transactionModel->insert(new Transaction([
				'amount'           => $amount,
				'action'           => 'credit',
				'user_id'          => $user_id,
				'summary'          => $comment,
				'transaction_type' => 'virtual',
				'status'           => 'success',
				'txn'              => 'VRT-' . rand(0000000, 9999999),
			]), true);

			if (!$transactionId) return redirect()->back()->withInput()->with('errors', ['Failed to create transaction.']);

			$walletId = $this->walletModel->insert(new Wallet([
				'wallet_type' => 'transaction',
				'amount'      => $amount,
				'action'      => 'credit',
				'user_id'     => $user_id,
				'status'      => 'success',
			]), true);

			if (!$walletId) return redirect()->back()->withInput()->with('errors', ['Failed to creating wallet payment']);

			$txnId = $this->walletTransactionModel->insert(new WalletTransaction([
				'wallet_id'      => $walletId,
				'transaction_id' => $transactionId,
			]), true);

			if (!$txnId) return redirect()->back()->withInput()->with('errors', ['Failed to creating wallet transaction']);
		}

		return redirect()->to(route_to('wallets'))->with('success', ['Wallet Added Successfully']);
	}
}

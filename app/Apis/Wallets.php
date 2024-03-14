<?php

namespace App\Apis;

use App\{Entities\Wallet, Entities\WalletReceiver, Models\WalletModel, Models\WalletReceiverModel};
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Wallets extends BaseResourceController
{
	protected $walletReceiverModel;
	protected $modelName = WalletModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
		$this->walletReceiverModel = new WalletReceiverModel();
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$sort    = $this->request->getVar('sort') ?? 'desc';
		$perPage = $this->request->getVar('perPage') ?? null;

		$wallets = $this->model->where('user_id', $this->authenticate->id())->orderBy('id', $sort)->paginate($perPage);
		return $this->success($wallets, 'success', 'Wallets fetched successfully.');
	}

	public function show($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$wallets = $this->model->getDetails($this->authenticate->id())->first();
		if (!is($wallets, 'object')) $wallets = ["total_debits" => 0, "total_credits" => 0, "balance" => 0];
		return $this->success($wallets, 'success', 'Wallets fetched successfully.');
	}

	public function check()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$rules = ['phoneOrEmail' => 'required'];
		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);
		$phoneOrEmail = $this->request->getVar('phoneOrEmail');

		$user = $this->userModel->where('phone', $phoneOrEmail)->orWhere('email', $phoneOrEmail)->first();
		if (!is($user, 'object')) return $this->fail('User not found.');

		return $this->success($user, 'success', 'User fetched successfully.');
	}

	public function create()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$rules = [
			'user_id' => 'required|is_natural_no_zero',
			'amount'  => 'required|is_natural_no_zero',
		];
		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);

		$user_id          = $this->authenticate->id();
		$amount           = $this->request->getVar('amount');
		$receiver_user_id = $this->request->getVar('user_id');

		$wallet = $this->model->getDetails($user_id)->first();
		if (!is($wallet, 'object')) $wallet = ["total_debits" => 0, "total_credits" => 0, "balance" => 0];
		if (!(isset($wallet->balance) && $wallet->balance > $amount)) return $this->fail(
			'Insufficient balance in your wallet.'
		);

		$sender_wallet_id = $this->model->insert(new Wallet([
			'amount'      => $amount,
			'action'      => 'debit',
			'user_id'     => $user_id,
			'status'      => 'success',
			'wallet_type' => 'transaction',
		]));
		if (!$sender_wallet_id) return $this->fail('Something went wrong while sending money.');

		$sender_wallet = $this->walletReceiverModel->save(new WalletReceiver([
			'user_id' => $receiver_user_id, 'wallet_id' => $sender_wallet_id,
		]));
		if (!$sender_wallet) return $this->fail('Something went wrong while linking wallet.');

		$formattedAmount = formatCurrency($amount);

		setNotification([
			'user_id'            => $user_id,
			'is_seen'            => 'unseen',
			'notification_type'  => 'transaction',
			'notification_title' => "$formattedAmount debited to your wallet",
			'notification_body'  => "$formattedAmount transaction has completed.",
		]);

		$receiver_wallet_id = $this->model->insert(new Wallet([
			'amount'      => $amount,
			'action'      => 'credit',
			'status'      => 'success',
			'wallet_type' => 'transaction',
			'user_id'     => $receiver_user_id,
		]));
		if (!$receiver_wallet_id) return $this->fail('Something went wrong while receiving money.');

		$receiver_wallet = $this->walletReceiverModel->save(new WalletReceiver([
			'user_id' => $user_id, 'wallet_id' => $receiver_wallet_id,
		]));
		if (!$receiver_wallet) return $this->fail('Something went wrong while linking wallet.');

		setNotification([
			'notification_type'  => 'transaction',
			'is_seen'            => 'unseen',
			'user_id'            => $receiver_user_id,
			'notification_title' => "$formattedAmount credit to your wallet",
			'notification_body'  => $this->authenticate->user()->firstname . " has send you $formattedAmount.",
		]);

		return $this->success(null, 'created', 'Wallet saved successfully.');
	}
}

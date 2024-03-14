<?php

namespace App\Apis;

use App\Entities\{Tip, OrderTip, Wallet, WalletReceiver};
use App\Models\{OrderModel, OrderTipModel, TipModel, WalletModel, WalletReceiverModel};
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Tips extends BaseResourceController
{
	protected $orderModel;
	protected $walletModel;
	protected $orderTipModel;
	protected $walletReceiverModelModel;
	protected $modelName = TipModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
		$this->orderModel          = new OrderModel();
		$this->walletModel         = new WalletModel();
		$this->orderTipModel       = new OrderTipModel();
		$this->walletReceiverModel = new WalletReceiverModel();
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');
		if (!config('Settings')->enableOrderTips)
			return $this->fail('Currently this service disabled by the administration.');

		$sort    = $this->request->getVar('sort') ?? 'desc';
		$perPage = $this->request->getVar('perPage') ?? null;

		$tips = $this->model->where('user_id', $this->authenticate->id())->orderBy('id', $sort)->paginate($perPage);

		return $this->success($tips, 'success', 'Tips fetched successfully.');
	}

	public function create()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');
		if (!config('Settings')->enableOrderTips)
			return $this->fail('Currently this service disabled by the administration.');

		$rules = [
			"order_id"   => 'required|is_natural_no_zero',
			"user_id"    => 'required|is_natural_no_zero',
			"tip_amount" => 'required|is_natural_no_zero',
		];
		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);

		$sender_user_id = $this->authenticate->id();
		$user_id        = $this->request->getVar('user_id');
		$order_id       = $this->request->getVar('order_id');
		$amount         = $this->request->getVar('tip_amount');
		$tip_comment    = $this->request->getVar('tip_comment');

		$order = $this->orderModel->find($order_id);
		if (!$order) return $this->fail('Order not found.');

		$tip_id = $this->model->insert(new Tip([
			'tip_amount' => $amount, 'tip_comment' => $tip_comment, 'user_id' => $sender_user_id,
		]));
		if (!$tip_id) return $this->fail('Something went wrong while saving tip.');

		$order_tip_id = $this->orderTipModel->insert(new OrderTip([
			'tip_id' => $tip_id, 'user_id' => $user_id, 'order_id' => $order_id, 'amount' => $amount,
		]));
		if (!$order_tip_id) return $this->fail('Something went wrong while sending tip.');


		$sender_wallet_id = $this->walletModel->insert(new Wallet([
			'wallet_type' => 'earn',
			'amount'      => $amount,
			'action'      => 'debit',
			'status'      => 'success',
			'user_id'     => $sender_user_id,
		]));
		if (!$sender_wallet_id) return $this->fail('Something went wrong while sending money.');

		$sender_wallet = $this->walletReceiverModel->save(new WalletReceiver([
			'user_id' => $user_id, 'wallet_id' => $sender_wallet_id
		]));
		if (!$sender_wallet) return $this->fail('Something went wrong while linking wallet.');

		$formattedAmount = formatCurrency($amount);

		setNotification([
			'user_id'            => $user_id,
			'is_seen'            => 'unseen',
			'notification_type'  => 'transaction',
			'notification_title' => "$formattedAmount tip send to the driver",
			'notification_body'  => "$formattedAmount debited to your wallet.",
		]);

		$wallet_id = $this->walletModel->insert(new Wallet([
			'wallet_type' => 'earn',
			'amount'      => $amount,
			'action'      => 'credit',
			'user_id'     => $user_id,
			'status'      => 'success',
		]));
		if (!$wallet_id) return $this->fail('Something went wrong while receiving money.');

		$wallet = $this->walletReceiverModel->save(new WalletReceiver(['user_id' => $sender_user_id, 'wallet_id' => $wallet_id]));
		if (!$wallet) return $this->fail('Something went wrong while linking wallet.');

		setNotification([
			'user_id'            => $user_id,
			'is_seen'            => 'unseen',
			'notification_type'  => 'transaction',
			'notification_title' => "$formattedAmount credit to your wallet",
			'notification_body'  => $this->authenticate->user()->firstname . " has send you tip $formattedAmount.",
		]);

		return $this->success(null, 'created', 'Tip saved successfully.');
	}
}

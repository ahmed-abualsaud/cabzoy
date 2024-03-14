<?php

namespace App\Controllers;

use App\Entities\{Transaction, Wallet, WalletTransaction};
use App\Models\{TransactionModel, WalletModel, WalletTransactionModel};
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class PaymentController extends BaseController
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

	private function formattedResponse($data)
	{
		$formattedData = json_encode($data);
		return print("<script>if (window?.ReactNativeWebView) window?.ReactNativeWebView?.postMessage(JSON.stringify($formattedData))
		else JSON.stringify($formattedData);</script>");
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		$flutterwavePublicKey = config('Settings')->flutterwavePublicKey;

		$amount   = $this->request->getVar('amount') ?? 0;
		$type     = $this->request->getVar('type') ?? 'transaction';
		$currency = config('Settings')->defaultCurrencyUnit ?? 'USD';

		$amountForDisplay = number_to_currency($amount, $currency);

		if ($amount > 0) {
			$transactionId = $this->transactionModel->insert(new Transaction([
				'amount'           => $amount,
				'action'           => 'credit',
				'user_id'          => user_id(),
				'status'           => 'pending',
				'transaction_type' => 'payment-gateway',
				'summary'          => 'Payment for ' . $amountForDisplay,
			]), true);

			if (!$transactionId) return $this->formattedResponse([
				'status' => '400', 'error' => '400', 'messages' => 'Failed to create transaction',
			]);

			$walletId = $this->walletModel->insert(new Wallet([
				'wallet_type' => $type,
				'amount'      => $amount,
				'action'      => 'credit',
				'user_id'     => user_id(),
				'status'      => 'pending',
			]), true);

			if (!$walletId) return $this->formattedResponse([
				'status' => '400', 'error' => '400', 'messages' => 'Failed to creating wallet payment',
			]);

			$txnId = $this->walletTransactionModel->insert(new WalletTransaction([
				'wallet_id'      => $walletId,
				'transaction_id' => $transactionId,
			]), true);

			if (!$txnId) return $this->formattedResponse([
				'status' => '400', 'error' => '400', 'messages' => 'Failed to creating wallet transaction',
			]);

			return view('pages/payment/index', [
				'txnId'                => $txnId,
				'amount'               => $amount,
				'amountForDisplay'     => $amountForDisplay,
				'flutterwavePublicKey' => $flutterwavePublicKey,
			]);
		}
		return die(json_encode(['error' => 'Amount must be greater than 0', 'status' => '400']));
	}

	public function responseRedirect($id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));

		$walletTransaction = $this->walletTransactionModel->find($id);
		if (!is($walletTransaction, 'object')) return redirect()->back()->with('errors', ['Wallet Transaction not found']);

		$status         = $this->request->getVar('status');
		$transaction_id = $this->request->getVar('transaction_id');

		if (is($status) && is($transaction_id)) {
			$transaction = $this->transactionModel->update($walletTransaction->transaction_id, [
				'txn'    => $transaction_id,
				'status' => $status === 'successful' ? 'success' : 'failed',
			]);
			if (!$transaction) return redirect()->back()->with('errors', ['Something went wrong while updating the transaction.']);

			$wallet = $this->walletModel->update($walletTransaction->wallet_id, [
				'status' => $status === 'successful' ? 'success' : 'failed',
			]);
			if (!$wallet) return redirect()->back()->with('errors', ['Something went wrong while updating the wallet.']);

			return $this->formattedResponse(['status' => '200', 'message' => 'Payment Successful.']);
		}
		return redirect()->back()->with('errors', ['Wallet Transaction not valid']);
	}
}

<?php

namespace App\Apis;

use App\{Entities\Account, Entities\PaymentRelation, Models\AccountModel, Models\PaymentRelationModel};
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Accounts extends BaseResourceController
{
	protected $paymentRelationModel;
	protected $modelName = AccountModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->paymentRelationModel = new PaymentRelationModel();
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$sort    = $this->request->getVar('sort') ?? 'desc';
		$perPage = $this->request->getVar('perPage') ?? null;

		$accounts = $this->paymentRelationModel->where('user_id', $this->authenticate->id())->typeIs('account')->orderBy('id', $sort)->paginate($perPage);

		return $this->success($accounts, 'success', 'Accounts fetched successfully.');
	}

	public function show($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$account = $this->model->find($id);
		if (!is($account, 'object')) return $this->fail('Account not found.');

		return $this->success($account, 'success', 'Accounts fetched successfully.');
	}

	public function create()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$rules = [
			'account_holdername' => 'required|alpha_space',
			'bank_name'          => 'required|alpha_space',
			'is_default'         => 'required|in_list[0,1]',
			'account_code'       => 'required|alpha_numeric',
			'account_number'     => 'required|is_natural|is_unique[accounts.account_number,id]',
		];
		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);

		$created_by         = $this->authenticate->id();
		$bank_name          = $this->request->getVar('bank_name');
		$is_default         = $this->request->getVar('is_default');
		$account_code       = $this->request->getVar('account_code');
		$branch_address     = $this->request->getVar('branch_address');
		$account_number     = $this->request->getVar('account_number');
		$account_holdername = $this->request->getVar('account_holdername');

		$account_id = $this->model->insert(new Account([
			'account_status'     => 'pending',
			'bank_name'          => $bank_name,
			'is_default'         => $is_default,
			'created_by'         => $created_by,
			'account_code'       => $account_code,
			'branch_address'     => $branch_address,
			'account_number'     => $account_number,
			'account_holdername' => $account_holdername,
		]));
		if (!$account_id) return $this->fail('Something went wrong while saving account.');

		$paymentRelation = $this->paymentRelationModel->save(new PaymentRelation([
			'user_id' => $created_by, 'relation_type' => 'account', 'account_id' => $account_id,
		]));
		if (!$paymentRelation) return $this->fail('Something went wrong while linking account.');

		return $this->success(null, 'created', 'Account saved successfully.');
	}

	public function update($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$account = $this->model->find($id);
		if (!is($account, 'object')) return $this->fail('Account not found.');

		$rules = [
			'account_holdername' => 'required|alpha_space',
			'bank_name'          => 'required|alpha_space',
			'is_default'         => 'required|in_list[0,1]',
			'account_code'       => 'required|alpha_numeric',
			'account_number'     => "required|is_natural|is_unique[accounts.account_number,id,{$id}]",
		];
		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);

		$created_by         = $this->authenticate->id();
		$bank_name          = $this->request->getVar('bank_name');
		$is_default         = $this->request->getVar('is_default');
		$account_code       = $this->request->getVar('account_code');
		$branch_number      = $this->request->getVar('branch_number');
		$branch_address     = $this->request->getVar('branch_address');
		$account_number     = $this->request->getVar('account_number');
		$account_holdername = $this->request->getVar('account_holdername');

		$account_id = $this->model->update($id, new Account([
			'account_status'     => 'pending',
			'bank_name'          => $bank_name,
			'is_default'         => $is_default,
			'created_by'         => $created_by,
			'account_code'       => $account_code,
			'branch_number'      => $branch_number,
			'branch_address'     => $branch_address,
			'account_number'     => $account_number,
			'account_holdername' => $account_holdername,
		]));
		if (!$account_id) return $this->fail('Something went wrong while updating account.');

		return $this->success(null, 'created', 'Account updated successfully.');
	}

	public function delete($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$account = $this->model->find($id);
		if (!is($account, 'object')) return $this->fail('Account not found.');

		$account = $this->model->delete($id);
		if (!$account) return $this->fail('Something went wrong while deleting account.');

		return $this->success(null, 'deleted', 'Account deleted successfully.');
	}
}

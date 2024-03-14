<?php

namespace App\Controllers;

use App\Entities\{Account, PaymentRelation};
use App\Models\{AccountModel, PaymentRelationModel};
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Modules\Corporate\Models\CompanyModel;
use Psr\Log\LoggerInterface;

class AccountController extends BaseController
{
	protected $accountModel;
	protected $companyModel;
	protected $paymentRelationModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->accountModel         = new AccountModel();
		$this->companyModel         = new CompanyModel();
		$this->paymentRelationModel = new PaymentRelationModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('accounts', 'read')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to see accounts',
		]);
		$paymentRelations = $this->paymentRelationModel->typeIs('account')->orderBy('id', 'desc')->findAll();

		return view('pages/account/list', ['paymentRelations' => $paymentRelations]);
	}

	public function add()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('accounts', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create account',
		]);

		$userIds        = [];
		$existsRelation = $this->paymentRelationModel->typeIs('account')->findAll();

		if (is($existsRelation, 'array')) foreach ($existsRelation as $value) {
			if (!empty($value->user_id)) array_push($userIds, $value->user_id);
		}

		$users = $this->userModel;
		if (is($userIds, 'array')) $users = $users->whereNotIn('id', $userIds);
		$users = $users->findAll();

		$companies = $this->companyModel->findAll();

		return view('pages/account/add', ['validation' => $this->validation, 'users' => $users, 'companies' => $companies]);
	}

	public function save(): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('accounts', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create account',
		]);

		$rules = [
			"account_number"     => "required|integer",
			"account_holdername" => "required|alpha_space",
			"bank_name"          => "required|alpha_space",
			"is_default"         => "required|in_list[0, 1]",
			"account_code"       => "required|alpha_numeric",
			"status"             => "required|in_list[approved, pending, rejected]",
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$status         = $this->request->getPost('status');
		$user_id        = $this->request->getPost('user_id');
		$bank_name      = $this->request->getPost('bank_name');
		$is_default     = $this->request->getPost('is_default');
		$company_id     = $this->request->getPost('company_id');
		$code           = $this->request->getPost('account_code');
		$branch_number  = $this->request->getPost('branch_number');
		$branch_address = $this->request->getPost('branch_address');
		$number         = $this->request->getPost('account_number');
		$holdername     = $this->request->getPost('account_holdername');

		if (!is($company_id) && !is($user_id)) return redirect()->back()->withInput()->with('errors', ['Please select user or company.']);

		if (!perm('accounts', 'add-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to create account'
		]);

		$account_id = $this->accountModel->insert(new Account([
			'account_code'       => $code,
			'account_number'     => $number,
			'account_status'     => $status,
			'created_by'         => user_id(),
			'bank_name'          => $bank_name,
			'is_default'         => $is_default,
			'account_holdername' => $holdername,
			'branch_number'      => $branch_number,
			'branch_address'     => $branch_address,
		]));

		if (!$account_id) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in save account, please try sometime later.',
		]);

		$relation_id = $this->paymentRelationModel->insert(new PaymentRelation([
			'relation_type' => 'account',
			'account_id'    => $account_id,
			'user_id'       => is($user_id) ? $user_id : null,
			'company_id'    => is($company_id) ? $company_id : null,
		]));

		if (!$relation_id) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in assign account, please try sometime later.',
		]);

		return redirect()->to(route_to('accounts'))->with('success', ['Account Added Successfully']);
	}

	public function edit(?int $id = null)
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('accounts', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update account',
		]);

		$account            = $this->accountModel->find($id);
		$paymentRelation = $this->paymentRelationModel->typeIs('account')->where('account_id', $account->id)->first();
		if (!is($account, 'object')) return redirect()->back()->with('errors', ['Account not found']);
		if (!is($paymentRelation, 'object')) return redirect()->back()->with('errors', ['Account Relation not found']);

		$userIds        = [];
		$companyIds     = [];
		$existsRelation = $this->paymentRelationModel->findAll();

		if (is($existsRelation, 'array')) foreach ($existsRelation as $value) {
			if (!empty($value->user_id) && $value->user_id !== $paymentRelation->user_id) array_push($userIds, $value->user_id);
			if (!empty($value->company_id) && $value->company_id !== $paymentRelation->company_id) array_push($companyIds, $value->company_id);
		}

		$users = $this->userModel;
		if (is($userIds, 'array')) $users = $users->whereNotIn('id', $userIds);
		$users = $users->findAll();

		$companies = $this->companyModel;
		if (is($companyIds, 'array')) $companies = $companies->whereNotIn('id', $companyIds);
		$companies = $companies->findAll();

		return view('pages/account/edit', [
			'account'            => $account,
			'users'           => $users,
			'companies'       => $companies,
			'paymentRelation' => $paymentRelation,
			'validation'      => $this->validation,
		]);
	}

	public function update(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('accounts', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update account',
		]);

		$account = $this->accountModel->find($id);
		$paymentRelation = $this->paymentRelationModel->where('account_id', $account->id)->first();
		if (!is($account, 'object')) return redirect()->back()->with('errors', ['Account not found']);
		if (!is($paymentRelation, 'object')) return redirect()->back()->with('errors', ['Account Relation not found']);

		$rules = [
			"account_number"     => "required|integer",
			"account_holdername" => "required|alpha_space",
			"bank_name"          => "required|alpha_space",
			"is_default"         => "required|in_list[0, 1]",
			"account_code"       => "required|alpha_numeric",
			"status"             => "required|in_list[approved, pending, rejected]",
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$status         = $this->request->getPost('status');
		$user_id        = $this->request->getPost('user_id');
		$bank_name      = $this->request->getPost('bank_name');
		$is_default     = $this->request->getPost('is_default');
		$company_id     = $this->request->getPost('company_id');
		$code           = $this->request->getPost('account_code');
		$branch_number  = $this->request->getPost('branch_number');
		$branch_address = $this->request->getPost('branch_address');
		$number         = $this->request->getPost('account_number');
		$holdername     = $this->request->getPost('account_holdername');

		if (!is($company_id) && !is($user_id)) return redirect()->back()->withInput()->with('errors', ['Please select user or company.']);

		if (!perm('accounts', 'update-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to edit account'
		]);

		$account_id = $this->accountModel->update($id, new Account([
			'account_code'       => $code,
			'account_number'     => $number,
			'account_status'     => $status,
			'created_by'         => user_id(),
			'bank_name'          => $bank_name,
			'is_default'         => $is_default,
			'account_holdername' => $holdername,
			'branch_number'      => $branch_number,
			'branch_address'     => $branch_address,
		]));

		if (!$account_id) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in save account, please try sometime later.',
		]);

		$relation_id = $this->paymentRelationModel->update($paymentRelation->id, new PaymentRelation([
			'user_id'    => is($user_id) ? $user_id : null,
			'company_id' => is($company_id) ? $company_id : null,
		]));

		if (!$relation_id) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in assign account, please try sometime later.',
		]);

		return redirect()->to(route_to('accounts'))->with('success', ['Account Updated Successfully']);
	}

	public function delete(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('accounts', 'delete')) return redirect()->to(route_to('dashboard'))->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to delete account',
		]);

		$account = $this->accountModel->find($id);
		if (is($account, 'object')) {
			if ($this->accountModel->delete($id, true)) $this->paymentRelationModel->where('account_id', $id)->delete($id);
			return redirect()->back()->with('success', ['Account deleted successfully']);

			return redirect()->back()->with('errors', ['Account not deleted']);
		}

		return redirect()->back()->with('errors', ['Account not found']);
	}
}

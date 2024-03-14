<?php

namespace App\Controllers;

use App\Entities\{Card, PaymentRelation};
use App\Models\{CardModel, PaymentRelationModel};
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Modules\Corporate\Models\CompanyModel;
use Psr\Log\LoggerInterface;

class CardController extends BaseController
{
	protected $cardModel;
	protected $companyModel;
	protected $paymentRelationModel;

	function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->cardModel            = new CardModel();
		$this->companyModel         = new CompanyModel();
		$this->paymentRelationModel = new PaymentRelationModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('cards', 'read')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to see cards',
		]);

		$paymentRelations = $this->paymentRelationModel->typeIs('card')->findAll();

		return view('pages/card/list', ['paymentRelations' => $paymentRelations]);
	}

	public function add()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('cards', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create card',
		]);

		$userIds        = [];
		$companyIds     = [];
		$existsRelation = $this->paymentRelationModel->typeIs('card')->findAll();

		if (is($existsRelation, 'array')) foreach ($existsRelation as $value) {
			if (!empty($value->user_id)) array_push($userIds, $value->user_id);
			if (!empty($value->company_id)) array_push($companyIds, $value->company_id);
		}

		$users = $this->userModel;
		if (is($userIds, 'array')) $users = $users->whereNotIn('id', $userIds);
		$users = $users->findAll();

		$companies = $this->companyModel;
		if (is($companyIds, 'array')) $companies = $companies->whereNotIn('id', $companyIds);
		$companies = $companies->findAll();

		return view('pages/card/add', ['validation' => $this->validation, 'users' => $users, 'companies' => $companies]);
	}

	public function save(): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('cards', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create card',
		]);

		$rules = [
			"card_number"     => "required|integer",
			"card_holdername" => "required|alpha_space",
			"is_default"      => "required|in_list[0, 1]",
			"card_expire"     => "required|valid_date[Y-m]",
			"type"            => "required|in_list[credit, debit]",
			"card_cvv"        => "required|min_length[3]|max_length[4]",
			"status"          => "required|in_list[approved, pending, rejected]",
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$type       = $this->request->getPost('type');
		$status     = $this->request->getPost('status');
		$user_id    = $this->request->getPost('user_id');
		$cvv        = $this->request->getPost('card_cvv');
		$company_id = $this->request->getPost('company_id');
		$number     = $this->request->getPost('card_number');
		$holdername = $this->request->getPost('card_holdername');
		$expire     = $this->request->getPost('card_expire') ?? '';
		$is_default = $this->request->getPost('is_default') ?? '0';

		if (!is($company_id) && !is($user_id)) return redirect()->back()->withInput()->with('errors', ['Please select user or company.']);

		if (!perm('cards', 'add-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to create card'
		]);

		$expireDate = explode('-', $expire);

		$card_id = $this->cardModel->insert(new Card([
			'card_cvv'        => $cvv,
			'card_type'       => $type,
			'card_number'     => $number,
			'card_status'     => $status,
			'created_by'      => user_id(),
			'is_default'      => $is_default,
			'card_holdername' => $holdername,
			'card_month'      => $expireDate[1],
			'card_year'       => $expireDate[0],
		]));

		if (!$card_id) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in save card, please try sometime later.',
		]);

		$relation_id = $this->paymentRelationModel->insert(new PaymentRelation([
			'relation_type' => 'card',
			'card_id'       => $card_id,
			'user_id'       => is($user_id) ? $user_id : null,
			'company_id'    => is($company_id) ? $company_id : null,
		]));

		if (!$relation_id) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in assign card, please try sometime later.',
		]);

		return redirect()->to(route_to('cards'))->with('success', ['Card Added Successfully']);
	}

	public function edit(?int $id = null)
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('cards', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update card',
		]);

		$card            = $this->cardModel->find($id);
		$paymentRelation = $this->paymentRelationModel->typeIs('card')->where('card_id', $card->id)->first();
		if (!is($card, 'object')) return redirect()->back()->with('errors', ['Card not found']);
		if (!is($paymentRelation, 'object')) return redirect()->back()->with('errors', ['Card Relation not found']);

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

		return view('pages/card/edit', [
			'card'            => $card,
			'users'           => $users,
			'companies'       => $companies,
			'paymentRelation' => $paymentRelation,
			'validation'      => $this->validation,
		]);
	}

	public function update(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('cards', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update card',
		]);

		$card = $this->cardModel->find($id);
		$paymentRelation = $this->paymentRelationModel->typeIs('card')->where('card_id', $card->id)->first();
		if (!is($card, 'object')) return redirect()->back()->with('errors', ['Card not found']);
		if (!is($paymentRelation, 'object')) return redirect()->back()->with('errors', ['Card Relation not found']);

		$rules = [
			"card_number"     => "required|integer",
			"card_holdername" => "required|alpha_space",
			"is_default"      => "required|in_list[0, 1]",
			"card_expire"     => "required|valid_date[Y-m]",
			"type"            => "required|in_list[credit, debit]",
			"card_cvv"        => "required|min_length[3]|max_length[4]",
			"status"          => "required|in_list[approved, pending, rejected]",
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$type       = $this->request->getPost('type');
		$status     = $this->request->getPost('status');
		$user_id    = $this->request->getPost('user_id');
		$cvv        = $this->request->getPost('card_cvv');
		$is_default = $this->request->getPost('is_default');
		$company_id = $this->request->getPost('company_id');
		$number     = $this->request->getPost('card_number');
		$holdername = $this->request->getPost('card_holdername');
		$expire     = $this->request->getPost('card_expire') ?? '';

		if (!is($company_id) && !is($user_id)) return redirect()->back()->withInput()->with('errors', ['Please select user or company.']);

		if (!perm('cards', 'update-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to edit card'
		]);

		$expireDate = explode('-', $expire);

		$card_id = $this->cardModel->update($id, new Card([
			'card_cvv'        => $cvv,
			'card_type'       => $type,
			'card_number'     => $number,
			'card_status'     => $status,
			'created_by'      => user_id(),
			'is_default'      => $is_default,
			'card_holdername' => $holdername,
			'card_month'      => $expireDate[1],
			'card_year'       => $expireDate[0],
		]));

		if (!$card_id) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in update card, please try sometime later.',
		]);

		$relation_id = $this->paymentRelationModel->update($paymentRelation->id, new PaymentRelation([
			'user_id'    => is($user_id) ? $user_id : null,
			'company_id' => is($company_id) ? $company_id : null,
		]));

		if (!$relation_id) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in assign card, please try sometime later.',
		]);

		return redirect()->to(route_to('cards'))->with('success', ['Card Updated Successfully']);
	}

	public function delete(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('cards', 'delete')) return redirect()->to(route_to('dashboard'))->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to delete card',
		]);

		if ($this->isDemo) return redirect()->back()->with('errors', [
			'Few actions are blocked in this version, which is mainly for demonstration purposes.',
			'The demo version does not allow you to make any changes.'
		]);

		$card = $this->cardModel->find($id);
		if (is($card, 'object')) {
			if ($this->cardModel->delete($id, true)) $this->paymentRelationModel->typeIs('card')->where('card_id', $id)->delete($id);
			return redirect()->back()->with('success', ['Card deleted successfully']);

			return redirect()->back()->with('errors', ['Card not deleted']);
		}

		return redirect()->back()->with('errors', ['Card not found']);
	}
}

<?php

namespace App\Controllers;

use App\Entities\Commission;
use App\Models\{CategoryModel, CommissionModel, CommissionRelationModel, VehicleModel};
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Modules\Corporate\Models\CompanyModel;
use Psr\Log\LoggerInterface;

class CommissionController extends BaseController
{
	protected $companyModel;
	protected $vehicleModel;
	protected $categoryModel;
	protected $commissionModel;
	protected $commissionRelationModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->vehicleModel            = new VehicleModel();
		$this->companyModel            = new CompanyModel();
		$this->categoryModel           = new CategoryModel();
		$this->commissionModel         = new CommissionModel();
		$this->commissionRelationModel = new CommissionRelationModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('commissions', 'read')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to see commissions',
		]);

		$commissions = $this->commissionRelationModel->orderBy('id', 'desc')->findAll();

		return view('pages/commission/list', ['commissions' => $commissions]);
	}

	public function add()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('commissions', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create commission',
		]);

		$categoryIds = $companyIds = $vehicleIds = [];
		$commissions = $this->commissionModel->findAll();

		foreach ($commissions as $commission) {
			if (isset($commission->vehicle_id) && !empty($commission->vehicle_id)) $vehicleIds[]    = $commission->vehicle_id;
			if (isset($commission->company_id) && !empty($commission->company_id)) $companyIds[]    = $commission->company_id;
			if (isset($commission->category_id) && !empty($commission->category_id)) $categoryIds[] = $commission->category_id;
		}

		$users                 = $this->userModel->findAll();
		$beneficiary_companies = $this->companyModel->findAll();
		$companies             = $this->companyModel;
		$vehicles              = $this->vehicleModel->is('approved');
		$categories            = $this->categoryModel->typeOf('vehicle');

		if (!empty($vehicleIds)) $vehicles = $vehicles->whereNotIn('id', $vehicleIds);
		$vehicles = $vehicles->findAll();

		if (!empty($categoryIds)) $categories = $categories->whereNotIn('id', $categoryIds);
		$categories = $categories->findAll();

		if (!empty($companyIds)) $companies = $companies->whereNotIn('id', $companyIds);
		$companies = $companies->findAll();

		return view('pages/commission/add', [
			'users'                 => $users,
			'vehicles'              => $vehicles,
			'companies'             => $companies,
			'categories'            => $categories,
			'validation'            => $this->validation,
			'beneficiary_companies' => $beneficiary_companies,
		]);
	}

	public function save(): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('commissions', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create commission'
		]);
		$rules = [
			"commission"             => "required|decimal",
			"commission_name"        => "required|alpha_space",
			"commission_type"        => "required|in_list[percentage, flat]",
			"beneficiary_company_id" => "required_without[beneficiary_user_id]",
			"beneficiary_user_id"    => "required_without[beneficiary_company_id]",
			"commission_status"      => "required|in_list[approved, pending, rejected]",
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$vehicle_id             = $this->request->getPost('vehicle_id');
		$company_id             = $this->request->getPost('company_id');
		$commission             = $this->request->getPost('commission');
		$category_id            = $this->request->getPost('category_id');
		$commission_name        = $this->request->getPost('commission_name');
		$commission_type        = $this->request->getPost('commission_type');
		$commission_status      = $this->request->getPost('commission_status');
		$beneficiary_user_id    = $this->request->getPost('beneficiary_user_id');
		$beneficiary_company_id = $this->request->getPost('beneficiary_company_id');

		if (!perm('commissions', 'add-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to create commission'
		]);

		$commission_id = $this->commissionModel->insert(new Commission([
			'created_by'        => user_id(),
			'commission'        => $commission,
			'commission_name'   => $commission_name,
			'commission_type'   => $commission_type,
			'commission_status' => $commission_status,
			'vehicle_id'        => empty($vehicle_id) ? null : $vehicle_id,
			'company_id'        => empty($company_id) ? null : $company_id,
			'category_id'       => empty($category_id) ? null : $category_id,
		]), true);

		if (!$commission_id) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong, please try sometime later.',
		]);

		$commissionRelation = $this->commissionRelationModel->save([
			'commission_id' => $commission_id,
			'user_id'       => empty($beneficiary_user_id) ? null : $beneficiary_user_id,
			'company_id'    => empty($beneficiary_company_id) ? null : $beneficiary_company_id,
		]);

		if (!$commissionRelation) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong, please try sometime later.',
		]);

		return redirect()->to(route_to('commissions'))->with('success', ['Commission Added Successfully']);
	}

	public function edit(?int $id = null)
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('commissions', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update commission',
		]);

		$commissionRelation = $this->commissionRelationModel->find($id);
		if (!is($commissionRelation, 'object')) return redirect()->back()->with('errors', ['Commission not found']);

		$categoryIds = $companyIds = $vehicleIds = [];
		$commissions = $this->commissionModel->where('id!=', $commissionRelation->commission_id)->findAll();

		foreach ($commissions as $commission) {
			if (isset($commission->vehicle_id) && !empty($commission->vehicle_id)) $vehicleIds[]    = $commission->vehicle_id;
			if (isset($commission->company_id) && !empty($commission->company_id)) $companyIds[]    = $commission->company_id;
			if (isset($commission->category_id) && !empty($commission->category_id)) $categoryIds[] = $commission->category_id;
		}

		$companies             = $this->companyModel;
		$users                 = $this->userModel->findAll();
		$beneficiary_companies = $this->companyModel->findAll();
		$vehicles              = $this->vehicleModel->is('approved');
		$categories            = $this->categoryModel->typeOf('vehicle');

		if (!empty($vehicleIds)) $vehicles = $vehicles->whereNotIn('id', $vehicleIds);
		$vehicles = $vehicles->findAll();

		if (!empty($categoryIds)) $categories = $categories->whereNotIn('id', $categoryIds);
		$categories = $categories->findAll();

		if (!empty($companyIds)) $companies = $companies->whereNotIn('id', $companyIds);
		$companies = $companies->findAll();

		return view('pages/commission/edit', [
			'users'                 => $users,
			'vehicles'              => $vehicles,
			'companies'             => $companies,
			'categories'            => $categories,
			'validation'            => $this->validation,
			'commission'            => $commissionRelation,
			'beneficiary_companies' => $beneficiary_companies,
		]);
	}

	public function update(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('commissions', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update commission',
		]);

		$commissionRelationObject = $this->commissionRelationModel->find($id);
		if (!is($commissionRelationObject, 'object')) return redirect()->back()->with('errors', ['Commission not found']);

		$rules = [
			"commission"             => "required|decimal",
			"commission_name"        => "required|alpha_space",
			"commission_type"        => "required|in_list[percentage, flat]",
			"beneficiary_company_id" => "required_without[beneficiary_user_id]",
			"beneficiary_user_id"    => "required_without[beneficiary_company_id]",
			"commission_status"      => "required|in_list[approved, pending, rejected]",
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$vehicle_id             = $this->request->getPost('vehicle_id');
		$company_id             = $this->request->getPost('company_id');
		$commission             = $this->request->getPost('commission');
		$category_id            = $this->request->getPost('category_id');
		$commission_name        = $this->request->getPost('commission_name');
		$commission_type        = $this->request->getPost('commission_type');
		$commission_status      = $this->request->getPost('commission_status');
		$beneficiary_user_id    = $this->request->getPost('beneficiary_user_id');
		$beneficiary_company_id = $this->request->getPost('beneficiary_company_id');

		if (!perm('commissions', 'update-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to edit commission'
		]);

		$newCommission = $this->commissionModel->update($commissionRelationObject->commission_id, new Commission([
			'created_by'        => user_id(),
			'commission'        => $commission,
			'commission_name'   => $commission_name,
			'commission_type'   => $commission_type,
			'commission_status' => $commission_status,
			'vehicle_id'        => empty($vehicle_id) ? null : $vehicle_id,
			'company_id'        => empty($company_id) ? null : $company_id,
			'category_id'       => empty($category_id) ? null : $category_id,
		]));

		if (!$newCommission) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong, please try sometime later.',
		]);

		$newCommissionRelation = $this->commissionRelationModel->update($id, [
			'commission_id' => $commissionRelationObject->commission_id,
			'user_id'       => empty($beneficiary_user_id) ? null : $beneficiary_user_id,
			'company_id'    => empty($beneficiary_company_id) ? null : $beneficiary_company_id,
		]);

		if (!$newCommissionRelation) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong, please try sometime later.',
		]);

		return redirect()->to(route_to('commissions'))->with('success', ['Commission Updated Successfully']);
	}

	public function delete(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('commissions', 'delete')) return redirect()->to(route_to('dashboard'))->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to delete commission',
		]);

		$commissionRelation = $this->commissionRelationModel->find($id);
		if (is($commissionRelation, 'object')) {
			if ($this->commissionRelationModel->delete($id, true) && $this->commissionModel->delete($commissionRelation->id, true))
				return redirect()->back()->with('success', ['Commission deleted successfully']);

			return redirect()->back()->with('errors', ['Commission not deleted']);
		}

		return redirect()->back()->with('errors', ['Commission not found']);
	}
}

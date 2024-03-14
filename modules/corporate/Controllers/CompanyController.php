<?php

namespace Modules\Corporate\Controllers;

use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Modules\Corporate\Entities\Company;
use Psr\Log\LoggerInterface;

class CompanyController extends BaseController
{

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		checkDir(UPLOADPATH . 'companies/profile');
		checkDir(UPLOADPATH . 'companies/documents');
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('corporate_login'));
		if (!perm('companies', 'read, mine', true)) return redirect()->to(route_to('corporate_dashboard'))->with('errors', [
			'You don\'t have permission to see companies',
		]);

		$companies = $this->companyModel->with('users')->findAll();

		return corporateView('pages/company/list', ['companies' => $companies]);
	}

	public function edit(?int $id = null)
	{
		if (!logged_in()) return redirect()->to(route_to('corporate_login'));
		if (!perm('companies', 'update')) return redirect()->to(route_to('corporate_dashboard'))->with('errors', [
			'You don\'t have permission to update company',
		]);

		$company = $this->companyModel->find($id);
		if (!is($company, 'object')) return redirect()->back()->with('errors', ['Company not found']);
		$defaultCompany = $this->companyModel->isDefault()->where('id!=', $id)->first();

		return corporateView('pages/company/edit', [
			'company'        => $company,
			'defaultCompany' => $defaultCompany,
			'validation'     => $this->validation,
		]);
	}

	public function update(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('corporate_login'));
		if (!perm('companies', 'update')) return redirect()->to(route_to('corporate_dashboard'))->with('errors', [
			'You don\'t have permission to update company',
		]);

		$company = $this->companyModel->find($id);
		if (!is($company, 'object')) return redirect()->back()->with('errors', ['Company not found']);

		$rules = [
			'email'   => 'required|valid_email',
			'address' => 'required|string|min_length[3]',
			'mobile'  => 'required|decimal|min_length[6]',
			'name'    => 'required|alpha_space|min_length[3]',
			'status'  => 'required|in_list[approved, pending, rejected]',
			'doc'     => 'max_size[doc,2048]|ext_in[doc,png,jpg,jpeg,pdf]',
			'image'   => 'uploaded[image]|max_size[image,2048]|ext_in[image,png,jpg,jpeg]',
		];

		$defaultCompany = $this->companyModel->isDefault()->where('id!=', $id)->findAll();
		if (!is($defaultCompany, 'array')) $rules['is_default'] = 'in_list[0, 1]';

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$doc_name   = null;
		$doc        = $this->request->getFile('doc');
		$name       = $this->request->getPost('name');
		$email      = $this->request->getPost('email');
		$image      = $this->request->getFile('image');
		$mobile     = $this->request->getPost('mobile');
		$status     = $this->request->getPost('status');
		$address    = $this->request->getPost('address');
		$is_default = $this->request->getPost('is_default') ?? '0';

		if (!perm('companies', 'update-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to edit company'
		]);

		if (!$image->isValid())
			return redirect()->back()->withInput()->with('errors', [$image->getErrorString()]);

		$image_name = 'companies/profile/' . $image->getRandomName();
		$image->move(UPLOADPATH, $image_name);

		if (!$image->hasMoved()) return redirect()->back()->withInput()->with('errors', [$image->getErrorString()]);

		if ($doc->isFile()) {
			$doc_name = 'companies/documents/' . $doc->getRandomName();
			$doc->move(UPLOADPATH, $doc_name);

			if (!$doc->hasMoved()) return redirect()->back()->withInput()->with('errors', [$doc->getErrorString()]);

			if (file_exists(PUBLICPATH . $company->getCompanyDocument(false)) && is_file(PUBLICPATH . $company->getCompanyDocument(false)) && $doc->hasMoved()) unlink(PUBLICPATH . $company->getCompanyDocument(false));
		} else $doc_name = $this->request->getPost('prevDoc');

		if (file_exists(PUBLICPATH . $company->getCompanyImage(false)) && is_file(PUBLICPATH . $company->getCompanyImage(false)) && $image->hasMoved()) unlink(PUBLICPATH . $company->getCompanyImage(false));

		$updatedUser = $this->companyModel->update($id, new Company([
			'company_name'     => $name,
			'company_email'    => $email,
			'company_mobile'   => $mobile,
			'company_status'   => $status,
			'company_address'  => $address,
			'company_document' => $doc_name,
			'company_image'    => $image_name,
			'is_default'       => $is_default,
		]));

		if (!$updatedUser) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong while updating the company',
		]);

		return redirect()->to(route_to('companies'))->with('success', ["{$name} Updated Successfully"]);
	}
}

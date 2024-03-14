<?php

namespace App\Controllers;

use App\{Entities\Promo, Models\PromoModel};
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class PromoController extends BaseController
{
	protected $promoModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->promoModel = new PromoModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('promos', 'read')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to see promos',
		]);
		$promos = $this->promoModel->orderBy('id', 'desc')->findAll();

		return view('pages/promo/list', ['promos' => $promos]);
	}

	public function add()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('promos', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create promo',
		]);

		return view('pages/promo/add', ['validation' => $this->validation]);
	}

	public function save(): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('promos', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create promo',
		]);

		$rules = [
			'promo_min_amount'    => "numeric",
			'promo_max_amount'    => "numeric",
			'promo_count'         => "numeric",
			'promo_discount'      => "required|numeric",
			'promo_discount_type' => 'in_list[percentage, flat]',
			'promo_status'        => 'in_list[approved, pending, rejected]',
			'promo_code'          => 'required|is_unique[promos.promo_code,id]',
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$promo_code          = $this->request->getPost('promo_code');
		$promo_count         = $this->request->getPost('promo_count');
		$promo_status        = $this->request->getPost('promo_status');
		$promo_discount      = $this->request->getPost('promo_discount');
		$promo_min_amount    = $this->request->getPost('promo_min_amount');
		$promo_max_amount    = $this->request->getPost('promo_max_amount');
		$promo_discount_type = $this->request->getPost('promo_discount_type');

		if (!perm('promos', 'add-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to create promo'
		]);

		$promo_id = $this->promoModel->insert(new Promo([
			'user_id'             => user_id(),
			'promo_code'          => $promo_code,
			'promo_count'         => $promo_count,
			'promo_status'        => $promo_status,
			'promo_discount'      => $promo_discount,
			'promo_min_amount'    => $promo_min_amount,
			'promo_max_amount'    => $promo_max_amount,
			'promo_discount_type' => $promo_discount_type,
		]));

		if (!$promo_id) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in save promo, please try sometime later.',
		]);

		return redirect()->to(route_to('promos'))->with('success', ['Promo Added Successfully']);
	}

	public function edit(?int $id = null)
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('promos', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update promo',
		]);

		$promo = $this->promoModel->find($id);
		if (!is($promo, 'object')) return redirect()->back()->with('errors', ['Promo not found']);

		return view('pages/promo/edit', ['promo' => $promo, 'validation' => $this->validation]);
	}

	public function update(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('promos', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update promo',
		]);

		$promo = $this->promoModel->find($id);
		if (!is($promo, 'object')) return redirect()->back()->with('errors', ['Promo not found']);

		$rules = [
			'promo_min_amount'    => "numeric",
			'promo_max_amount'    => "numeric",
			'promo_count'         => "numeric",
			'promo_discount'      => "required|numeric",
			'promo_discount_type' => 'in_list[percentage,flat]',
			'promo_status'        => 'in_list[approved,pending,rejected]',
			'promo_code'          => "required|is_unique[promos.promo_code,id,{$id}]",
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$promo_code          = $this->request->getPost('promo_code');
		$promo_count         = $this->request->getPost('promo_count');
		$promo_status        = $this->request->getPost('promo_status');
		$promo_discount      = $this->request->getPost('promo_discount');
		$promo_min_amount    = $this->request->getPost('promo_min_amount');
		$promo_max_amount    = $this->request->getPost('promo_max_amount');
		$promo_discount_type = $this->request->getPost('promo_discount_type');

		if (!perm('promos', 'update-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to edit promo'
		]);

		$promo_id = $this->promoModel->update($id, new Promo([
			'user_id'             => user_id(),
			'promo_code'          => $promo_code,
			'promo_count'         => $promo_count,
			'promo_status'        => $promo_status,
			'promo_discount'      => $promo_discount,
			'promo_min_amount'    => $promo_min_amount,
			'promo_max_amount'    => $promo_max_amount,
			'promo_discount_type' => $promo_discount_type,
		]));

		if (!$promo_id) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in save promo, please try sometime later.',
		]);

		return redirect()->to(route_to('promos'))->with('success', ['Promo Updated Successfully']);
	}

	public function delete(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('promos', 'delete')) return redirect()->to(route_to('dashboard'))->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to delete promo',
		]);

		$promo = $this->promoModel->find($id);
		if (is($promo, 'object')) {
			if ($this->promoModel->delete($id, true))
				return redirect()->back()->with('success', ['Promo deleted successfully']);

			return redirect()->back()->with('errors', ['Promo not deleted']);
		}

		return redirect()->back()->with('errors', ['Promo not found']);
	}
}

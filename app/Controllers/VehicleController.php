<?php

namespace App\Controllers;

use App\{Entities\Vehicle, Models\CategoryModel, Models\VehicleModel, Models\VehicleRelationModel};
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class VehicleController extends BaseController
{
	protected $vehicleModel;
	protected $categoryModel;
	protected $vehicleRelationModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		checkDir(UPLOADPATH . 'vehicles');
		$this->vehicleModel         = new VehicleModel();
		$this->categoryModel        = new CategoryModel();
		$this->vehicleRelationModel = new VehicleRelationModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('vehicles', 'read')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to see vehicles',
		]);
		$vehicles  = $this->vehicleModel->orderBy('id', 'desc')->findAll();

		return view('pages/vehicle/list', ['vehicles' => $vehicles]);
	}

	public function add()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('vehicles', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create vehicle',
		]);

		return view('pages/vehicle/add', ['validation' => $this->validation]);
	}

	public function save(): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('vehicles', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create vehicle',
		]);

		$rules = [
			'vehicle_brand'  => 'required',
			'vehicle_modal'  => 'required',
			'vehicle_number' => 'required',
			'vehicle_color'  => 'required',
			'vehicle_seats'  => 'required',
			'status'         => 'required|in_list[approved, pending, rejected]',
			'image'          => 'uploaded[image]|max_size[image,2048]|ext_in[image,png,jpg,jpeg]',
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$file   = $this->request->getFile('image');
		$status = $this->request->getPost('status');
		$brand  = $this->request->getPost('vehicle_brand');
		$seats  = $this->request->getPost('vehicle_seats');
		$modal  = $this->request->getPost('vehicle_modal');
		$color  = $this->request->getPost('vehicle_color');
		$number = $this->request->getPost('vehicle_number');

		if (!perm('vehicles', 'add-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to create vehicle'
		]);

		if (!$file->isValid())
			return redirect()->back()->withInput()->with('errors', [$file->getErrorString()]);

		$image = 'vehicles/' . $file->getRandomName();
		$file->move(UPLOADPATH, $image);

		if (!$file->hasMoved()) return redirect()->back()->withInput()->with('errors', [$file->getErrorString()]);

		$vehicle_id = $this->vehicleModel->insert(new Vehicle([
			'vehicle_image'  => $image,
			'vehicle_modal'  => $modal,
			'vehicle_brand'  => $brand,
			'vehicle_color'  => $color,
			'vehicle_seats'  => $seats,
			'vehicle_status' => $status,
			'vehicle_number' => $number,
			'created_by'     => user_id(),
		]));

		if (!$vehicle_id) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in save vehicle, please try sometime later.',
		]);

		return redirect()->to(route_to('vehicles'))->with('success', ['Vehicle Added Successfully']);
	}

	public function edit(?int $id = null)
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('vehicles', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update vehicle',
		]);

		$vehicle = $this->vehicleModel->find($id);
		if (!is($vehicle, 'object')) return redirect()->back()->with('errors', ['Vehicle not found']);

		return view('pages/vehicle/edit', [
			'vehicle'    => $vehicle,
			'validation' => $this->validation,
		]);
	}

	public function update(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('vehicles', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update vehicle',
		]);

		$vehicle = $this->vehicleModel->find($id);
		if (!is($vehicle, 'object')) return redirect()->back()->with('errors', ['Vehicle not found']);

		$rules = [
			'vehicle_brand'  => 'required',
			'vehicle_modal'  => 'required',
			'vehicle_number' => 'required',
			'vehicle_color'  => 'required',
			'vehicle_seats'  => 'required',
			'status'         => 'required|in_list[approved, pending, rejected]',
			'image'          => 'uploaded[image]|max_size[image,2048]|ext_in[image,png,jpg,jpeg]',
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$file   = $this->request->getFile('image');
		$status = $this->request->getPost('status');
		$brand  = $this->request->getPost('vehicle_brand');
		$seats  = $this->request->getPost('vehicle_seats');
		$modal  = $this->request->getPost('vehicle_modal');
		$color  = $this->request->getPost('vehicle_color');
		$number = $this->request->getPost('vehicle_number');

		if (!perm('vehicles', 'update-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to edit vehicle'
		]);

		if (!$file->isValid())
			return redirect()->back()->withInput()->with('errors', [$file->getErrorString()]);

		$image = 'vehicles/' . $file->getRandomName();
		$file->move(UPLOADPATH, $image);

		if (!$file->hasMoved()) return redirect()->back()->withInput()->with('errors', [$file->getErrorString()]);

		$vehicle = $this->vehicleModel->update($id, new Vehicle([
			'vehicle_image'  => $image,
			'vehicle_modal'  => $modal,
			'vehicle_brand'  => $brand,
			'vehicle_color'  => $color,
			'vehicle_seats'  => $seats,
			'vehicle_status' => $status,
			'vehicle_number' => $number,
		]));

		if (!$vehicle) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in save vehicle, please try sometime later.',
		]);

		return redirect()->to(route_to('vehicles'))->with('success', ['Vehicle Updated Successfully']);
	}

	public function delete(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('vehicles', 'delete')) return redirect()->to(route_to('dashboard'))->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to delete vehicle',
		]);

		$vehicle = $this->vehicleModel->find($id);
		if (is($vehicle, 'object')) {
			if ($this->vehicleModel->delete($id))
				return redirect()->back()->with('success', ['Vehicle deleted successfully']);

			return redirect()->back()->with('errors', ['Vehicle not deleted']);
		}

		return redirect()->back()->with('errors', ['Vehicle not found']);
	}
}

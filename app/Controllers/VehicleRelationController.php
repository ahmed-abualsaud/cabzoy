<?php

namespace App\Controllers;

use App\Models\{CategoryModel, UserVehicleModel, VehicleModel, VehicleRelationModel};
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class VehicleRelationController extends BaseController
{
	protected $vehicleModel;
	protected $categoryModel;
	protected $userVehicleModel;
	protected $vehicleRelationModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		checkDir(UPLOADPATH . 'vehicles');
		$this->vehicleModel         = new VehicleModel();
		$this->categoryModel        = new CategoryModel();
		$this->userVehicleModel     = new UserVehicleModel();
		$this->vehicleRelationModel = new VehicleRelationModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('vehicles', 'assign')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to assign vehicle.',
		]);

		$assignedVehicles = $this->vehicleRelationModel->findAll();
		$categories       = $this->categoryModel->typeOf('vehicle')->findAll();
		$relation         = $this->vehicleRelationModel->notEnded()->findAll();

		if (config('Settings')->enableDefaultVehicleAssign) {
			$defaultVehicles = $this->userVehicleModel->findAll();
			if (is($defaultVehicles, 'array')) array_push($relation, ...$defaultVehicles);
		}

		$vehicleArrayId = $userArrayId = [];
		foreach ($relation as $value) {
			$userArrayId[]    = $value->user_id;
			$vehicleArrayId[] = $value->vehicle_id;
		}

		$vehicleModel = $this->vehicleModel;
		if (!empty($vehicleArrayId)) $vehicleModel = $vehicleModel->whereNotIn('vehicles.id', $vehicleArrayId);
		$vehicles = $vehicleModel->findAll();

		$userModel = $this->userModel->inGroup('drivers')->isOnline('offline');
		if (!empty($userArrayId)) $userModel = $userModel->whereNotIn('users.id', $userArrayId);
		$drivers = $userModel->findAll();

		return view('pages/vehicle/assign', [
			'drivers'          => $drivers,
			'vehicles'         => $vehicles,
			'categories'       => $categories,
			'assignedVehicles' => $assignedVehicles,
			'validation'       => $this->validation,
		]);
	}


	public function save(): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('vehicles', 'assign')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to assign vehicle',
		]);

		$rules = ['category_id' => 'required|is_natural_no_zero', 'driver_id' => 'required|is_natural_no_zero'];
		if (!config('Settings')->enableDefaultVehicleAssign && config('Settings')->enableVehicleAssignUser)
			$rules['vehicle_id'] = 'required';
		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$driver_id   = $this->request->getPost('driver_id');
		$vehicle_id  = $this->request->getPost('vehicle_id');
		$category_id = $this->request->getPost('category_id');

		if (config('Settings')->enableDefaultVehicleAssign || config('Settings')->enableDefaultVehicleAssignUser) {
			$userVehicle = $this->userVehicleModel->where('user_id', $driver_id)->first();
			if (!is($userVehicle, 'object') || !isset($userVehicle->vehicle_id))
				return redirect()->back()->withInput()->with('errors', [lang('Lang.theDefaultVehicleNotAssignedYet')]);
			$vehicle_id = $userVehicle->vehicle_id;
		}

		$relationAvailable = $this->vehicleRelationModel->notEnded(null, $vehicle_id)->findAll();
		if (is($relationAvailable, 'array')) return redirect()->back()->with('errors', [
			'This vehicle is used by another driver.'
		]);

		$alreadyAssigned = $this->vehicleRelationModel->notEnded($driver_id)->first();
		if (is($alreadyAssigned, 'object')) $this->vehicleRelationModel->update($alreadyAssigned->id, [
			'status' => 'not-available', 'ended_at' => datetime()
		]);

		if (!perm('vehicles', 'assign')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to assign vehicle'
		]);

		$vehicleRelation = $this->vehicleRelationModel->save([
			'user_id'     => $driver_id,
			'category_id' => $category_id,
			'vehicle_id'  => $vehicle_id,
			'started_at'  => datetime(),
			'status'      => 'available'
		]);

		if (!$vehicleRelation) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in save vehicle, please try sometime later.',
		]);

		$updateStatus = $this->userModel->update($driver_id, [
			'is_online' => 'online',
			'lat'       => config('Settings')->defaultLat,
			'long'      => config('Settings')->defaultLong
		]);
		if (!$updateStatus) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in update driver status, please try sometime later.',
		]);

		return redirect()->to(route_to('assign_vehicle'))->with('success', ['Vehicle Assigned Successfully']);
	}


	public function update(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('vehicles', 'assign')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to assign vehicle',
		]);

		$vehicle = $this->vehicleRelationModel->notEnded()->find($id);
		if (!is($vehicle, 'object')) return redirect()->back()->with('errors', ['Remaining assigned work not found.']);

		if (!perm('vehicles', 'update-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to edit vehicle'
		]);

		$vehicleRelationUpdated = $this->vehicleRelationModel->update($id, [
			'ended_at' => datetime(), 'status' => 'not-available'
		]);
		if (!$vehicleRelationUpdated) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in finishing assigned work, please try after sometime.',
		]);
		$userStatusUpdated = $this->userModel->update($vehicle->user->id, ['is_online' => 'offline']);
		if (!$userStatusUpdated) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong in updating the user status, please try after sometime.',
		]);

		return redirect()->to(route_to('assign_vehicle'))->with('success', ['Assigned work finished successfully.']);
	}


	public function delete(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('vehicles', 'assign')) return redirect()->to(route_to('dashboard'))->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to assign vehicle',
		]);

		$vehicle = $this->vehicleRelationModel->find($id);
		if (is($vehicle, 'object')) {
			if ($this->vehicleRelationModel->delete($id))
				return redirect()->to(route_to('assign_vehicle'))->with('success', ['Record deleted successfully.']);

			return redirect()->back()->with('errors', ['Record not deleted.']);
		}

		return redirect()->back()->with('errors', ['Record not found.']);
	}
}

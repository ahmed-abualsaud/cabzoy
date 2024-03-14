<?php

namespace App\Apis;


use App\Entities\VehicleRelation;
use App\Models\{OrderDriverModel, UserModel, UserVehicleModel, VehicleModel, VehicleRelationModel};
use CodeIgniter\HTTP\{RequestInterface, Response, ResponseInterface};
use Psr\Log\LoggerInterface;

class Vehicles extends BaseResourceController
{
	protected $modelName = VehicleModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$userVehicle     = new UserVehicleModel();
		$vehicleRelation = new VehicleRelationModel();

		$relation = $vehicleRelation->notEnded()->findAll();

		if (config('Settings')->enableDefaultVehicleAssign) {
			$defaultVehicles = $userVehicle->findAll();
			if (is($defaultVehicles, 'array')) array_push($relation, ...$defaultVehicles);
		}

		$vehicleArrayId = [];
		foreach ($relation as $value) {
			$vehicleArrayId[] = $value->vehicle_id;
		}

		$vehicleModel = $this->model;
		if (!empty($vehicleArrayId)) $vehicleModel->whereNotIn('vehicles.id', $vehicleArrayId);
		$vehicles = $vehicleModel->findAll();

		return $this->success($vehicles, 'success', 'Vehicles fetched successfully.');
	}

	public function activeVehicle(int $order_id): Response
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$orderDriver = new OrderDriverModel();
		$vehicleRelation = new VehicleRelationModel();

		$driver = $orderDriver->where('order_id', $order_id)->where('action', 'accept')->first();
		if (!is($driver, 'object')) return $this->fail('The order driver not found.');

		$relation = $vehicleRelation->where('user_id', $driver->driver_id)->notEnded()->first();
		if (!is($relation, 'object')) return $this->fail('The driver assigned vehicle not found.');

		$vehicle = $this->model->find($relation->vehicle_id);
		if (!is($vehicle, 'object')) return $this->fail('Vehicle not found.');
		return $this->success($vehicle, 'success', 'Vehicles fetched successfully.');
	}

	public function create()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$rules = ['category_id' => 'required'];
		if (!config('Settings')->enableDefaultVehicleAssign && config('Settings')->enableVehicleAssignUser)
			$rules['vehicle_id'] = 'required';

		if (!$this->validate($rules)) return $this->failValidationErrors($this->validator->getErrors());

		$vehicle_id = $this->request->getVar('vehicle_id');
		$category_id = $this->request->getVar('category_id');

		$userModel            = new UserModel();
		$userVehicleModel     = new UserVehicleModel();
		$vehicleRelationModel = new VehicleRelationModel();

		if (config('Settings')->enableDefaultVehicleAssign || config('Settings')->enableDefaultVehicleAssignUser) {
			$userVehicle = $userVehicleModel->where('user_id', $this->authenticate->id())->first();
			if (!is($userVehicle, 'object') || !isset($userVehicle->vehicle_id))
				return $this->fail('The default vehicle not assigned yet.');
			$vehicle_id = $userVehicle->vehicle_id;
		}

		helper('docs');

		if (config('Settings')->requiredDriverDocument && !isDocumentVerified('driver'))
			return $this->fail('Driver\'s document not verified yet.');

		$relation = $vehicleRelationModel->save(new VehicleRelation([
			'started_at'  => datetime(),
			'status'      => 'available',
			'vehicle_id'  => $vehicle_id,
			'category_id' => $category_id,
			'user_id'     => $this->authenticate->id(),
		]));

		if ($relation) {
			$updateStatus = $userModel->update($this->authenticate->id(), ['is_online' => 'online']);
			if ($updateStatus) return $this->success(null, 'no-content', 'You are now online & vehicle successfully assigned.');
			return $this->fail('Something went wrong when try to go online.');
		}
		return $this->fail('Something went wrong when try to go online or vehicle assigning.');
	}

	public function delete($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$userModel            = new UserModel();
		$vehicleRelationModel = new VehicleRelationModel();

		$getRelation = $vehicleRelationModel->notEnded($this->authenticate->id())->first();
		if (!$getRelation) return $this->fail('You are already offline please re-login to your account.');

		$relation = $vehicleRelationModel->update($getRelation->id, new VehicleRelation(['ended_at' => datetime(), 'status' => 'not-available']));

		if ($relation) {
			$updateStatus = $userModel->update($this->authenticate->id(), ['is_online' => 'offline']);
			if ($updateStatus) return $this->success(null, 'no-content', 'You are successfully offline & vehicle successfully unassigned.');
			return $this->fail('Something went wrong when try to go online.');
		}
		return $this->fail('Something went wrong when try to go online or vehicle assigning.');
	}
}

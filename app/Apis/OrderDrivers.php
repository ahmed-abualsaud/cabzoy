<?php

namespace App\Apis;

use App\Entities\OrderDriver;
use App\Models\{OrderDriverModel, OrderLocationModel, OrderModel, VehicleRelationModel};
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class OrderDrivers extends BaseResourceController
{
	protected $orderModel;
	protected $vehicleRelationModel;
	protected $orderLocationModel;
	protected $modelName = OrderDriverModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->orderModel           = new OrderModel();
		$this->orderLocationModel   = new OrderLocationModel();
		$this->vehicleRelationModel = new VehicleRelationModel();
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$action = $this->request->getVar('action');
		$sort   = $this->request->getVar('sort') ?? 'desc';

		$orders = $this->model->orderBy('id', $sort)->where('driver_id', $this->authenticate->id());
		if ($action) $orders = $orders->where('action', $action);
		$orders = $orders->paginate($this->request->getVar('perPage') ?? null);

		return $this->success($orders, 'success', 'Driver Orders fetched successfully.');
	}

	public function show($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$order = $this->model->find($id);
		if (!$order) return $this->fail('Driver Order not found.');

		return $this->success($order, 'success', 'Driver Order fetched successfully.');
	}


	public function update($id = null)
	{
		helper('docs');

		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$rules = ['action' => 'required|in_list[accept,pending,rejected]'];
		if (!$this->validate($rules)) return $this->failValidationErrors($this->validator->getErrors());

		$driverOrder = $this->model->without(['users', 'orders'])->find($id);
		if (!$driverOrder) return $this->failNotFound('Driver Order not found');

		$action = $this->request->getVar('action');
		$driverAction = $this->model->update($id, ['action' => $action]);
		if (!$driverAction) return $this->fail('Driver order not updated');

		if ($action === 'accept') {
			$userOrder = $this->orderModel->update($driverOrder->order_id, ['order_status' => 'booked']);
			if (!$userOrder) return $this->fail('User order not updated');
		}

		if ($action === 'rejected') {
			$order = $this->orderModel->find($driverOrder->order_id);
			if (!$order) return $this->failNotFound('Order not found');

			$availableVehicles = $this->vehicleRelationModel
				->getRelationFromCategoryName(true, strtolower($order->order_vehicle) === 'any' ? null : $order->order_vehicle)->without(['users', 'categories', 'vehicles'])
				->findAll();
			if (!is($availableVehicles, 'array')) {
				$this->orderModel->update($driverOrder->order_id, ['order_status' => 'cancel']);
				return $this->fail(
					$order->order_vehicle === 'any' ? 'Any driver/vehicle not available yet.' : 'The selected vehicle is not available yet.'
				);
			}

			$driverIdsArray = [];
			$driverIdsArray = array_map(static fn ($vehicles) => $driverIdsArray[$vehicles->user_id] = $vehicles->user_id, $availableVehicles);

			$orderLocation = $this->orderLocationModel->where('order_id', $driverOrder->order_id)->where('order_location_type', 'pickup')->without('orders')->first();
			if (empty($orderLocation)) {
				$this->orderModel->update($driverOrder->order_id, ['order_status' => 'cancel']);
				return $this->fail('Location not found');
			}

			$locationsArray =  [
				'lat' => $orderLocation->order_location_lat, 'long' => $orderLocation->order_location_long
			];

			if (!(isset($locationsArray['lat']) && isset($locationsArray['long']))) {
				$this->orderModel->update($driverOrder->order_id, ['order_status' => 'cancel']);
				return $this->fail('Invalid Location');
			}

			$pickup_lat  = $locationsArray['lat'];
			$pickup_long = $locationsArray['long'];

			$nearestDriver = $this->userModel->getNearestDriver($pickup_lat, $pickup_long, $driverIdsArray, [$this->authenticate->id()])->inGroup('drivers')->first();

			if (!is($nearestDriver, 'object')) {
				$this->orderModel->update($driverOrder->order_id, ['order_status' => 'cancel']);
				return $this->fail(
					'All the drivers are busy with other rides, too far for the location or not online right now.'
				);
			}

			if (config('Settings')->requiredDriverDocument && !isDocumentVerified('driver', $nearestDriver->id))
				$nearestDriver = $this->userModel->getNearestDriver($pickup_lat, $pickup_long, $driverIdsArray, [$nearestDriver->id, $this->authenticate->id()])->inGroup('drivers')->first();
			if (!is($nearestDriver, 'object')) {
				$this->orderModel->update($driverOrder->order_id, ['order_status' => 'cancel']);
				return $this->fail(
					'All the drivers are busy with other rides or not online right now.'
				);
			}

			$newOrderDriver = $this->model->save(new OrderDriver([
				'action' => 'pending', 'order_id' => $driverOrder->order_id, 'driver_id' => $nearestDriver->id,
			]));

			if (!$newOrderDriver) {
				$this->orderModel->update($driverOrder->order_id, ['order_status' => 'cancel']);
				return $this->fail(
					'Something went wrong while saving driver order, please try sometime later.',
				);
			}

			$driverData = $this->userModel->find($nearestDriver->id);
			if ($driverData && $driverData->app_token) {
				sendNotification($driverData->app_token, ['title' => 'You have a new ride order', 'body' => 'Don\'t wait the user for the ride, please check it.']);
			}

			setNotification([
				'notification_type'  => 'order',
				'is_seen'            => 'unseen',
				'user_id'            => $nearestDriver->id,
				'notification_title' => 'You have a new ride order',
				'notification_body'  => 'Don\'t wait the user for the ride, please check it.'
			]);
		}

		$updatedOrder = $this->model->update($id, ['action' => $action]);
		if (!$updatedOrder) return $this->fail('Driver Order not updated');

		$userId = null;
		if (isset($driverOrder->order->order_users) && is_array($driverOrder->order->order_users)) {
			foreach ($driverOrder->order->order_users as $orderUser) {
				$userId = $orderUser->user_id;
			}
		}

		$driverName = $driverOrder->user->firstname ?? 'driver';

		if ($userId) setNotification([
			'user_id'            => $userId,
			'is_seen'            => 'unseen',
			'notification_type'  => 'order',
			'notification_title' => 'Your order has been ' . $action,
			'notification_body'  => "Your booking ID #$driverOrder->order_id has been $action by $driverName.",
		]);

		return $this->success($this->model->without(['orders', 'users'])->find($id), 'success', 'Driver Orders updated successfully.');
	}
}

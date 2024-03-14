<?php

namespace App\Controllers;

use App\Entities\{Order, OrderDriver};
use App\Models\{CategoryModel, OrderDriverModel, OrderModel};
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class OrderController extends BaseController
{
	protected $orderModel;
	protected $categoryModel;
	protected $orderDriverModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->orderModel       = new OrderModel();
		$this->categoryModel    = new CategoryModel();
		$this->orderDriverModel = new OrderDriverModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('orders', 'read, mine', true)) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to read order',
		]);

		if (in_groups('company-admins')) $orders = [];
		else $orders = $this->orderModel->orderBy('id', 'desc')->findAll();


		return view('pages/order/list', ['orders' => $orders]);
	}

	public function add()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('orders', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create order',
		]);

		$users    = $this->userModel->inGroup('users')->findAll();
		$drivers  = $this->userModel->inGroup('drivers')->findAll();
		$vehicles = $this->categoryModel->typeOf('vehicle')->findAll();

		return view('pages/order/add', [
			'users'      => $users,
			'drivers'    => $drivers,
			'vehicles'   => $vehicles,
			'validation' => $this->validation,
		]);
	}

	public function save(): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('orders', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create order',
		]);

		$rules = [
			'pickup_location'    => 'required',
			'drop_location'      => 'required',
			'order_type'         => 'required|in_list[normal]',
			'order_payment_mode' => 'required|in_list[online, corporate, cod]',
			'order_status'       => 'required|in_list[new,booked,dispatched,arrived,picked,ongoing,complete,cancel]',
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$order_type         = $this->request->getPost('order_type');
		$order_status       = $this->request->getPost('order_status');
		$drop_location      = $this->request->getPost('drop_location');
		$pickup_location    = $this->request->getPost('pickup_location');
		$order_payment_mode = $this->request->getPost('order_payment_mode');

		if (!perm('orders', 'add-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to create order'
		]);

		$order_id = $this->orderModel->insert([
			'created_by'         => user_id(),
			'order_type'         => $order_type,
			'order_status'       => $order_status,
			'drop_location'      => $drop_location,
			'pickup_location'    => $pickup_location,
			'order_payment_mode' => $order_payment_mode,
		]);

		if (!$order_id) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong, please try sometime later.',
		]);

		return redirect()->to(route_to('update_order', $order_id))->with('success', ['Order Added Successfully']);
	}

	public function edit(?int $id = null)
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('orders', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update order',
		]);

		$order = $this->orderModel->find($id);
		if (!is($order, 'object')) return redirect()->back()->with('errors', ['Order not found']);

		$drivers = $this->userModel->inGroup('drivers')->findAll();

		return view('pages/order/edit', ['order' => $order, 'drivers' => $drivers, 'validation' => $this->validation]);
	}

	public function update(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('orders', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update order',
		]);

		$order = $this->orderModel->find($id);
		if (!is($order, 'object')) return redirect()->back()->with('errors', ['Order not found']);

		$rules = [
			'driver_id'    => 'required',
			'is_paid'      => 'required|in_list[paid, not-paid]',
			'order_status' => 'required|in_list[new, booked, arrived, cancel, ongoing, complete, picked, dispatched]',
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$is_paid      = $this->request->getPost('is_paid');
		$driver_id    = $this->request->getPost('driver_id');
		$order_status = $this->request->getPost('order_status');

		if (!perm('orders', 'update-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to edit order'
		]);

		$updatedOrder = $this->orderModel->update($id, new Order(['order_status' => $order_status, 'is_paid' => $is_paid]));
		if (!$updatedOrder) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong while updating the order',
		]);

		helper('notification');
		$orderDriverRow = $this->orderDriverModel->where('driver_id', $driver_id)->first();
		if (is($orderDriverRow, 'object')) {
			$updateDriver = $this->orderDriverModel->update($orderDriverRow->id, new OrderDriver(['action' => 'accept']));
			if (!$updateDriver) return redirect()->back()->withInput()->with('errors', [
				'Something went wrong while updating the assigned driver',
			]);
			setNotification([
				'user_id'            => $driver_id,
				'notification_type'  => 'order',
				'notification_title' => 'You have a new ride order',
				'notification_body'  => 'Dispatcher assign you a new order, please check it.'
			]);
		} else {
			$updateDriver = $this->orderDriverModel->insert(new OrderDriver([
				'order_id'  => $id,
				'action'    => 'accept',
				'driver_id' => $driver_id,
			]));
			if (!$updateDriver) return redirect()->back()->withInput()->with('errors', [
				'Something went wrong while assign the driver',
			]);
			setNotification([
				'notification_type'  => 'order',
				'user_id'            => $driver_id,
				'notification_title' => 'You have a new ride order',
				'notification_body'  => 'Dispatcher assign you a new order, please check it.'
			]);
		}

		return redirect()->back()->with('success', ["Order Updated Successfully"]);
	}

	public function delete(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('orders', 'delete')) return redirect()->to(route_to('dashboard'))->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to delete order',
		]);

		$order = $this->orderModel->find($id);
		if (is($order, 'object')) {
			if ($this->orderModel->delete($id))
				return redirect()->back()->with('success', ['Order deleted successfully']);

			return redirect()->back()->with('errors', ['Order not deleted']);
		}

		return redirect()->back()->with('errors', ['Order not found']);
	}
}

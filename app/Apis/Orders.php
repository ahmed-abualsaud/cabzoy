<?php

namespace App\Apis;

use App\Entities\{Order, OrderCommission, OrderDriver, OrderFare, OrderPromo, OrderUser, Wallet};
use App\Models\{CategoryModel, DocumentModel, FareRelationModel, NotificationModel, OrderCancelModel, OrderCommissionModel, OrderFareModel, OrderModel, OrderDriverModel, OrderLocationModel, OrderPromoModel, OrderUserModel, PromoModel, VehicleRelationModel, WalletModel, ZoneModel};
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Modules\Corporate\Models\CompanyModel;
use Psr\Log\LoggerInterface;

class Orders extends BaseResourceController
{
	protected $zoneModel;
	protected $promoModel;
	protected $walletModel;
	protected $companyModel;
	protected $documentModel;
	protected $categoryModel;
	protected $orderFareModel;
	protected $orderUserModel;
	protected $orderPromoModel;
	protected $orderCancelModel;
	protected $orderDriverModel;
	protected $fareRelationModel;
	/** @var \App\Models\NotificationModel */
	protected $notificationModel;
	protected $orderLocationModel;
	protected $orderCommissionModel;
	protected $vehicleRelationModel;

	protected $modelName = OrderModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->zoneModel            = new ZoneModel();
		$this->promoModel           = new PromoModel();
		$this->walletModel          = new WalletModel();
		$this->companyModel         = new CompanyModel();
		$this->documentModel        = new DocumentModel();
		$this->categoryModel        = new CategoryModel();
		$this->orderFareModel       = new OrderFareModel();
		$this->orderUserModel       = new OrderUserModel();
		$this->orderPromoModel      = new OrderPromoModel();
		$this->orderCancelModel     = new OrderCancelModel();
		$this->orderDriverModel     = new OrderDriverModel();
		$this->notificationModel    = new NotificationModel();
		$this->fareRelationModel    = new FareRelationModel();
		$this->orderLocationModel   = new OrderLocationModel();
		$this->orderCommissionModel = new OrderCommissionModel();
		$this->vehicleRelationModel = new VehicleRelationModel();
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$type    = $this->request->getVar('type');
		$status  = $this->request->getVar('status');
		$action  = $this->request->getVar('action');
		$sort    = $this->request->getVar('sort') ?? 'desc';
		$perPage = $this->request->getVar('perPage') ?? null;

		if ($type === 'users') {
			$orders = $this->orderUserModel->orderBy('id', $sort)->where('user_id', $this->authenticate->id())->orderBy('id', $sort)->paginate($perPage);
			return $this->success($orders, 'success', 'User orders fetched successfully.');
		} else if ($type === 'drivers') {
			$orders = $this->orderDriverModel->orderBy('id', $sort)->where('driver_id', $this->authenticate->id());
			if ($action) $orders = $orders->where('action', $action);
			$orders = $orders->orderBy('id', $sort)->paginate($perPage);
			return $this->success($orders, 'success', 'Driver orders fetched successfully.');
		} else {
			$orders = $this->model->orderBy('id', $sort)->where('created_by', $this->authenticate->id());
			if ($status) $orders = $orders->where('order_status', $status);
			$orders = $orders->orderBy('id', $sort)->paginate($perPage);
			return $this->success($orders, 'success', 'Orders fetched successfully.');
		}
	}

	public function new()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$id    = $this->request->getVar('id') ?? $this->authenticate->id();
		$type  = $this->request->getVar('type');
		$order = json_encode(json_encode([]));

		if ($type === 'users') {
			$userOrder = $this->orderUserModel->where('user_id', $id)->orderBy('id', 'desc')->first();
			if ($userOrder)
				$order = $this->model->where('id', $userOrder->order_id)->whereNotIn('order_status', ['cancel'])->first();
		} else {
			$order = $this->orderDriverModel->orderBy('id', 'desc')
				->where('driver_id', $id)
				->where('action', 'pending')->orWhere('action', 'accept')->first();
		}
		return $this->success($order, 'success', 'New Order fetched successfully.');
	}

	public function create()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$rules = [
			'drop_text'   => 'required|string',
			'pickup_text' => 'required|string',
			'drop_lat'    => 'required|decimal',
			'drop_long'   => 'required|decimal',
			'pickup_lat'  => 'required|decimal',
			'pickup_long' => 'required|decimal',
			'order_kms'   => 'required|decimal',
			'user_id'     => 'required|is_natural_no_zero',
			'order_type'  => 'required|in_list[normal, outdoor, advanced]',
		];

		if (config('Settings')->enableCorporateAccount &&  config('Settings')->enableCorporatePayment)
			$rules['payment_mode'] = 'required|in_list[online, corporate, cod]';
		else $rules['payment_mode'] = 'required|in_list[online, cod]';

		if (!empty($this->request->getVar('order_vehicle'))) $rules['order_vehicle'] = 'alpha_space';
		if (!empty($this->request->getVar('booking_from'))) $rules['booking_from']   = 'in_list[web, app]';
		if (!empty($this->request->getVar('created_by'))) $rules['created_by']       = 'is_natural_no_zero';
		if (!empty($this->request->getVar('promo_code'))) $rules['promo_code']       = 'alpha_numeric_punct';
		if (!empty($this->request->getVar('order_comment'))) $rules['order_comment'] = 'alpha_numeric_space';
		if (!empty($this->request->getVar('booking_at'))) $rules['booking_at']       = 'valid_date[Y-m-d H:i:s]';

		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);

		if (config('Settings')->requiredUserDocument && !isDocumentVerified('user'))
			return $this->fail('User\'s documents not verified yet.');

		$promoCodeId   = null;
		$is_paid       = 'not-paid';
		$drop_lat      = $this->request->getVar('drop_lat');
		$drop_long     = $this->request->getVar('drop_long');
		$drop_text     = $this->request->getVar('drop_text');
		$pickup_lat    = $this->request->getVar('pickup_lat');
		$promo_code    = $this->request->getVar('promo_code');
		$pickup_long   = $this->request->getVar('pickup_long');
		$pickup_text   = $this->request->getVar('pickup_text');
		$order_comment = $this->request->getVar('order_comment');
		$drop_kms      = $this->request->getVar('drop_kms') ?? 0;
		$order_kms     = $this->request->getVar('order_kms') ?? 0;
		$pickup_kms    = $this->request->getVar('pickup_kms') ?? 0;
		$booking_from  = $this->request->getVar('booking_from') ?? 'web';
		$order_type    = $this->request->getVar('order_type') ?? 'normal';
		$order_vehicle = $this->request->getVar('order_vehicle') ?? 'any';
		$payment_mode  = $this->request->getVar('payment_mode') ?? 'online';
		$booking_at    = $this->request->getVar('booking_at') ?? date('Y-m-d H:i:s');
		$user_id       = $this->request->getVar('user_id') ?? $this->authenticate->id();
		$created_by    = $this->request->getVar('created_by') ?? $this->authenticate->id();

		if (!config('Settings')->enableBooking)
			return $this->fail('Currently this service disabled by the administration.');

		if (config('Settings')->enableIncludeRoutePathFromBase && !(is($pickup_kms, 'number') || is($drop_kms, 'number')))
			return $this->fail('Pickup or drop distance is invalid.');

		if ($payment_mode === 'cod' && !config('Settings')->enableCashPayment)
			return $this->fail('Currently cash payment disabled by the administration.');

		if ($this->model->checkRecentOrder($user_id))
			return $this->fail('You cannot book another ride until your ride is completed or canceled.');

		if (config('Settings')->enableBoundary) {
			/** @var object Map Boundary */
			$zoneBoundary = $this->zoneModel->typeOf('boundary')->first();

			if (!is($zoneBoundary, 'object'))
				return $this->fail('The default map extent is not set yet.');

			$defaultCompany = $this->companyModel->isDefault()->first();
			if (!is($defaultCompany, 'object')) return $this->fail('The Default company not set yet.');

			if (!checkPointInsidePolygon($pickup_lat, $pickup_long, $zoneBoundary->zone))
				return $this->fail('The pickup location is not inside a serviceable area.');

			if (!checkPointInsidePolygon($drop_lat, $drop_long, $zoneBoundary->zone))
				return $this->fail('The destination location is not inside the serviceable area.');
		}

		$availableVehicles = $this->vehicleRelationModel
			->getRelationFromCategoryName(true, strtolower($order_vehicle) === 'any' ? null : $order_vehicle)
			->findAll();
		if (!is($availableVehicles, 'array')) return $this->fail(
			$order_vehicle === 'any' ? 'Any driver/vehicle not available yet.' : 'The selected vehicle is not available yet.'
		);

		$categoryIdsArray = [];
		$categoryIdsArray = array_map(static fn ($vehicles) => $categoryIdsArray[$vehicles->category_id] = $vehicles->category_id, $availableVehicles);

		$driverIdsArray = [];
		$driverIdsArray = array_map(static fn ($vehicles) => $driverIdsArray[$vehicles->user_id] = $vehicles->user_id, $availableVehicles);

		$nearestDriver = $this->userModel->getNearestDriver($pickup_lat, $pickup_long, $driverIdsArray)->inGroup('drivers')->first();
		if (!is($nearestDriver, 'object')) return $this->fail(
			'All the drivers are busy with other rides, too far for the location or not online right now.'
		);

		if (config('Settings')->requiredDriverDocument && !isDocumentVerified('driver', $nearestDriver->id))
			$nearestDriver = $this->userModel->getNearestDriver($pickup_lat, $pickup_long, $driverIdsArray, [$nearestDriver->id])->inGroup('drivers')->first();
		if (!is($nearestDriver, 'object')) return $this->fail(
			'All the drivers are busy with other rides or not online right now.'
		);

		if (!empty($promo_code)) {
			$promo = $this->promoModel->where('promo_code', $promo_code)->statusIs()->first();
			if (!is($promo, 'object')) return $this->fail('Invalid promo code.');
			$promoCount = $this->orderPromoModel->where('promo_id', $promo->id)->where('user_id', $created_by)->count();
			if ($promoCount >= $promo->promo_count) return $this->fail('Promo code has been used maximum number of times.');
			$promoCodeId = $promo->id;
		}

		$calculatedFare = orderFareCalculation($order_kms, $pickup_lat, $pickup_long, $drop_lat, $drop_long, implode(',', $categoryIdsArray), $booking_at, $promoCodeId, $pickup_kms, $drop_kms);
		if (!is($calculatedFare, 'object') && is($calculatedFare->total_fare))
			return $this->fail('Something went wrong, The fare is not calculated yet.');

		$newOrderId = $this->model->insert(new Order([
			'order_status'  => 'new',
			'is_paid'       => $is_paid,
			'order_kms'     => $order_kms,
			'created_by'    => $created_by,
			'order_type'    => $order_type,
			'booking_at'    => $booking_at,
			'booking_from'  => $booking_from,
			'payment_mode'  => $payment_mode,
			'order_comment' => $order_comment,
			'order_vehicle' => $order_vehicle,
			'order_otp'     => rand(111111, 999999),
			'order_price'   => $calculatedFare->total_fare,
		]));

		if (!$newOrderId) return $this->fail(
			'Something went wrong while saving order, please try sometime later.',
		);

		$newOrderUser = $this->orderUserModel->save(new OrderUser([
			'user_id' => $user_id, 'order_id' => $newOrderId,
		]));

		if (!$newOrderUser) return $this->fail(
			'Something went wrong while saving user order, please try sometime later.',
		);

		$newOrderDriver = $this->orderDriverModel->save(new OrderDriver([
			'action' => 'pending', 'order_id' => $newOrderId, 'driver_id' => $nearestDriver->id,
		]));

		if (!$newOrderDriver) return $this->fail(
			'Something went wrong while saving driver order, please try sometime later.',
		);

		$isNotificationSeen = $this->notificationModel->markAsRead($user_id, 'order');
		if (!$isNotificationSeen) return $this->fail('Something went wrong while mark as read previous notification, please try sometime later.');

		$isNotificationSeen = $this->notificationModel->markAsRead($nearestDriver->id, 'order');
		if (!$isNotificationSeen) return $this->fail('Something went wrong while mark as read previous notification, please try sometime later.');

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

		if (is($calculatedFare->fare_array, 'array')) {
			$newOrderFare = false;
			foreach ($calculatedFare->fare_array as $fare) {
				$newOrderFare = $this->orderFareModel->save(new OrderFare(['fare_id' => $fare->id, 'order_id' => $newOrderId]));
			}
			if (!$newOrderFare) return $this->fail(
				'Something went wrong while saving fare order, please try sometime later.',
			);
		}

		if (config('Settings')->enableTaxCommissionCalculation && is($calculatedFare->commission_array, 'array')) {
			$newOrderCommission = false;
			foreach ($calculatedFare->commission_array as $commission) {
				if ($commission->commission_type === 'percentage')
					$commission_amount = $calculatedFare->calculate_fare * ($commission->commission / 100);
				else $commission_amount = $commission->commission;
				$newOrderCommission = $this->orderCommissionModel->save(new OrderCommission([
					'order_id' => $newOrderId, 'commission_id' => $commission->id, 'commission_amount' => $commission_amount
				]));
			}
			if (!$newOrderCommission) return $this->fail(
				'Something went wrong while saving commission order, please try sometime later.',
			);
		}

		if (config('Settings')->enablePromoCode && is($calculatedFare->promo_codes, 'array')) {
			$newOrderPromo = false;
			foreach ($calculatedFare->promo_codes as $promo) {
				$newOrderPromo = $this->orderPromoModel->save(new OrderPromo([
					'user_id'  => $user_id,
					'promo_id' => $promo->id,
					'order_id' => $newOrderId,
					'discount' => $calculatedFare->discount ?? 0,
				]));
			}
			if (!$newOrderPromo) return $this->fail(
				'Something went wrong while saving promo order, please try sometime later.',
			);
		}

		$newOrderLocation = $this->orderLocationModel->insertBatch([
			[
				'order_location_type' => 'pickup',
				'order_id'            => $newOrderId,
				'order_location_lat'  => $pickup_lat,
				'order_location_text' => $pickup_text,
				'order_location_long' => $pickup_long,
			], [
				'order_location_type' => 'drop',
				'order_location_lat'  => $drop_lat,
				'order_location_text' => $drop_text,
				'order_location_long' => $drop_long,
				'order_id'            => $newOrderId,
			],
		]);

		if (!$newOrderLocation) return $this->fail('Something went wrong while saving order location, please try sometime later.');

		setNotification([
			'notification_type'  => 'order',
			'is_seen'            => 'unseen',
			'user_id'            => $user_id,
			'notification_title' => 'Your ride has been booked',
			'notification_body'  => 'Your ride order has been placed successfully.',
		]);

		return $this->success($newOrderId, 'created', 'Order Placed Successfully');
	}

	public function update($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$rules = ['order_status' => 'required|in_list[new,booked,dispatched,arrived,picked,ongoing,complete,cancel]'];

		if (!empty($this->request->getVar('drop_text'))) $rules['drop_text']         = 'string';
		if (!empty($this->request->getVar('pickup_text'))) $rules['pickup_text']     = 'string';
		if (!empty($this->request->getVar('drop_lat'))) $rules['drop_lat']           = 'decimal';
		if (!empty($this->request->getVar('drop_long'))) $rules['drop_long']         = 'decimal';
		if (!empty($this->request->getVar('pickup_lat'))) $rules['pickup_lat']       = 'decimal';
		if (!empty($this->request->getVar('pickup_long'))) $rules['pickup_long']     = 'decimal';
		if (!empty($this->request->getVar('order_kms'))) $rules['order_kms']         = 'decimal';
		if (!empty($this->request->getVar('drop_kms'))) $rules['drop_kms']           = 'decimal';
		if (!empty($this->request->getVar('pickup_kms'))) $rules['pickup_kms']       = 'decimal';

		if (!empty($this->request->getVar('is_paid'))) $rules['is_paid'] = 'in_list[paid, not-paid]';
		if ($this->request->getVar('order_status') === 'cancel') $rules['category_id'] = 'required|is_natural_no_zero';
		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);

		$order = $this->model->find($id);
		if (!$order) return $this->fail('Order not found.');

		$defaultCompany = $this->companyModel->isDefault()->first();
		if (!is($defaultCompany, 'object')) return $this->fail('The Default company not set yet.');

		$orderData = [];

		if (!empty($this->request->getVar('is_paid'))) $orderData['is_paid']            = $this->request->getVar('is_paid');
		if (!empty($this->request->getVar('order_status'))) $orderData['order_status']  = $this->request->getVar('order_status');
		if (!empty($this->request->getVar('drop_lat'))) $orderData['drop_lat']          = $this->request->getVar('drop_lat');
		if (!empty($this->request->getVar('drop_long'))) $orderData['drop_long']        = $this->request->getVar('drop_long');
		if (!empty($this->request->getVar('drop_text'))) $orderData['drop_text']        = $this->request->getVar('drop_text');
		if (!empty($this->request->getVar('pickup_lat'))) $orderData['pickup_lat']      = $this->request->getVar('pickup_lat');
		if (!empty($this->request->getVar('pickup_long'))) $orderData['pickup_long']    = $this->request->getVar('pickup_long');
		if (!empty($this->request->getVar('pickup_text'))) $orderData['pickup_text']    = $this->request->getVar('pickup_text');
		if (!empty($this->request->getVar('order_kms'))) $orderData['order_kms']        = $this->request->getVar('order_kms');


		$driver = $this->orderDriverModel->where('order_id', $id)->where('action', 'accept')->first();

		$order_users = $this->orderUserModel->where('order_id', $id)->findAll();
		if (is($order_users, 'array')) foreach ($order_users as $orderUser) {
			if ($orderUser->user_id) {
				$title = 'Your order status update to ' . $orderData['order_status'];
				$message = "Your booking ID #$id status has been updated to " . $orderData['order_status'];

				switch ($orderData['order_status']) {
					case 'dispatched':
						$vehicle = $this->vehicleRelationModel->where('user_id', $driver->driver_id)->where('status', 'available')->first();
						$title = 'Driver is on his way to pick you';
						$message = 'Driver will be in your pickup location soon.';
						if (isset($vehicle->vehicle->vehicle_color)) $message = "Driver will be in your pickup location soon on " . strtoupper($vehicle->vehicle->vehicle_color . ' ' . $vehicle->vehicle->vehicle_brand . ' ' . $vehicle->vehicle->vehicle_modal . ' ' . $vehicle->vehicle->vehicle_number);
						break;

					case 'arrived':
						$title = 'Driver arrived!';
						$message = 'Driver arrived on your pickup location.';
						break;

					case 'picked':
						$title = $order->order_otp . ' to start the ride';
						$message = 'Driver has arrived, tell the driver your code to start the ride.';
						break;

					case 'ongoing':
						$title = 'On your way to drop off';
						$message = 'Your ride is started and on your way to drop off.';
						break;

					case 'complete':
						$title = 'Your ride has been completed';
						$message  = 'You reached your destination and your ride is successfully completed.';
						break;

					case 'cancel':
						$title = 'Your ride is cancelled';
						$message = "Your booking ID #$id status has been updated to " . $orderData['order_status'];
						break;

					default:
						$title = 'Your order status update to ' . $orderData['order_status'];
						$message = "Your booking ID #$id status has been updated to " . $orderData['order_status'];
						break;
				}


				setNotification([
					'notification_title' => $title,
					'notification_type'  => 'order',
					'is_seen'            => 'unseen',
					'notification_body'  => $message,
					'user_id'            => $orderUser->user_id,
				]);
			}
		}

		if ($this->request->getVar('order_status') === 'complete') {
			if ($order->is_paid === 'not-paid') {

				if ($order->payment_mode === 'online' || $order->payment_mode === 'corporate') {

					$companyId = null;
					$wallet = $this->walletModel->getDetails($order->created_by)->first();

					if (!is($wallet, 'object')) $wallet = ["total_debits" => 0, "total_credits" => 0, "balance" => 0];
					if (is_null($companyId) && !(isset($wallet->balance) && $wallet->balance > $order->order_price))
						return $this->fail(
							'You don\'t have insufficient balance in your wallet to book a ride.'
						);

					$userWallet = $this->walletModel->insert(new Wallet([
						'action'      => 'debit',
						'wallet_type' => 'order',
						'status'      => 'success',
						'company_id'  => $companyId,
						'amount'      => $order->order_price,
						'user_id'     => is_null($companyId) ? $order->created_by : $companyId,
					]));
					if (!$userWallet) return $this->fail(
						'Something went wrong while deducting order price from wallet, please try sometime later.',
					);

					$driverWallet = $this->walletModel->insert(new Wallet([
						'wallet_type' => 'order',
						'action'      => 'credit',
						'status'      => 'success',
						'user_id'     => $driver->driver_id,
						'amount'      => $order->order_price,
					]));
					if (!$driverWallet) return $this->fail(
						'Something went wrong while processing the payment, please try sometime later.',
					);
				}

				if (isset($order->order_commissions) && !empty($order->order_commissions) && is_array($order->order_commissions))
					foreach ($order->order_commissions as  $commission) {
						$driverOrderWallet = $this->walletModel->save(new Wallet([
							'action'      => 'debit',
							'wallet_type' => 'charges',
							'status'      => 'success',
							'user_id'     => $driver->driver_id,
							'amount'      => $commission->commission_amount,
						]));
						if (!$driverOrderWallet) return $this->fail(
							'Something went wrong while processing the payment, please try sometime later.',
						);

						$driverOrderWallet = $this->walletModel->save(new Wallet([
							'wallet_type' => 'charges',
							'action'      => 'credit',
							'status'      => 'success',
							'company_id'  => $defaultCompany->id,
							'amount'      => $commission->commission_amount,
						]));

						if (!$driverOrderWallet) return $this->fail(
							'Something went wrong while processing the payment, please try sometime later.',
						);
					}
			}

			$paymentUpdate = $this->model->update($id, new Order(['is_paid' => 'paid']));
			if (!$paymentUpdate) return $this->fail('Something went wrong while updating order, please try sometime later.');

			$isNotificationSeen = $this->notificationModel->markAsRead($orderUser->user_id, 'order');
			if (!$isNotificationSeen) return $this->fail('Something went wrong while mark as read previous notification, please try sometime later.');
		} 
		
		else if ($this->request->getVar('order_status') === 'cancel') 
		{
			$reason = $this->categoryModel->typeOf('cancellation')->find($this->request->getVar('category'));
			if (!$reason) return $this->fail('Invalid Cancellation reason, Please select the valid reason for the cancellation.');

			$userWallet = $this->walletModel->insert(new Wallet([
				'action'      => 'debit',
				'wallet_type' => 'order',
				'status'      => 'success',
				'user_id'     => $order->created_by,
				'amount'      => config('Settings')->defaultCancellationAmount,
			]));
			if (!$userWallet) return $this->fail(
				'Something went wrong while deducting order price from wallet, please try sometime later.',
			);

			$driverOrderWallet = $this->walletModel->save(new Wallet([
				'wallet_type' => 'charges',
				'action'      => 'credit',
				'status'      => 'success',
				'company_id'  => $defaultCompany->id,
				'amount'      => config('Settings')->defaultCancellationAmount,
			]));

			if (!$driverOrderWallet) return $this->fail(
				'Something went wrong while processing the payment, please try sometime later.',
			);


			$orderCancel = $this->orderCancelModel->save([
				'order_id'    => $id,
				'user_id'     => $orderUser->user_id,
				'comment'     => $this->request->getVar('comment'),
				'category_id' => $this->request->getVar('category_id'),
			]);

			if (!$orderCancel) return $this->fail('Something went wrong while update the cancellation reason.');

			$isNotificationSeen = $this->notificationModel->markAsRead($orderUser->user_id, 'order');
			if (!$isNotificationSeen) return $this->fail('Something went wrong while mark as read previous notification, please try sometime later.');
		}


		if (!empty(array_intersect_key($orderData, array_flip(['drop_lat', 'drop_long', 'drop_text', 'pickup_lat', 'pickup_long', 'pickup_text', 'order_kms', 'drop_kms', 'pickup_kms'])))) {

			$promoCodeId = null;
			$orderData['booking_at'] = $this->request->getVar('booking_at') ?? $order->booking_at;
			$orderData['order_vehicle'] = $this->request->getVar('order_vehicle') ?? $order->order_vehicle;

			if (!empty($this->request->getVar('promo_code'))) {
				$promo = $this->promoModel->where('promo_code', $this->request->getVar('promo_code'))->statusIs()->first();
				if (!is($promo, 'object')) return $this->fail('Invalid promo code.');
				$promoCodeId = $promo->id;
			}

			$availableVehicles = $this->vehicleRelationModel
			->getRelationFromCategoryName(true, strtolower($orderData['order_vehicle']) === 'any' ? null : $orderData['order_vehicle'])
			->findAll();
			if (!is($availableVehicles, 'array')) return $this->fail(
				$orderData['order_vehicle'] === 'any' ? 'Any driver/vehicle not available yet.' : 'The selected vehicle is not available yet.'
			);

			$drop_kms = $this->request->getVar('drop_kms') ?? null;
			$pickup_kms = $this->request->getVar('pickup_kms') ?? null;

			$categoryIdsArray = [];
			$categoryIdsArray = array_map(static fn ($vehicles) => $categoryIdsArray[$vehicles->category_id] = $vehicles->category_id, $availableVehicles);
			
			$calculatedFare = orderFareCalculation($orderData['order_kms'], $orderData['pickup_lat'], $orderData['pickup_long'], $orderData['drop_lat'], $orderData['drop_long'], implode(',', $categoryIdsArray), $orderData['booking_at'], $promoCodeId, $pickup_kms, $drop_kms);
			if (!is($calculatedFare, 'object') && is($calculatedFare->total_fare))
				return $this->fail('Something went wrong, The fare is not calculated yet.');

			$orderData['order_price'] = $calculatedFare->total_fare;

			if (is($calculatedFare->fare_array, 'array')) {
				$newOrderFare = false;
				foreach ($calculatedFare->fare_array as $fare) {
					$newOrderFare = $this->orderFareModel->save(new OrderFare(['fare_id' => $fare->id, 'order_id' => $id]));
				}
				if (!$newOrderFare) return $this->fail(
					'Something went wrong while saving fare order, please try sometime later.',
				);
			}
	
			if (config('Settings')->enableTaxCommissionCalculation && is($calculatedFare->commission_array, 'array')) {
				$newOrderCommission = false;
				foreach ($calculatedFare->commission_array as $commission) {
					if ($commission->commission_type === 'percentage')
						$commission_amount = $calculatedFare->calculate_fare * ($commission->commission / 100);
					else $commission_amount = $commission->commission;
					$newOrderCommission = $this->orderCommissionModel->save(new OrderCommission([
						'order_id' => $id, 'commission_id' => $commission->id, 'commission_amount' => $commission_amount
					]));
				}
				if (!$newOrderCommission) return $this->fail(
					'Something went wrong while saving commission order, please try sometime later.',
				);
			}
	
			$user_id       = $this->request->getVar('user_id') ?? $this->authenticate->id();
			if (config('Settings')->enablePromoCode && is($calculatedFare->promo_codes, 'array')) {
				$newOrderPromo = false;
				foreach ($calculatedFare->promo_codes as $promo) {
					$newOrderPromo = $this->orderPromoModel->save(new OrderPromo([
						'user_id'  => $user_id,
						'promo_id' => $promo->id,
						'order_id' => $id,
						'discount' => $calculatedFare->discount ?? 0,
					]));
				}
				if (!$newOrderPromo) return $this->fail(
					'Something went wrong while saving promo order, please try sometime later.',
				);
			}
		}

		$updateOrder = $this->model->update($id, new Order($orderData));
		if (!$updateOrder) return $this->fail('Something went wrong while updating order, please try sometime later.');

		return $this->success($this->model->find($id), 'Order ' . $this->request->getVar('order_status') . 'updated successfully.');
	}

	public function show($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$order = $this->model->find($id);
		if (!$order) return $this->fail('Order not found.');

		return $this->success($order, 'Order fetched successfully.');
	}
}

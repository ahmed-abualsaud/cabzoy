<?php

namespace App\Controllers;

use App\Entities\{Order, OrderCommission, OrderDriver, OrderFare, OrderPromo, OrderUser};
use App\Models\{CategoryModel,  FareRelationModel, OrderCommissionModel, OrderDriverModel, OrderFareModel, OrderLocationModel, OrderModel, OrderPromoModel, OrderUserModel, PromoModel, TransactionModel, VehicleModel, VehicleRelationModel, WalletModel, ZoneModel};
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Modules\Corporate\Models\CompanyModel;
use Psr\Log\LoggerInterface;

class HomeController extends BaseController
{
	protected $zoneModel;
	protected $orderModel;
	protected $promoModel;
	protected $walletModel;
	protected $companyModel;
	protected $vehicleModel;
	protected $categoryModel;
	protected $orderFareModel;
	protected $orderUserModel;
	protected $orderPromoModel;
	protected $orderDriverModel;
	protected $transactionModel;
	protected $fareRelationModel;
	protected $orderLocationModel;
	protected $orderCommissionModel;
	protected $vehicleRelationModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		helper(['theme', 'notification', 'geo']);

		$this->zoneModel            = new ZoneModel();
		$this->orderModel           = new OrderModel();
		$this->promoModel           = new PromoModel();
		$this->walletModel          = new WalletModel();
		$this->companyModel         = new CompanyModel();
		$this->vehicleModel         = new VehicleModel();
		$this->categoryModel        = new CategoryModel();
		$this->orderFareModel       = new OrderFareModel();
		$this->orderUserModel       = new OrderUserModel();
		$this->orderPromoModel      = new OrderPromoModel();
		$this->transactionModel     = new TransactionModel();
		$this->orderDriverModel     = new OrderDriverModel();
		$this->fareRelationModel    = new FareRelationModel();
		$this->orderLocationModel   = new OrderLocationModel();
		$this->orderCommissionModel = new OrderCommissionModel();
		$this->vehicleRelationModel = new VehicleRelationModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));

		if (in_array('users', user()->getRoles()) || in_array('drivers', user()->getRoles()))
			return view('pages/dashboard/empty');

		$totalWalletBalance        = 0;
		$totalCompanyWalletBalance = 0;
		$totalTransactionBalance   = 0;
		$totalWalletCredit         = 0;
		$totalWalletDebit          = 0;

		$defaultCompany = $this->companyModel->isDefault()->first();
		$users          = $this->userModel->inGroup('users')->findAll();
		$drivers        = $this->userModel->inGroup('drivers')->findAll();
		$orders         = $this->orderModel->orderBy('id', 'desc')->limit(3)->findAll();

		$completeOrders = $this->orderModel->isStatus('complete')
			->without(['users', 'order_fares', 'order_locations', 'order_drivers', 'order_users'])->findAll();
		$ongoingOrders = $this->orderModel->notStatus(['complete', 'cancel'])
			->without(['users', 'order_fares', 'order_locations', 'order_drivers', 'order_users'])->findAll();
		$allOrders = $this->orderModel
			->without(['users', 'order_fares', 'order_locations', 'order_drivers', 'order_users'])->findAll();
		$advancedOrders = $this->orderModel->isType('advanced')
			->without(['users', 'order_fares', 'order_locations', 'order_drivers', 'order_users'])->findAll();
		$cancelOrders = $this->orderModel->isStatus('cancel')
			->without(['users', 'order_fares', 'order_locations', 'order_drivers', 'order_users'])->findAll();
		$onlineOrders = $this->orderModel->paymentType('online')
			->without(['users', 'order_fares', 'order_locations', 'order_drivers', 'order_users'])->findAll();
		$corporateOrders = $this->orderModel->paymentType('corporate')
			->without(['users', 'order_fares', 'order_locations', 'order_drivers', 'order_users'])->findAll();
		$codOrders = $this->orderModel->paymentType('cod')
			->without(['users', 'order_fares', 'order_locations', 'order_drivers', 'order_users'])->findAll();

		$walletData = $this->walletModel
			->without(['users', 'companies', 'wallet_receivers'])
			->getDetails()->first();
		if (!is($walletData, 'object')) $walletData = ["total_debits" => 0, "total_credits" => 0, "balance" => 0];

		$transactionData = $this->transactionModel->without(['users', 'companies'])->getDetails()->first();
		if (!is($transactionData, 'object')) $transactionData = ["total_debits" => 0, "total_credits" => 0, "balance" => 0];

		if (is($walletData, 'object')) {
			$totalWalletBalance = $walletData->balance;
			$totalWalletDebit   = $walletData->total_debits;
			$totalWalletCredit  = $walletData->total_credits;
		}

		if (is($transactionData, 'object')) $totalTransactionBalance = $transactionData->balance;

		if (is($defaultCompany, 'object')) {
			$companyWalletData = $this->walletModel
				->without(['users', 'companies', 'wallet_receivers'])
				->getDetails(null, $defaultCompany->id)->first();

			if (is($companyWalletData, 'object')) $totalCompanyWalletBalance = $companyWalletData->balance;
		}

		$countUsers           = is_countable($users) ? count($users) : 0;
		$countDrivers         = is_countable($drivers) ? count($drivers) : 0;
		$countOrders          = is_countable($allOrders) ? count($allOrders) : 0;
		$countCodOrders       = is_countable($codOrders) ? count($codOrders) : 0;
		$countOnlineOrders    = is_countable($onlineOrders) ? count($onlineOrders) : 0;
		$countCancelOrders    = is_countable($cancelOrders) ? count($cancelOrders) : 0;
		$countOngoingOrders   = is_countable($ongoingOrders) ? count($ongoingOrders) : 0;
		$countAdvancedOrders  = is_countable($advancedOrders) ? count($advancedOrders) : 0;
		$countCompleteOrders  = is_countable($completeOrders) ? count($completeOrders) : 0;
		$countCorporateOrders = is_countable($corporateOrders) ? count($corporateOrders) : 0;

		$orderHourCount = $this->orderModel->without(['users', 'order_fares', 'order_locations', 'order_drivers', 'order_users'])->countEveryHour();

		return view('pages/dashboard/home', [
			'orders'                    => $orders,
			'countUsers'                => $countUsers,
			'countOrders'               => $countOrders,
			'countDrivers'              => $countDrivers,
			'countCodOrders'            => $countCodOrders,
			'orderHourCount'            => $orderHourCount,
			'totalWalletDebit'          => $totalWalletDebit,
			'countCancelOrders'         => $countCancelOrders,
			'countOnlineOrders'         => $countOnlineOrders,
			'totalWalletCredit'         => $totalWalletCredit,
			'countOngoingOrders'        => $countOngoingOrders,
			'totalWalletBalance'        => $totalWalletBalance,
			'countCompleteOrders'       => $countCompleteOrders,
			'countAdvancedOrders'       => $countAdvancedOrders,
			'countCorporateOrders'      => $countCorporateOrders,
			'totalTransactionBalance'   => $totalTransactionBalance,
			'totalCompanyWalletBalance' => $totalCompanyWalletBalance,
		]);
	}

	public function dispatch()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));

		$orders     = $this->orderModel->notStatus(['cancel', 'complete', 'ongoing', 'picked'])->orderBy('id', 'desc')->findAll();
		$users      = $this->userModel->inGroup('users')->findAll();
		$drivers    = $this->userModel->inGroup('drivers')->findAll();
		$categories = $this->categoryModel->typeOf('vehicle')->findAll();

		return view('pages/dashboard/dispatch', [
			'users'      => $users,
			'orders'     => $orders,
			'drivers'    => $drivers,
			'categories' => $categories,
			'validation' => $this->validation,
		]);
	}

	public function birdEye()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));

		return view('pages/dashboard/birdEye');
	}

	public function processDispatch(): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('dispatch', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create dispatch.',
		]);

		$rules = [
			'drop_text'    => 'required|string',
			'pickup_text'  => 'required|string',
			'drop_lat'     => 'required|decimal',
			'drop_long'    => 'required|decimal',
			'pickup_lat'   => 'required|decimal',
			'pickup_long'  => 'required|decimal',
			'order_kms'    => 'required|decimal',
			'user_id'      => 'required|is_natural_no_zero',
			'order_type'   => 'required|in_list[normal, outdoor, advanced]',
		];

		if (config('Settings')->enableCorporateAccount &&  config('Settings')->enableCorporatePayment)
			$rules['payment_mode'] = 'required|in_list[online, corporate, cod]';
		else $rules['payment_mode'] = 'required|in_list[online, cod]';

		if (!empty($this->request->getPost('order_vehicle'))) $rules['order_vehicle'] = 'alpha_space';
		if (!empty($this->request->getPost('booking_from'))) $rules['booking_from']   = 'in_list[web, app]';
		if (!empty($this->request->getPost('created_by'))) $rules['created_by']       = 'is_natural_no_zero';
		if (!empty($this->request->getPost('promo_code'))) $rules['promo_code']       = 'alpha_numeric_punct';
		if (!empty($this->request->getPost('order_comment'))) $rules['order_comment'] = 'alpha_numeric_space';

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with(
			'errors',
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);

		$promoCodeId   = null;
		$is_paid       = $this->request->getPost('is_paid');
		$drop_lat      = $this->request->getPost('drop_lat');
		$drop_long     = $this->request->getPost('drop_long');
		$drop_text     = $this->request->getPost('drop_text');
		$driver_id     = $this->request->getPost('driver_id');
		$pickup_lat    = $this->request->getPost('pickup_lat');
		$promo_code    = $this->request->getPost('promo_code');
		$pickup_long   = $this->request->getPost('pickup_long');
		$pickup_text   = $this->request->getPost('pickup_text');
		$drop_kms      = $this->request->getVar('drop_kms') ?? 0;
		$order_comment = $this->request->getPost('order_comment');
		$pickup_kms    = $this->request->getVar('pickup_kms') ?? 0;
		$order_kms     = $this->request->getPost('order_kms') ?? 0;
		$booking_from  = $this->request->getPost('booking_from') ?? 'web';
		$order_type    = $this->request->getPost('order_type') ?? 'normal';
		$order_vehicle = $this->request->getPost('order_vehicle') ?? 'any';
		$payment_mode  = $this->request->getPost('payment_mode') ?? 'online';
		$user_id       = $this->request->getPost('user_id') ?? $this->authenticate->id();
		$created_by    = $this->request->getPost('created_by') ?? $this->authenticate->id();
		$booking_at    = $this->request->getPost('booking_at_date') && $this->request->getPost('booking_at_time') ? $this->request->getPost('booking_at_date') . ' ' . $this->request->getPost('booking_at_time') . ':00' : date('Y-m-d H:i:s');

		if (!config('Settings')->enableBooking)
			return redirect()->back()->withInput()->with('errors', ['Currently this service disabled by the administration.']);

		if (config('Settings')->enableIncludeRoutePathFromBase && !(is($pickup_kms, 'number') || is($drop_kms, 'number')))
			return redirect()->back()->withInput()->with('errors', ['Pickup or drop distance is invalid.']);

		if ($payment_mode === 'cod' && !config('Settings')->enableCashPayment)
			return redirect()->back()->withInput()->with('errors', ['Currently cash payment disabled by the administration.']);

		if (config('Settings')->enableBoundary) {
			/** @var object Map Boundary */
			$zoneBoundary = $this->zoneModel->typeOf('boundary')->first();

			if (!is($zoneBoundary, 'object'))
				return redirect()->back()->withInput()->with('errors', ['The default map extent is not set yet.']);

			$defaultCompany = $this->companyModel->isDefault()->first();
			if (!is($defaultCompany, 'object')) return redirect()->back()->withInput()->with('errors', [
				'The Default company not set yet.'
			]);

			if (!checkPointInsidePolygon($pickup_lat, $pickup_long, $zoneBoundary->zone))
				return redirect()->back()->withInput()->with('errors', [
					'The pickup location is not inside a serviceable area.'
				]);

			if (!checkPointInsidePolygon($drop_lat, $drop_long, $zoneBoundary->zone))
				return redirect()->back()->withInput()->with('errors', [
					'The destination location is not inside the serviceable area.'
				]);
		}

		$availableVehicles = $this->vehicleRelationModel
			->getRelationFromCategoryName(true, strtolower($order_vehicle) === 'any' ? null : $order_vehicle)
			->findAll();
		if (!is($availableVehicles, 'array')) return redirect()->back()->withInput()->with('errors', [
			$order_vehicle === 'any' ? lang('Lang.anyDriverVehicleNotAvailableYet') : lang('Lang.theSelectedVehicleIsNotAvailableYet')
		]);

		$categoryIdsArray = [];
		$categoryIdsArray = array_map(
			static fn ($vehicles) => $categoryIdsArray[$vehicles->category_id] = $vehicles->category_id,
			$availableVehicles
		);

		$driverIdsArray = [];
		$driverIdsArray = array_map(
			static fn ($vehicles) => $driverIdsArray[$vehicles->user_id] = $vehicles->user_id,
			$availableVehicles
		);

		if (!$driver_id) {
			$nearestDriver = $this->userModel
				->getNearestDriver($pickup_lat, $pickup_long, $driverIdsArray)->inGroup('drivers')->first();
			if (!is($nearestDriver, 'object')) return redirect()->back()->withInput()->with('errors', [
				'All the drivers are busy with other rides or not online right now.'
			]);
			$driver_id = $nearestDriver->id;
		}

		if (!empty($promo_code)) {
			$promo = $this->promoModel->where('promo_code', $promo_code)->statusIs()->first();
			if (!is($promo, 'object')) return redirect()->back()->withInput()->with('errors', ['Invalid promo code.']);
			$promoCount = $this->orderPromoModel->where('promo_id', $promo->id)->where('user_id', $created_by)->count();
			if ($promoCount >= $promo->promo_count) return redirect()->back()->withInput()->with('errors', [
				'Promo code has been used maximum number of times.'
			]);
			$promoCodeId = $promo->id;
		}

		$calculatedFare = orderFareCalculation($order_kms, $pickup_lat, $pickup_long, $drop_lat, $drop_long, implode(',', $categoryIdsArray), $booking_at, $promoCodeId, $pickup_kms, $drop_kms);
		if (!is($calculatedFare, 'object') && is($calculatedFare->total_fare))
			return redirect()->back()->withInput()->with('errors', ['Something went wrong, The fare is not calculated yet.']);

		if (!perm('orders', 'add-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to create order'
		]);

		$newOrderId = $this->orderModel->insert(new Order([
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

		if (!$newOrderId) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong while saving order, please try sometime later.'
		]);

		$newOrderUser = $this->orderUserModel->save(new OrderUser([
			'user_id' => $user_id, 'order_id' => $newOrderId,
		]));

		if (!$newOrderUser) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong while saving user order, please try sometime later.'
		]);

		if (!empty($driver_id)) {
			$newOrderDriver = $this->orderDriverModel->save(new OrderDriver([
				'action' => 'pending', 'order_id' => $newOrderId, 'driver_id' => $driver_id,
			]));

			if (!$newOrderDriver) return redirect()->back()->withInput()->with('errors', [
				'Something went wrong while saving driver order, please try sometime later.'
			]);

			$driverData = $this->userModel->find($driver_id);
			if ($driverData && $driverData->app_token) {
				sendNotification($driverData->app_token, [
					'title' => lang('Lang.youHaveNewRideOrder'), 'body' => lang('Lang.dontWaitTheUserForTheRidePleaseCheckIt')
				]);
			}

			setNotification([
				'notification_type'  => 'order',
				'is_seen'            => 'unseen',
				'user_id'            => $driver_id,
				'notification_title' => lang('Lang.youHaveNewRideOrder'),
				'notification_body'  => lang('Lang.dontWaitTheUserForTheRidePleaseCheckIt')
			]);
		}

		if (is($calculatedFare->fare_array, 'array')) {
			$newOrderFare = false;
			foreach ($calculatedFare->fare_array as $fare) {
				$newOrderFare = $this->orderFareModel->save(new OrderFare(['fare_id' => $fare->id, 'order_id' => $newOrderId]));
			}
			if (!$newOrderFare) return redirect()->back()->withInput()->with('errors', [
				'Something went wrong while saving fare order, please try sometime later.'
			]);
		}

		if (config('Settings')->enableTaxCommissionCalculation && is($calculatedFare->commission_array, 'array')) {
			$newOrderCommission = false;
			foreach ($calculatedFare->commission_array as $commission) {
				if ($commission->commission_type === 'percentage')
					$commissionAmount = $calculatedFare->calculate_fare * ($commission->commission / 100);
				else $commissionAmount = $commission->commission;
				$newOrderCommission = $this->orderCommissionModel->save(new OrderCommission([
					'order_id' => $newOrderId, 'commission_id' => $commission->id, 'commission_amount' => $commissionAmount
				]));
			}
			if (!$newOrderCommission) return redirect()->back()->withInput()->with('errors', [
				'Something went wrong while saving commission order, please try sometime later.'
			]);
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
			if (!$newOrderPromo) return redirect()->back()->withInput()->with('errors', [
				'Something went wrong while saving promo order, please try sometime later.'
			]);
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

		if (!$newOrderLocation) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong while saving order location, please try sometime later.'
		]);

		setNotification([
			'notification_type'  => 'order',
			'is_seen'            => 'unseen',
			'user_id'            => $user_id,
			'notification_title' => lang('Lang.yourRideHasBeenBooked'),
			'notification_body'  => lang('Lang.yourRideOrderHasBeenPlacedSuccessfully'),
		]);

		return redirect()->to(route_to('dispatch'))->with('success', ['Ride Booked Successfully']);
	}
}

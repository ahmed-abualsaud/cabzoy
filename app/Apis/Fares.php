<?php

namespace App\Apis;

use App\Models\VehicleRelationModel;
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Fares extends BaseResourceController
{
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$drop_lat        = $this->request->getVar('drop_lat') ?? 0;
		$drop_long       = $this->request->getVar('drop_long') ?? 0;
		$pickup_lat      = $this->request->getVar('pickup_lat') ?? 0;
		$promo_id        = $this->request->getVar('promo_id') ?? null;
		$pickup_long     = $this->request->getVar('pickup_long') ?? 0;
		$drop_distance   = $this->request->getVar('drop_distance') ?? 0;
		$total_distance  = $this->request->getVar('total_distance') ?? 0;
		$category_id     = $this->request->getVar('category_id') ?? null;
		$pickup_distance = $this->request->getVar('pickup_distance') ?? 0;
		$booking_time    = $this->request->getVar('booking_time') ?? date('Y-m-d H:i:s');

		if (gettype($category_id) === 'string' && !is_numeric($category_id)) {
			$vehicleRelationModel = new VehicleRelationModel();

			$availableVehicles = $vehicleRelationModel
				->getRelationFromCategoryName(true, strtolower($category_id) === 'any' ? null : $category_id)
				->findAll();

			if (is($availableVehicles, 'array')) {
				$categoryIdsArray = [];
				$categoryIdsArray = array_map(static fn ($vehicles) => $categoryIdsArray[$vehicles->category_id] = $vehicles->category_id, $availableVehicles);

				$category_id = implode(',', $categoryIdsArray);
			}
		}

		$fares = orderFareCalculation($total_distance, $pickup_lat, $pickup_long, $drop_lat, $drop_long, $category_id, $booking_time, $promo_id, $pickup_distance, $drop_distance);

		return $this->success($fares, 'success', 'Fares calculated successfully.');
	}
}

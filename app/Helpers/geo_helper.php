<?php

use App\Models\{CommissionModel, FareModel, FareRelationModel, OrderPromoModel, PromoModel, VehicleRelationModel};

if (!function_exists('checkPointInsidePolygon')) {
	/** Check Coordinates exists inside Polygon
	 *
	 * @param float  $x       Latitude
	 * @param float  $y       Longitude
	 * @param string $polygon Polygon eg. [{lat: "12", lng:"-74"}]
	 *
	 * @return bool */
	function checkPointInsidePolygon(?float $x = null, ?float $y = null, ?string $polygon = null)
	{
		$inside = false;
		if (is_string($polygon))
			$polygon = json_decode($polygon);

		for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
			$xi = $polygon[$i]->lat;
			$yi = $polygon[$i]->lng;
			$xj = $polygon[$j]->lat;
			$yj = $polygon[$j]->lng;

			$intersect = (($yi > $y) !== ($yj > $y))
				&& ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
			if ($intersect)
				$inside = !$inside;
		}

		return $inside;
	}
}

if (!function_exists('orderFareCalculation')) {
	/** Dynamic Fare Calculation
	 *
	 * @param boolean $apiCall Is this Api Call or Function Call
	 * @param float $totalKM
	 * @param float $pickup_lat
	 * @param float $pickup_long
	 * @param float $drop_lat
	 * @param float $drop_long
	 * @param string $vehicleCategory Vehicle Category ID array string with comma-separated eg. `1,2,3`
	 * @param string $bookingTime Booking Time eg. `2019-01-01 10:00:00`
	 * @param int $promoId Promo Code ID
	 * @param float $pickupTotalKms
	 * @param float $dropTotalKms
	 * @return object */
	function orderFareCalculation($totalKM = 0, $pickup_lat = null, $pickup_long = null, $drop_lat = null, $drop_long = null, string $vehicleCategory = null, $bookingTime = null, $promoId = null, $pickupTotalKms = null, $dropTotalKms = null)
	{
		$perKM             = 0;
		$discount          = 0;
		$commission        = 0;
		$minFare           = 0;
		$totalFare         = 0;
		$totalDistance     = 0;
		$CalculatedFare    = 0;
		$fareArray         = [];
		$commissionArray   = [];
		$appliedPromoArray = [];

		$zoneFareIdArray    = [];
		$zoneMinFareArray   = [];
		$zonePerKMFareArray = [];

		$baseFareIdArray    = [];
		$baseMinFareArray   = [];
		$basePerKMFareArray = [];

		$robinFareIdArray    = [];
		$robinMinFareArray   = [];
		$robinPerKMFareArray = [];

		$hourlyFareIdArray    = [];
		$hourlyMinFareArray   = [];
		$hourlyPerKMFareArray = [];

		$vehicleFareIdArray    = [];
		$vehicleMinFareArray   = [];
		$vehiclePerKMFareArray = [];

		$categoryFareIdArray    = [];
		$categoryMinFareArray   = [];
		$categoryPerKMFareArray = [];

		$fareModel            = new FareModel();
		$promoModel           = new PromoModel();
		$orderPromoModel      = new OrderPromoModel();
		$commissionModel      = new CommissionModel();
		$fareRelationModel    = new FareRelationModel();
		$vehicleRelationModel = new VehicleRelationModel();

		// Get Total distance in default unit
		$lengthUnit    = strtolower(getDefaultConfig('defaultLengthUnit', 'kms'));
		$defaultLength = ($lengthUnit === 'km' || $lengthUnit === 'kms') ? 0.001 : 0.000621372;
		$totalDistance = $totalKM * $defaultLength;

		// Get Robin Fares
		if (config('Settings')->enableIncludeRoutePathFromBase) {

			// Get Include Pickup Path From Base Fares
			if (config('Settings')->enableIncludePickupPathFromBase && is_numeric($pickupTotalKms)) {
				$robinPickupFareObject = $fareModel->isType('base')->isBetween($pickupTotalKms)->first();
				if (is($robinPickupFareObject, 'object')) {
					$robinFareIdArray[]    = $robinPickupFareObject;
					$robinPerKMFareArray[] = $robinPickupFareObject->fare;
					$robinMinFareArray[]   = $robinPickupFareObject->min_fare;
				}
			}

			// Get Include Destination Path From Base Fares
			if (config('Settings')->enableIncludeDestinationPathFromBase && is_numeric($dropTotalKms)) {
				$robinDropFareObject = $fareModel->isType('base')->isBetween($dropTotalKms)->first();
				if (is($robinDropFareObject, 'object')) {
					$robinFareIdArray[]    = $robinDropFareObject;
					$robinPerKMFareArray[] = $robinDropFareObject->fare;
					$robinMinFareArray[]   = $robinDropFareObject->min_fare;
				}
			}
		}

		// Get Base Fares
		if (config('Settings')->enableFareBaseCalculation && $totalDistance > 0) {
			$baseFareObject = $fareModel->isType('base')->isBetween($totalDistance)->first();
			if (is($baseFareObject, 'object')) {
				$baseFareIdArray[]    = $baseFareObject;
				$basePerKMFareArray[] = $baseFareObject->fare;
				$baseMinFareArray[]   = $baseFareObject->min_fare;
			}
		}

		// Get Vehicle & Vehicle Category Fares
		if (config('Settings')->enableFareCategoryCalculation && is($vehicleCategory, 'string')) {
			$categoryArray = explode(',', $vehicleCategory);
			if (is($categoryArray, 'array')) {
				$vehicleRelateArray = $vehicleRelationModel->notEnded()->whereIn('category_id', $categoryArray)->findAll();

				if (is($vehicleRelateArray, 'array')) {
					foreach ($vehicleRelateArray as $value) {

						// Get all fare relate to vehicle category
						$categoryFareRelateArray =  $fareRelationModel->relate(null, $value->category_id)->findAll();
						if (is($categoryFareRelateArray, 'array')) foreach ($categoryFareRelateArray as $categoryFareValue) {
							$categoryFareIdArray[]	  = $categoryFareValue->fare;
							$categoryPerKMFareArray[] = $categoryFareValue->fare->fare;
							$categoryMinFareArray[]	  = $categoryFareValue->fare->min_fare;
						}

						// Get all fare relate to vehicle
						$vehicleFareRelate =  $fareRelationModel->relate(null, null, $value->vehicle_id)->findAll();
						if (is($vehicleFareRelate, 'array')) foreach ($vehicleFareRelate as $vehicleFareValue) {
							$vehicleFareIdArray[]    = $vehicleFareValue->fare;
							$vehiclePerKMFareArray[] = $vehicleFareValue->fare->fare;
							$vehicleMinFareArray[]   = $vehicleFareValue->fare->min_fare;
						}
					}
				}
			}
		}

		// Get Hourly Fares
		if (config('Settings')->enableFareHourlyCalculation) {
			$hourlyFareObject = $fareModel->timeBetween($bookingTime)->first();
			if (is($hourlyFareObject, 'object')) {
				$hourlyFareIdArray[]    = $hourlyFareObject;
				$hourlyPerKMFareArray[] = $hourlyFareObject->fare;
				$hourlyMinFareArray[]   = $hourlyFareObject->min_fare;
			}
		}

		// Get Zone Fares
		if (config('Settings')->enableFareZoneCalculation) {
			$pickupLocation = $fareRelationModel->getZonesFare($pickup_lat, $pickup_long);
			if (is($pickupLocation, 'object')) {
				$pickupZoneFareRelate = $fareRelationModel->relate($pickupLocation->id)->first();
				if (is($pickupZoneFareRelate, 'object')) {
					if (array_search($pickupZoneFareRelate->fare->id, array_column($zoneFareIdArray, 'id')) === false) {
						$zoneFareIdArray[]    = $pickupZoneFareRelate->fare;
						$zonePerKMFareArray[] = $pickupZoneFareRelate->fare->fare;
						$zoneMinFareArray[]   = $pickupZoneFareRelate->fare->min_fare;
					}
				}
			}

			$dropLocation = $fareRelationModel->getZonesFare($drop_lat, $drop_long);
			if (is($dropLocation, 'object')) {
				$dropZoneFareRelate = $fareRelationModel->relate($dropLocation->id)->first();
				if (is($dropZoneFareRelate, 'object')) {
					if (array_search($dropZoneFareRelate->fare->id, array_column($zoneFareIdArray, 'id')) === false) {
						$zoneFareIdArray[]    = $dropZoneFareRelate->fare;
						$zonePerKMFareArray[] = $dropZoneFareRelate->fare->fare;
						$zoneMinFareArray[]   = $dropZoneFareRelate->fare->min_fare;
					}
				}
			}
		}

		$combinePerKMFareArray = array_merge($basePerKMFareArray, $categoryPerKMFareArray, $vehiclePerKMFareArray, $hourlyPerKMFareArray, $zonePerKMFareArray, $robinPerKMFareArray);
		$combineMinFareArray   = array_merge($baseMinFareArray, $categoryMinFareArray, $vehicleMinFareArray, $hourlyMinFareArray, $zoneMinFareArray, $robinMinFareArray);
		$combineFareIdArray    = array_merge($baseFareIdArray, $categoryFareIdArray, $vehicleFareIdArray, $hourlyFareIdArray, $zoneFareIdArray, $robinFareIdArray);

		// Fare Calculation
		switch (config('Settings')->fareCalculationType) {

			case 'sum':
				$minFare   += array_sum($baseMinFareArray);
				$perKM     += array_sum($basePerKMFareArray);
				$fareArray  = [...$baseFareIdArray, ...$fareArray];

				$minFare   += array_sum($hourlyMinFareArray);
				$perKM     += array_sum($hourlyPerKMFareArray);
				$fareArray  = [...$hourlyFareIdArray, ...$fareArray];

				$minFare   += array_sum($zoneMinFareArray);
				$perKM     += array_sum($zonePerKMFareArray);
				$fareArray  = [...$zoneFareIdArray, ...$fareArray];

				$minFare   += array_sum($categoryMinFareArray);
				$perKM     += array_sum($categoryPerKMFareArray);
				$fareArray  = [...$categoryFareIdArray, ...$fareArray];

				$minFare   += array_sum($vehicleMinFareArray);
				$perKM     += array_sum($vehiclePerKMFareArray);
				$fareArray  = [...$vehicleFareIdArray, ...$fareArray];

				$minFare   += array_sum($robinMinFareArray);
				$perKM     += array_sum($robinPerKMFareArray);
				$fareArray  = [...$robinFareIdArray, ...$fareArray];
				break;


			case 'greater-sum':
				if (is($basePerKMFareArray, 'array')) $perKM  += max($basePerKMFareArray);
				if (is($baseMinFareArray, 'array')) $minFare  += max($baseMinFareArray);
				if (is($baseFareIdArray, 'array')) $fareArray  = [
					array_reduce($baseFareIdArray, fn ($a, $b) => @$a->fare > $b->fare && @$a->min_fare > $b->min_fare ? $a : $b), ...$fareArray
				];

				if (is($hourlyPerKMFareArray, 'array')) $perKM  += max($hourlyPerKMFareArray);
				if (is($hourlyMinFareArray, 'array')) $minFare  += max($hourlyMinFareArray);
				if (is($hourlyFareIdArray, 'array')) $fareArray  = [
					array_reduce($hourlyFareIdArray, fn ($a, $b) => @$a->fare > $b->fare && @$a->min_fare > $b->min_fare ? $a : $b), ...$fareArray
				];

				if (is($zonePerKMFareArray, 'array')) $perKM  += max($zonePerKMFareArray);
				if (is($zoneMinFareArray, 'array')) $minFare  += max($zoneMinFareArray);
				if (is($zoneFareIdArray, 'array')) $fareArray  = [
					array_reduce($zoneFareIdArray, fn ($a, $b) => @$a->fare > $b->fare && @$a->min_fare > $b->min_fare ? $a : $b), ...$fareArray
				];

				if (is($categoryPerKMFareArray, 'array')) $perKM  += max($categoryPerKMFareArray);
				if (is($categoryMinFareArray, 'array')) $minFare  += max($categoryMinFareArray);
				if (is($categoryFareIdArray, 'array')) $fareArray  = [
					array_reduce($categoryFareIdArray, fn ($a, $b) => @$a->fare > $b->fare && @$a->min_fare > $b->min_fare ? $a : $b), ...$fareArray
				];

				if (is($vehiclePerKMFareArray, 'array')) $perKM  += max($vehiclePerKMFareArray);
				if (is($vehicleMinFareArray, 'array')) $minFare  += max($vehicleMinFareArray);
				if (is($vehicleFareIdArray, 'array')) $fareArray  = [
					array_reduce($vehicleFareIdArray, fn ($a, $b) => @$a->fare > $b->fare && @$a->min_fare > $b->min_fare ? $a : $b), ...$fareArray
				];

				if (is($robinPerKMFareArray, 'array')) $perKM  += max($robinPerKMFareArray);
				if (is($robinMinFareArray, 'array')) $minFare  += max($robinMinFareArray);
				if (is($robinFareIdArray, 'array')) $fareArray  = [
					array_reduce($robinFareIdArray, fn ($a, $b) => @$a->fare > $b->fare && @$a->min_fare > $b->min_fare ? $a : $b), ...$fareArray
				];
				break;


			case 'less-sum':
				if (is($basePerKMFareArray, 'array')) $perKM += min($basePerKMFareArray);
				if (is($baseMinFareArray, 'array')) $minFare += min($baseMinFareArray);
				if (is($baseFareIdArray, 'array')) {
					$baseFareMin = min(array_column($baseFareIdArray, 'fare'));
					$fareArray   = [...array_filter($baseFareIdArray, fn ($value) => ($value->fare === $baseFareMin)), ...$fareArray];
				}

				if (is($hourlyPerKMFareArray, 'array')) $perKM += min($hourlyPerKMFareArray);
				if (is($hourlyMinFareArray, 'array')) $minFare += min($hourlyMinFareArray);
				if (is($hourlyFareIdArray, 'array')) {
					$hourlyFareMin = min(array_column($hourlyFareIdArray, 'fare'));
					$fareArray   = [...array_filter($hourlyFareIdArray, fn ($value) => ($value->fare === $hourlyFareMin)), ...$fareArray];
				}

				if (is($zonePerKMFareArray, 'array')) $perKM += min($zonePerKMFareArray);
				if (is($zoneMinFareArray, 'array')) $minFare += min($zoneMinFareArray);
				if (is($zoneFareIdArray, 'array')) {
					$zoneFareMin = min(array_column($zoneFareIdArray, 'fare'));
					$fareArray   = [...array_filter($zoneFareIdArray, fn ($value) => ($value->fare === $zoneFareMin)), ...$fareArray];
				}

				if (is($categoryPerKMFareArray, 'array')) $perKM += min($categoryPerKMFareArray);
				if (is($categoryMinFareArray, 'array')) $minFare += min($categoryMinFareArray);
				if (is($categoryFareIdArray, 'array')) {
					$categoryFareMin = min(array_column($categoryFareIdArray, 'fare'));
					$fareArray       = [...array_filter($categoryFareIdArray, fn ($value) => ($value->fare === $categoryFareMin)), ...$fareArray];
				}

				if (is($vehiclePerKMFareArray, 'array')) $perKM += min($vehiclePerKMFareArray);
				if (is($vehicleMinFareArray, 'array')) $minFare += min($vehicleMinFareArray);
				if (is($vehicleFareIdArray, 'array')) {
					$vehicleFareMin = min(array_column($vehicleFareIdArray, 'fare'));
					$fareArray      = [...array_filter($vehicleFareIdArray, fn ($value) => ($value->fare === $vehicleFareMin)), ...$fareArray];
				}

				if (is($robinPerKMFareArray, 'array')) $perKM += min($robinPerKMFareArray);
				if (is($robinMinFareArray, 'array')) $minFare += min($robinMinFareArray);
				if (is($robinFareIdArray, 'array')) {
					$robinFareMin = min(array_column($robinFareIdArray, 'fare'));
					$fareArray      = [...array_filter($robinFareIdArray, fn ($value) => ($value->fare === $robinFareMin)), ...$fareArray];
				}
				break;


			case 'greater':
				if (is($combinePerKMFareArray, 'array')) $perKM  = max($combinePerKMFareArray);
				if (is($combineMinFareArray, 'array')) $minFare  = max($combineMinFareArray);
				if (is($combineFareIdArray, 'array')) $fareArray = [
					array_reduce($combineFareIdArray, fn ($a, $b) => @$a->fare > $b->fare && @$a->min_fare > $b->min_fare ? $a : $b), ...$fareArray
				];
				break;


			case 'less':
				if (is($combinePerKMFareArray, 'array')) $perKM = min($combinePerKMFareArray);
				if (is($combineMinFareArray, 'array')) $minFare = min($combineMinFareArray);
				if (is($combineFareIdArray, 'array')) {
					$combineFareMin = min(array_column($combineFareIdArray, 'fare'));
					$fareArray      = [...array_filter($combineFareIdArray, fn ($value) => ($value->fare === $combineFareMin)), ...$fareArray];
				}
				break;


			default:
				$minFare   += array_sum($baseMinFareArray);
				$perKM     += array_sum($basePerKMFareArray);
				$fareArray  = [...$baseFareIdArray, ...$fareArray];

				$minFare   += array_sum($hourlyMinFareArray);
				$perKM     += array_sum($hourlyPerKMFareArray);
				$fareArray  = [...$hourlyFareIdArray, ...$fareArray];

				$minFare   += array_sum($zoneMinFareArray);
				$perKM     += array_sum($zonePerKMFareArray);
				$fareArray  = [...$zoneFareIdArray, ...$fareArray];

				$minFare   += array_sum($categoryMinFareArray);
				$perKM     += array_sum($categoryPerKMFareArray);
				$fareArray  = [...$categoryFareIdArray, ...$fareArray];

				$minFare   += array_sum($vehicleMinFareArray);
				$perKM     += array_sum($vehiclePerKMFareArray);
				$fareArray  = [...$vehicleFareIdArray, ...$fareArray];

				$minFare   += array_sum($robinMinFareArray);
				$perKM     += array_sum($robinPerKMFareArray);
				$fareArray  = [...$robinFareIdArray, ...$fareArray];
				break;
		}

		// Calculate Fare
		$totalFare = $CalculatedFare = $totalDistance * $perKM;

		// Promo Code
		if (config('Settings')->enablePromoCode && is($promoId)) {
			$promoUsed = $orderPromoModel->where('promo_id', $promoId)->findAll();
			$promoUsedCount = is_countable($promoUsed) ? count($promoUsed) : 0;

			$promo = $promoModel->valid($totalFare, $promoUsedCount)->find($promoId);

			if (is($promo, 'object')) {
				if ($promo->promo_discount_type === 'percentage')
					$discount = $totalFare * ($promo->promo_discount / 100);
				else $discount = $promo->promo_discount;

				$totalFare = $totalFare - $discount;
				$appliedPromoArray[] = $promo;
			}
		}

		// Tax & Commission
		if (config('Settings')->enableTaxCommissionCalculation) {

			$categoryCommissionArray = [];
			$companyCommissionArray  = [];
			$vehicleCommissionArray  = [];

			if (is($vehicleRelateArray, 'array')) foreach ($vehicleRelateArray as $vehicleRelate) {

				// Vehicle Category Commission
				if (!empty($vehicleRelate->category_id))
					$categoryCommissionArray = [...$commissionModel->relate($vehicleRelate->category_id)->findAll(), ...$categoryCommissionArray];

				// Vehicle Commission
				if (!empty($vehicleRelate->vehicle_id))
					$vehicleCommissionArray  = [...$commissionModel->relate(null, $vehicleRelate->vehicle_id)->findAll(), ...$vehicleCommissionArray];

				// Company Commission
				// if (!empty($vehicleRelate->company_id))
				// 	$companyCommissionArray  = [...$commissionModel->relate(null, null, $vehicleRelate->company_id)->findAll(), ...$companyCommissionArray];
			}

			$commissionArray = [...$categoryCommissionArray, ...$companyCommissionArray, ...$vehicleCommissionArray];

			// Calculate Tax
			if (is($commissionArray, 'array')) foreach ($commissionArray as $commissionItem) {
				if ($commissionItem->commission_type === 'percentage')
					$commission += $totalFare * ($commissionItem->commission / 100);
				else $commission += $commissionItem->commission;
			}

			// Include or Exclude Tax & Commission
			if (!config('Settings')->includeTaxCommission)
				$totalFare = $totalFare + $commission;
		}

		// Round Up
		if (config('Settings')->enableFareCalculateRound) {
			$perKM          = round($perKM);
			$minFare        = round($minFare);
			$discount       = round($discount);
			$totalFare      = round($totalFare);
			$CalculatedFare = round($CalculatedFare);
		}

		$response = [
			'total_distance'   => $totalDistance,
			'fare'             => $perKM,
			'min_fare'         => $minFare,
			'discount'         => $discount,
			'commission'       => $commission,
			'calculate_fare'   => $CalculatedFare,
			'total_fare'       => $totalFare >= $minFare ? $totalFare : $minFare,
			'fare_array'       => $fareArray,
			'promo_codes'      => $appliedPromoArray,
			'commission_array' => $commissionArray,
		];

		return json_decode(json_encode($response));
	}
}

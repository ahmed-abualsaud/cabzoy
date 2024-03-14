<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<h3><?php print(config('Settings')->siteName ?? 'Dashboard') ?>'s Dispatch Panel</h3>
</div>
<div class="page-content" style="background-color: var(--bs-body-bg);" id="fullscreen-div">
	<button type="button" id="toggle-fullscreen" class="btn bg-white position-fixed top-0 end-0" data-bs-toggle="tooltip" data-bs-placement="left" title="Full Screen Mode">
		<i data-feather="maximize"></i>
	</button>
	<section class="row">
		<div class="col-12 col-lg-12">
			<div class="row">
				<div class="col-12 col-xl-7">
					<div class="card">
						<div class="card-header">
							<h4>Order A Ride</h4>
						</div>
						<div class="card-body">
							<form action="<?= route_to('dispatch') ?>" method="post">
								<div class="row">
									<div class="col-lg-6">
										<div class="form-group mb-3">
											<label for="pickupText">Pickup Location</label>
											<input type="text" id="pickupText" name="pickup_text" class="form-control form-control-lg <?php $validation->hasError('pickup_text') && print 'is-invalid'; ?>" placeholder="Search Pickup Location" value="<?= old('pickup_text') ?>">
											<div class="invalid-feedback"><?= $validation->getError('pickup_text') ?></div>

											<input type="hidden" name="pickup_lat" id="pickupTextLat" value="<?= old('pickup_lat') ?>">
											<input type="hidden" name="pickup_long" id="pickupTextLong" value="<?= old('pickup_long') ?>">
											<input type="hidden" name="order_kms" id="totalDistance" value="<?= old('order_kms') ?>">
											<input type="hidden" name="pickup_kms" id="pickupDistance" value="<?= old('pickup_kms') ?>">
											<input type="hidden" name="drop_kms" id="dropDistance" value="<?= old('drop_kms') ?>">
											<small id="fullscreenWarning" class="d-block mt-1 d-none">Place suggestion may not visible on fullscreen, <br> Please select place first then go to fullscreen mode.</small>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-group mb-3">
											<label for="dropText">Drop Location</label>
											<input type="text" id="dropText" name="drop_text" class="form-control form-control-lg <?php $validation->hasError('drop_text') && print 'is-invalid'; ?>" placeholder="Search Drop Location" value="<?= old('drop_text') ?>">
											<div class="invalid-feedback"><?= $validation->getError('drop_text') ?></div>

											<input type="hidden" name="drop_lat" id="dropTextLat" value="<?= old('drop_lat') ?>">
											<input type="hidden" name="drop_long" id="dropTextLong" value="<?= old('drop_long') ?>">
											<small id="fullscreenWarning2" class="d-block mt-1 d-none">Place suggestion may not visible on fullscreen, <br> Please select place first then go to fullscreen mode.</small>
										</div>
									</div>
									<div class="col-12">
										<div class="form-group">
											<label for="order_comment">Comment</label>
											<textarea name="order_comment" class="form-control <?php $validation->hasError('order_comment') && print 'is-invalid'; ?>" id="order_comment" placeholder="Comment for the driver or trip"></textarea>
											<div class="invalid-feedback"><?= $validation->getError('order_comment') ?></div>
										</div>
									</div>
									<div class="col-lg-3">
										<?= select_box('order_type', 'Booking Type', ['normal' => 'Immediate Booking', 'advanced' => 'Advanced Booking'], $validation); ?>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label for="booking_at_date" class="mb-2">Date of Booking</label>
											<input type="date" class="form-control form-control-lg <?php $validation->hasError('booking_at_date') && print 'is-invalid'; ?>" min="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d', strtotime('+6 day')); ?>" name="booking_at_date" id="booking_at_date" value="<?= old('booking_at_date') ?>">
											<div class="invalid-feedback"><?= $validation->getError('booking_at_date') ?></div>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label for="booking_at_time" class="mb-2">Time of Booking</label>
											<input type="time" class="form-control form-control-lg <?php $validation->hasError('booking_at_time') && print 'is-invalid'; ?>" name="booking_at_time" id="booking_at_time" value="<?= old('booking_at_time') ?>">
											<div class="invalid-feedback"><?= $validation->getError('booking_at_time') ?></div>
										</div>
									</div>
									<div class="col-lg-3">
										<?php $category = [];
										$category = array_map(static function ($data) use ($category) {
											return $category[] = ucwords($data->category_name);
										}, $categories);
										$category[] = 'ANY'; ?>
										<?= select_box('order_vehicle', 'Vehicle Type', array_combine($category, $category), $validation); ?>
									</div>
									<div class="col-lg-6">
										<?php $user = [];
										$user = array_map(static function ($data) use ($user) {
											return $user[$data->id] = ucwords($data->firstname . ' ' . $data->lastname);
										}, $users); ?>
										<?= select_box('user_id', 'User', $user, $validation); ?>
										<small>Not found user,<a href="<?= route_to('add_user'); ?>" class="btn-btn-primary btn-sm">Create new user here</a></small>
									</div>
									<div class="col-lg-6">
										<?php $driver = [];
										$driver = array_map(static function ($data) use ($driver) {
											return $driver[$data->id] = ucwords($data->firstname . ' ' . $data->lastname);
										}, $drivers); ?>
										<?= select_box('driver_id', 'Driver', $driver, $validation); ?>
										<small>Not found driver,<a href="<?= route_to('add_driver'); ?>" class="btn-btn-primary btn-sm">Assign driver here</a></small>
									</div>
									<div class="col-lg-6">
										<?= select_box('is_paid', 'Payment Status', ['paid' => 'Paid', 'not-paid' => 'Not Paid'], $validation); ?>
									</div>
									<div class="col-lg-6">
										<?= config('Settings')->enableCorporateAccount &&  config('Settings')->enableCorporatePayment ? select_box('payment_mode', 'Payment Mode', ['online' => 'Online', 'corporate' => 'Corporate Account', 'cod' => 'After Ride Cash'], $validation) : select_box('payment_mode', 'Payment Mode', ['online' => 'Online', 'cod' => 'After Ride Cash'], $validation); ?>
									</div>
									<div class="col-lg-8 mt-3">
										<div class="btn-group btn-block">
											<button class="btn btn-primary btn-lg" type="submit" name="bookFromDispatch" value="dispatch">Book</button>

											<button class="btn btn-primary btn-lg" id="calculateFare" type="button" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Calculate Fares</button>
										</div>
									</div>
									<div class="col-lg-4 mt-3">
										<button class="btn btn-danger btn-lg btn-block" id="reset" type="reset">Reset</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-12 col-xl-5">
					<div class="card d-none mb-3" id="fareCard">
						<div class="card-header">
							<h4>Estimated</h4>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-12">
									<legend>Start Destination: </legend>
									<span id="start_destination"></span>
								</div>
								<div class="col-12 mt-3">
									<legend>End Destination: </legend>
									<span id="end_destination"></span>
								</div>
								<div class="col-6 mt-3">
									<legend>Total Distance: </legend>
									<span id="total_distance"></span>
								</div>
								<div class="col-6 mt-3">
									<legend>Total Duration: </legend>
									<span id="total_duration"></span>
								</div>
							</div>
						</div>
					</div>

					<div class="card">
						<div class="card-body">
							<div id="myGoogleMap" class="shadow" style="height:60vh; border-radius: 24px">
								<noscript>Map are not loaded</noscript>
							</div>
						</div>
					</div>
				</div>

				<div class="col-12">
					<div class="card">
						<div class="card-body">
							<h4 class="card-title">New Orders</h4>
							<p class="card-text">Only order status type <mark>new</mark> and <mark>booked</mark> are shown here.
								<br>
								Others will be show <a href="<?= route_to('orders'); ?>">here</a>
							</p>

							<div class="table-responsive">
								<table class="table" id="table1">
									<thead>
										<tr>
											<th>#</th>
											<th>User</th>
											<th>Details</th>
											<th>Location</th>
											<th>Driver</th>
											<th data-type="date" data-format="MMM DD, YYYY">Booking At</th>
											<?php if (false && perm('orders', 'show, update, delete', true)) : ?>
												<th data-sortable="false">Action</th>
											<?php endif ?>
										</tr>
									</thead>
									<tbody>
										<?php $i = 0; ?>
										<?php if (is($orders, 'array')) foreach ($orders as $order) : ?>
											<tr>
												<td><?= ++$i; ?></td>
												<td>
													<?php if (!empty($order->order_users) && is_array($order->order_users)) : ?>
														<?php foreach ($order->order_users as $orderUser) : ?>
															<div class="d-flex align-items-center">
																<div class="flex-shrink-0">
																	<?= image($orderUser->user->profile_pic, $orderUser->user->firstname, strrev($orderUser->user->lastname)) ?>
																</div>
																<div class="flex-grow-1 ms-3">
																	<p class="lead text-truncate mb-1 text-capitalize">
																		<?= $orderUser->user->firstname . ' ' . $orderUser->user->lastname; ?>
																	</p>
																	<a href="<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? 'javascript:void(0)' : "tel://" . $orderUser->user->phone; ?>" class="text-truncate text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Call the user">
																		+<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($orderUser->user->phone, 3) : $orderUser->user->phone; ?>
																	</a>
																	<br>
																	<a href="<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? 'javascript:void(0)' : "mailto://" . $orderUser->user->email; ?>" class="text-truncate text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Send mail to user">
																		<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($orderUser->user->email, 2, true) : $orderUser->user->email; ?>
																	</a>
																</div>
															</div>
														<?php endforeach; ?>
													<?php endif; ?>
												</td>
												<td>
													<p class="my-1">
														Distance: <span class="font-bold mr-2"><?= $order->getOrderKms(true); ?></span>
													</p>
													<p class="my-1">
														Type:
														<span class="font-bold mr-2"><?= $order->order_vehicle; ?></span>
														<?= badge([
															'booked'     => 'info',
															'dispatched' => 'info',
															'cancel'     => 'danger',
															'new'        => 'warning',
															'ongoing'    => 'primary',
															'complete'   => 'success',
															'picked'     => 'secondary',
														], $order->order_status); ?>
													</p>
													<p class="my-1">
														Price:
														<span class="font-bold mr-2"><?= $order->getOrderPrice(true); ?></span>
														<?= badge(['not-paid' => 'danger', 'paid' => 'success'], $order->is_paid); ?>
													</p>

												</td>
												<td>
													<?php if (!empty($order->order_locations) && is_array($order->order_locations)) : ?>
														<?php foreach ($order->order_locations as $orderLocation) : ?>
															<p>
																<span class="text-<?= $orderLocation->order_location_type === 'pickup' ? 'success' : 'danger' ?>">
																	<i data-feather="arrow-right-circle"></i>
																</span>
																<span><?= $orderLocation->order_location_text; ?></span>
															</p>
														<?php endforeach; ?>
													<?php endif; ?>
												</td>
												<td>
													<?php if (!empty($order->order_drivers) && is_array($order->order_drivers)) : ?>
														<?php foreach ($order->order_drivers as $orderDriver) : ?>
															<?php if ($orderDriver->action !== 'rejected') : ?>
																<div class="d-flex align-items-center">
																	<div class="flex-shrink-0">
																		<?= image($orderDriver->user->profile_pic, $orderDriver->user->firstname, strrev($orderDriver->user->lastname)) ?>
																	</div>
																	<div class="flex-grow-1 ms-3">
																		<p class="lead text-truncate mb-1 text-capitalize">
																			<?= $orderDriver->user->firstname . ' ' . $orderDriver->user->lastname; ?>
																			<?= badge(['rejected' => 'danger', 'pending' => 'warning', 'accept' => 'success'], $orderDriver->action); ?>
																		</p>
																		<a href="<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? 'javascript:void(0)' : "tel://" . $orderDriver->user->phone; ?>" class="text-truncate text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Call the user">
																			+<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($orderDriver->user->phone, 3) : $orderDriver->user->phone ?>
																		</a>
																		<br>
																		<a href="<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? 'javascript:void(0)' : "mailto://" . $orderDriver->user->email; ?>" class="text-truncate text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Send mail to user">
																			<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($orderDriver->user->email, 2, true) : $orderDriver->user->email; ?>
																		</a>
																	</div>
																</div>
															<?php endif; ?>
														<?php endforeach; ?>
													<?php else : ?>
														<p>No driver assigned, assign one <a href="<?= route_to('update_order', $order->id); ?>">here</a></p>
													<?php endif; ?>
												</td>

												<td>
													<?= $order->getBookingAt() ?>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="staticBackdropLabel">Fare Details</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-0">
				<div id="modalFare">
					<div class="d-flex justify-content-center m-5">
						<div class="spinner-border" role="status">
							<span class="visually-hidden">Loading...</span>

						</div>
						<p class="ms-3 mt-1 text-capitalize text-primary text-xl">Calculating the fare</p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?libraries=places&key=<?= config('Settings')->googleMapApi; ?>"></script>
<style>
	.pac-container:after {
		content: none !important;
	}
</style>

<script>
	window.markers = [];
	const googleMap = google.maps;
	const directionsService = new googleMap.DirectionsService();
	const directionsRenderer = new googleMap.DirectionsRenderer({
		polylineOptions: {
			strokeColor: "#000000"
		},
		suppressMarkers: true,
		suppressInfoWindows: true
	});

	googleMap?.event.addDomListener(window, "load", () => {

		document.getElementById('staticBackdrop').addEventListener('show.bs.modal', function(event) {
			const modalFare = document.getElementById('modalFare');
			const endLat = document.getElementById('dropTextLat').value;
			const endLong = document.getElementById('dropTextLong').value;
			const startLat = document.getElementById('pickupTextLat').value;
			const startLong = document.getElementById('pickupTextLong').value;
			const dropDistance = document.getElementById('dropDistance').value;
			const orderVehicle = document.getElementById('order_vehicle').value;
			const totalDistance = document.getElementById('totalDistance').value;
			const bookingAtDate = document.getElementById('booking_at_date').value;
			const bookingAtTime = document.getElementById('booking_at_time').value;
			const pickupDistance = document.getElementById('pickupDistance').value;

			fetch('<?= site_url('api/fares'); ?>?' + new URLSearchParams({
				"category_id": orderVehicle,
				"drop_lat": endLat ? parseFloat(endLat) : 0.0,
				"drop_long": endLong ? parseFloat(endLong) : 0.0,
				"pickup_lat": startLat ? parseFloat(startLat) : 0.0,
				"pickup_long": startLong ? parseFloat(startLong) : 0.0,
				"drop_distance": dropDistance ? parseFloat(dropDistance) : 0.0,
				"total_distance": totalDistance ? parseFloat(totalDistance) : 0.0,
				"pickup_distance": pickupDistance ? parseFloat(pickupDistance) : 0.0,
				"booking_time": bookingAtDate && bookingAtTime ? bookingAtDate + ' ' + bookingAtTime + ':00' : null,
			})).then(data => data.json()).then(response => {
				if (response.status === 200) {
					let fareArray = '';
					let commissionArray = '';

					if (response.data.fare_array.length > 0) {
						for (let fareIndex = 0; fareIndex < response?.data?.fare_array.length; fareIndex++) {
							const element = response?.data?.fare_array[fareIndex];
							fareArray += '<tr><td>' + element.fare_name + '</td><td>' + element.fare + '</td><td>' + element.min_fare + '</td></tr>';
						}
					}

					if (response.data.commission_array.length > 0) {
						for (let fareIndex = 0; fareIndex < response?.data?.commission_array.length; fareIndex++) {
							const element = response?.data?.commission_array[fareIndex];
							fareArray += '<tr><td colspan="2">' + element.commission_name + '</td><td>' + element.commission + element.commission_type === 'percentage' ? '%' : '' + '</td></tr>';
						}
					}

					modalFare.innerHTML = `<table class="table table-bordered table-lg table-responsive text-capitalize mb-0"><thead><tr><th>Fare Name</th><th>Per <?= config('Settings')->defaultLengthUnit; ?></th><th>Minimum Fare</th></tr></thead><tbody>${fareArray}${commissionArray}<tr class="bg-secondary text-white"><td colspan="2">total distance</td><td>${formatUnit(response.data.total_distance, false)}</td></tr><tr class="bg-secondary text-white"><td colspan="2">per <?= config('Settings')->defaultLengthUnit; ?> fare</td><td>${formatCurrency(response.data.fare)}</td></tr><tr class="bg-secondary text-white"><td colspan="2">minimum fare</td><td>${formatCurrency(response.data.min_fare)}</td></tr><tr class="bg-secondary text-white"><td colspan="2">discount</td><td>${formatCurrency(response.data.discount)}</td></tr><tr class="bg-secondary text-white"><td colspan="2">commission</td><td>${formatCurrency(response.data.commission)}</td></tr><tr class="bg-secondary text-white"><td colspan="2">calculate fare</td><td>${formatCurrency(response.data.calculate_fare)}</td></tr><tr class="bg-black bg-gradient text-white"><td colspan="2">total fare</td><td>${formatCurrency(response.data.total_fare)}</td></tr></tbody></table>`;
				} else {
					modalFare.innerHTML = response.error;
				}
			}).catch(console.error);
		})

		window.map = new googleMap.Map(document.getElementById('myGoogleMap'), {
			zoom: 10,
			disableDefaultUI: true,
			fullscreenControl: true,
			keyboardShortcuts: false,
			mapTypeId: googleMap.MapTypeId.ROADMAP,
			center: new googleMap.LatLng(<?= config('Settings')->defaultLat ?>, <?= config('Settings')->defaultLong ?>),
		});

		placeAutoComplete('pickupText');
		placeAutoComplete('dropText');
		directionsRenderer.setMap(window.map);

		const endLat = document.getElementById('dropTextLat').value;
		const endLong = document.getElementById('dropTextLong').value;
		const startLat = document.getElementById('pickupTextLat').value;
		const startLong = document.getElementById('pickupTextLong').value;

		if (endLat && endLong && startLat && startLong) showRoute();
	});

	function placeAutoComplete(placesInput) {
		const autocomplete = new googleMap.places.Autocomplete(document.getElementById(placesInput), {
			// types: ["address"],
			radius: 10000,
			components: 'country:<?= config('Settings')->defaultCountryNameCode ?>',
			language: '<?= config('Settings')->defaultLanguage ?>'
		});

		googleMap.event.addListener(autocomplete, 'place_changed', () => {
			const place = autocomplete.getPlace();

			console.log('Place Inputs', placesInput);
			document.getElementById(placesInput + "Lat").value = place.geometry.location.lat();
			document.getElementById(placesInput + "Long").value = place.geometry.location.lng();
			if (placesInput === 'pickupText')
				window.markers[0] = setMarker('Pick Up Location', place.geometry.location)
			else if (placesInput === 'dropText') {
				window.markers[1] = setMarker('Drop Location', place.geometry.location);
				showRoute();
			}
		})
	}

	function setMarker(title, position, icon = null) {
		var marker = new google.maps.Marker({
			title: title,
			icon: {
				url: '<?= site_url('assets/img/marker.png') ?>', // url
				scaledSize: new google.maps.Size(18, 18), // scaled size
				origin: new google.maps.Point(0, 0), // origin
				anchor: new google.maps.Point(0, 0) // anchor
			},
			map: window.map,
			position: position,
		});

		const infoWindow = new googleMap.InfoWindow();

		marker.addListener("click", () => {
			infoWindow.close();
			infoWindow.setContent('<div class="card mb-0 px-2 py-1 shadow"><h5 class="text-center mb-0">' + marker.getTitle() + '</h5></div>');
			infoWindow.open(marker.getMap(), marker);
		});

		window.map.setCenter(marker.getPosition());

		return marker;
	}

	function showRoute() {
		const endLat = document.getElementById('dropTextLat').value;
		const endLong = document.getElementById('dropTextLong').value;
		const startLat = document.getElementById('pickupTextLat').value;
		const startLong = document.getElementById('pickupTextLong').value;

		// Distance Between Pickup to Base
		directionsService.route({
			travelMode: 'DRIVING',
			origin: new googleMap.LatLng(startLat, startLong),
			destination: new googleMap.LatLng('<?= config('Settings')->defaultLat ?>', '<?= config('Settings')->defaultLong ?>'),
		}, (result, status) => {
			if (status === googleMap.DirectionsStatus.OK)
				document.getElementById('pickupDistance').value = result.routes[0].legs[0].distance.value;
		})

		// Distance Between Destination to Base
		directionsService.route({
			travelMode: 'DRIVING',
			destination: new googleMap.LatLng(endLat, endLong),
			origin: new googleMap.LatLng('<?= config('Settings')->defaultLat ?>', '<?= config('Settings')->defaultLong ?>'),
		}, (result, status) => {
			if (status === googleMap.DirectionsStatus.OK)
				document.getElementById('dropDistance').value = result.routes[0].legs[0].distance.value;
		})

		directionsService.route({
			origin: new googleMap.LatLng(startLat, startLong),
			destination: new googleMap.LatLng(endLat, endLong),
			travelMode: 'DRIVING'
		}, (result, status) => {
			if (status === googleMap.DirectionsStatus.OK) {
				document.getElementById('totalDistance').value = result.routes[0].legs[0].distance.value;

				document.getElementById('pickupTextLat').value = result.routes[0].legs[0].start_location.lat();
				document.getElementById('pickupTextLong').value = result.routes[0].legs[0].start_location.lng();

				document.getElementById('dropTextLat').value = result.routes[0].legs[0].end_location.lat();
				document.getElementById('dropTextLong').value = result.routes[0].legs[0].end_location.lng();

				document.getElementById('start_destination').innerHTML = result.routes[0].legs[0].start_address;
				document.getElementById('end_destination').innerHTML = result.routes[0].legs[0].end_address;
				document.getElementById('total_distance').innerHTML = result.routes[0].legs[0].distance.text;
				document.getElementById('total_duration').innerHTML = result.routes[0].legs[0].duration.text;

				document.getElementById('fareCard').classList.remove('d-none');

				// window.markers.map((marker) => {
				// 	if (marker) marker.setMap(null)
				// });
				// setMarker(result.routes[0].legs[0].start_address, result.routes[0].legs[0].start_location);
				// setMarker(result.routes[0].legs[0].end_address, result.routes[0].legs[0].end_location);
				directionsRenderer.setDirections(result);
			}
		})
	}

	document.getElementById('reset').addEventListener('click', () => {
		window.markers.map((marker) => {
			if (marker) marker.setMap(null)
		});

		directionsRenderer.setMap(null);
		document.getElementById('fareCard').classList.add('d-none');

		if (document.getElementById('dropTextLat').value !== '')
			document.getElementById('dropTextLat').value = '';

		if (document.getElementById('dropTextLong').value !== '')
			document.getElementById('dropTextLong').value = '';

		if (document.getElementById('pickupTextLat').value !== '')
			document.getElementById('pickupTextLat').value = '';

		if (document.getElementById('pickupTextLong').value !== '')
			document.getElementById('pickupTextLong').value = '';

		window.map.setCenter(new googleMap.LatLng(<?= config('Settings')->defaultLat ?>, <?= config('Settings')->defaultLong ?>));
	})
</script>

<script>
	document.getElementById('fullscreen-div').addEventListener('fullscreenchange', (event) => {
		if (document.fullscreenElement)
			document.getElementById('fullscreen-div').classList.add('p-5');
		else {
			if (document.getElementById('fullscreen-div').classList.contains('p-5'))
				document.getElementById('fullscreen-div').classList.remove('p-5');
		}
	});

	document.getElementById('toggle-fullscreen').addEventListener('click', (event) => {
		if (document.fullscreenElement) {
			document.exitFullscreen();
			document.getElementById('fullscreenWarning').classList.add('d-none');
			document.getElementById('fullscreenWarning2').classList.add('d-none');
			document.getElementById('toggle-fullscreen').innerHTML = feather.icons.maximize.toSvg();
		} else {
			document.getElementById('fullscreen-div').requestFullscreen();
			document.getElementById('fullscreenWarning').classList.remove('d-none');
			document.getElementById('fullscreenWarning2').classList.remove('d-none');
			document.getElementById('toggle-fullscreen').innerHTML = feather.icons.minimize.toSvg();
		}
	});
</script>
<?= $this->endSection(); ?>
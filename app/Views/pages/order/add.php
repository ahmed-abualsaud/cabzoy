<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Add Booking</h3>
				<p class="text-subtitle text-muted">For user to add order</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('orders'); ?>">Bookings</a></li>
						<li class="breadcrumb-item active" aria-current="page">Add Booking</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-body p-5">
						<form action="<?= route_to('order') ?>" method="post" enctype="multipart/form-data">
							<?= csrf_field(); ?>
							<div class="row mb-3">
								<div class="col-lg-6">
									<div class="form-group">
										<input type="text" name="pickup_location" class="form-control form-control-lg" id="pickupSelector" autocomplete="off" placeholder="Enter Pick Up Location">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<input type="text" name="drop_location" class="form-control form-control-lg" id="dropoffSelector" autocomplete="off" placeholder="Enter Drop Location">
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group">
										<textarea name="comment" id="comment" cols="30" rows="5" class="form-control" placeholder="Comment for the order / driver"></textarea>
									</div>
								</div>

								<div class="col-lg-4 mt-3">
									<?php $vehicle = [];
									if (is($vehicles, 'array')) foreach ($vehicles as $value) {
										$vehicle[$value->id] = ucwords($value->name);
									} ?>
									<?= select_box('vehicle_id', 'Vehicle', $vehicle, $validation); ?>
								</div>

								<div class="col-lg-4 mt-3">
									<?php $user = [];
									if (is($users, 'array')) foreach ($users as $value) {
										$user[$value->id] = ucwords($value->name);
									} ?>
									<?= select_box('user_id', 'User', $user, $validation); ?>
								</div>

								<div class="col-lg-4 mt-3">
									<?php $driver = [];
									if (is($drivers, 'array')) foreach ($drivers as $value) {
										$driver[$value->id] = ucwords($value->name);
									} ?>
									<?= select_box('driver_id', 'Driver', $driver, $validation); ?>
								</div>

								<div class="col-lg-4 mt-3">
									<?= select_box('order_status', 'Order Status', [
										'new'        => 'New',
										'booked'     => 'Booked',
										'cancel'     => 'Cancel',
										'ongoing'    => 'Ongoing',
										'pickuped'   => 'Pickuped',
										'complete'   => 'Complete',
										'dispatched' => 'Dispatched',
									], $validation); ?>
								</div>

								<div class="col-lg-4 mt-3">
									<?= select_box('order_type', 'Order Type', [
										'normal' => 'Normal',
									], $validation); ?>
								</div>

								<div class="col-lg-4 mt-3">
									<?= select_box('order_payment_mode', 'Payment Method', [
										'online' => 'Online', 'cod' => 'COD',
									], $validation); ?>
								</div>

								<div class="col-md-6">
									<div class="form-group mt-4">
										<div class="form-check">
											<div class="custom-control custom-checkbox">
												<input type="checkbox" class="form-check-input form-check-success form-check-glow" name="is_paid" id="is_paid" <?php old('is_paid') === 'on' && print 'checked'; ?>>
												<label class="form-check-label h5" for="is_paid">Is Payment Received</label>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Add Booking</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=<?= config('Settings')->googleMapApi; ?>&language=en"></script>
<script>
	function initialize() {
		new google.maps.places.Autocomplete(
			document.getElementById("pickupSelector"), {
				// bounds: new google.maps.LatLngBounds(45.4215296, -75.6971931),
			}
		);
		new google.maps.places.Autocomplete(
			document.getElementById("dropoffSelector"), {
				// bounds: new google.maps.LatLngBounds(45.4215296, -75.6971931),
			}
		);
	}
	if (google) google.maps.event.addDomListener(window, 'load', initialize);
</script>
<?= $this->endSection(); ?>
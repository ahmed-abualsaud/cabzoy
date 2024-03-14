<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Update Default Vehicle</h3>
				<p class="text-subtitle text-muted">For user to add vehicle</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('user_vehicles'); ?>">Default Vehicles</a></li>
						<li class="breadcrumb-item active" aria-current="page">Update Default Vehicle</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('vehicle') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body p-5">
							<div class="row mb-3">
								<div class="col-6">
									<?php $driver = [];
									if (is($drivers, 'array')) $driver = array_map(static function ($data) use ($driver) {
										return $driver[$data->id] = ucwords($data->firstname . ' ' . $data->lastname);
									}, $drivers); ?>
									<?= select_box('user_id', 'Drivers', $driver, $validation, $user_vehicle->user_id); ?>
								</div>
								<div class="col-6">
									<?php $vehicle = [];
									if (is($vehicles, 'array')) $vehicle = array_map(static function ($data) use ($vehicle) {
										return $vehicle[$data->id] = ucwords($data->vehicle_number);
									}, $vehicles); ?>
									<?= select_box('vehicle_id', 'Vehicles', $vehicle, $validation, $user_vehicle->vehicle_id); ?>
								</div>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Update Default Vehicle</button>
							</div>

						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
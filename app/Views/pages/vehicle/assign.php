<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Assign Vehicle</h3>
				<p class="text-subtitle text-muted">For user to assign vehicle</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('vehicles'); ?>">Vehicles</a></li>
						<li class="breadcrumb-item active" aria-current="page">Assign Vehicle</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('vehicle') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="row">
				<div class="col-lg-8">
					<div class="card">
						<div class="card-body p-5">
							<div class="table-responsive">
								<table class="table" id="table1">
									<thead>
										<tr>
											<th>#</th>
											<th>Category</th>
											<th>Driver</th>
											<th>Vehicle</th>
											<th>Status</th>
											<th data-type="date" data-format="MMM DD, YYYY">Started At</th>
											<th data-type="date" data-format="MMM DD, YYYY">Ended At</th>
											<?php if (perm('vehicles', 'show, update, assign, delete', true)) : ?>
												<th data-sortable="false">Action</th>
											<?php endif ?>
										</tr>
									</thead>
									<tbody>
										<?php $i = 0; ?>
										<?php if (is($assignedVehicles, 'array')) foreach ($assignedVehicles as $data) : ?>
											<tr>
												<td><?= ++$i; ?></td>
												<td class="text-capitalize"><?= $data->category->category_name; ?></td>
												<td class="text-capitalize">
													<?= ucwords($data->user->firstname . ' ' . $data->user->lastname); ?>
												</td>
												<td class="text-uppercase"><?= $data->vehicle->vehicle_number; ?></td>
												<td>
													<?= badge(['available' => 'success', 'busy' => 'warning', 'not-available' => 'danger'], $data->status); ?>
												</td>
												<td class="text-truncate"><?= $data->started_at; ?></td>
												<td class="text-truncate"><?= $data->ended_at; ?></td>

												<?php if (perm('vehicles', 'assign, delete', true)) : ?>
													<td class="text-truncate">
														<?php if (perm('vehicles', 'assign') && $data->ended_at === 'not ended yet.') : ?>
															<a href="<?= route_to('update_assign_vehicle', $data->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Finish Work">
																<i data-feather="check-circle"></i>
															</a>
														<?php endif ?>

														<?php if (perm('vehicles', 'assign')) : ?>
															<a href="<?= route_to('delete_assign_vehicle', $data->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Work">
																<i data-feather="trash"></i>
															</a>
														<?php endif ?>
													</td>
												<?php endif ?>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="card">
						<div class="card-body p-5">
							<form action="<?= route_to('assign_vehicle') ?>" method="post" enctype="multipart/form-data">
								<div class="row">
									<div class="col-12">
										<?php $driver = [];
										if (is($drivers, 'array')) $driver = array_map(static function ($data) use ($driver) {
											return $driver[$data->id] = ucwords($data->firstname . ' ' . $data->lastname);
										}, $drivers); ?>
										<?= select_box('driver_id', 'Driver', $driver, $validation); ?>
									</div>

									<?php if (!config('Settings')->enableDefaultVehicleAssign) : ?>
										<div class="col-12">
											<?php $vehicle = [];
											if (is($vehicles, 'array')) $vehicle = array_map(static function ($data) use ($vehicle) {
												return $vehicle[$data->id] = ucwords($data->vehicle_number);
											}, $vehicles); ?>
											<?= select_box('vehicle_id', 'Vehicle', $vehicle, $validation); ?>
										</div>
									<?php endif; ?>

									<div class="col-12">
										<?php $category = [];
										if (is($categories, 'array')) $category = array_map(static function ($data) use ($category) {
											return $category[$data->id] = ucwords($data->category_name);
										}, $categories); ?>
										<?= select_box('category_id', 'Category', $category, $validation); ?>
									</div>

									<div class="mt-3 col">
										<button type="submit" class="btn btn-lg btn-success">Assign</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
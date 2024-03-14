<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Vehicles</h3>
				<p class="text-subtitle text-muted">For user to check vehicles list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Vehicles</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section">
		<div class="card">
			<div class="card-header">
				<button class="csv btn btn-primary">Export CSV</button>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table" id="table1">
						<thead>
							<tr>
								<th>#</th>
								<th>Vehicle</th>
								<th>Seats</th>
								<th>Status</th>
								<th data-type="date" data-format="MMM DD, YYYY">Created At</th>
								<?php if (perm('vehicles', 'show, update, delete', true)) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($vehicles, 'array')) foreach ($vehicles as $data) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($data->vehicle_image, $data->vehicle_number, strrev($data->vehicle_number)) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<span class="badge text-capitalize bg-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Vehicle Brand">
													<?= $data->vehicle_brand; ?>
												</span>
												<span class="badge text-capitalize bg-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Vehicle Modal">
													<?= $data->vehicle_modal; ?>
												</span>
												<span class="badge text-capitalize bg-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Vehicle Color"><?= $data->vehicle_color; ?></span>
												<h3 class="text-truncate mt-1"><?= $data->vehicle_number; ?></h3>
											</div>
										</div>
									</td>
									<td><?= $data->vehicle_seats; ?></td>
									<td>
										<?= badge(['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'], $data->vehicle_status); ?>
									</td>
									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($data->user->profile_pic, $data->user->firstname, strrev($data->user->lastname)) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<p class="lead text-truncate mb-1 text-capitalize">
													<?= $data->user->firstname . ' ' . $data->user->lastname; ?>
												</p>
												<span class="text-truncate"><?= $data->getCreatedAt(true) ?></span>
											</div>
										</div>
									</td>
									<?php if (perm('vehicles', 'show, update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('vehicles', 'update')) : ?>
												<a href="<?= route_to('update_vehicle', $data->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Vehicle">
													<i data-feather="edit"></i>
												</a>
											<?php endif ?>

											<?php if (perm('vehicles', 'delete')) : ?>
												<a href="<?= route_to('delete_vehicle', $data->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Vehicle">
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
	</section>
</div>
<?= $this->endSection(); ?>
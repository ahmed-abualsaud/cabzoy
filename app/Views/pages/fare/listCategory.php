<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Vehicle Type Fares</h3>
				<p class="text-subtitle text-muted">For user to check fares list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Vehicle Type Fares</li>
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
								<th>Fare Name</th>
								<th>Vehicle Type Name</th>
								<th>Fare Per <?= config('Settings')->defaultLengthUnit; ?></th>
								<th>Min Fare Amount</th>
								<th data-type="date" data-format="MMM DD, YYYY">Created By</th>
								<?php if (perm('fares', 'show')) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($fares, 'array')) foreach ($fares as $fare) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td class="text-capitalize"><?= $fare->fare->fare_name ?></td>
									<td class="text-capitalize">
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($fare->category->category_image, $fare->category->category_name, strrev($fare->category->category_name)) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<p class="lead text-truncate mb-1 text-capitalize">
													<?= $fare->category->category_name; ?>
												</p>
												<span class="text-truncate"><?= $fare->category->getCreatedAt(true) ?></span>
											</div>
										</div>
									</td>
									<td><?= $fare->fare->getFare(true); ?>/<?= config('Settings')->defaultLengthUnit; ?></td>
									<td><?= $fare->fare->getMinFare(true); ?></td>
									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($fare->category->user->profile_image, $fare->category->user->firstname, strrev($fare->category->user->lastname)) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<p class="lead text-truncate mb-1 text-capitalize">
													<?= $fare->category->user->firstname . ' ' . $fare->category->user->lastname; ?>
												</p>
												<span class="text-truncate"><?= $fare->fare->getCreatedAt(true) ?></span>
											</div>
										</div>
									</td>
									<?php if (perm('fares', 'update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('fares', 'update')) : ?>
												<a href="<?= route_to('update_category_fare', $fare->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Fare">
													<i data-feather="edit"></i>
												</a>
											<?php endif ?>

											<?php if (perm('fares', 'delete')) : ?>
												<a href="<?= route_to('delete_category_fare', $fare->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Fare" onclick="return confirm('Are you sure?')">
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
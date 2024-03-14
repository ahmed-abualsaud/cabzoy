<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Commissions</h3>
				<p class="text-subtitle text-muted">For user to check commissions list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Commissions</li>
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
								<th>Commission Name</th>
								<th>Commission</th>
								<th>Commission on</th>
								<th>Beneficiary</th>
								<th>Status</th>
								<th data-type="date" data-format="MMM DD, YYYY">Created By</th>
								<?php if (perm('commissions', 'update, delete')) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($commissions, 'array')) foreach ($commissions as $relation) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td class="text-capitalize"><?= $relation->commission->commission_name; ?></td>
									<td class="text-capitalize"><?= $relation->commission->getCommission(true); ?></td>
									<td>
										<?php if (isset($relation->commission->category) && !empty($relation->commission->category) && is_object($relation->commission->category)) : ?>
											<div class="d-flex align-items-center">
												<div class="flex-shrink-0">
													<?= image($relation->commission->category->category_image, $relation->commission->category->category_name, strrev($relation->commission->category->category_name)) ?>
												</div>
												<div class="flex-grow-1 ms-3">
													<p class="lead text-truncate mb-1 text-capitalize">
														<?= $relation->commission->category->category_name; ?>
													</p>
													<span class="text-truncate">
														<?= $relation->commission->category->getCreatedAt(true) ?>
													</span>
												</div>
											</div>
										<?php elseif (isset($relation->commission->vehicle) && !empty($relation->commission->vehicle) && is_object($relation->commission->vehicle)) : ?>
											<div class="d-flex align-items-center">
												<div class="flex-shrink-0">
													<?= image($relation->commission->vehicle->vehicle_image, $relation->commission->vehicle->vehicle_number, strrev($relation->commission->vehicle->vehicle_number)) ?>
												</div>
												<div class="flex-grow-1 ms-3">
													<p class="lead text-truncate mb-1 text-capitalize">
														<?= $relation->commission->vehicle->vehicle_number; ?>
													</p>
													<span class="text-truncate">
														<?= $relation->commission->vehicle->getCreatedAt(true) ?>
													</span>
												</div>
											</div>
										<?php elseif (isset($relation->commission->company) && !empty($relation->commission->company) && is_object($relation->commission->company)) : ?>
											<div class="d-flex align-items-center">
												<div class="flex-shrink-0">
													<?= image($relation->commission->company->company_image, $relation->commission->company->company_name, strrev($relation->commission->company->company_name)) ?>
												</div>
												<div class="flex-grow-1 ms-3">
													<p class="lead text-truncate mb-1 text-capitalize">
														<?= $relation->commission->company->company_name; ?>
													</p>
													<span class="text-truncate">
														<?= $relation->commission->company->getCreatedAt(true) ?>
													</span>
												</div>
											</div>
										<?php endif; ?>
									</td>
									<td>
										<?php if (isset($relation->user) && !empty($relation->user) && is_object($relation->user)) : ?>
											<div class="d-flex align-items-center">
												<div class="flex-shrink-0">
													<?= image($relation->user->profile_pic, $relation->user->firstname, strrev($relation->user->lastname)) ?>
												</div>
												<div class="flex-grow-1 ms-3">
													<p class="lead text-truncate mb-1 text-capitalize">
														<?= $relation->user->firstname . ' ' . $relation->user->lastname; ?>
													</p>
													<span class="text-truncate"><?= $relation->user->getCreatedAt(true) ?></span>
												</div>
											</div>
										<?php elseif (isset($relation->company) && !empty($relation->company) && is_object($relation->company)) : ?>
											<div class="d-flex align-items-center">
												<div class="flex-shrink-0">
													<?= image($relation->company->company_image, $relation->company->company_name, strrev($relation->company->company_name)) ?>
												</div>
												<div class="flex-grow-1 ms-3">
													<p class="lead text-truncate mb-1 text-capitalize">
														<?= $relation->company->company_name; ?>
													</p>
													<span class="text-truncate"><?= $relation->company->getCreatedAt(true) ?></span>
												</div>
											</div>
										<?php endif; ?>
									</td>
									<td>
										<?= badge(['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'], $relation->commission->commission_status); ?>
									</td>
									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($relation->commission->user->profile_pic, $relation->commission->user->firstname, strrev($relation->commission->user->lastname)) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<p class="lead text-truncate mb-1 text-capitalize">
													<?= $relation->commission->user->firstname . ' ' . $relation->commission->user->lastname; ?>
												</p>
												<span class="text-truncate"><?= $relation->commission->getCreatedAt(true) ?></span>
											</div>
										</div>
									</td>
									<?php if (perm('commissions', 'show, update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('commissions', 'update')) : ?>
												<a href="<?= route_to('update_commission', $relation->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Commission">
													<i data-feather="edit"></i>
												</a>
											<?php endif ?>

											<?php if (perm('commissions', 'delete')) : ?>
												<a href="<?= route_to('delete_commission', $relation->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Commission">
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
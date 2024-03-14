<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Promo Codes</h3>
				<p class="text-subtitle text-muted">For user to check promo codes list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Promo Codes</li>
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
								<th>Promo Code</th>
								<th>Discount</th>
								<th>Minimum Uses Amount</th>
								<th>Maximum Uses Amount</th>
								<th>Uses Count</th>
								<th>Status</th>
								<?php if (perm('promos', 'show, update, delete', true)) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($promos, 'array')) foreach ($promos as $promo) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td><?= $promo->promo_code; ?></td>
									<td><?= $promo->getPromoDiscount(true); ?></td>
									<td><?= $promo->getPromoMinAmount(true); ?></td>
									<td><?= $promo->getPromoMaxAmount(true); ?></td>
									<td><?= $promo->promo_count; ?></td>
									<td>
										<?= badge(['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'], $promo->promo_status); ?>
									</td>

									<?php if (perm('promos', 'show, update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('promos', 'update')) : ?>
												<a href="<?= route_to('update_promo', $promo->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Promo">
													<i data-feather="edit"></i>
												</a>
											<?php endif ?>

											<?php if (perm('promos', 'delete')) : ?>
												<a href="<?= route_to('delete_promo', $promo->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Promo">
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
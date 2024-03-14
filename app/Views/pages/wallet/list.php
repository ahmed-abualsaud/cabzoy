<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Wallets</h3>
				<p class="text-subtitle text-muted">For user to check wallets list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Wallets</li>
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
								<th>Affected</th>
								<th>Amount</th>
								<th>Action Type</th>
								<th>Transaction Type</th>
								<th>Status</th>
								<th data-type="date" data-format="MMM DD, YYYY">Created At</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($wallets, 'array')) foreach ($wallets as $data) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td>
										<?php if (!is_null($data->company_id)) : ?>
											<div class="d-flex align-items-center">
												<div class="flex-shrink-0">
													<?= image($data->company->company_image, $data->company->company_name, strrev($data->company->company_name)) ?>
												</div>
												<div class="flex-grow-1 ms-3">
													<p class="lead text-truncate mb-1 text-capitalize">
														<?= $data->company->company_name; ?>
													</p>
												</div>
											</div>
										<?php else : ?>
											<div class="d-flex align-items-center">
												<div class="flex-shrink-0">
													<?= image($data->user->profile_pic, $data->user->firstname, strrev($data->user->lastname)) ?>
												</div>
												<div class="flex-grow-1 ms-3">
													<p class="lead text-truncate mb-1 text-capitalize">
														<?= $data->user->firstname . ' ' . $data->user->lastname; ?>
													</p>
												</div>
											</div>
										<?php endif; ?>
									</td>
									<td><?= $data->getAmount(true); ?></td>
									<td><?= badge(['credit' => 'success', 'debit' => 'danger'], $data->action) ?></td>
									<td><?= badge([
											'offer'       => 'success',
											'earn'        => 'success',
											'others'      => 'dark',
											'payout'      => 'info',
											'charges'     => 'danger',
											'order'       => 'warning',
											'reward'      => 'success',
											'transaction' => 'primary',
										], $data->wallet_type) ?></td>
									<td><?= badge(['success' => 'success', 'pending' => 'warning', 'failed' => 'danger'], $data->status) ?></td>
									<td><span class="text-truncate"><?= $data->getCreatedAt(true) ?></span></td>
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
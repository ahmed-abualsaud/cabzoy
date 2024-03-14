<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Accounts</h3>
				<p class="text-subtitle text-muted">For user to check accounts list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Accounts</li>
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
								<th>Account Holdername</th>
								<th>Account Number</th>
								<th>Bank Name</th>
								<th>Bank code</th>
								<th>Is Default</th>
								<th>Status</th>
								<th data-type="date" data-format="MMM DD, YYYY">User/Company</th>
								<?php if (perm('accounts', 'show, update, delete', true)) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($paymentRelations, 'array')) foreach ($paymentRelations as $paymentRelation) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td><?= $paymentRelation->account->account_holdername; ?></td>
									<td><?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($paymentRelation->account->account_number, 2) : $paymentRelation->account->account_number; ?></td>
									<td><?= $paymentRelation->account->bank_name ?></td>
									<td><?= $paymentRelation->account->account_code ?></td>
									<td><?= $paymentRelation->account->is_default ? 'Yes' : 'No'; ?></td>
									<td>
										<?= badge(['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'], $paymentRelation->account->account_status); ?>
									</td>
									<td>
										<?php if (!empty($paymentRelation->user)) : ?>
											<div class="d-flex align-items-center">
												<div class="flex-shrink-0">
													<?= image($paymentRelation->user->profile_pic, $paymentRelation->user->firstname, $paymentRelation->user->lastname) ?>
												</div>
												<div class="flex-grow-1 ms-3">
													<p class="lead text-truncate mb-1"><?= ucfirst($paymentRelation->user->firstname); ?>&nbsp;<?= ucfirst($paymentRelation->user->lastname); ?></p>
													<span class="text-truncate"><?= $paymentRelation->account->getCreatedAt(); ?></span>
												</div>
											</div>
										<?php elseif (!empty($paymentRelation->company)) : ?>
											<div class="d-flex align-items-center">
												<div class="flex-shrink-0">
													<?= image($paymentRelation->company->company_image, $paymentRelation->company->company_name, $paymentRelation->company->company_name) ?>
												</div>
												<div class="flex-grow-1 ms-3">
													<p class="lead text-truncate mb-1"><?= ucfirst($paymentRelation->account->user->firstname); ?>&nbsp;<?= ucfirst($paymentRelation->account->user->lastname); ?></p>
													<span class="text-truncate"><?= $paymentRelation->account->getCreatedAt(); ?></span>
												</div>
											</div>
										<?php endif; ?>
									</td>
									<?php if (perm('accounts', 'show, update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('accounts', 'update')) : ?>
												<a href="<?= route_to('update_account', $paymentRelation->account->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Account">
													<i data-feather="edit"></i>
												</a>
											<?php endif ?>

											<?php if (perm('accounts', 'delete')) : ?>
												<a href="<?= route_to('delete_account', $paymentRelation->account->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Account">
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
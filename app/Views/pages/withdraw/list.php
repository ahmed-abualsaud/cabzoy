<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Withdraws</h3>
				<p class="text-subtitle text-muted">For user to check withdraws list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Withdraws</li>
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
								<th>User</th>
								<th>Amount</th>
								<th>Comment</th>
								<th>Status</th>
								<?php if (perm('withdraws', 'show, update, delete', true)) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($withdraws, 'array')) foreach ($withdraws as $withdraw) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($withdraw->user->profile_pic, $withdraw->user->firstname, $withdraw->user->lastname) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<p class="lead text-truncate mb-1"><?= ucfirst($withdraw->user->firstname); ?>&nbsp;<?= ucfirst($withdraw->user->lastname); ?></p>
												<span class="text-truncate"><?= $withdraw->getCreatedAt(); ?></span>
											</div>
										</div>
									</td>
									<td><?= $withdraw->getAmount(true); ?></td>
									<td><?= $withdraw->comment; ?></td>
									<td>
										<?= badge(['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'], $withdraw->status); ?>
									</td>

									<?php if (perm('withdraws', 'show, update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('withdraws', 'update')) : ?>
												<a href="<?= route_to('update_withdraw', $withdraw->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Withdraw">
													<i data-feather="edit"></i>
												</a>
											<?php endif ?>

											<?php if (perm('withdraws', 'delete')) : ?>
												<a href="<?= route_to('delete_withdraw', $withdraw->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Withdraw">
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
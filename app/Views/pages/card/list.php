<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Cards</h3>
				<p class="text-subtitle text-muted">For user to check cards list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Cards</li>
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
								<th>Card</th>
								<th>CVV</th>
								<th>Expire Date</th>
								<th>Type</th>
								<th>Is Default</th>
								<th>Status</th>
								<th data-type="date" data-format="MMM DD, YYYY">User/Company</th>
								<?php if (perm('cards', 'show, update, delete', true)) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($paymentRelations, 'array')) foreach ($paymentRelations as $paymentRelation) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td>
										<h4 class="mb-1"><?= strtoupper($paymentRelation->card->card_holdername); ?></h4>
										<h5><?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($paymentRelation->card->card_number, 2) : $paymentRelation->card->card_number; ?></h5>
									</td>
									<td><?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($paymentRelation->card->card_cvv, 0) : $paymentRelation->card->card_cvv; ?></td>
									<td><?= $paymentRelation->card->card_month ?>/<?= $paymentRelation->card->card_year ?></td>
									<td><?= badge(['debit' => 'success', 'credit' => 'secondary'], $paymentRelation->card->card_type); ?></td>
									<td><?= $paymentRelation->card->is_default ? 'Yes' : 'No'; ?></td>
									<td>
										<?= badge(['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'], $paymentRelation->card->card_status); ?>
									</td>
									<td>
										<?php if (!empty($paymentRelation->user)) : ?>
											<div class="d-flex align-items-center">
												<div class="flex-shrink-0">
													<?= image($paymentRelation->user->profile_pic, $paymentRelation->user->firstname, $paymentRelation->user->lastname) ?>
												</div>
												<div class="flex-grow-1 ms-3">
													<p class="lead text-truncate mb-1"><?= ucfirst($paymentRelation->user->firstname); ?>&nbsp;<?= ucfirst($paymentRelation->user->lastname); ?></p>
													<span class="text-truncate"><?= $paymentRelation->card->getCreatedAt(); ?></span>
												</div>
											</div>
										<?php elseif (!empty($paymentRelation->company)) : ?>
											<div class="d-flex align-items-center">
												<div class="flex-shrink-0">
													<?= image($paymentRelation->company->company_image, $paymentRelation->company->company_name, $paymentRelation->company->company_name) ?>
												</div>
												<div class="flex-grow-1 ms-3">
													<p class="lead text-truncate mb-1"><?= ucfirst($paymentRelation->card->user->firstname); ?>&nbsp;<?= ucfirst($paymentRelation->card->user->lastname); ?></p>
													<span class="text-truncate"><?= $paymentRelation->card->getCreatedAt(); ?></span>
												</div>
											</div>
										<?php endif; ?>
									</td>
									<?php if (perm('cards', 'show, update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('cards', 'update')) : ?>
												<a href="<?= route_to('update_card', $paymentRelation->card->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Card">
													<i data-feather="edit"></i>
												</a>
											<?php endif ?>

											<?php if (perm('cards', 'delete')) : ?>
												<a href="<?= route_to('delete_card', $paymentRelation->card->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Card">
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
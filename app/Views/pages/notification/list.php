<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Notifications</h3>
				<p class="text-subtitle text-muted">For user to check notifications list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Notifications</li>
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
								<th>Notification</th>
								<th>User</th>
								<th>Status</th>
								<th data-type="date" data-format="MMM DD, YYYY">Created By</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($notifications, 'array')) foreach ($notifications as $notification) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($notification->notification_image, $notification->notification_title, strrev($notification->notification_title)) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<h4 class="text-capitalize mb-0"><?= $notification->notification_title ?></h4>
												<p class="text-capitalize"><?= $notification->notification_body; ?></p>
											</div>
										</div>
									</td>
									<td class="text-capitalize">
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($notification->user->profile_pic, $notification->user->firstname, $notification->user->lastname) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<p class="lead text-truncate mb-1">
													<?= ucfirst($notification->user->firstname); ?>&nbsp;
													<?= ucfirst($notification->user->lastname); ?>
												</p>
											</div>
										</div>
									</td>
									<td><?= badge(['seen' => 'success', 'unseen' => 'warning'], $notification->is_seen); ?></td>
									<td><?= $notification->getCreatedAt(true); ?></td>
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
<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Reports</h3>
				<p class="text-subtitle text-muted">For user to check reports list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Reports</li>
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
								<th>IP Address</th>
								<th>Email</th>
								<th>User</th>
								<th>Attempt</th>
								<th data-type="date" data-format="MMM DD, YYYY">Date</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($reports, 'array')) foreach ($reports as $data) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td><?= $data->ip_address; ?></td>
									<td><?= $data->email ?></td>
									<td>
										<?php if (isset($data->user)) : ?>
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
									<td>
										<?= badge(['success' => 'success', 'blocked' => 'danger'], $data->success == '1' ? 'success' : 'blocked'); ?>
									</td>
									<td><?= $data->date ?></td>
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
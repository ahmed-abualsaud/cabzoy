<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Company's Users</h3>
				<p class="text-subtitle text-muted">For user to check company's Users list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('corporate_dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Company's Users</li>
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
								<th>Company</th>
								<?php if (perm('companies_users', 'show, update, delete', true)) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($companies_users, 'array')) foreach ($companies_users as $data) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($data->user->profile_pic, $data->user->firstname, strrev($data->user->lastname)) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<p class="lead text-truncate mb-1 text-capitalize">
													<?= $data->user->firstname . ' ' . $data->user->lastname; ?>
												</p>
												<a href="<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? 'javascript:void(0)' : "mailto://" . $data->user->email; ?>" class="text-truncate text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Send mail to user">
													<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($data->user->email, 2, true) : $data->user->email; ?>
												</a>
											</div>
										</div>
									</td>
									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($data->company->company_image, $data->company->company_name, strrev($data->company->company_name)) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<h3 class="text-truncate mt-1 text-capitalize"><?= $data->company->company_name; ?></h3>
												<a href="<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? 'javascript:void(0)' : "mailto://" . $data->company->company_email; ?>" class="text-truncate text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Send mail to user">
													<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($data->company->company_email, 2, true) : $data->company->company_email; ?>
												</a>
											</div>
										</div>
									</td>
									<?php if (perm('companies_users', 'show, update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('companies_users', 'delete')) : ?>
												<a href="<?= route_to('delete_companies_user', $data->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Company">
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
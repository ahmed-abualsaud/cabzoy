<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Companies</h3>
				<p class="text-subtitle text-muted">For user to check companies list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Companies</li>
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
								<th>Company</th>
								<th>Phone</th>
								<th>Email</th>
								<th>Default Company</th>
								<th data-type="date" data-format="MMM DD, YYYY">Created By</th>
								<?php if (perm('companies', 'show, update, delete', true)) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($companies, 'array')) foreach ($companies as $company) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($company->company_image, $company->company_name, strrev($company->company_name)) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<h4 class="text-capitalize fs-5"><?= $company->company_name; ?></h4>
												<p class="text-capitalize fs-5"><?= $company->company_address; ?></p>
											</div>
										</div>
									</td>
									<td>
										<a class="text-success" href="<?= !in_groups('creators') && config('Settings')->enableHideSensitiveInfo ? 'javascript:void(0)' : "tel://$company->company_mobile"; ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Call the user">
											+<?= !in_groups('creators') && config('Settings')->enableHideSensitiveInfo ? encrypt($company->company_mobile, 3) : $company->company_mobile; ?>
										</a>
									</td>
									<td>
										<a class="text-secondary d-block" href="<?= !in_groups('creators') && config('Settings')->enableHideSensitiveInfo ? 'javascript:void(0)' : "mailto://$company->company_email"; ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Send mail to user">
											<?= !in_groups('creators') && config('Settings')->enableHideSensitiveInfo ? encrypt($company->company_email, 2, true) : $company->company_email; ?>
										</a>
									</td>
									<td><?= $company->is_default ? 'Yes' : 'No'; ?></td>
									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?php isset($company->user->profile_pic) && print(image($company->user->profile_pic, $company->user->firstname, $company->user->lastname)) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<p class="lead text-truncate mb-1"><?php isset($company->user->firstname) && print(ucfirst($company->user->firstname)); ?></p>
												<span class="text-truncate"><?= $company->getCreatedAt(); ?></span>
											</div>
										</div>
									</td>
									<?php if (perm('companies', 'show, update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('companies', 'update')) : ?>
												<a href="<?= route_to('update_company', $company->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Company">
													<i data-feather="edit"></i>
												</a>
											<?php endif ?>

											<?php if (perm('companies', 'delete')) : ?>
												<a href="<?= route_to('delete_company', $company->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Company">
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
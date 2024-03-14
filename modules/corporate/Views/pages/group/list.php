<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Company's Groups</h3>
				<p class="text-subtitle text-muted">For group to check company's Groups list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('corporate_dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Company's Groups</li>
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
								<th>Group</th>
								<th>Description</th>
								<th>Policy</th>
								<?php if (perm('companies_groups', 'show, update, delete', true)) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($companies_groups, 'array')) foreach ($companies_groups as $data) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td><?= $data->title; ?></td>
									<td><?= $data->detail; ?></td>
									<td class="text-capitalize"><?= $data->companies_policy->title; ?></td>
									<?php if (perm('companies_groups', 'show, update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('companies_groups', 'update')) : ?>
												<a href="<?= route_to('update_company_group', $data->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Company Group">
													<i data-feather="edit"></i>
												</a>
											<?php endif ?>

											<?php if (perm('companies_groups', 'delete')) : ?>
												<a href="<?= route_to('delete_company_group', $data->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Company Group">
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
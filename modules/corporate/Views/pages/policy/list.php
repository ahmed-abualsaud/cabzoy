<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Company's Policies</h3>
				<p class="text-subtitle text-muted">For group to check company's policies list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('corporate_dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Company's Policies</li>
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
								<th>Policy</th>
								<th>Description</th>
								<?php if (perm('companies_policies', 'show, update, delete', true)) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($companies_policies, 'array')) foreach ($companies_policies as $data) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td>
										<p class="lead text-truncate mb-1 text-capitalize">
											<?= $data->title; ?>
										</p>
									</td>
									<td>
										<?= $data->detail; ?>
									</td>
									<?php if (perm('companies_policies', 'show, update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('companies_policies', 'delete')) : ?>
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
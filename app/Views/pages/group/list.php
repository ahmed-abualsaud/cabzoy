<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Groups</h3>
				<p class="text-subtitle text-muted">For user to check groups list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Groups</li>
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
								<th>Name</th>
								<?php if (perm('groups', 'show, update, delete', true)) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($groups, 'array')) foreach ($groups as $group) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td>
										<h2><?= humanize($group->name, '-'); ?></h2>
										<p class="lead text-truncate"><?= $group->description ?></p>
									</td>
									<?php if (perm('groups', 'show, update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('groups', 'update')) : ?>
												<a href="<?= route_to($group->name); ?>" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="Show User's in this group">
													<i data-feather="eye"></i>
												</a>
											<?php endif ?>

											<?php if (perm('groups', 'update')) : ?>
												<a href="<?= route_to('update_group', $group->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Group">
													<i data-feather="edit"></i>
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
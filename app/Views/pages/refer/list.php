<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Refers</h3>
				<p class="text-subtitle text-muted">For user to check refers list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Refers</li>
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
								<th>Refer</th>
								<th>Referred User</th>
								<th data-type="date" data-format="MMM DD, YYYY">Created By</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($refers, 'array')) foreach ($refers as $refer) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td class="text-capitalize"><?= $refer->refer->user->firstname; ?></td>
									<td><?= $refer->refer->refer; ?></td>
									<td class="text-capitalize"><?= $refer->user->firstname; ?></td>
									<td><?= $refer->getCreatedAt(true); ?></td>
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
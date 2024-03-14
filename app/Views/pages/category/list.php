<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Categories</h3>
				<p class="text-subtitle text-muted">For user to check categories list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Categories</li>
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
								<th>Icon</th>
								<th>Name</th>
								<th>Type</th>
								<th>Status</th>
								<th data-type="date" data-format="MMM DD, YYYY">Created By</th>
								<?php if (perm('categories', 'show, update, delete', true)) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($categories, 'array')) foreach ($categories as $category) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td><?= image($category->category_icon, $category->category_name, strrev($category->category_name)) ?></td>
									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($category->category_image, $category->category_name, strrev($category->category_name)) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<h5 class="text-truncate"><?= $category->category_name; ?></h5>
												<p class="text-truncate text-capitalize"><?= $category->category_description; ?></p>
											</div>
										</div>
									</td>
									<td>
										<?= badge(['vehicle' => 'secondary', 'ticket' => 'warning', 'faq' => 'success', 'complaint' => 'danger', 'cancellation' => 'danger', 'review' => 'dark'], $category->category_type, false); ?>
									</td>
									<td>
										<?= badge(['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'], $category->category_status); ?>
									</td>
									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($category->user->profile_pic, $category->user->firstname, $category->user->lastname) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<p class="lead text-truncate mb-1"><?= ucfirst($category->user->firstname); ?></p>
												<span class="text-truncate"><?= $category->getCreatedAt(); ?></span>
											</div>
										</div>
									</td>
									<?php if (perm('categories', 'show, update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('categories', 'update')) : ?>
												<a href="<?= route_to('update_category', $category->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Category">
													<i data-feather="edit"></i>
												</a>
											<?php endif ?>

											<?php if (perm('categories', 'delete')) : ?>
												<a href="<?= route_to('delete_category', $category->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Category">
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
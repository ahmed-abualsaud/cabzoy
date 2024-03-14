<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Reviews</h3>
				<p class="text-subtitle text-muted">For user to check reviews list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Reviews</li>
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
								<th>Rating</th>
								<th>Review</th>
								<th>Order</th>
								<?php if (perm('reviews', 'show, update, delete', true)) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($reviews, 'array')) foreach ($reviews as $review) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($review->user->profile_pic, $review->user->firstname, $review->user->lastname) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<p class="lead text-truncate mb-1"><?= ucfirst($review->user->firstname); ?>&nbsp;<?= ucfirst($review->user->lastname); ?></p>
												<span class="text-truncate"><?= $review->getCreatedAt(); ?></span>
											</div>
										</div>
									</td>
									<td><?= $review->rating; ?></td>
									<td class="text-capitalize"><?= $review->review; ?></td>

									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($review->review_drivers[0]->user->profile_pic, $review->review_drivers[0]->user->firstname, $review->review_drivers[0]->user->lastname) ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<p class="lead text-truncate mb-1"><?= ucfirst($review->review_drivers[0]->user->firstname); ?>&nbsp;<?= ucfirst($review->review_drivers[0]->user->lastname); ?></p>
												<span class="text-truncate"><?= $review->getCreatedAt(); ?></span>
											</div>
										</div>
									</td>

									<?php if (perm('reviews', 'show, update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('reviews', 'update')) : ?>
												<!-- <a href="<?= route_to('update_review', $review->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Review">
													<i data-feather="edit"></i>
												</a> -->
											<?php endif ?>

											<?php if (perm('reviews', 'delete')) : ?>
												<a href="<?= route_to('delete_reviews', $review->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Review">
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
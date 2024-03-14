<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Add Vehicle Type Fare</h3>
				<p class="text-subtitle text-muted">For user to add Vehicle Type fare</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('fares'); ?>">Fares</a></li>
						<li class="breadcrumb-item active" aria-current="page">Add Vehicle Type Fare</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('fare') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="card">
				<div class="card-body p-5">
					<div class="row mb-3">
						<div class="col-lg-12">
							<?php $category = [];
							$category = array_map(static function ($data) use ($category) {
								return $category[$data->id] = ucwords($data->category_name);
							}, $categories) ?>
							<?= select_box('category_id', 'Vehicle Categories', $category, $validation); ?>
						</div>

						<div class="col-lg-6">
							<div class="form-group mb-4">
								<label class="mb-2" for="fare">Per <?= config('Settings')->defaultLengthUnit; ?>'s Fare</label>
								<input type="number" min="0" step="0.01" id="fare" name="fare" class="form-control form-control-lg <?php $validation->hasError('fare') && print 'is-invalid'; ?>" placeholder="Fare" value="<?= old('fare'); ?>">
								<div class="invalid-feedback"><?= $validation->getError('fare') ?></div>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group mb-4">
								<label class="mb-2" for="min_fare">Minimum Fare</label>
								<input type="number" min="0" step="0.01" id="min_fare" name="min_fare" class="form-control form-control-lg <?php $validation->hasError('min_fare') && print 'is-invalid'; ?>" placeholder="Minimum Fare" value="<?= old('min_fare'); ?>">
								<div class="invalid-feedback"><?= $validation->getError('min_fare') ?></div>
							</div>
						</div>
					</div>

					<div class="mt-3">
						<button type="submit" class="btn btn-lg btn-success">Add Vehicle Type Fare</button>
					</div>

				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
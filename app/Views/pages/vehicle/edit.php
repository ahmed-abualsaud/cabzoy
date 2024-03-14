<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Update Vehicle</h3>
				<p class="text-subtitle text-muted">For user to update vehicle</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('vehicles'); ?>">Vehicles</a></li>
						<li class="breadcrumb-item active" aria-current="page">Update Vehicle</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('vehicle') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="row">
				<div class="col-lg-8">
					<div class="card">
						<div class="card-body p-5">
							<div class="row mb-3">
								<div class="col-lg-6">
									<?= select_box('status', 'Vehicle Status', [
										'approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected',
									], $validation, $vehicle->vehicle_status); ?>
								</div>

								<div class="col-lg-6">
									<div class="form-group mb-4">
										<label class="mb-2" for="vehicle_brand">Vehicle Brand</label>
										<input type="text" id="vehicle_brand" name="vehicle_brand" class="form-control form-control-lg <?php $validation->hasError('vehicle_brand') && print 'is-invalid'; ?>" placeholder="Vehicle Brand" value="<?= old('vehicle_brand') ?? esc($vehicle->vehicle_brand); ?>">
										<div class="invalid-feedback"><?= $validation->getError('vehicle_brand') ?></div>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group mb-4">
										<label class="mb-2" for="vehicle_modal">Vehicle Modal</label>
										<input type="text" id="vehicle_modal" name="vehicle_modal" class="form-control form-control-lg <?php $validation->hasError('vehicle_modal') && print 'is-invalid'; ?>" placeholder="Vehicle Modal" value="<?= old('vehicle_modal') ?? esc($vehicle->vehicle_modal); ?>">
										<div class="invalid-feedback"><?= $validation->getError('vehicle_modal') ?></div>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group mb-4">
										<label class="mb-2" for="vehicle_color">Vehicle Color</label>
										<input type="text" id="vehicle_color" name="vehicle_color" class="form-control form-control-lg <?php $validation->hasError('vehicle_color') && print 'is-invalid'; ?>" placeholder="Vehicle Color" value="<?= old('vehicle_color') ?? esc($vehicle->vehicle_color); ?>">
										<div class="invalid-feedback"><?= $validation->getError('vehicle_color') ?></div>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group mb-4">
										<label class="mb-2" for="vehicle_number">Vehicle Number</label>
										<input type="text" id="vehicle_number" name="vehicle_number" class="form-control form-control-lg <?php $validation->hasError('vehicle_number') && print 'is-invalid'; ?>" placeholder="Vehicle Number" value="<?= old('vehicle_number') ?? esc($vehicle->vehicle_number); ?>">
										<div class="invalid-feedback"><?= $validation->getError('vehicle_number') ?></div>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group mb-4">
										<label class="mb-2" for="vehicle_seats">Vehicle Seats</label>
										<input type="text" id="vehicle_seats" name="vehicle_seats" class="form-control form-control-lg <?php $validation->hasError('vehicle_seats') && print 'is-invalid'; ?>" placeholder="Vehicle Seats" value="<?= old('vehicle_seats') ?? esc($vehicle->vehicle_seats); ?>">
										<div class="invalid-feedback"><?= $validation->getError('vehicle_seats') ?></div>
									</div>
								</div>

							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Update Vehicle</button>
							</div>

						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="card">
						<div class="card-body p-5">
							<div class="form-group mb-4">
								<label for="image">Vehicle Image</label><br>
								<small>FileType: png, jpg, jpeg below 2MB</small>
								<?php if (null !== $vehicle->vehicle_image) : ?>
									<input type="hidden" class="prev-image-upload" value="<?= esc($vehicle->vehicle_image); ?>">
								<?php endif ?>
								<input type="file" accept="image/png, image/jpg, image/jpeg" class="form-control image-upload mt-3" name="image" id="image">
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Update Promo Code</h3>
				<p class="text-subtitle text-muted">For user to update promo code</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('promos'); ?>">Promo Codes</a></li>
						<li class="breadcrumb-item active" aria-current="page">Update Promo Code</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('promo') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body p-5">
							<div class="row mb-3">
								<div class="col-lg-6">
									<div class="form-group mb-4">
										<label class="mb-2" for="promo_code">Promo Code</label>
										<input type="text" id="promo_code" name="promo_code" class="form-control form-control-lg <?php $validation->hasError('promo_code') && print 'is-invalid'; ?>" placeholder="Promo Code" value="<?= old('promo_code') ?? esc($promo->promo_code); ?>">
										<div class="invalid-feedback"><?= $validation->getError('promo_code') ?></div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group mb-4">
										<label class="mb-2" for="promo_discount">Promo Code Discount</label>
										<input type="number" id="promo_discount" name="promo_discount" class="form-control form-control-lg <?php $validation->hasError('promo_discount') && print 'is-invalid'; ?>" placeholder="Promo Code Discount" value="<?= old('promo_discount') ?? esc($promo->promo_discount) ?? '0'; ?>">
										<div class="invalid-feedback"><?= $validation->getError('promo_discount') ?></div>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group mb-4">
										<label class="mb-2" for="promo_min_amount">Promo Code Minimum Uses Amount</label>
										<input type="number" id="promo_min_amount" name="promo_min_amount" class="form-control form-control-lg <?php $validation->hasError('promo_min_amount') && print 'is-invalid'; ?>" placeholder="Promo Code Minimum Uses Amount" value="<?= old('promo_min_amount') ?? esc($promo->promo_min_amount) ?? '0'; ?>">
										<div class="invalid-feedback"><?= $validation->getError('promo_min_amount') ?></div>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group mb-4">
										<label class="mb-2" for="promo_max_amount">Promo Code Maximum Uses Amount</label>
										<input type="number" id="promo_max_amount" name="promo_max_amount" class="form-control form-control-lg <?php $validation->hasError('promo_max_amount') && print 'is-invalid'; ?>" placeholder="Promo Code Maximum Uses Amount" value="<?= old('promo_max_amount') ?? esc($promo->promo_max_amount) ?? '0'; ?>">
										<div class="invalid-feedback"><?= $validation->getError('promo_max_amount') ?></div>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group mb-4">
										<label class="mb-2" for="promo_count">Promo Code Uses Count</label>
										<input type="number" id="promo_count" name="promo_count" class="form-control form-control-lg <?php $validation->hasError('promo_count') && print 'is-invalid'; ?>" placeholder="Promo Code Uses Count" value="<?= old('promo_count') ?? esc($promo->promo_count) ?? '0'; ?>">
										<div class="invalid-feedback"><?= $validation->getError('promo_count') ?></div>
									</div>
								</div>
								<div class="col-lg-6">
									<?= select_box('promo_discount_type', 'Promo Discount Type', ['percentage' => 'Percentage', 'flat' => 'Flat'], $validation, $promo->promo_discount_type); ?>
								</div>
								<div class="col-lg-6">
									<?= select_box('promo_status', 'Status', [
										'approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected',
									], $validation, $promo->promo_status); ?>
								</div>

							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Update Promo Code</button>
							</div>

						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
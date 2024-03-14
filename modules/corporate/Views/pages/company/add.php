<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Add Company</h3>
				<p class="text-subtitle text-muted">For user to add company</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('corporate_dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('companies'); ?>">Companies</a></li>
						<li class="breadcrumb-item active" aria-current="page">Add Company</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('company') ?>" id="saveLocation" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="row">
				<div class="col-lg-8">
					<div class="card">
						<div class="card-body p-5">
							<div class="row mb-3">
								<div class="col">
									<div class="form-group mb-4">
										<label class="mb-2" for="name">Company Name</label>
										<input type="text" id="name" name="name" class="form-control form-control-lg <?php $validation->hasError('name') && print 'is-invalid'; ?>" placeholder="Company Name" value="<?= old('name'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('name') ?></div>
									</div>
								</div>

								<?php if (!is($defaultCompany, 'object')) : ?>
									<div class="col-lg-6">
										<?= select_box('is_default', 'Is Default Company', ['0' => 'No', '1' => 'Yes'], $validation); ?>
									</div>
								<?php endif; ?>

								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group mb-4">
												<label class="mb-2" for="email">Company Email</label>
												<input type="email" id="email" name="email" class="form-control form-control-lg <?php $validation->hasError('email') && print 'is-invalid'; ?>" placeholder="Company Email" value="<?= old('email'); ?>">
												<div class="invalid-feedback"><?= $validation->getError('email') ?></div>
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group mb-4">
												<label class="mb-2" for="mobile">Company Mobile</label>
												<input type="tel" id="mobile" name="mobile" class="form-control form-control-lg <?php $validation->hasError('mobile') && print 'is-invalid'; ?>" placeholder="Company Mobile" value="<?= old('mobile'); ?>">
												<div class="invalid-feedback"><?= $validation->getError('mobile') ?></div>
											</div>
										</div>
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="address">Company Address</label>
										<textarea id="address" name="address" class="form-control form-control-lg <?php $validation->hasError('address') && print 'is-invalid'; ?>" placeholder="Company Address"><?= old('address'); ?></textarea>
										<div class="invalid-feedback"><?= $validation->getError('address') ?></div>
									</div>
								</div>

								<div class="col-lg-6">
									<?= select_box('status', 'Company Status', [
										'approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected',
									], $validation); ?>
								</div>

								<div class="col-lg-6">
									<?php $user = [];
									if (is($users, 'array')) $user = array_map(static function ($data) use ($user) {
										return $user[$data->id] = ucwords($data->firstname . ' ' . $data->lastname);
									}, $users); ?>
									<?= select_box('user_id', 'Users', $user, $validation); ?>
								</div>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Add Company</button>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="card">
						<div class="card-body p-5">
							<div class="form-group mb-4">
								<label for="image">Image</label><br>
								<small>FileType: png, jpg, jpeg below 2MB</small>
								<input type="file" accept="image/png, image/jpg, image/jpeg" class="form-control image-upload mt-3" name="image" id="image">
							</div>
						</div>
					</div>

					<div class="card">
						<div class="card-body p-5">
							<div class="form-group mb-4">
								<label for="doc">Document</label><br>
								<small>FileType: png, jpg, jpeg, pdf below 2MB</small>
								<input type="file" accept="image/png, image/jpg, image/jpeg, .pdf" class="form-control image-upload mt-3" name="doc" id="doc">
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
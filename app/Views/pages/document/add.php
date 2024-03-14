<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Add Document</h3>
				<p class="text-subtitle text-muted">For user to add document</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('documents'); ?>">Documents</a></li>
						<li class="breadcrumb-item active" aria-current="page">Add Document</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('document') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="row">
				<div class="col-lg-8">
					<div class="card">
						<div class="card-body p-5">
							<div class="row mb-3">
								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="document_title">Document Title</label>
										<input type="text" id="document_title" name="document_title" class="form-control form-control-lg <?php $validation->hasError('document_title') && print 'is-invalid'; ?>" placeholder="Document Title" value="<?= old('document_title'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('document_title') ?></div>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="document_number">Document Number</label>
										<input type="tel" id="document_number" name="document_number" class="form-control form-control-lg <?php $validation->hasError('document_number') && print 'is-invalid'; ?>" inputmode="numeric" pattern="[0-9\s]{5,30}" placeholder="xxxx xxxx xxxx xxxx" value="<?= old('document_number'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('document_number') ?></div>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="document_comment">Document Comment</label>
										<textarea id="document_comment" name="document_comment" class="form-control form-control-lg <?php $validation->hasError('document_comment') && print 'is-invalid'; ?>" placeholder="Document Comment"><?= old('document_comment'); ?></textarea>
										<div class="invalid-feedback"><?= $validation->getError('document_comment') ?></div>
									</div>
								</div>

								<div class="col-lg-6">
									<?php $user = [];
									if (is($users, 'array')) $user = array_map(static function ($data) use ($user) {
										return $user[$data->id] = ucwords($data->firstname . ' ' . $data->lastname);
									}, $users); ?>
									<?= select_box('user_id', 'User', $user, $validation); ?>
								</div>

								<div class="col-lg-6">
									<?= select_box('status', 'Document Status', [
										'approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected',
									], $validation); ?>
								</div>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Add Document</button>
							</div>

						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="card">
						<div class="card-body p-5">
							<div class="form-group mb-4">
								<label for="image">Front Image</label><br>
								<small>FileType: png, jpg, jpeg below 2MB</small>
								<input type="file" accept="image/png, image/jpg, image/jpeg" class="form-control image-upload mt-3" name="front_image" id="front_image">
							</div>
							<div class="form-group mb-4">
								<label for="image">Back Image</label><br>
								<small>FileType: png, jpg, jpeg below 2MB</small>
								<input type="file" accept="image/png, image/jpg, image/jpeg" class="form-control image-upload mt-3" name="back_image" id="back_image">
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
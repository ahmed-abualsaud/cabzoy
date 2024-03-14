<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Add Category</h3>
				<p class="text-subtitle text-muted">For user to add category</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('categories'); ?>">Categories</a></li>
						<li class="breadcrumb-item active" aria-current="page">Add Category</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('category') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="row">
				<div class="col-lg-8">
					<div class="card">
						<div class="card-body p-5">
							<div class="row mb-3">
								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="name">Category Name</label>
										<input type="text" id="name" name="name" class="form-control form-control-lg <?php $validation->hasError('name') && print 'is-invalid'; ?>" placeholder="Category Name" value="<?= old('name'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('name') ?></div>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="description">Category Description</label>
										<textarea type="text" id="description" name="description" class="form-control form-control-lg <?php $validation->hasError('description') && print 'is-invalid'; ?>" placeholder="Category Description" value=""><?= old('description'); ?></textarea>
										<div class="invalid-feedback"><?= $validation->getError('description') ?></div>
									</div>
								</div>

								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<?= select_box('status', 'Category Status', [
												'approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected',
											], $validation); ?>
										</div>

										<div class="col-lg-6">
											<?= select_box('type', 'Category Type', [
												'vehicle' => 'Vehicle Category', 'complaint' => 'Complaint Types', 'faq' => 'F&Q', 'ticket' => 'Ticket Category', 'cancellation' => 'Cancellation Reasons', 'review' => 'Review Types'
											], $validation); ?>
										</div>
									</div>
								</div>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Add Category</button>
							</div>

						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="card">
						<div class="card-body p-5">
							<div class="form-group mb-4">
								<label for="image">Category Image</label><br>
								<small>FileType: png, jpg, jpeg below 2MB</small>
								<input type="file" accept="image/png, image/jpg, image/jpeg" class="form-control image-upload mt-3" name="image" id="image">
							</div>
						</div>
					</div>

					<div class="card">
						<div class="card-body p-5">
							<div class="form-group mb-4">
								<label for="icon">Category Icon</label><br>
								<small>FileType: png, jpg, jpeg below 2MB</small>
								<input type="file" accept="image/png, image/jpg, image/jpeg" class="form-control image-upload mt-3" name="icon" id="icon">
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
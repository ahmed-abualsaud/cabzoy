<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Update Category</h3>
				<p class="text-subtitle text-muted">For user to update category</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('categories'); ?>">Categories</a></li>
						<li class="breadcrumb-item active" aria-current="page">Update Category</li>
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
										<input type="text" id="name" name="name" class="form-control form-control-lg <?php $validation->hasError('name') && print 'is-invalid'; ?>" placeholder="Category Name" value="<?= old('name') ?? esc($category->category_name); ?>">
										<div class="invalid-feedback"><?= $validation->getError('name') ?></div>
									</div>
								</div>

								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<?= select_box('status', 'Category Status', [
												'approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected',
											], $validation, $category->category_status); ?>
										</div>

										<div class="col-lg-6">
											<?= select_box('type', 'Category Type', [
												'vehicle' => 'Vehicle', 'complaint' => 'Complaint', 'faq' => 'F&Q',
											], $validation, $category->category_type); ?>
										</div>
									</div>
								</div>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Update Category</button>
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
								<?php if (null !== $category->category_image) : ?>
									<input type="hidden" class="prev-image-upload" value="<?= esc($category->category_image); ?>">
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
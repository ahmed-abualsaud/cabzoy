<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Add Setting</h3>
				<p class="text-subtitle text-muted">For user to add setting</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('orders'); ?>">Settings</a></li>
						<li class="breadcrumb-item active" aria-current="page">Add Setting</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-body p-5">
						<form action="<?= route_to('order') ?>" method="post" enctype="multipart/form-data">
							<?= csrf_field(); ?>
							<div class="row mb-3">
								<div class="col-lg-8">
									<div class="form-group mb-4">
										<label for="name">Name</label>
										<input type="text" id="name" name="name" class="form-control form-control-lg <?php $validation->hasError('name') && print 'is-invalid'; ?>" value="<?= old('name'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('name') ?></div>
									</div>
								</div>
								<div class="col-lg-4">
									<?= select_box('datatype', 'Data Type', [
										'string' => 'String', 'int' => 'Int', 'uri' => 'URI', 'image' => 'Image', 'bool' => 'Boolean',
									], $validation); ?>
								</div>
								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label for="summary">Summary</label>
										<input type="text" id="summary" name="summary" class="form-control form-control-lg <?php $validation->hasError('summary') && print 'is-invalid'; ?>" value="<?= old('summary'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('summary') ?></div>
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label for="image">Content</label>
										<small>FileType: png, jpg, jpeg below 2MB</small>

										<input type="file" accept="image/png, image/jpg, image/jpeg" class="form-control image-upload mt-3" name="image" id="image">

										<input type="text" class="form-control mt-5 form-control-lg <?php $validation->hasError('content') && print 'is-invalid'; ?>" name="content" value="<?= old('content'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('content') ?></div>
									</div>
								</div>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Add Setting</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?= $this->endSection(); ?>
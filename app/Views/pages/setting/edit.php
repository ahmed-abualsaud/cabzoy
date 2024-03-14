<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Update Setting</h3>
				<p class="text-subtitle text-muted">For user to update setting</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('orders'); ?>">Settings</a></li>
						<li class="breadcrumb-item active" aria-current="page">Update Setting</li>
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
								<?php if (perm('settings', 'add')) : ?>
									<div class="col-lg-8">
										<div class="form-group mb-4">
											<label for="name">Name</label>
											<input type="text" readonly id="name" name="name" class="form-control form-control-lg <?php $validation->hasError('name') && print 'is-invalid'; ?>" value="<?= old('name') ?? esc($setting->name); ?>">
											<div class="invalid-feedback"><?= $validation->getError('name') ?></div>
										</div>
									</div>
									<div class="col-lg-4">
										<?= select_box('datatype', 'Data Type', [
											'string' => 'String', 'int' => 'Int', 'uri' => 'URI', 'image' => 'Image', 'bool' => 'Boolean', 'color' => 'Color',
										], $validation, $setting->datatype, true); ?>
									</div>
									<div class="col-lg-12">
										<div class="form-group mb-4">
											<label for="summary">Summary</label>
											<input type="text" readonly id="summary" name="summary" class="form-control form-control-lg <?php $validation->hasError('summary') && print 'is-invalid'; ?>" value="<?= old('summary') ?? esc($setting->summary); ?>">
											<div class="invalid-feedback"><?= $validation->getError('summary') ?></div>
										</div>
									</div>
								<?php else : ?>
									<input type="hidden" name="name" value="<?= esc($setting->name); ?>">
									<input type="hidden" name="datatype" value="<?= esc($setting->datatype); ?>">
									<input type="hidden" name="summary" value="<?= esc($setting->summary); ?>">
								<?php endif ?>

								<div class="col-lg-12">
									<div class="form-group mb-4">
										<?php if ($setting->datatype !== 'color' && $setting->datatype !== 'bool') : ?>
											<label for="image">
												<?= perm('settings', 'add') ? 'Content' : humanize($setting->name); ?>
											</label>
										<?php endif; ?>
										<?php if ($setting->datatype === 'image') : ?>
											<small>FileType: png, jpg, jpeg below 2MB</small>
											<?php if (null !== $setting->content) : ?>
												<input type="hidden" class="prev-image-upload" value="<?= esc($setting->content); ?>">
											<?php endif ?>
											<input type="file" accept="image/png, image/jpg, image/jpeg" class="form-control image-upload mt-3" name="image" id="image">
										<?php elseif ($setting->datatype === 'int') : ?>
											<input type="number" class="form-control form-control-lg <?php $validation->hasError('content') && print 'is-invalid'; ?>" name="content" id="content" min="0" value="<?= old('content') ?? esc($setting->content); ?>">
										<?php elseif ($setting->datatype === 'uri') : ?>
											<input type="text" class="form-control form-control-lg <?php $validation->hasError('content') && print 'is-invalid'; ?>" name="content" id="content" min="0" value="<?= old('content') ?? esc($setting->content); ?>">
										<?php elseif ($setting->datatype === 'bool') : ?>
											<?= select_box('content', humanize($setting->name), ['0' => 'Disable', '1' => 'Enable'], $validation, $setting->content); ?>
										<?php elseif ($setting->datatype === 'color') : ?>
											<?= select_box('content', humanize($setting->name), [
												'gray' => 'Gray', 'red' => 'Red', 'orange' => 'Orange', 'yellow' => 'Yellow', 'green' => 'Green', 'aqua' => 'Aqua', 'blue' => 'Blue', 'purple' => 'Purple', 'pink' => 'Pink',
											], $validation, $setting->content); ?>
										<?php else : ?>
											<input type="text" class="form-control form-control-lg <?php $validation->hasError('content') && print 'is-invalid'; ?>" name="content" value="<?= old('content') ?? esc($setting->content); ?>">
										<?php endif ?>
										<div class="invalid-feedback"><?= $validation->getError('content') ?></div>
									</div>
								</div>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Update Setting</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?= $this->endSection(); ?>
<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Add Group</h3>
				<p class="text-subtitle text-muted">For user to add group</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('groups'); ?>">Groups</a></li>
						<li class="breadcrumb-item active" aria-current="page">Add Group</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('group') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body p-5">
							<div class="row mb-3">
								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="name">Group Name</label>
										<input type="text" id="name" name="name" class="form-control form-control-lg <?php $validation->hasError('name') && print 'is-invalid'; ?>" placeholder="Group Name" value="<?= old('name'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('name') ?></div>
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="description">Group Description</label>
										<input type="text" id="description" name="description" class="form-control form-control-lg <?php $validation->hasError('description') && print 'is-invalid'; ?>" placeholder="Group Description" value="<?= old('description'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('description') ?></div>
									</div>
								</div>

								<?php if (is($permissions, 'array')) : ?>
									<h3>Permissions</h3>
									<?php foreach ($permissions as $permission) : ?>
										<div class="col-lg-3">
											<div class="form-group mt-4">
												<div class="form-check">
													<div class="custom-control custom-checkbox">
														<input type="checkbox" class="form-check-input form-check-info form-check-glow" name="permissions[]" id="<?= $permission['name']; ?>" value="<?= $permission['name']; ?>" <?php old($permission['name']) === $permission['name'] && print 'checked'; ?>>
														<label class="form-check-label h5" for="<?= $permission['name']; ?>">
															<?= humanize($permission['name'], '.'); ?>
														</label>
													</div>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Add Group</button>
							</div>

						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
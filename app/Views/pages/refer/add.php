<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Add Notification</h3>
				<p class="text-subtitle text-muted">For user to add notification</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('categories'); ?>">Categories</a></li>
						<li class="breadcrumb-item active" aria-current="page">Add Notification</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('notification') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body p-5">
							<div class="row mb-3">
								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="notification_title">Notification Title</label>
										<input type="text" id="notification_title" name="notification_title" class="form-control form-control-lg <?php $validation->hasError('notification_title') && print 'is-invalid'; ?>" placeholder="Notification Title" value="<?= old('notification_title'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('notification_title') ?></div>
									</div>
								</div>

								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="notification_body">Notification Body</label>
										<input type="text" id="notification_body" name="notification_body" class="form-control form-control-lg <?php $validation->hasError('notification_body') && print 'is-invalid'; ?>" placeholder="Notification Body" value="<?= old('notification_body'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('notification_body') ?></div>
									</div>
								</div>

								<div class="col-lg-12">
									<?php $user = [];
									$user = array_map(static function ($data) use ($user) {
										return $user[$data->id] = ucwords($data->firstname . ' ' . $data->lastname);
									}, $users); ?>
									<?= select_box('user_id', 'User', $user, $validation); ?>
								</div>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Add Notification</button>
							</div>

						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
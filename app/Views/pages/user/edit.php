<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<?php $roleName = singular(humanize($role, '-')); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Update <?= $roleName; ?></h3>
				<p class="text-subtitle text-muted">For <?= $roleName; ?> to check they list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item">
							<a href="<?= route_to($role); ?>"><?= humanize($role, '-'); ?></a>
						</li>
						<li class="breadcrumb-item active" aria-current="page">Update <?= $roleName; ?></li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to(singular($role)) ?>" method="post" enctype="multipart/form-data">
			<div class="row">
				<div class="col-lg-8">
					<div class="card">
						<div class="card-body p-5">
							<?= csrf_field(); ?>
							<div class="row">
								<div class="col-lg-6 mb-2">
									<div class="form-group">
										<label class="mb-2" for="firstname">First Name</label>
										<input type="text" id="firstname" name="firstname" class="form-control form-control-lg <?php $validation->hasError('firstname') && print 'is-invalid'; ?>" placeholder="First Name" value="<?= old('firstname') ?? $user->firstname; ?>">
										<div class="invalid-feedback"><?= $validation->getError('firstname') ?></div>
									</div>
								</div>
								<div class="col-lg-6 mb-2">
									<div class="form-group">
										<label class="mb-2" for="lastname">Last Name</label>
										<input type="text" id="lastname" name="lastname" class="form-control form-control-lg <?php $validation->hasError('lastname') && print 'is-invalid'; ?>" placeholder="Last Name" value="<?= old('lastname') ?? $user->lastname; ?>">
										<div class="invalid-feedback"><?= $validation->getError('lastname') ?></div>
									</div>
								</div>
								<div class="col-lg-6 mb-2">
									<div class="form-group">
										<label class="mb-2" for="username">Username</label>
										<input type="text" id="username" name="username" class="form-control form-control-lg <?php $validation->hasError('username') && print 'is-invalid'; ?>" placeholder="Username" value="<?= old('username') ?? !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($user->username, 2) : $user->username; ?>">
										<div class="invalid-feedback"><?= $validation->getError('username') ?></div>
									</div>
								</div>
								<div class="col-lg-6 mb-2">
									<div class="form-group">
										<label class="mb-2" for="email">Email</label>
										<input type="email" id="email" name="email" class="form-control form-control-lg <?php $validation->hasError('email') && print 'is-invalid'; ?>" placeholder="Email" value="<?= old('email') ?? !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($user->email, 2, true) : $user->email; ?>">
										<div class="invalid-feedback"><?= $validation->getError('email') ?></div>
									</div>
								</div>
								<div class="col-lg-6 mb-2">
									<div class="form-group">
										<label class="mb-2" for="phone">Phone Number</label>
										<input type="tel" id="phone" name="phone" class="form-control form-control-lg <?php $validation->hasError('phone') && print 'is-invalid'; ?>" placeholder="Phone Number" value="<?= old('phone') ?? !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($user->phone, 3) : $user->phone; ?>">
										<div class="invalid-feedback"><?= $validation->getError('phone') ?></div>
									</div>
								</div>
								<div class="col-lg-6 mb-2">
									<div class="form-group mb-3">
										<label class="mb-2" for="password">Password</label>
										<input type="text" id="password" name="password" class="form-control form-control-lg <?php $validation->hasError('password') && print 'is-invalid'; ?>" placeholder="Password" value="<?= old('password'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('password') ?></div>
									</div>
								</div>
								<div class="col-lg-6 mb-2">
									<?php $status = [0 => 'Not Active', 1 => 'Active'];
									if (!config('Settings')->enableAutoVerifyUser) $status[2] = 'Not Verified'; ?>
									<?= select_box('active', 'Is User Active', $status, $validation, $user->active); ?>
								</div>
								<div class="col-lg-6 mb-2">
									<?= select_box('status', 'User Status', ['0' => 'Not Banned', 'banned' => 'Banned'], $validation, empty($user->status) ? '0' : $user->status); ?>
								</div>
								<div class="col-lg-12 mb-2">
									<div class="form-group mb-3">
										<label class="mb-2" for="status_message">Status Message</label>
										<textarea type="text" id="status_message" name="status_message" class="form-control form-control-lg <?php $validation->hasError('status_message') && print 'is-invalid'; ?>" placeholder="Status Message" value=""><?= old('status_message') ?? esc($user->status_message); ?></textarea>
										<div class="invalid-feedback"><?= $validation->getError('status_message') ?></div>
									</div>
								</div>
								<div class="col-lg-6 mb-2">
									<div class="mt-3">
										<button type="submit" class="btn btn-lg btn-success">Update <?= $roleName; ?></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="card">
						<div class="card-body p-5">
							<div class="form-group mb-4">
								<label for="profile_pic">Profile Pic</label><br>
								<small>FileType: png, jpg, jpeg below 2MB</small>
								<?php if (null !== $user->profile_pic) : ?>
									<input type="hidden" class="prev-image-upload" value="<?= esc($user->profile_pic); ?>">
								<?php endif ?>
								<input type="file" accept="image/png, image/jpg, image/jpeg" class="form-control image-upload" name="profile_pic" id="profile_pic">
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
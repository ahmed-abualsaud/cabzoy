<?= $this->extend('layouts/default'); ?>

<?= $this->section('styles'); ?>
<link rel="stylesheet" href="<?= site_url("assets/css/pages/auth.css"); ?>">
<?= $this->endSection() ?>

<?= $this->section('main'); ?>
<div id="auth">
	<div class="row h-100">
		<div class="col-lg-5 col-12">
			<div id="auth-left">
				<div class="auth-logo">
					<a href="<?= route_to('dashboard') ?>">
						<h2 class="display-6 font-bold">
							<?= config('Settings')->siteName ?? env('app.name', 'Fab IT Hub'); ?>
						</h2>
					</a>
				</div>
				<h1 class="auth-title"><?= lang('Auth.forgotPassword'); ?></h1>
				<p class="auth-subtitle mb-5">Input your email and we will send you reset password link.</p>

				<form action="<?= route_to('forgot'); ?>" method="POST">

					<?= csrf_field() ?>

					<div class="form-group position-relative has-icon-left mb-4">
						<input type="text" class="form-control form-control-xl <?php ($validation->hasError('email') || session()->has('errors')) && print 'is-invalid'; ?>" placeholder="<?= lang('Auth.email'); ?>" name="email" value="<?= old('email'); ?>">
						<div class="form-control-icon"><i data-feather="mail"></i></div>
						<div class="invalid-feedback"><?= $validation->getError('email') ?></div>
					</div>

					<button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Send</button>
				</form>
				<div class="text-center mt-5 text-lg fs-4">
					<?php if ($config->allowRegistration) : ?>
						<p class="text-gray-600">Remember your account?
							<a href="<?= route_to('login') ?>" class="font-bold">Log in</a>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="col-lg-7 d-none d-lg-block">
			<div id="auth-right"></div>
		</div>
	</div>
</div>
<?= $this->endSection(); ?>
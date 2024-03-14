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
					<a href="<?= route_to('corporate_dashboard') ?>">
						<h2 class="display-6 font-bold">
							<?= config('Settings')->siteName ?? env('app.name', 'Fab IT Hub'); ?>
						</h2>
					</a>
				</div>
				<h1 class="auth-title">Company Log in</h1>
				<p class="auth-subtitle mb-5">Log in with your data that you entered during registration.</p>

				<?php if (session('errors') !== null && is_array(session('errors')) && isset(session('errors')['auth'])) : ?>
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<h4 class="alert-heading">Something not right</h4>
						<p><?= session('errors')['auth']; ?></p>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				<?php endif ?>

				<form action="<?= route_to('corporate_login'); ?>" method="POST">

					<?= csrf_field() ?>

					<div class="form-group position-relative has-icon-left mb-4">
						<input type="text" class="form-control form-control-xl <?php $validation->hasError('login') && print 'is-invalid'; ?>" placeholder="<?= lang('Auth.emailOrUsername'); ?>" name="login" value="<?= old('login'); ?>">
						<div class="form-control-icon"><i data-feather="user"></i></div>
						<div class="invalid-feedback"><?= $validation->getError('login') ?></div>
					</div>

					<div class="form-group position-relative has-icon-left mb-4">
						<input type="password" name="password" class="form-control form-control-xl <?php $validation->hasError('password') && print 'is-invalid'; ?>" placeholder="<?= lang('Auth.password') ?>">
						<div class="form-control-icon"><i data-feather="shield"></i></div>
						<div class="invalid-feedback"><?= $validation->getError('password') ?></div>
					</div>

					<?php if ($config->allowRemembering) : ?>
						<div class="form-check form-check-lg d-flex align-items-end">
							<input name="remember" class="form-check-glow form-check-input form-check-primary me-2" type="checkbox" value="" id="rememberMe" <?php old('remember') && print 'checked' ?>>
							<label class="form-check-label text-gray-600" for="rememberMe">
								<?= lang('Auth.rememberMe') ?>
							</label>
						</div>
					<?php endif; ?>

					<button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">
						<?= lang('Auth.loginAction'); ?>
					</button>
				</form>
				<div class="text-center mt-5 text-lg fs-4">
					<p class="text-gray-600">Don't have an account?
						<a href="<?= route_to('corporate_register') ?>" class="font-bold">Create one</a>
					</p>
				</div>
			</div>
		</div>
		<div class="col-lg-7 d-none d-lg-block">
			<div id="auth-right" style="background-image: url(https://picsum.photos/1080)"></div>
		</div>
	</div>
</div>
<?= $this->endSection(); ?>
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
				<h1 class="auth-title"><?= lang('Auth.resetYourPassword') ?></h1>
				<p class="auth-subtitle mb-5">Log in with your data that you entered during registration.</p>

				<form action="<?= route_to('reset-password'); ?>" method="POST">

					<?= csrf_field() ?>

					<div class="form-group position-relative has-icon-left mb-4">
						<input type="text" class="form-control form-control-xl <?php ($validation->hasError('token') || session()->has('errors')) && print 'is-invalid'; ?>" placeholder="<?= lang('Auth.token'); ?>" name="token" value="<?= old('token', $token ?? ''); ?>">
						<div class="form-control-icon"><i data-feather="shield"></i></div>
						<div class="invalid-feedback"><?= $validation->getError('token') ?></div>
					</div>

					<div class="form-group position-relative has-icon-left mb-4">
						<input type="email" class="form-control form-control-xl <?php ($validation->hasError('email') || session()->has('errors')) && print 'is-invalid'; ?>" placeholder="<?= lang('Auth.email'); ?>" name="email" value="<?= old('email'); ?>">
						<div class="form-control-icon"><i data-feather="mail"></i></div>
						<div class="invalid-feedback"><?= $validation->getError('email') ?></div>
					</div>

					<div class="form-group position-relative has-icon-left mb-4">
						<input type="password" name="password" class="form-control form-control-xl <?php ($validation->hasError('password') || session()->has('errors')) && print 'is-invalid'; ?>" placeholder="<?= lang('Auth.password') ?>">
						<div class="form-control-icon"><i data-feather="lock"></i></div>
						<div class="invalid-feedback"><?= $validation->getError('password') ?></div>
					</div>

					<div class="form-group position-relative has-icon-left mb-4">
						<input type="password" name="pass_confirm" class="form-control form-control-xl <?php ($validation->hasError('pass_confirm') || session()->has('errors')) && print 'is-invalid'; ?>" placeholder="<?= lang('Auth.password') ?>">
						<div class="form-control-icon"><i data-feather="lock"></i></div>
						<div class="invalid-feedback"><?= $validation->getError('pass_confirm') ?></div>
					</div>

					<button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5"><?= lang('Auth.resetPassword'); ?></button>
				</form>
			</div>
		</div>
		<div class="col-lg-7 d-none d-lg-block">
			<div id="auth-right"></div>
		</div>
	</div>
</div>
<?= $this->endSection(); ?>
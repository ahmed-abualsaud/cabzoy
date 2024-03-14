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
				<h1 class="auth-title">Sign Up</h1>
				<p class="auth-subtitle mb-5">Input your data to register to our website.</p>

				<form action="<?= route_to('register'); ?>" method="POST">

					<?= csrf_field() ?>

					<div class="form-group position-relative has-icon-left">
						<input type="email" class="form-control form-control-xl <?php ($validation->hasError('email') || session()->has('errors')) && print 'is-invalid'; ?>" placeholder="<?= lang('Auth.email'); ?>" name="email" value="<?= old('email'); ?>">
						<div class="form-control-icon"><i data-feather="mail"></i></div>
						<div class="invalid-feedback"><?= $validation->getError('email') ?></div>
					</div>
					<div class="mb-4">
						<small id="emailHelp" class="form-text text-muted"><?= lang('Auth.weNeverShare') ?></small>
					</div>
					<div class="form-group position-relative has-icon-left mb-4">
						<input type="text" class="form-control form-control-xl <?php ($validation->hasError('username') || session()->has('errors')) && print 'is-invalid'; ?>" placeholder="<?= lang('Auth.username'); ?>" name="username" value="<?= old('username'); ?>">
						<div class="form-control-icon"><i data-feather="user"></i></div>
						<div class="invalid-feedback"><?= $validation->getError('username') ?></div>
					</div>

					<div class="form-group position-relative has-icon-left mb-4">
						<input type="password" name="password" class="form-control form-control-xl <?php ($validation->hasError('password')) && print 'is-invalid'; ?>" placeholder="<?= lang('Auth.password') ?>" autocomplete="off">
						<div class="form-control-icon"><i data-feather="lock"></i></div>
						<div class="invalid-feedback"><?= $validation->getError('password') ?></div>
					</div>

					<div class="form-group position-relative has-icon-left mb-4">
						<input type="password" name="pass_confirm" class="form-control form-control-xl <?php ($validation->hasError('pass_confirm')) && print 'is-invalid'; ?>" placeholder="<?= lang('Auth.repeatPassword') ?>" autocomplete="off">
						<div class="form-control-icon"><i data-feather="lock"></i></div>
						<div class="invalid-feedback"><?= $validation->getError('pass_confirm') ?></div>
					</div>

					<button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">
						<?= lang('Auth.register'); ?>
					</button>
				</form>
				<div class="text-center mt-5 text-lg fs-4">
					<p class='text-gray-600'>
						<?= lang('Auth.alreadyRegistered'); ?>
						<a href="<?= route_to('login'); ?>" class="font-bold">
							<?= lang('Auth.signIn'); ?>
						</a>.
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
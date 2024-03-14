<?= $this->extend('layouts/default'); ?>

<?= $this->section('main'); ?>
<div class="container pt-5">
	<form method="POST" class="row">
		<div class="col-lg-12">
			<div class="bg-dark p-5" style="border-radius: 62px;">
				<h2 class="font-bold text-white mb-4">Configure Your Backend Server</h2>

				<div class="row">
					<div class="col-12">
						<h4 class="text-secondary">Project Configuration</h4>
					</div>
					<div class="col-lg-12">
						<div class="form-group mb-4">
							<label class="mb-2 text-success" for="site_name">Project Name</label>
							<input type="text" id="site_name" autocomplete="off" spellcheck="false" name="site_name" class="form-control form-control-lg bg-dark border-success text-success <?php $validation->hasError('site_name') && print 'is-invalid'; ?>" placeholder="Project Name" value="<?= old('site_name'); ?>">
							<div class="invalid-feedback"><?= $validation->getError('site_name') ?></div>
						</div>
					</div>
					<!-- <div class="col-lg-12">
						<div class="form-group mb-4">
							<label class="mb-2 text-success" for="cc_license_key">CodeCanyon License Key</label>
							<input type="text" id="cc_license_key" autocomplete="off" spellcheck="false" name="cc_license_key" class="form-control form-control-lg bg-dark border-success text-success <?php $validation->hasError('cc_license_key') && print 'is-invalid'; ?>" placeholder="CodeCanyon License Key" value="<?= old('cc_license_key'); ?>">
							<div class="invalid-feedback"><?= $validation->getError('cc_license_key') ?></div>
						</div>
					</div> -->
					<div class="col-lg-12">
						<div class="form-group mb-4">
							<label class="mb-2 text-success" for="fcm_server_key">FCM Server Key</label>
							<input type="text" id="fcm_server_key" autocomplete="off" spellcheck="false" name="fcm_server_key" class="form-control form-control-lg bg-dark border-success text-success <?php $validation->hasError('fcm_server_key') && print 'is-invalid'; ?>" placeholder="FCM Server Key" value="<?= old('fcm_server_key'); ?>">
							<div class="invalid-feedback"><?= $validation->getError('fcm_server_key') ?></div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-12">
						<h4 class="text-secondary">Database Configuration</h4>
					</div>
					<div class="col-lg-4">
						<div class="form-group mb-4">
							<label class="mb-2 text-success" for="db_username">Database Username</label>
							<input type="text" id="db_username" autocomplete="off" spellcheck="false" name="db_username" class="form-control form-control-lg bg-dark border-success text-success <?php $validation->hasError('db_username') && print 'is-invalid'; ?>" placeholder="Database Username" value="<?= old('db_username'); ?>">
							<div class="invalid-feedback"><?= $validation->getError('db_username') ?></div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group mb-4">
							<label class="mb-2 text-success" for="db_password">Database Password</label>
							<input type="password" id="db_password" autocomplete="off" spellcheck="false" name="db_password" class="form-control form-control-lg bg-dark border-success text-success <?php $validation->hasError('db_password') && print 'is-invalid'; ?>" placeholder="Database Password" value="<?= old('db_password'); ?>">
							<div class="invalid-feedback"><?= $validation->getError('db_password') ?></div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group mb-4">
							<label class="mb-2 text-success" for="db_name">Database Name</label>
							<input type="text" id="db_name" autocomplete="off" spellcheck="false" name="db_name" class="form-control form-control-lg bg-dark border-success text-success <?php $validation->hasError('db_name') && print 'is-invalid'; ?>" placeholder="Database Name" value="<?= old('db_name'); ?>">
							<div class="invalid-feedback"><?= $validation->getError('db_name') ?></div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-12">
						<h4 class="text-secondary">SMTP Configuration</h4>
					</div>
					<div class="col-lg-4">
						<div class="form-group mb-4">
							<label class="mb-2 text-success" for="smtp_host">SMTP Host</label>
							<input type="text" id="smtp_host" autocomplete="off" spellcheck="false" name="smtp_host" class="form-control form-control-lg bg-dark border-success text-success <?php $validation->hasError('smtp_host') && print 'is-invalid'; ?>" placeholder="SMTP Host" value="<?= old('smtp_host'); ?>">
							<div class="invalid-feedback"><?= $validation->getError('smtp_host') ?></div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group mb-4">
							<label class="mb-2 text-success" for="smtp_username">SMTP Username</label>
							<input type="text" id="smtp_username" autocomplete="off" spellcheck="false" name="smtp_username" class="form-control form-control-lg bg-dark border-success text-success <?php $validation->hasError('smtp_username') && print 'is-invalid'; ?>" placeholder="Smtp Username" value="<?= old('smtp_username'); ?>">
							<div class="invalid-feedback"><?= $validation->getError('smtp_username') ?></div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group mb-4">
							<label class="mb-2 text-success" for="smtp_password">SMTP Password</label>
							<input type="password" id="smtp_password" autocomplete="off" spellcheck="false" name="smtp_password" class="form-control form-control-lg bg-dark border-success text-success <?php $validation->hasError('smtp_password') && print 'is-invalid'; ?>" placeholder="Smtp Password" value="<?= old('smtp_password'); ?>">
							<div class="invalid-feedback"><?= $validation->getError('smtp_password') ?></div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-12">
						<button type="submit" class="btn btn-success btn-lg btn-block">Save</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<?= $this->endSection(); ?>
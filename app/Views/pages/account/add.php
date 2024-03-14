<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Add Account</h3>
				<p class="text-subtitle text-muted">For user to add account</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('accounts'); ?>">Accounts</a></li>
						<li class="breadcrumb-item active" aria-current="page">Add Account</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('account') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body p-5">
							<div class="row mb-3">
								<div class="col">
									<div class="form-group mb-4">
										<label class="mb-2" for="account_holdername">Account Holdername</label>
										<input type="text" id="account_holdername" name="account_holdername" class="form-control form-control-lg <?php $validation->hasError('account_holdername') && print 'is-invalid'; ?>" placeholder="Account Holdername" value="<?= old('account_holdername'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('account_holdername') ?></div>
									</div>
								</div>

								<div class="col-lg-6">
									<?= select_box('is_default', 'Is Default Account', ['0' => 'No', '1' => 'Yes'], $validation); ?>
								</div>

								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="account_number">Account Number</label>
										<input type="tel" id="account_number" name="account_number" class="form-control form-control-lg <?php $validation->hasError('account_number') && print 'is-invalid'; ?>" inputmode="numeric" pattern="[0-9\s]{5,30}" placeholder="xxxx xxxx xxxx xxxx" value="<?= old('account_number'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('account_number') ?></div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group mb-4">
										<label class="mb-2" for="bank_name">Bank Name</label>
										<input type="text" id="bank_name" name="bank_name" class="form-control form-control-lg <?php $validation->hasError('bank_name') && print 'is-invalid'; ?>" placeholder="Bank Name" value="<?= old('bank_name'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('bank_name') ?></div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group mb-4">
										<label class="mb-2" for="branch_number">Branch Number</label>
										<input type="text" id="branch_number" name="branch_number" class="form-control form-control-lg <?php $validation->hasError('branch_number') && print 'is-invalid'; ?>" placeholder="Branch Number" value="<?= old('branch_number'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('branch_number') ?></div>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="branch_address">Branch Address</label>
										<input type="text" id="branch_address" name="branch_address" class="form-control form-control-lg <?php $validation->hasError('branch_address') && print 'is-invalid'; ?>" placeholder="Branch Address" value="<?= old('branch_address'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('branch_address') ?></div>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group mb-4">
										<label class="mb-2" for="account_code">Account Code</label>
										<input type="text" id="account_code" name="account_code" class="form-control form-control-lg <?php $validation->hasError('account_code') && print 'is-invalid'; ?>" placeholder="Account Code" value="<?= old('account_code'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('account_code') ?></div>
									</div>
								</div>
								<div class="col-lg-6">
									<?= select_box('status', 'Account Status', [
										'approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected',
									], $validation); ?>
								</div>

								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<?php $user = [];
											if (is($users, 'array')) $user = array_map(static function ($data) use ($user) {
												return $user[$data->id] = ucwords($data->firstname . ' ' . $data->lastname);
											}, $users); ?>
											<?= select_box('user_id', 'User', $user, $validation); ?>
										</div>
										<div class="col-lg-6">
											<?php $company = [];
											if (is($companies, 'array')) $company = array_map(static function ($data) use ($company) {
												return $company[$data->id] = ucwords($data->company_name);
											}, $companies); ?>
											<?= select_box('company_id', 'Company', $company, $validation); ?>
										</div>
									</div>
								</div>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Add Account</button>
							</div>

						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
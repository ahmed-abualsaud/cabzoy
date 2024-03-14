<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Add Commission</h3>
				<p class="text-subtitle text-muted">For user to add commission</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('commissions'); ?>">Commissions</a></li>
						<li class="breadcrumb-item active" aria-current="page">Add Commission</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('commission') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="card">
				<div class="card-body p-5">
					<div class="row mb-3">
						<div class="col-lg-12 bg-light p-4 mb-3">
							<div class="row">
								<div class="col-lg-12 text-center mb-4">
									<h4>Please choose only one out of three.</h4>
								</div>

								<div class="col-lg-4">
									<?php $category = [];
									$category = array_map(static function ($data) use ($category) {
										return $category[$data->id] = ucwords($data->category_name);
									}, $categories) ?>
									<?= select_box('category_id', 'Commission on Vehicle Categories', $category, $validation); ?>
								</div>

								<div class="col-lg-4">
									<?php $vehicle = [];
									$vehicle = array_map(static function ($data) use ($vehicle) {
										return $vehicle[$data->id] = ucwords($data->vehicle_number);
									}, $vehicles) ?>
									<?= select_box('vehicle_id', 'Commission on Vehicles', $vehicle, $validation); ?>
								</div>

								<div class="col-lg-4">
									<?php $company = [];
									$company = array_map(static function ($data) use ($company) {
										return $company[$data->id] = ucwords($data->company_name);
									}, $companies) ?>
									<?= select_box('company_id', 'Commission on Companies', $company, $validation); ?>
								</div>
							</div>
						</div>


						<div class="col-lg-12">
							<div class="form-group mb-4">
								<label for="commission_name" class="mb-2">Commission Name</label>
								<input type="text" id="commission_name" name="commission_name" class="form-control form-control-lg <?php $validation->hasError('commission_name') && print 'is-invalid'; ?>" placeholder="Commission Name" value="<?= old('commission_name'); ?>">
								<div class="invalid-feedback"><?= $validation->getError('commission_name') ?></div>
							</div>
						</div>

						<div class="col-lg-4">
							<?= select_box('commission_type', 'Commission Type', [
								'percentage' => 'Percentage Commission', 'flat' => 'Flat Commission'
							], $validation); ?>
						</div>

						<div class="col-lg-4">
							<div class="form-group mb-4">
								<label class="mb-2" for="commission">Commission</label>
								<input type="number" min="0" step="0.01" id="commission" name="commission" class="form-control form-control-lg <?php $validation->hasError('commission') && print 'is-invalid'; ?>" placeholder="Commission" value="<?= old('commission'); ?>">
								<div class="invalid-feedback"><?= $validation->getError('commission') ?></div>
							</div>
						</div>

						<div class="col-lg-4">
							<?= select_box('commission_status', 'Commission Status', [
								'approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected'
							], $validation); ?>
						</div>


						<div class="col-lg-12 bg-light p-4 mb-3">
							<div class="row">
								<div class="col-lg-12 text-center mb-4">
									<h4>Please choose only one out of two.</h4>
								</div>

								<div class="col-lg-6">
									<?php $company = [];
									$company = array_map(static function ($data) use ($company) {
										return $company[$data->id] = ucwords($data->company_name);
									}, $beneficiary_companies) ?>
									<?= select_box('beneficiary_company_id', 'Beneficiary Companies', $company, $validation); ?>
								</div>

								<div class="col-lg-6">
									<?php $user = [];
									$user = array_map(static function ($data) use ($user) {
										return $user[$data->id] = ucwords($data->firstname . ' ' . $data->lastname);
									}, $users) ?>
									<?= select_box('beneficiary_user_id', 'Beneficiary Users', $user, $validation); ?>
								</div>
							</div>
						</div>
					</div>

					<div class="mt-3">
						<button type="submit" class="btn btn-lg btn-success">Add Commission</button>
					</div>

				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
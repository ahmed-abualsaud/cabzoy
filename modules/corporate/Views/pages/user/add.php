<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Assign Company's User</h3>
				<p class="text-subtitle text-muted">For user to add company</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('corporate_dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('user_companies'); ?>">Company's Users</a></li>
						<li class="breadcrumb-item active" aria-current="page">Assign Company's User</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('company') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body p-5">
							<div class="row mb-3">
								<div class="col-6">
									<?php $user = [];
									if (is($users, 'array')) $user = array_map(static function ($data) use ($user) {
										return $user[$data->id] = ucwords($data->firstname . ' ' . $data->lastname);
									}, $users); ?>
									<?= select_box('user_id', 'Users', $user, $validation); ?>
								</div>
								<div class="col-6">
									<?php $company = [];
									if (is($companies, 'array')) $company = array_map(static function ($data) use ($company) {
										return $company[$data->id] = ucwords($data->company_name);
									}, $companies); ?>
									<?= select_box('company_id', 'Companies', $company, $validation); ?>
								</div>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Assign Company's User</button>
							</div>

						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
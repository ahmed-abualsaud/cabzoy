<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Update Company's Group</h3>
				<p class="text-subtitle text-muted">For group to add company</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('corporate_dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('companies_groups'); ?>">Company's Groups</a></li>
						<li class="breadcrumb-item active" aria-current="page">Update Company's Group</li>
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
								<div class="col-md-6">
									<div class="form-group mb-4">
										<label class="mb-2" for="title">Group Name</label>
										<input type="text" id="title" name="title" class="form-control form-control-lg <?php $validation->hasError('title') && print 'is-invalid'; ?>" placeholder="Group Name" value="<?= old('title') ?? esc($companyGroup->title); ?>">
										<div class="invalid-feedback"><?= $validation->getError('title') ?></div>
									</div>
								</div>
								<div class="col-lg-6">
									<?php $policy = [];
									if (is($policies, 'array')) $policy = array_map(static function ($data) use ($policy) {
										return $policy[$data->id] = ucwords($data->title);
									}, $policies); ?>
									<?= select_box('policy_id', 'Policies', $policy, $validation, $companyGroup->company_policy_id); ?>
								</div>
								<div class="col-md-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="detail">Group Description</label>
										<input type="text" id="detail" name="detail" class="form-control form-control-lg <?php $validation->hasError('detail') && print 'is-invalid'; ?>" placeholder="Group Description" value="<?= old('detail') ?? esc($companyGroup->detail); ?>">
										<div class="invalid-feedback"><?= $validation->getError('detail') ?></div>
									</div>
								</div>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Update Group</button>
							</div>

						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
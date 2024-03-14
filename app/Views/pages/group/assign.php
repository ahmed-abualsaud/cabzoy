<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Assign Group</h3>
				<p class="text-subtitle text-muted">For user to assign group</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('groups'); ?>">Groups</a></li>
						<li class="breadcrumb-item active" aria-current="page">Assign Group</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('group') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body p-5">
							<div class="row mb-3">
								<div class="col-lg-6">
									<?php $group = [];
									if (is($groups, 'array')) foreach ($groups as $value) {
										$group[$value->id] = ucwords($value->name);
									} ?>
									<?= select_box('group_id', 'Groups', $group, $validation); ?>
								</div>

								<div class="col-lg-6">
									<?php $user = [];
									if (is($users, 'array')) foreach ($users as $value) {
										$user[$value->id] = ucwords($value->name);
									} ?>
									<?= select_box('user_id', 'Users', $user, $validation); ?>
								</div>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Assign User to Group</button>
							</div>

						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
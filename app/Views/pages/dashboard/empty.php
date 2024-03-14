<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<h3><?php print(config('Settings')->siteName ?? 'Dashboard') ?>'s Welcome Screen</h3>
</div>
<div class="page-content">
	<section class="row">
		<div class="col-12 col-lg-9">
			<div class="row">
				<div class="col-12">
					<div class="card overflow-auto">
						<div class="card-body">
							<h4>Welcome <?php print(user()->firstname); ?>, You can manage your account via app.</h4>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-12 col-lg-3">
			<div class="card overflow-auto">
				<div class="card-body py-4 px-5">
					<div class="d-flex align-items-center">
						<div class="avatar avatar-xl">
							<?= image(user()->profile_pic, user()->firstname, strrev(user()->lastname)) ?>
						</div>
						<div class="ms-3 name">
							<h5 class="font-bold text-capitalize"><?= user()->name; ?></h5>
							<h6 class="text-muted mb-0">@<?= user()->username; ?></h6>
						</div>
					</div>
				</div>
			</div>
		</div>

	</section>
</div>
<?= $this->endSection(); ?>
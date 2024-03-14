<?= $this->extend('layouts/default'); ?>

<?= $this->section('main'); ?>
<div class="container pt-5">
	<div class="row">
		<div class="col-lg-12">
			<div class="bg-dark p-5" style="border-radius: 62px;">
				<h1 class="text-white mb-5"><?= $title; ?></h1>
				<?php if (is($output, 'array')) foreach ($output as $value) : ?>
					<h4 class="text-white"><?= str_replace(' ::
', ' ::<br></h2><p class="text-success text-capitalize">', $value); ?></p>
					<?php endforeach; ?>
			</div>
		</div>
		<div class="col-lg-12 py-4 text-center">
			<a class="btn btn-lg rounded-pill btn-outline-primary" href="<?= route_to('login') ?>">Go to Login Page</a>
		</div>
	</div>
</div>
<?= $this->endSection(); ?>
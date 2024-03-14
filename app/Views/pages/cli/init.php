<?= $this->extend('layouts/default'); ?>

<?= $this->section('main'); ?>
<div class="container pt-5">
	<div class="row">
		<div class="col-lg-12">
			<div class="bg-dark p-5" style="border-radius: 62px;">
				<h2 class="font-bold text-white mb-4"><?= $title; ?></h2>
				<?php if (is($output, 'array')) foreach ($output as $value) : ?>
					<p class="text-success"><?= $value; ?></p>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection(); ?>
<?= $this->extend('layouts/default'); ?>

<?= $this->section('main'); ?>
<div class="container pt-5">
	<div class="row">
		<div class="col-lg-12">
			<div class="bg-dark p-5" style="border-radius: 62px;">
				<h2 class="font-bold text-white mb-4">Environment Setup</h2>

				<p class="text-success">
					Your Project successfully setup, Please wait for creating & update the database.
					<br>You will redirect in few seconds.
				</p>
			</div>
		</div>
	</div>
</div>
<script>
	window.setTimeout(function() {
		window.location.href = "<?= route_to('resetAll') ?>";
	}, 3000);
</script>
<?= $this->endSection(); ?>
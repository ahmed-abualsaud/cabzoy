<?= $this->extend('layouts/default'); ?>

<?= $this->section('main'); ?>
<div class="bg-primary bg-gradient vh-100 vw-100 align-items-center d-flex justify-content-center">
	<div class="card shadow-lg w-75 " style="border-radius: 15px;">
		<div class="card-header p-4">
			<h3>Check Requirements</h3>
			<h6 class="card-subtitle text-dark text-opacity-50">If all requirement is fine then you good to go.</h6>
		</div>
		<div class="card-body p-4 overflow-scroll">
			<ul class="list-group rounded">
				<?php foreach ($check as $req => $value) : ?>
					<li class="list-group-item">
						<div class="d-flex align-items-center justify-content-between">
							<div>
								<h5 class="text-capitalize mb-0"><?= humanize("{$req}"); ?></h5>
								<p class="text-dark text-opacity-50 mb-0">
									<?= "It's " . ($value['value'] ? '' : 'not') . ' good to go.'; ?>
								</p>
							</div>
							<?php if ($value['value']) : ?>
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-patch-check-fill text-success" viewBox="0 0 16 16" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $value['message']; ?>">
									<path d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.89-.011a2.89 2.89 0 0 0-2.924 2.924l.01.89-.636.622a2.89 2.89 0 0 0 0 4.134l.637.622-.011.89a2.89 2.89 0 0 0 2.924 2.924l.89-.01.622.636a2.89 2.89 0 0 0 4.134 0l.622-.637.89.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.89.636-.622a2.89 2.89 0 0 0 0-4.134l-.637-.622.011-.89a2.89 2.89 0 0 0-2.924-2.924l-.89.01-.622-.636zm.287 5.984-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708.708z" />
								</svg>
							<?php else : ?>
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill text-warning" viewBox="0 0 16 16" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $value['message']; ?>">
									<path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
								</svg>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="card-footer d-flex align-items-center justify-content-end p-3">
			<a href="install">
				<button type="button" <?= $is_valid ? '' : 'disabled'; ?> class="btn btn-primary px-5">Next</button>
			</a>
		</div>
	</div>
</div>
<?= $this->endSection(); ?>
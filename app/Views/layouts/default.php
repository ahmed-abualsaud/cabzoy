<?php helper('color'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $title ?? getDefaultConfig('siteName', env('app.name', 'Fab IT Hub')); ?></title>

	<link rel="shortcut icon" href="<?= site_url(getDefaultConfig('siteFavicon', getDefaultConfig('siteLogo', 'favicon.ico'))) ?>">

	<?php initDynamicColors(); ?>
	<?= $this->renderSection('stylesFirst'); ?>
	<?php if (!CI_DEBUG) : ?>
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js@1.11.2/src/toastify.min.css">
	<?php else : ?>
		<link rel="stylesheet" href="<?= site_url('assets/vendors/toastify/toastify.css') ?>">
	<?php endif ?>
	<?php if (env('app.baseURL')) : ?>
		<link rel="stylesheet" href="<?= site_url('assets/vendors/bootstrap/bootstrap.css') ?>">
	<?php else : ?>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/combine/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
	<?php endif; ?>
	<link rel="stylesheet" href="<?= site_url('assets/css/app.css') ?>">
	<?= $this->renderSection('styles'); ?>
</head>

<body>
	<?= $this->renderSection('main'); ?>

	<?php if (CI_DEBUG) : ?>
		<script src="<?= site_url('assets/vendors/bootstrap/bootstrap.bundle.min.js'); ?>"></script>
		<script src="<?= site_url('assets/vendors/feather-icons/feather.min.js'); ?>"></script>
		<script src="<?= site_url('assets/vendors/toastify/toastify.js'); ?>"></script>
	<?php else : ?>
		<script src="https://cdn.jsdelivr.net/combine/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js,npm/toastify-js@1.11.2,npm/feather-icons@4.28.0"></script>
	<?php endif ?>
	<script>
		// Feather Icons
		if (feather) feather.replace();

		<?php foreach (session()->getFlashData('warn') ?? session()->getFlashData('success') ?? session()->getFlashData('errors') ?? [] as $flash) : ?>
			// Toastify
			if (Toastify) Toastify({
				close: true,
				duration: 3000,
				stopOnFocus: true,
				text: "<?= $flash ?>",
				className: '<?php session()->has('warn') && print 'bg-warning' ?><?php session()->has('errors') && print 'bg-danger' ?><?php session()->has('success') && print 'bg-success' ?> bg-gradient',
			}).showToast();
		<?php endforeach; ?>

		// Bootstrap Popover
		var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
		if (popoverTriggerList.length > 0) popoverTriggerList.map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl))

		// Bootstrap Tooltip
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		if (tooltipTriggerList.length > 0) tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))
	</script>
	<?= $this->renderSection('scripts'); ?>
</body>

</html>
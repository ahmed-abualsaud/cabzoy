<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $title ?? getDefaultConfig('siteName', env('app.name', 'Fab IT Hub')); ?></title>
	<link rel="shortcut icon" href="<?= site_url(getDefaultConfig('siteFavicon', getDefaultConfig('siteLogo', 'favicon.ico'))) ?>">
	<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="<?= site_url('assets/vendors/bootstrap/bootstrap.css') ?>">
	<link rel="stylesheet" href="<?= site_url('assets/css/app.css') ?>">
	<link rel="stylesheet" href="<?= site_url('assets/css/pages/error.css') ?>">
</head>


<body>
	<div id="error">
		<div class="error-page container">
			<div class="col-md-8 col-12 offset-md-2">
				<img class="img-error" src="<?= site_url('assets/img/error-404.png') ?>" alt="Not Found">
				<div class="text-center">
					<h1 class="error-title">NOT FOUND</h1>
					<p class='fs-5 text-gray-600'>
						<?php if (ENVIRONMENT !== 'production') : ?>
							<?= nl2br(esc($message)) ?>
						<?php else : ?>
							<?= lang('Errors.sorryCannotFind') ?>
						<?php endif ?>
					</p>
					<a href="<?= route_to('dashboard'); ?>" class="btn btn-lg btn-outline-primary mt-3">Go Home</a>
				</div>
			</div>
		</div>
	</div>
</body>

</html>
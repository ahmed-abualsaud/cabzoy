<!doctype html>
<html>

<head>
	<meta charset="UTF-8">
	<meta name="robots" content="noindex">

	<title><?= lang('Errors.whoops') ?></title>

	<style>
		<?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
	</style>
</head>

<body>

	<div class="container text-center">

		<?php if (str_contains($exception->getMessage(), 'sql_mode=only_full_group_by')) : ?>
			<h1 class="headline">SQL Mode Error from Database</h1>
			<p class="lead"><?= $exception->getMessage(); ?></p>
			<p class="lead">Please disable <strong>ONLY_FULL_GROUP_BY</strong> mode</p>

			<ul class="list-group text-start">
				<li class="list-group-item">If you using <strong>PHPMyAdmin</strong>, Edit <strong>sql mode</strong> variable and remove the <strong>ONLY_FULL_GROUP_BY</strong> text from the value.</li>
				<li class="list-group-item">If you using command-line interface, run the following command: <strong>SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));</strong></li>
			</ul>
			<a class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" href="https://stackoverflow.com/questions/41887460/select-list-is-not-in-group-by-clause-and-contains-nonaggregated-column-inc" target="_blank" rel="noopener noreferrer">Help?</a>
		<?php else : ?>
			<h1 class="headline"><?= lang('Errors.whoops') ?></h1>
			<p class="lead"><?= lang('Errors.weHitASnag') ?></p>
		<?php endif; ?>


	</div>

</body>

</html>
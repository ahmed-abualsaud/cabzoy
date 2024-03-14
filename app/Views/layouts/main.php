<?= $this->extend('layouts/default'); ?>

<?= $this->section('styles'); ?>
<?php if (CI_DEBUG) : ?>
	<link rel="stylesheet" href="<?= site_url('assets/vendors/choicesjs/choices.css'); ?>">
	<?php if (url_is('list/*') || url_is('show/*') || url_is('dispatch') || url_is('dashboard')) : ?>
		<link rel="stylesheet" href="<?= site_url('/public/assets/vendors/simple-datatables/style.css'); ?>">
	<?php endif; ?>
	<link rel="stylesheet" href="<?= site_url('assets/vendors/perfect-scrollbar/perfect-scrollbar.css'); ?>">
<?php else : ?>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@9.0.1/public/assets/styles/choices.min.css">
	<?php if (url_is('list/*') || url_is('show/*') || url_is('dispatch') || url_is('dashboard')) : ?>
		<link rel="stylesheet" href="<?= site_url('/public/assets/vendors/simple-datatables/style.css'); ?>">
	<?php endif; ?>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/perfect-scrollbar@1.5.2/css/perfect-scrollbar.min.css">
<?php endif ?>
<?= $this->endSection(); ?>

<?= $this->section('main'); ?>
<div id="app">
	<?= view('layouts/sidebar'); ?>
	<div id="main">
		<?= view('layouts/header'); ?>
		<?= $this->renderSection('content'); ?>
		<?= view('layouts/footer'); ?>
	</div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<?= view('components/image-upload'); ?>
<?php if (CI_DEBUG) : ?>
	<script src="<?= site_url('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js'); ?>"></script>
	<script src="<?= site_url('assets/vendors/choicesjs/choices.js'); ?>"></script>
	<?php if (url_is('list/*') || url_is('show/*') || url_is('dispatch') || url_is('dashboard')) : ?>
		<script src="<?= site_url('assets/vendors/simple-datatables/simple-datatables.js'); ?>"></script>
	<?php endif; ?>
<?php else : ?>
	<script src="https://cdn.jsdelivr.net/npm/perfect-scrollbar@1.5.2/dist/perfect-scrollbar.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/choices.js@9.0.1/public/assets/scripts/choices.min.js"></script>
	<?php if (url_is('list/*') || url_is('show/*') || url_is('dispatch') || url_is('dashboard')) : ?>
		<script src="https://cdn.jsdelivr.net/npm/simple-datatables@3.1.2/dist/umd/simple-datatables.min.js"></script>
	<?php endif; ?>
<?php endif ?>

<script src="<?= site_url('assets/js/main.js') ?>"></script>
<script>
	if (typeof simpleDatatables !== 'undefined' && document.querySelector("#table1")) {
		let table1 = document.querySelector("#table1");
		if (table1 && simpleDatatables) {
			let datatable = new simpleDatatables.DataTable(table1, {
				perPage: <?= config('Settings')->perPage ?>,
				perPageSelect: [5, 10, 15, 20, 25],
			});
			datatable.on("datatable.sort", function() {
				if (feather) feather.replace();
			});
			datatable.on("datatable.search", function() {
				if (feather) feather.replace();
			});
			datatable.on("datatable.perpage", function() {
				if (feather) feather.replace();
			});
			datatable.on("datatable.page", function() {
				if (feather) feather.replace();
			});

			if (document.querySelector("button.csv"))
				document.querySelector("button.csv").addEventListener("click", () => {
					datatable.export({
						type: "csv",
						download: true,
						lineDelimiter: "\n",
						columnDelimiter: ";"
					})
				})
		}
	}
</script>

<script>
	// Currency Formatter
	function formatCurrency(number = 0) {
		const formatter = new Intl.NumberFormat('<?= config('Settings')->defaultLanguage; ?>' ?? 'en-US', {
			style: 'currency',
			minimumFractionDigits: 2,
			currencyDisplay: 'symbol',
			currency: '<?= config('Settings')->defaultCurrencyUnit; ?>',
		});
		return formatter.format(number).replace('NGN', '₦');
	}

	// Unit Formatter
	function formatUnit(number = 0, calculate = true) {
		const serverUnit = '<?= config('Settings')->defaultLengthUnit; ?>';
		const unit =
			serverUnit.toLowerCase() === 'km' ?
			'kilometer' :
			serverUnit.toLowerCase() === 'mile' ?
			'mile' :
			'kilometer';

		if (calculate) {
			if (serverUnit.toLowerCase() === 'km') number = number * 0.001;
			else if (serverUnit.toLowerCase() === 'mile') number = number * 0.000621371;
		}

		const formatter = new Intl.NumberFormat('<?= config('Settings')->defaultLanguage; ?>' ?? 'en-US', {
			unit,
			style: 'unit',
			minimumFractionDigits: 2,
		});
		return formatter.format(number);
	};
</script>
<?= $this->endSection(); ?>
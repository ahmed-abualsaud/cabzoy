<?= $this->extend('layouts/default'); ?>

<?= $this->section('styles'); ?>
<link rel="stylesheet" href="<?= site_url("assets/css/pages/auth.css"); ?>">
<?= $this->endSection() ?>

<?= $this->section('main'); ?>
<div class="d-flex align-items-center flex-column pt-4" style="height: 100vh;">
	<h1 class="text-success fw-bold mt-5">Account Activated</h1>
	<img src="<?= config('Settings')->siteLogo ?>" class="w-25 my-5" style="border-radius: 24px;" />
	<h4 class="text-center lh-base text-dark">Thank you for signing up on <?= config('Settings')->siteName ?? env('app.name', 'Fab IT Hub'); ?> .<br />
		Login in from your <?= config('Settings')->siteName ?? env('app.name', 'Fab IT Hub'); ?> Mobile App to enjoy our services.
	</h4>
	<a class="btn btn-primary btn-lg mt-5 px-5 py-3" href="<?= config('Settings')->siteUrl; ?>">Learn More</a>
</div>
<?= $this->endSection(); ?>
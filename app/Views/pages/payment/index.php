<?= $this->extend('layouts/default'); ?>

<?= $this->section('main'); ?>
<div class="container" style="display: grid;place-content: center;height: 100vh;">
	<div class="card">
		<div class="card-body">
			<form>
				<h1>Pay <?= $amountForDisplay ?? 0; ?></h1>
				<button class="btn btn-primary" type="button" onClick="makePayment()">Pay via FlutterWave</button>
				<button class="btn btn-danger" type="button" onClick="closePayment()">Cancel</button>
			</form>
		</div>
	</div>
</div>


<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="https://checkout.flutterwave.com/v3.js"></script>
<script>
	function closePayment() {
		if (window?.ReactNativeWebView) window?.ReactNativeWebView?.postMessage(JSON.stringify({
			"status": "400",
			"message": "Payment cancelled by user."
		}))
		else {
			JSON.stringify({
				"status": "400",
				"message": "Payment cancelled by user."
			})
		};
	}

	function makePayment() {
		FlutterwaveCheckout({
			public_key: "<?= $flutterwavePublicKey ?>",
			tx_ref: "<?= time(); ?>",
			amount: <?= $amount ?? 0; ?>,
			currency: "<?= config('Settings')->defaultCurrencyUnit ?? 'USD' ?>",
			country: "<?= config('Settings')->defaultCountryNameCode ?? 'US' ?>",
			payment_options: "card",

			// specified redirect URL
			redirect_url: "<?= route_to('payment_redirect', $txnId); ?>",

			// use customer details if user is not logged in, else add user_id to the request
			customer: {
				email: "<?= user()->email; ?>",
				phone_number: "<?= user()->phone; ?>",
				name: "<?= user()->firstname ?>",
			},
			callback: function(data) {
				console.log(data);
			},
			onclose: function() {
				// close modal
			},
			customizations: {
				title: "<?= config('Settings')->siteName ?? 'Taxi App' ?>",
				description: "Payment for <?= $amountForDisplay ?? 0; ?>",
				logo: "https://cdn.iconscout.com/icon/premium/png-256-thumb/payment-2193968-1855546.png",
			},
		});
	}
</script>
<?= $this->endSection(); ?>
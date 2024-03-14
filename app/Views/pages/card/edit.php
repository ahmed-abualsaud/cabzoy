<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Edit Card</h3>
				<p class="text-subtitle text-muted">For user to edit card</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('cards'); ?>">Cards</a></li>
						<li class="breadcrumb-item active" aria-current="page">Edit Card</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('card') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body p-5">
							<div class="row mb-3">
								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="card_holdername">Card Holdername</label>
										<input type="text" id="card_holdername" autocomplete="cc-name" name="card_holdername" class="form-control form-control-lg <?php $validation->hasError('card_holdername') && print 'is-invalid'; ?>" placeholder="Card Holdername" value="<?= old('card_holdername') ?? esc($card->card_holdername); ?>">
										<div class="invalid-feedback"><?= $validation->getError('card_holdername') ?></div>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="card_number">Card Number</label>
										<input type="tel" id="card_number" name="card_number" class="form-control form-control-lg <?php $validation->hasError('card_number') && print 'is-invalid'; ?>" inputmode="numeric" pattern="[0-9\s]{13,19}" autocomplete="cc-number" minlength="13" maxlength="19" placeholder="xxxx xxxx xxxx xxxx" value="<?= old('card_number') ?? esc($card->card_number); ?>">
										<div class="invalid-feedback"><?= $validation->getError('card_number') ?></div>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="card_expire">Card Expire Date</label>
										<input type="month" id="card_expire" name="card_expire" class="form-control form-control-lg <?php $validation->hasError('card_expire') && print 'is-invalid'; ?>" placeholder="Card Expire" value="<?= old('card_expire') ?? esc('20' . $card->card_year . '-' . $card->card_month); ?>">
										<div class="invalid-feedback"><?= $validation->getError('card_expire') ?></div>
									</div>
								</div>
								<div class="col">
									<div class="form-group mb-4">
										<label class="mb-2" for="card_cvv">Card CVV / CSV</label>
										<input type="tel" id="card_cvv" name="card_cvv" class="form-control form-control-lg <?php $validation->hasError('card_cvv') && print 'is-invalid'; ?>" inputmode="numeric" pattern="[0-9\s]{3,4}" autocomplete="cc-csc" minlength="3" maxlength="4" placeholder="xxx" value="<?= old('card_cvv') ?? esc($card->card_cvv); ?>">
										<div class="invalid-feedback"><?= $validation->getError('card_cvv') ?></div>
									</div>
								</div>

								<div class="col-lg-6">
									<?= select_box('is_default', 'Is Default Card', ['0' => 'No', '1' => 'Yes'], $validation, $card->is_default); ?>
								</div>

								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<?= select_box('status', 'Card Status', [
												'approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected',
											], $validation, $card->card_status); ?>
										</div>
										<div class="col-lg-6">
											<?= select_box('type', 'Card Type', [
												'credit' => 'Credit', 'debit' => 'Debit'
											], $validation, $card->card_type); ?>
										</div>
									</div>
								</div>

								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<?php $user = [];
											if (is($users, 'array')) $user = array_map(static function ($data) use ($user) {
												return $user[$data->id] = ucwords($data->firstname . ' ' . $data->lastname);
											}, $users); ?>
											<?= select_box('user_id', 'User', $user, $validation, $paymentRelation->user_id); ?>
										</div>
										<div class="col-lg-6">
											<?php $company = [];
											if (is($companies, 'array')) $company = array_map(static function ($data) use ($company) {
												return $company[$data->id] = ucwords($data->company_name);
											}, $companies); ?>
											<?= select_box('company_id', 'Company', $company, $validation, $paymentRelation->company_id); ?>
										</div>
									</div>
								</div>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Edit Card</button>
							</div>

						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
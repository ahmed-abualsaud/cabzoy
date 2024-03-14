<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Create Company's Policy</h3>
				<p class="text-subtitle text-muted">For group to add company policy</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('corporate_dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('companies_groups'); ?>">Company's Policies</a></li>
						<li class="breadcrumb-item active" aria-current="page">Create Company's Policy</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('add_companies_policy') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body p-5">
							<div class="row mb-3">
								<div class="col-md-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="title">Policy Title</label>
										<input type="text" id="title" name="title" class="form-control form-control-lg <?php $validation->hasError('title') && print 'is-invalid'; ?>" placeholder="Policy Title" value="<?= old('title'); ?>">
										<div class="invalid-feedback"><?= $validation->getError('title') ?></div>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="detail">Details of Policy</label>
										<textarea type="text" id="detail" name="detail" class="form-control form-control-lg <?php $validation->hasError('detail') && print 'is-invalid'; ?>" placeholder="Details of Policy" value="<?= old('detail'); ?>"></textarea>
										<div class="invalid-feedback"><?= $validation->getError('detail') ?></div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6 mb-4">
									<h3>Spending Allowance</h3>
									<div class="form-group mb-4">
										<div class="input-group">
											<label for="spending_allowance" class="input-group-text bg-light-primary text-dark">
												<?= config('Settings')->defaultCurrencyUnit ?? 'USD' ?>
											</label>
											<input type="number" id="spending_allowance" name="rules[spending_allowance]" class="form-control form-control-lg <?php $validation->hasError('rules.spending_allowance') && print 'is-invalid'; ?>" placeholder="Spending Allowance" value="<?= old('rules.spending_allowance'); ?>">
											<div class="invalid-feedback"><?= $validation->getError('rules.spending_allowance') ?></div>

											<select class="form-select form-select-lg <?php $validation->hasError('rules.spending_allowance_value') && print 'is-invalid'; ?>" id="spending_allowance_value" name="rules[spending_allowance_value]">
												<option value="day">Day</option>
												<option value="week">Week</option>
												<option value="month">Month</option>
												<option value="year">Year</option>
											</select>
											<div class="invalid-feedback"><?= $validation->getError('rules.spending_allowance_value') ?></div>
										</div>
									</div>
								</div>
								<div class="col-md-6 mb-4">
									<h3>Order Allowance</h3>
									<div class="form-group mb-4">
										<div class="input-group">
											<input type="number" id="order_allowance" name="rules[order_allowance]" class="form-control form-control-lg <?php $validation->hasError('rules.order_allowance') && print 'is-invalid'; ?>" placeholder="Number of Rides" value="<?= old('rules.order_allowance'); ?>">
											<span class="input-group-text bg-light-primary text-dark">Rides</span>
											<div class="invalid-feedback"><?= $validation->getError('rules.order_allowance') ?></div>

											<select class="form-select form-select-lg <?php $validation->hasError('rules.order_allowance_value') && print 'is-invalid'; ?>" id="order_allowance_value" name="rules[order_allowance_value]">
												<option value="day">Day</option>
												<option value="week">Week</option>
												<option value="month">Month</option>
												<option value="year">Year</option>
											</select>
											<div class="invalid-feedback"><?= $validation->getError('rules.order_allowance_value') ?></div>
										</div>
									</div>
								</div>
								<div class="col-md-6 mb-4">
									<?php $category = [];
									if (is($categories, 'array')) $category = array_map(static function ($data) use ($category) {
										return $category[$data->id] = ucwords($data->category_name);
									}, $categories); ?>
									<?= select_box('rules[category_id][]', 'Service Type', $category, $validation, null, false, true); ?>
								</div>
								<div class="col-md-6 mb-4">
									<h3>Expanse Tracking</h3>
									<div class="form-check">
										<label class="form-check-label" for="flexCheckDefault">
											Expanse Note
										</label>
										<input class="form-check-input" name="rules[expanse_note]" type="checkbox" value="1" id="flexCheckDefault">
									</div>
								</div>
								<div class="col-md-12 mb-4">
									<h3>Day & Time</h3>
									<div class="row">
										<?php foreach (WEEKDAY_ARRAY as $day) : ?>
											<div class="col-md-3 form-group mb-4">
												<label><?= $day; ?></label>
												<div class="input-group">
													<input type="time" name="rules[<?= strtolower($day); ?>_start_time]" class="form-control form-control-lg <?php $validation->hasError('rules' . strtolower($day) . '_start_time') && print 'is-invalid'; ?>" placeholder="Day & Time" value="<?= old('rules' . strtolower($day) . '_start_time'); ?>">
													<div class="invalid-feedback"><?= $validation->getError('rules' . strtolower($day) . '_start_time') ?></div>

													<input type="time" name="rules[<?= strtolower($day); ?>_end_time]" class="form-control form-control-lg <?php $validation->hasError('rules' . strtolower($day) . '_end_time') && print 'is-invalid'; ?>" placeholder="Day & Time" value="<?= old('rules' . strtolower($day) . '_end_time'); ?>">
													<div class="invalid-feedback"><?= $validation->getError('rules' . strtolower($day) . '_end_time') ?></div>
												</div>
											</div>
										<?php endforeach; ?>
									</div>

								</div>

							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Create Policy</button>
							</div>

						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Update Withdraw</h3>
				<p class="text-subtitle text-muted">For user to update withdraw</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('withdraws'); ?>">Withdraws</a></li>
						<li class="breadcrumb-item active" aria-current="page">Update Withdraw</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('withdraw') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body p-5">
							<div class="row mb-3">
								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="amount">Amount</label>
										<input type="number" id="amount" name="amount" class="form-control form-control-lg <?php $validation->hasError('amount') && print 'is-invalid'; ?>" placeholder="Amount" value="<?= old('amount') ?? esc($withdraw->amount); ?>">
										<div class="invalid-feedback"><?= $validation->getError('amount') ?></div>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="comment">Comment</label>
										<textarea id="comment" name="comment" class="form-control form-control-lg <?php $validation->hasError('comment') && print 'is-invalid'; ?>" placeholder="Comment"><?= old('comment') ?? esc($withdraw->comment); ?></textarea>
										<div class="invalid-feedback"><?= $validation->getError('comment') ?></div>
									</div>
								</div>
								<div class="col-lg-6">
									<?php $user = [];
									if (is($users, 'array')) $user = array_map(static function ($data) use ($user) {
										return $user[$data->id] = ucwords($data->firstname . ' ' . $data->lastname . ' => ' . formatCurrency($data->balance));
									}, $users); ?>
									<?= select_box('user_id', 'User', $user, $validation, $withdraw->user_id); ?>
								</div>
								<div class="col-lg-6">
									<?= select_box('status', 'Status', [
										'approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected',
									], $validation, $withdraw->status); ?>
								</div>

							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Update Withdraw</button>
							</div>

						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
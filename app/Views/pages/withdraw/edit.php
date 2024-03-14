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
				<div class="col-lg-8">
					<div class="card">
						<div class="card-body p-5">
							<div class="row mb-3">
								<div class="col-lg-6">
									<div class="form-group mb-4">
										<label class="mb-2" for="amount">Amount</label>
										<input type="number" id="amount" name="amount" class="form-control form-control-lg <?php $validation->hasError('amount') && print 'is-invalid'; ?>" placeholder="Amount" value="<?= old('amount') ?? esc($withdraw->amount); ?>">
										<div class="invalid-feedback"><?= $validation->getError('amount') ?></div>
									</div>
								</div>
								<div class="col-lg-6">
									<?= select_box('status', 'Status', !empty($account) ? [
										'approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected',
									] : ['pending' => 'Pending', 'rejected' => 'Rejected'], $validation, $withdraw->status); ?>
								</div>
								<div class="col-lg-12">
									<div class="form-group mb-4">
										<label class="mb-2" for="comment">Comment</label>
										<textarea id="comment" name="comment" class="form-control form-control-lg <?php $validation->hasError('comment') && print 'is-invalid'; ?>" placeholder="Comment"><?= old('comment') ?? esc($withdraw->comment); ?></textarea>
										<div class="invalid-feedback"><?= $validation->getError('comment') ?></div>
									</div>
								</div>
							</div>

							<div class="mt-3">
								<button type="submit" class="btn btn-lg btn-success">Update Withdraw</button>
							</div>

						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="card">
						<div class="card-body">
							<div class="d-flex align-items-center">
								<div class="flex-shrink-0">
									<?= image($user->profile_pic, $user->firstname, $user->lastname) ?>
								</div>
								<div class="flex-grow-1 ms-3">
									<p class="lead text-truncate mb-1"><?= ucfirst($user->firstname); ?>&nbsp;<?= ucfirst($user->lastname); ?></p>
									<a href="mailto://<?= $user->email ?>" class="text-truncate"><?= $user->email ?></a>
									<a class="d-block" href="tel://<?= $user->phone ?>" class="text-truncate">+<?= $user->phone ?></a>
								</div>
							</div>
							<div class="mt-5">
								<h1>Bank Details</h1>
								<?php if (!empty($account)) : ?>
									<table class="table table-bordered table-hover table-lg table-responsive table-striped">
										<thead>
											<tr>
												<th>Title</th>
												<th>Content</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>Bank Code</td>
												<td><?= $account->account_code; ?></td>
												<input type="hidden" name="bank_code" value="<?= $account->account_code; ?>">
											</tr>
											<tr>
												<td>Bank Name</td>
												<td><?= $account->bank_name; ?></td>
											</tr>
											<tr>
												<td>Account Holdername</td>
												<td><?= $account->account_holdername; ?></td>
											</tr>
											<tr>
												<td>Account Number</td>
												<td><?= $account->account_number; ?></td>
												<input type="hidden" name="account_number" value="<?= $account->account_number; ?>">
											</tr>
										</tbody>
									</table>
								<?php else : ?>
									<p>Default account not found.</p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
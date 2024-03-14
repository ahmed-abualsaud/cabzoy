<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3 class="text-capitalize"><?= humanize($role, '-'); ?></h3>
				<p class="text-subtitle text-muted">For <?= humanize($role, '-'); ?> to check their list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active text-capitalize" aria-current="page"><?= humanize($role, '-'); ?></li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section">
		<div class="card">
			<div class="card-header">
				<button class="csv btn btn-primary">Export CSV</button>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table" id="table1">
						<thead>
							<tr>
								<th>#</th>
								<th>Name</th>
								<th>Mobile</th>
								<th>Status</th>
								<th>Banned</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($users, 'array')) foreach ($users as $user) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<?= image($user->profile_pic, $user->firstname, $user->lastname); ?>
											</div>
											<div class="flex-grow-1 ms-3">
												<p class="lead mb-1"><?= $user->name; ?></p>
												<h3>
													<a href="<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? 'javascript:void(0)' : "mailto://$user->email"; ?>" class="text-truncate text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Send mail to user">
														<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($user->email, 2, true) : $user->email; ?>
													</a>
												</h3>
											</div>
										</div>
									</td>
									<td>
										<a href="<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? 'javascript:void(0)' : "tel://$user->phone"; ?>" class="text-truncate text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Call the user">
											+<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($user->phone, 3) : $user->phone ?>
										</a>
									</td>
									<td>
										<?= badge(
											['active' => 'success', 'not verified' => 'warning', 'email verified but not active' => 'warning', 'not active' => 'danger'],
											(int) $user->active === 1 ? 'active' : ((int) $user->active === 2 ? 'email verified but not active' : (!config('Settings')->enableAutoVerifyUser ? 'not verified' : 'not active'))
										) ?>
									</td>
									<td>
										<?= badge(
											['not banned' => 'success', 'banned' => 'danger'],
											$user->status === 'banned' ? 'banned' : 'not banned'
										) ?>
									</td>
									<td>
										<!-- <a href="<?= route_to('show_' . singular($role), $user->id); ?>" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="Show <?= singular($role) ?>">
											<i data-feather="eye"></i>
										</a> -->
										<?php if (perm($role, 'update')) : ?>
											<a href="<?= route_to('update_' . singular($role), $user->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update <?= singular($role) ?>">
												<i data-feather="edit"></i>
											</a>
										<?php endif ?>

										<?php if (perm($role, 'delete')) : ?>
											<a href="<?= route_to('delete_' . singular($role), $user->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete <?= singular($role) ?>">
												<i data-feather="trash"></i>
											</a>
										<?php endif ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</section>
</div>
<?= $this->endSection(); ?>
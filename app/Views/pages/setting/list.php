<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Settings</h3>
				<p class="text-subtitle text-muted">For user to check settings list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Settings</li>
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
								<th class="w-25">Name</th>
								<th>Content</th>
								<?php if (perm('settings', 'update')) : ?>
									<th class="text-center" data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($settings, 'array')) foreach ($settings as $setting) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td>
										<h4><?= $setting->name; ?></h4>
										<p class="lead text-truncate"><?= $setting->summary; ?></p>
									</td>
									<td>
										<?php if ($setting->datatype === 'image') : ?>
											<img src="<?= $setting->content; ?>" style="width: auto; height: 100px" class="rounded border border-primary bg-primary">
										<?php elseif ($setting->datatype === 'uri') : ?>
											<iframe src="<?= esc($setting->content); ?>" class="w-100 rounded m-2 border border-primary"></iframe>
										<?php elseif ($setting->datatype === 'bool') : ?>
											<?= (int) $setting->content === 1 ? 'Enabled' : 'Disabled' ?>
										<?php elseif (!in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) && (str_contains(strtolower($setting->name), 'key') || str_contains(strtolower($setting->name), 'api'))) : ?>
											<?= esc(encrypt($setting->content, 6)); ?>
										<?php else : ?>
											<?= esc($setting->content); ?>
										<?php endif ?>
									</td>
									<?php if (perm('settings', 'update')) : ?>
										<td class="text-truncate text-center">
											<?php if (perm('settings', 'update')) : ?>
												<a href="<?= route_to('update_setting', $setting->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Setting">
													<i data-feather="edit"></i>
												</a>
											<?php endif ?>
										</td>
									<?php endif ?>
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
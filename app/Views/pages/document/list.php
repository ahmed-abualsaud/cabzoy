<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Documents</h3>
				<p class="text-subtitle text-muted">For user to check documents list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Documents</li>
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
								<th>Document Name</th>
								<th>Front Image</th>
								<th>Back Image</th>
								<th>Comment</th>
								<th>Status</th>
								<th data-type="date" data-format="MMM DD, YYYY">User/Company</th>
								<?php if (perm('documents', 'show, update, delete', true)) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($documents, 'array')) foreach ($documents as $document) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td>
										<p class="mb-0 text-uppercase"><?= $document->document_title; ?></p>
										<h4><?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($document->document_number, 3) : $document->document_number; ?></h4>
									</td>
									<td>
										<?php if (!empty($document->document_front_image)) :  ?>
											<?= image($document->document_front_image, $document->document_title, strrev($document->document_title)); ?>
										<?php else : ?>
											Image not exists
										<?php endif; ?>
									</td>
									<td>
										<?php if (!empty($document->document_back_image)) :  ?>
											<?= image($document->document_back_image, $document->document_title, strrev($document->document_title)); ?>
										<?php else : ?>
											Image not exists
										<?php endif; ?>
									</td>
									<td>
										<?= $document->document_comment; ?>
									</td>
									<td>
										<?= badge(['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'], $document->document_status); ?>
									</td>
									<td>
										<?php if (!empty($document->user)) : ?>
											<div class="d-flex align-items-center">
												<div class="flex-shrink-0">
													<?= image($document->user->profile_pic, $document->user->firstname, $document->user->lastname) ?>
												</div>
												<div class="flex-grow-1 ms-3">
													<p class="lead text-truncate mb-1"><?= ucfirst($document->user->firstname); ?>&nbsp;<?= ucfirst($document->user->lastname); ?></p>
													<span class="text-truncate"><?= $document->getCreatedAt(); ?></span>
												</div>
											</div>
										<?php elseif (!empty($document->company)) : ?>
											<div class="d-flex align-items-center">
												<div class="flex-shrink-0">
													<?= image($document->company->company_image, $document->company->company_name, $document->company->company_name) ?>
												</div>
												<div class="flex-grow-1 ms-3">
													<p class="lead text-truncate mb-1"><?= ucfirst($document->user->firstname); ?>&nbsp;<?= ucfirst($document->user->lastname); ?></p>
													<span class="text-truncate"><?= $document->getCreatedAt(); ?></span>
												</div>
											</div>
										<?php endif; ?>
									</td>
									<?php if (perm('documents', 'show, update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('documents', 'update')) : ?>
												<a href="<?= route_to('update_document', $document->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Document">
													<i data-feather="edit"></i>
												</a>
											<?php endif ?>

											<?php if (perm('documents', 'delete')) : ?>
												<a href="<?= route_to('delete_document', $document->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Document">
													<i data-feather="trash"></i>
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
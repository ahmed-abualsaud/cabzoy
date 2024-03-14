<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Orders</h3>
				<p class="text-subtitle text-muted">For user to check orders list</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active" aria-current="page">Orders</li>
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
								<th>User</th>
								<th>Details</th>
								<th>Location</th>
								<th>Driver</th>
								<th data-type="date" data-format="MMM DD, YYYY">Booking At</th>
								<?php if (perm('orders', 'show, update, delete', true)) : ?>
									<th data-sortable="false">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							<?php if (is($orders, 'array')) foreach ($orders as $order) : ?>
								<tr>
									<td><?= ++$i; ?></td>
									<td>
										<?php if (!empty($order->order_users) && is_array($order->order_users)) : ?>
											<?php foreach ($order->order_users as $orderUser) : ?>
												<div class="d-flex align-items-center">
													<div class="flex-shrink-0">
														<?= image($orderUser->user->profile_pic, $orderUser->user->firstname, strrev($orderUser->user->lastname)) ?>
													</div>
													<div class="flex-grow-1 ms-3">
														<p class="lead text-truncate mb-1 text-capitalize">
															<?= $orderUser->user->firstname . ' ' . $orderUser->user->lastname; ?>
														</p>
														<a href="<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? 'javascript:void(0)' : "tel://" . $orderUser->user->phone; ?>" class="text-truncate text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Call the user">
															+<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($orderUser->user->phone, 3) : $orderUser->user->phone ?>
														</a>
														<br>
														<a href="<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? 'javascript:void(0)' : "mailto://" . $orderUser->user->email; ?>" class="text-truncate text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Send mail to user">
															<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($orderUser->user->email, 2, true) : $orderUser->user->email; ?>
														</a>
													</div>
												</div>
											<?php endforeach; ?>
										<?php endif; ?>
									</td>
									<td>
										<p class="my-1">
											Distance: <span class="font-bold mr-2"><?= $order->getOrderKms(true); ?></span>
										</p>
										<p class="my-1">
											Type:
											<span class="font-bold mr-2"><?= $order->order_vehicle; ?></span>
											<?= badge([
												'booked'     => 'info',
												'dispatched' => 'info',
												'cancel'     => 'danger',
												'new'        => 'warning',
												'ongoing'    => 'primary',
												'complete'   => 'success',
												'picked'     => 'secondary',
												'arrived'    => 'secondary',
											], $order->order_status); ?>
										</p>
										<p class="my-1">
											Price:
											<span class="font-bold mr-2"><?= $order->getOrderPrice(true); ?></span>
											<?= badge(['not-paid' => 'danger', 'paid' => 'success'], $order->is_paid); ?>
										</p>

									</td>
									<td>
										<?php if (!empty($order->order_locations) && is_array($order->order_locations)) : ?>
											<?php foreach ($order->order_locations as $orderLocation) : ?>
												<p>
													<span class="text-<?= $orderLocation->order_location_type === 'pickup' ? 'success' : 'danger' ?>">
														<i data-feather="arrow-right-circle"></i>
													</span>
													<span><?= $orderLocation->order_location_text; ?></span>
												</p>
											<?php endforeach; ?>
										<?php endif; ?>
									</td>
									<td>
										<?php if (!empty($order->order_drivers) && is_array($order->order_drivers)) : ?>
											<?php foreach ($order->order_drivers as $orderDriver) : ?>
												<?php if ($orderDriver->action !== 'rejected') : ?>
													<div class="d-flex align-items-center">
														<div class="flex-shrink-0">
															<?= image($orderDriver->user->profile_pic, $orderDriver->user->firstname, strrev($orderDriver->user->lastname)) ?>
														</div>
														<div class="flex-grow-1 ms-3">
															<p class="lead text-truncate mb-1 text-capitalize">
																<?= $orderDriver->user->firstname . ' ' . $orderDriver->user->lastname; ?>
																<?= badge(['rejected' => 'danger', 'pending' => 'warning', 'accept' => 'success'], $orderDriver->action); ?>
															</p>
															<a href="<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? 'javascript:void(0)' : "tel://" . $orderDriver->user->phone; ?>" class="text-truncate text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Call the user">
																+<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($orderDriver->user->phone, 3) : $orderDriver->user->phone ?>
															</a>
															<br>
															<a href="<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? 'javascript:void(0)' : "mailto://" . $orderDriver->user->email; ?>" class="text-truncate text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Send mail to user">
																<?= !in_groups('creators') && (config('Settings')->enableHideSensitiveInfo || in_groups('demo-admins')) ? encrypt($orderDriver->user->email, 2, true) : $orderDriver->user->email; ?>
															</a>
														</div>
													</div>
												<?php endif; ?>
											<?php endforeach; ?>
										<?php else : ?>
											<p class="text-center">No driver assigned</p>
										<?php endif; ?>
									</td>

									<td>
										<?= $order->getBookingAt() ?>
									</td>
									<?php if (perm('orders', 'update, delete', true)) : ?>
										<td class="text-truncate">
											<?php if (perm('orders', 'update')) : ?>
												<a href="<?= route_to('update_order', $order->id); ?>" class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Order">
													<i data-feather="edit"></i>
												</a>
											<?php endif ?>

											<?php if (perm('orders', 'delete')) : ?>
												<a href="<?= route_to('delete_order', $order->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Order">
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
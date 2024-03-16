<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
<h3><?= lang("Lang.statistics", [(config('Settings')->siteName ?? lang("Lang.dashboard"))]) ?></h3>
</div>
<div class="page-content">
	<section class="row">
		<div class="col-12 col-lg-9">
			<div class="row">
				<div class="col-12 col-md-6 col-lg-4 col-xl-3">
					<div class="card overflow-auto">
						<div class="card-body">
							<div class="d-flex">
								<div class="flex-shrink-0">
									<div class="stats-icon text-white red">
										<i data-feather="briefcase"></i>
									</div>
								</div>
								<div class="flex-grow-1 ms-3">
									<h6 class="text-muted font-semibold"><?= lang("Lang.totalOrders") ?></h6>
									<h6 class="font-extrabold mb-0"><?= $countOrders ?? 0; ?></h6>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-4 col-xl-3">
					<div class="card overflow-auto">
						<div class="card-body">
							<div class="d-flex">
								<div class="flex-shrink-0">
									<div class="stats-icon text-white purple">
										<i data-feather="user"></i>
									</div>
								</div>
								<div class="flex-grow-1 ms-3">
									<h6 class="text-muted font-semibold"><?= lang("Lang.totalUsers") ?></h6>
									<h6 class="font-extrabold mb-0"><?= $countUsers ?? 0; ?></h6>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-4 col-xl-3">
					<div class="card overflow-auto">
						<div class="card-body">
							<div class="d-flex">
								<div class="flex-shrink-0">
									<div class="stats-icon text-white blue">
										<i data-feather="users"></i>
									</div>
								</div>
								<div class="flex-grow-1 ms-3">
									<h6 class="text-muted font-semibold"><?= lang("Lang.totalDrivers") ?></h6>
									<h6 class="font-extrabold mb-0"><?= $countDrivers ?? 0 ?></h6>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-4 col-xl-3">
					<div class="card overflow-auto">
						<div class="card-body">
							<div class="d-flex">
								<div class="flex-shrink-0">
									<div class="stats-icon text-white green">
										<i data-feather="navigation"></i>
									</div>
								</div>
								<div class="flex-grow-1 ms-3">
									<h6 class="text-muted font-semibold"><?= lang("Lang.onlinePayment") ?></h6>
									<h6 class="font-extrabold mb-0"><?= $countOnlineOrders ?? 0; ?></h6>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-4 col-xl-3">
					<div class="card overflow-auto">
						<div class="card-body">
							<div class="d-flex">
								<div class="flex-shrink-0">
									<div class="stats-icon text-white bg-dark">
										<i data-feather="x-circle"></i>
									</div>
								</div>
								<div class="flex-grow-1 ms-3">
									<h6 class="text-muted font-semibold"><?= lang("Lang.cancelledOrders") ?></h6>
									<h6 class="font-extrabold mb-0"><?= $countCancelOrders ?? 0; ?></h6>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-4 col-xl-3">
					<div class="card overflow-auto">
						<div class="card-body">
							<div class="d-flex">
								<div class="flex-shrink-0">
									<div class="stats-icon text-white bg-warning">
										<i data-feather="clock"></i>
									</div>
								</div>
								<div class="flex-grow-1 ms-3">
									<h6 class="text-muted font-semibold"><?= lang("Lang.scheduledOrders") ?></h6>
									<h6 class="font-extrabold mb-0"><?= $countAdvancedOrders ?? 0; ?></h6>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-4 col-xl-3">
					<div class="card overflow-auto">
						<div class="card-body">
							<div class="d-flex">
								<div class="flex-shrink-0">
									<div class="stats-icon text-white bg-danger">
										<i data-feather="dollar-sign"></i>
									</div>
								</div>
								<div class="flex-grow-1 ms-3">
									<h6 class="text-muted font-semibold"><?= lang("Lang.codPaymnet") ?></h6>
									<h6 class="font-extrabold mb-0"><?= $countCodOrders ?? 0; ?></h6>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-4 col-xl-3">
					<div class="card overflow-auto">
						<div class="card-body">
							<div class="d-flex">
								<div class="flex-shrink-0">
									<div class="stats-icon text-white bg-secondary">
										<i data-feather="credit-card"></i>
									</div>
								</div>
								<div class="flex-grow-1 ms-3">
									<h6 class="text-muted font-semibold"><?= lang("Lang.totalEarnings") ?></h6>
									<h6 class="font-extrabold mb-0"><?= formatCurrency($totalCompanyWalletBalance ?? 0); ?></h6>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-12 col-md-6 col-lg-4 col-xl-3">
					<div class="card overflow-auto">
						<div class="card-body">
							<div class="d-flex">
								<div class="flex-shrink-0">
									<div class="stats-icon text-white blue">
										<i data-feather="credit-card"></i>
									</div>
								</div>
								<div class="flex-grow-1 ms-3">
									<h6 class="text-muted font-semibold"><?= lang("Lang.totalTransactions") ?></h6>
									<h6 class="font-extrabold mb-0"><?= formatCurrency($totalTransactionBalance ?? 0); ?></h6>

								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-4 col-xl-3">
					<div class="card overflow-auto">
						<div class="card-body">
							<div class="d-flex">
								<div class="flex-shrink-0">
									<div class="stats-icon text-white bg-secondary">
										<i data-feather="dollar-sign"></i>
									</div>
								</div>
								<div class="flex-grow-1 ms-3">
									<h6 class="text-muted font-semibold"><?= lang("Lang.totalWallet") ?></h6>
									<h6 class="font-extrabold mb-0"><?= formatCurrency($totalWalletBalance ?? 0); ?></h6>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-4 col-xl-3">
					<div class="card overflow-auto">
						<div class="card-body">
							<div class="d-flex">
								<div class="flex-shrink-0">
									<div class="stats-icon text-white green">
										<i data-feather="plus"></i>
									</div>
								</div>
								<div class="flex-grow-1 ms-3">
									<h6 class="text-muted font-semibold"><?= lang("Lang.totalDebit") ?></h6>
									<h6 class="font-extrabold mb-0"><?= formatCurrency($totalWalletDebit ?? 0); ?></h6>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-4 col-xl-3">
					<div class="card overflow-auto">
						<div class="card-body">
							<div class="d-flex">
								<div class="flex-shrink-0">
									<div class="stats-icon text-white bg-warning">
										<i data-feather="minus"></i>
									</div>
								</div>
								<div class="flex-grow-1 ms-3">
									<h6 class="text-muted font-semibold"><?= lang("Lang.totalCredit") ?></h6>
									<h6 class="font-extrabold mb-0"><?= formatCurrency($totalWalletCredit ?? 0); ?></h6>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="card overflow-auto">
						<div class="card-header">
							<h4><?= lang("Lang.todaysRides") ?></h4>
						</div>
						<div class="card-body">
							<div id="daily-order-history"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="card overflow-auto">
				<div class="card-body">
					<h4 class="card-title"><?= lang("Lang.history") ?></h4>
					<p class="card-text"><?= lang("Lang.bookingHistoryOfAll") ?></p>

					<div class="table-responsive">
						<table class="table" id="table1">
							<thead>
								<tr>
									<th>#</th>
									<th><?= lang("Lang.user") ?></th>
									<th><?= lang("Lang.details") ?></th>
									<th><?= lang("Lang.location") ?></th>
									<th><?= lang("Lang.driver") ?></th>
									<th data-type="date" data-format="MMM DD, YYYY"><?= lang("Lang.bookingAt") ?></th>
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
													<p class="lead text-truncate mb-1 text-capitalize">
														<?= $orderUser->user->firstname . ' ' . $orderUser->user->lastname; ?>
													</p>
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
														<p class="lead text-truncate mb-1 text-capitalize">
															<?= $orderDriver->user->firstname . ' ' . $orderDriver->user->lastname; ?>
														</p>
													<?php endif; ?>
												<?php endforeach; ?>
											<?php else : ?>
												<p>No driver assigned</p>
											<?php endif; ?>
										</td>
										<td>
											<?= badge([
												'booked'     => 'info',
												'dispatched' => 'info',
												'cancel'     => 'danger',
												'new'        => 'warning',
												'ongoing'    => 'primary',
												'complete'   => 'success',
												'picked'     => 'secondary',
											], $order->order_status); ?>
										</td>
										<td>
											<?= $order->getBookingAt() ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-12 col-lg-3">
			<div class="card overflow-auto">
				<div class="card-body py-4 px-5">
					<div class="d-flex align-items-center">
						<div class="avatar avatar-xl">
							<?= image(user()->profile_pic, user()->firstname, strrev(user()->lastname)) ?>
						</div>
						<div class="ms-3 name">
							<h5 class="font-bold text-capitalize"><?= user()->name; ?></h5>
							<h6 class="text-muted mb-0">@<?= user()->username; ?></h6>
						</div>
					</div>
				</div>
			</div>
			<div class="card">
				<div class="card-header">
					<h4><?= lang("Lang.orderType") ?></h4>
				</div>
				<div class="card-body">
					<div id="order-type"></div>
				</div>
			</div>
			<div class="card">
				<div class="card-header">
					<h4><?= lang("Lang.paymentType") ?></h4>
				</div>
				<div class="card-body">
					<div id="payment-type"></div>
				</div>
			</div>
		</div>

	</section>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="assets/vendors/apexcharts/apexcharts.min.js"></script>
<script>
	var optionsDailyOrder = {
		annotations: {
			position: 'back'
		},
		dataLabels: {
			enabled: false
		},
		chart: {
			type: 'bar',
			height: 300
		},
		series: [{
			name: 'rides',
			data: <?= $orderHourCount; ?>
		}],
		colors: '#fc0a04',
	}

	let optionsPaymentType = {
		series: [<?= config('Settings')->enableCorporateAccount &&  config('Settings')->enableCorporatePayment ? "$countOnlineOrders, $countCorporateOrders, $countCodOrders" : "$countOnlineOrders, $countCodOrders" ?>],
		labels: [<?= config('Settings')->enableCorporateAccount &&  config('Settings')->enableCorporatePayment ? "'Online Payment', 'Corporate Payment', 'COD Payment'" : "'Online Payment','COD Payment'" ?>],
		// labels: [<?= config('Settings')->enableCorporateAccount &&  config('Settings')->enableCorporatePayment ? lang("Lang.onlinePayment").", ".lang("Lang.corporatePayment").", ".lang("Lang.codPayment") : lang("Lang.onlinePayment").", ".lang("Lang.codPayment") ?>],
		colors: [<?= config('Settings')->enableCorporateAccount &&  config('Settings')->enableCorporatePayment ? "'#fc0a04', '#ffd451', '#55c6e8'" : "'#fc0a04', '#55c6e8'" ?>],
		chart: {
			type: 'donut',
			width: '100%',
			height: '350px'
		},
		legend: {
			position: 'bottom'
		},
		plotOptions: {
			pie: {
				donut: {
					size: '30%'
				}
			}
		}
	}

	let optionsOrderType = {
		series: [<?= $countCancelOrders; ?>, <?= $countCompleteOrders; ?>, <?= $countOngoingOrders; ?>],
		labels: ['Cancel', 'Complete', 'On Going'],
		// labels: [<?= lang("Lang.cancel") ?>, <?= lang("Lang.complete") ?>, <?= lang("Lang.onGoing") ?>],
		colors: ['#fc0a04', '#5ddab4', '#55c6e8'],
		chart: {
			type: 'pie',
			width: '100%',
			height: '350px'
		},
		legend: {
			position: 'bottom'
		},
		plotOptions: {
			pie: {
				donut: {
					size: '30%'
				}
			}
		}
	}

	var chartDailyOrder = new ApexCharts(document.getElementById('daily-order-history'), optionsDailyOrder);
	var chartPaymentType = new ApexCharts(document.getElementById('payment-type'), optionsPaymentType);
	var chartOrderType = new ApexCharts(document.getElementById('order-type'), optionsOrderType);

	chartDailyOrder.render();
	chartOrderType.render();
	chartPaymentType.render()
</script>
<?= $this->endSection(); ?>
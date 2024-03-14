<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<div class="page-title">
		<div class="row">
			<div class="col-12 col-md-6 order-md-1 order-last">
				<h3>Update Order</h3>
				<p class="text-subtitle text-muted">For user to update order</p>
			</div>
			<div class="col-12 col-md-6 order-md-2 order-first">
				<nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?= route_to('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item"><a href="<?= route_to('orders'); ?>">Orders</a></li>
						<li class="breadcrumb-item active" aria-current="page">Update Order</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
	<section class="section mt-5">
		<form action="<?= route_to('order') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field(); ?>
			<div class="card">
				<div class="card-body">
					<form action="<?= route_to('dispatch') ?>" method="post">
						<div class="row">
							<div class="col-lg-4">
								<?php $driver = [];
								$driver_id = null;
								if (!empty($order->order_drivers) && is_array($order->order_drivers)) {
									foreach ($order->order_drivers as $order_driver) {
										if ($order_driver->action !== 'rejected') $driver_id = $order_driver->driver_id;
									}
								}

								$driver = array_map(static function ($data) use ($driver) {
									return $driver[$data->id] = ucwords($data->firstname . ' ' . $data->lastname);
								}, $drivers); ?>
								<?= select_box('driver_id', 'Driver', $driver, $validation, old('driver_id') ?? $driver_id); ?>
								<small>Not found driver,<a href="<?= route_to('add_driver'); ?>" class="btn-btn-primary btn-sm">Create driver here</a></small>
							</div>
							<div class="col-lg-4">
								<?= select_box('is_paid', 'Payment Status', ['paid' => 'Paid', 'not-paid' => 'Not Paid'], $validation, $order->is_paid); ?>
							</div>
							<div class="col-lg-4">
								<?= select_box('order_status', 'Order Status', [
									'new'        => 'New Order',
									'booked'     => 'Order Booked',
									'cancel'     => 'Cancel Order',
									'ongoing'    => 'Ongoing Ride',
									'complete'   => 'Complete Order',
									'picked'     => 'User Picked Up',
									'arrived'    => 'Driver Arrived',
									'dispatched' => 'Dispatched (On the way) Driver',
								], $validation, $order->order_status); ?>
							</div>
							<div class="col-lg-12 mt-3 ">
								<button class="btn btn-primary btn-lg btn-block" type="submit" name="bookFromDispatch" value="dispatch">Update</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</form>
	</section>
</div>
<?= $this->endSection(); ?>
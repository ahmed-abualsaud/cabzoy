<div id="sidebar" class="active">
	<div class="sidebar-wrapper active">
		<div class="sidebar-header">
			<div class="d-flex justify-content-between">
				<div class="logo">
					<a href="<?= route_to('dashboard'); ?>">
						<?php if (config('Settings')->siteLogo) : ?>
							<img src="<?= site_url(config('Settings')->siteLogo) ?>" style="height: 100%; width: 100%; border-radius: 5%;" alt="Logo">
						<?php else : ?>
							<h2><?= config('Settings')->siteName ?? env('app.name', 'Fab IT Hub'); ?></h2>
						<?php endif ?>
					</a>
				</div>
				<div class="toggler">
					<a href="#" class="sidebar-hide d-xl-none d-block"><i data-feather="x" class="align-top"></i></a>
				</div>
			</div>
		</div>
		<div class="sidebar-menu">
			<ul class="menu">
				<?php if (!in_groups('company-admins')) : ?>
					<li class="sidebar-item <?php isRoute('dashboard', false) && print 'active'; ?>">
						<a href="<?= route_to('dashboard'); ?>" class="sidebar-link">
							<i data-feather="monitor"></i>
							<span>Main Dashboard</span>
						</a>
					</li>
				<?php endif; ?>

				<?php foreach (directory_map(MODULESPATH, 0) as $folder => $files) {
					if (!empty($folder)) {
						$folder = str_replace('/', '\\', ucfirst($folder));
						$filePath = "Modules\\{$folder}Views\layouts\sidebar";
						if (file_exists(ROOTPATH . str_replace('\\', '/', $filePath) . '.php')) print($this->include($filePath));
					}
				} ?>

				<?php if (perm('dispatch', 'read, add') && config('Settings')->enableBooking) : ?>
					<li class="sidebar-item <?php isRoute('dispatch', false) && print 'active'; ?>">
						<a href="<?= route_to('dispatch'); ?>" class="sidebar-link">
							<i data-feather="airplay"></i>
							<span><?= in_groups('company-admins') ? 'Ride Booker' : 'Dispatch Dashboard' ?></span>
						</a>
					</li>
				<?php endif; ?>

				<?php if (perm('bird_eyes', 'read')) : ?>
					<li class="sidebar-item <?php isRoute('bird-eye', false) && print 'active'; ?>">
						<a href="<?= route_to('bird-eye'); ?>" class="sidebar-link">
							<i data-feather="target"></i>
							<span>Bird Eye View</span>
						</a>
					</li>
				<?php endif; ?>

				<?php if (perm('categories', 'read, add', true)) : ?>
					<li class="sidebar-item <?php isRoute('categories') && print 'active'; ?> has-sub">
						<a href="#" class="sidebar-link">
							<i data-feather="layers"></i>
							<span>Categories</span>
						</a>
						<ul class="submenu <?php isRoute('categories') && print 'active'; ?>">
							<?php if (perm('categories', 'read')) : ?>
								<li class="submenu-item <?php isRoute('list/categories', false) && print 'active'; ?>">
									<a href="<?= route_to('categories'); ?>">All Categories</a>
								</li>
							<?php endif; ?>
							<?php if (perm('categories', 'add')) : ?>
								<li class="submenu-item <?php isRoute('add/category', false) && print 'active'; ?>">
									<a href="<?= route_to('add_category'); ?>">Add Category</a>
								</li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>

				<?php if (perm('orders', 'read, add', true) && config('Settings')->enableBooking) : ?>
					<li class="sidebar-item <?php isRoute('orders') && print 'active'; ?>">
						<a href="<?= route_to('orders'); ?>" class="sidebar-link">
							<i data-feather="package"></i>
							<span>Booking</span>
						</a>
					</li>
				<?php endif; ?>

				<?php if (perm('cards', 'read, add', true)) : ?>
					<li class="sidebar-item <?php isRoute('cards') && print 'active'; ?> has-sub">
						<a href="#" class="sidebar-link">
							<i data-feather="credit-card"></i>
							<span>Cards</span>
						</a>
						<ul class="submenu <?php isRoute('cards') && print 'active'; ?>">
							<?php if (perm('cards', 'read')) : ?>
								<li class="submenu-item <?php isRoute('list/cards', false) && print 'active'; ?>">
									<a href="<?= route_to('cards'); ?>">All Cards</a>
								</li>
							<?php endif; ?>
							<?php if (perm('cards', 'add')) : ?>
								<li class="submenu-item <?php isRoute('add/cards', false) && print 'active'; ?>">
									<a href="<?= route_to('add_card'); ?>">Add Card</a>
								</li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>

				<?php if (perm('accounts', 'read, add', true)) : ?>
					<li class="sidebar-item <?php isRoute(route_to('accounts')) && print 'active'; ?> has-sub">
						<a href="#" class="sidebar-link">
							<i data-feather="at-sign"></i>
							<span>Accounts</span>
						</a>
						<ul class="submenu <?php isRoute(route_to('accounts')) && print 'active'; ?>">
							<?php if (perm('accounts', 'read')) : ?>
								<li class="submenu-item <?php isRoute(route_to('accounts'), false) && print 'active'; ?>">
									<a href="<?= route_to('accounts'); ?>">All Accounts</a>
								</li>
							<?php endif; ?>
							<?php if (perm('accounts', 'add')) : ?>
								<li class="submenu-item <?php isRoute(route_to('add_account'), false) && print 'active'; ?>">
									<a href="<?= route_to('add_account'); ?>">Add Account</a>
								</li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>

				<?php if (perm('documents', 'read, add', true) && config('Settings')->enableDocumentVerification) : ?>
					<li class="sidebar-item <?php isRoute(route_to('documents')) && print 'active'; ?> has-sub">
						<a href="#" class="sidebar-link">
							<i data-feather="file-text"></i>
							<span>Documents</span>
						</a>
						<ul class="submenu <?php isRoute(route_to('documents')) && print 'active'; ?>">
							<?php if (perm('documents', 'read')) : ?>
								<li class="submenu-item <?php isRoute(route_to('documents'), false) && print 'active'; ?>">
									<a href="<?= route_to('documents'); ?>">All Documents</a>
								</li>
							<?php endif; ?>
							<?php if (perm('documents', 'add')) : ?>
								<li class="submenu-item <?php isRoute(route_to('add_document'), false) && print 'active'; ?>">
									<a href="<?= route_to('add_document'); ?>">Add Document</a>
								</li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>

				<?php if (perm('withdraws', 'read, add', true)) : ?>
					<li class="sidebar-item <?php isRoute(route_to('withdraws')) && print 'active'; ?> has-sub">
						<a href="#" class="sidebar-link">
							<i data-feather="git-pull-request"></i>
							<span>Payout / Withdraw</span>
						</a>
						<ul class="submenu <?php isRoute(route_to('withdraws')) && print 'active'; ?>">
							<?php if (perm('withdraws', 'read')) : ?>
								<li class="submenu-item <?php isRoute(route_to('withdraws'), false) && print 'active'; ?>">
									<a href="<?= route_to('withdraws'); ?>">All Withdraws</a>
								</li>
							<?php endif; ?>
							<?php if (perm('withdraws', 'add')) : ?>
								<li class="submenu-item <?php isRoute(route_to('add_withdraw'), false) && print 'active'; ?>">
									<a href="<?= route_to('add_withdraw'); ?>">Add Withdraw</a>
								</li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>


				<?php if (perm('vehicles', 'read, add', true)) : ?>
					<li class="sidebar-item <?php (isRoute('list/vehicles', false) || isRoute('add/vehicle', false) || isRoute('update/vehicle', false)) && print 'active'; ?> has-sub">
						<a href="#" class="sidebar-link">
							<i data-feather="truck"></i>
							<span>Vehicles</span>
						</a>
						<ul class="submenu <?php (isRoute('list/vehicles', false) || isRoute('add/vehicle', false) || isRoute('update/vehicle', false)) && print 'active'; ?>">
							<?php if (perm('vehicles', 'read')) : ?>
								<li class="submenu-item <?php isRoute('list/vehicles', false) && print 'active'; ?>">
									<a href="<?= route_to('vehicles'); ?>">All Vehicles</a>
								</li>
							<?php endif; ?>
							<?php if (perm('vehicles', 'add')) : ?>
								<li class="submenu-item <?php isRoute('add/vehicle', false) && print 'active'; ?>">
									<a href="<?= route_to('add_vehicle'); ?>">Add Vehicle</a>
								</li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>

				<?php if (perm('vehicles', 'assign')) : ?>
					<li class="sidebar-item <?php isRoute('assign/vehicle', false) && print 'active'; ?>">
						<a href="<?= route_to('assign_vehicle'); ?>" class="sidebar-link">
							<i data-feather="briefcase"></i>
							<span>Assign Jobs</span>
						</a>
					</li>
				<?php endif; ?>

				<?php if (perm('refers', 'read, add', true) && config('Settings')->enableReferralSystem) : ?>
					<li class="sidebar-item <?php isRoute('refers') && print 'active'; ?>">
						<a href="<?= route_to('refers'); ?>" class="sidebar-link">
							<i data-feather="bell"></i>
							<span>Refers</span>
						</a>
					</li>
				<?php endif; ?>

				<?php if (perm('fares', 'read, add', true)) : ?>
					<li class="sidebar-item <?php url_is('*fare*') && print 'active'; ?> has-sub">
						<a href="#" class="sidebar-link">
							<i data-feather="dollar-sign"></i>
							<span>Fares</span>
						</a>
						<ul class="submenu <?php url_is('*fare*') && print 'active'; ?>">
							<?php if (config('Settings')->enableFareCategoryCalculation) : ?>
								<?php if (perm('fares', 'read')) : ?>
									<li class="submenu-item <?php isRoute('list/category-fares', false) && print 'active'; ?>">
										<a href="<?= route_to('category_fares'); ?>">All Vehicle type Fares</a>
									</li>
								<?php endif; ?>
								<?php if (perm('fares', 'add')) : ?>
									<li class="submenu-item <?php isRoute('add/category-fare', false) && print 'active'; ?>">
										<a href="<?= route_to('add_category_fare'); ?>">Add Vehicle type Fare</a>
									</li>
								<?php endif; ?>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>

				<?php if (perm('promos', 'read, add', true) && config('Settings')->enablePromoCode) : ?>
					<li class="sidebar-item <?php isRoute('promos') && print 'active'; ?> has-sub">
						<a href="#" class="sidebar-link">
							<i data-feather="percent"></i>
							<span>Promos</span>
						</a>
						<ul class="submenu <?php isRoute('promos') && print 'active'; ?>">
							<?php if (perm('promos', 'read')) : ?>
								<li class="submenu-item <?php isRoute('list/promos', false) && print 'active'; ?>">
									<a href="<?= route_to('promos'); ?>">All Promos</a>
								</li>
							<?php endif; ?>
							<?php if (perm('promos', 'add')) : ?>
								<li class="submenu-item <?php isRoute('add/promo', false) && print 'active'; ?>">
									<a href="<?= route_to('add_promo'); ?>">Add Promo</a>
								</li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>

				<?php if (perm('commissions', 'read, add', true) && config('Settings')->enableTaxCommissionCalculation) : ?>
					<li class="sidebar-item <?php isRoute('commissions') && print 'active'; ?> has-sub">
						<a href="#" class="sidebar-link">
							<i data-feather="dollar-sign"></i>
							<span>Commissions</span>
						</a>
						<ul class="submenu <?php isRoute('commissions') && print 'active'; ?>">
							<?php if (perm('commissions', 'read')) : ?>
								<li class="submenu-item <?php isRoute('list/commissions', false) && print 'active'; ?>">
									<a href="<?= route_to('commissions'); ?>">All Commissions</a>
								</li>
							<?php endif; ?>
							<?php if (perm('commissions', 'add')) : ?>
								<li class="submenu-item <?php isRoute('add/commission', false) && print 'active'; ?>">
									<a href="<?= route_to('add_commission'); ?>">Add Commission</a>
								</li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>

				<?php if (perm('transactions', 'add, read, mine', true)) : ?>
					<li class="sidebar-item <?php isRoute('transaction') && print 'active'; ?>">
						<a href="<?= route_to('transactions'); ?>" class="sidebar-link">
							<i data-feather="dollar-sign"></i>
							<span>Transactions</span>
						</a>
					</li>
				<?php endif; ?>

				<?php if (perm('wallets', 'read, add', true)) : ?>
					<li class="sidebar-item <?php isRoute('wallets') && print 'active'; ?> has-sub">
						<a href="#" class="sidebar-link">
							<i data-feather="dollar-sign"></i>
							<span>Wallets</span>
						</a>
						<ul class="submenu <?php isRoute('wallets') && print 'active'; ?>">
							<?php if (perm('wallets', 'read')) : ?>
								<li class="submenu-item <?php isRoute('list/wallets', false) && print 'active'; ?>">
									<a href="<?= route_to('wallets'); ?>">All Wallets</a>
								</li>
							<?php endif; ?>
							<?php if (perm('wallets', 'add')) : ?>
								<li class="submenu-item <?php isRoute('add/wallet', false) && print 'active'; ?>">
									<a href="<?= route_to('add_wallet'); ?>">Add Wallet</a>
								</li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>

				<?php if (perm('notifications', 'read, add', true)) : ?>
					<li class="sidebar-item <?php isRoute('notifications') && print 'active'; ?> has-sub">
						<a href="#" class="sidebar-link">
							<i data-feather="bell"></i>
							<span>Notifications</span>
						</a>
						<ul class="submenu <?php isRoute('notifications') && print 'active'; ?>">
							<?php if (perm('notifications', 'read')) : ?>
								<li class="submenu-item <?php isRoute('list/notifications', false) && print 'active'; ?>">
									<a href="<?= route_to('notifications'); ?>">All Notifications</a>
								</li>
							<?php endif; ?>
							<?php if (perm('notifications', 'add')) : ?>
								<li class="submenu-item <?php isRoute('add/notification', false) && print 'active'; ?>">
									<a href="<?= route_to('add_notification'); ?>">Add notification</a>
								</li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>

				<?php if (perm('reports', 'read, add', true)) : ?>
					<li class="sidebar-item <?php isRoute('report') && print 'active'; ?> has-sub">
						<a href="#" class="sidebar-link">
							<i data-feather="activity"></i>
							<span>Reports</span>
						</a>
						<ul class="submenu <?php isRoute('report') && print 'active'; ?>">
							<?php if (perm('reports', 'read')) : ?>
								<li class="submenu-item <?php isRoute('list/reports', false) && print 'active'; ?>">
									<a href="<?= route_to('reports'); ?>">All Reports</a>
								</li>
							<?php endif; ?>

						</ul>
					</li>
				<?php endif; ?>

				<?php if (perm('reviews', 'read, add', true)) : ?>
					<li class="sidebar-item <?php isRoute('review') && print 'active'; ?> has-sub">
						<a href="#" class="sidebar-link">
							<i data-feather="star"></i>
							<span>Rating & Reviews</span>
						</a>
						<ul class="submenu <?php isRoute('review') && print 'active'; ?>">
							<?php if (perm('reviews', 'read')) : ?>
								<li class="submenu-item <?php isRoute('list/reviews', false) && print 'active'; ?>">
									<a href="<?= route_to('reviews'); ?>">All Reviews</a>
								</li>
							<?php endif; ?>
							<?php if (perm('reviews', 'add') && false) : ?>
								<li class="submenu-item <?php isRoute('add/review', false) && print 'active'; ?>">
									<a href="<?= route_to('add_review'); ?>">Add Review</a>
								</li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>

				<?php foreach (user_groups(true) as $group) : ?>
					<?php if (perm($group->name, 'read, add', true)) : ?>
						<li class="sidebar-item <?php isRoute($group->name) && print 'active'; ?> has-sub">
							<a href="#" class="sidebar-link">
								<i data-feather="users"></i>
								<span class="text-capitalize"><?= humanize($group->name, '-'); ?></span>
							</a>
							<ul class="submenu <?php isRoute($group->name) && print 'active'; ?>">
								<?php if (perm($group->name, 'read')) : ?>
									<li class="submenu-item <?php isRoute("list/{$group->name}", false) && print 'active'; ?>">
										<a class="text-capitalize" href="<?= route_to($group->name); ?>">
											All <?= humanize($group->name, '-'); ?>
										</a>
									</li>
								<?php endif; ?>
								<?php if (perm($group->name, 'add')) : ?>
									<li class="submenu-item <?php isRoute("add/$group->name", false) && print 'active'; ?>">
										<a class="text-capitalize" href="<?= route_to('add_' . singular($group->name)); ?>">
											Add <?= singular(humanize($group->name, '-')); ?>
										</a>
									</li>
								<?php endif; ?>
							</ul>
						</li>
					<?php endif; ?>
				<?php endforeach ?>

				<?php if (perm('settings', 'read, add', true)) : ?>
					<li class="sidebar-item <?php isRoute('settings') && print 'active'; ?> has-sub">
						<a href="#" class="sidebar-link">
							<i data-feather="settings"></i>
							<span>Settings</span>
						</a>
						<ul class="submenu <?php isRoute('settings') && print 'active'; ?>">
							<?php if (perm('settings', 'read')) : ?>
								<li class="submenu-item <?php isRoute('list/settings', false) && print 'active'; ?>">
									<a href="<?= route_to('settings'); ?>">All Settings</a>
								</li>
							<?php endif; ?>
							<?php if (perm('settings', 'add') and false) : ?>
								<li class="submenu-item <?php isRoute('add/setting', false) && print 'active'; ?>">
									<a href="<?= route_to('add_setting'); ?>">Add Setting</a>
								</li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>

				<li class="sidebar-item">
					<a href="<?= route_to('logout'); ?>" class="sidebar-link">
						<i data-feather="log-out"></i>
						<span>Logout</span>
					</a>
				</li>

			</ul>
		</div>
	</div>
</div>
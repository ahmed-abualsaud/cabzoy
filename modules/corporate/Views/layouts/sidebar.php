<?php if (perm('companies', 'read, add, mine', true)) : ?>
	<li class="sidebar-item <?php isRoute('companies') && print 'active'; ?>">
		<a href="<?= route_to('companies'); ?>" class="sidebar-link">
			<i data-feather="server"></i>
			<span>Companies</span>
		</a>
	</li>
<?php endif; ?>
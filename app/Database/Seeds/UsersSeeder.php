<?php

namespace App\Database\Seeds;

class UsersSeeder extends BaseSeeder
{
	public function run()
	{
		if ($this->db->tableExists('auth_groups')) {
			$this->authorize->createGroup('super-admins', 'The Site Administrators with god-like powers.');
			$this->authorize->createGroup('admins', 'The Site Admins with less super-admins powers.');
			$this->authorize->createGroup('managers', 'The Site Manager with less admins powers.');
			$this->authorize->createGroup('fleets', 'The Fleet Manager with Vehicles powers.');
			$this->authorize->createGroup('drivers', 'The Drivers with less fleets powers.');
			$this->authorize->createGroup('users', 'The Users with less drivers powers.');


			$this->userModel->withGroup('super-admins')->save($this->user->fill([
				'active'            => 1,
				'force_pass_reset'  => 0,
				'is_phone_verified' => 1,
				'password'          => '1234',
				'firstname'         => 'demo',
				'is_online'         => 'offline',
				'lastname'          => 'superadmin',
				'username'          => 'super-admin',
				'phone'             => '917014569040',
				'email'             => 'super-admin@user.com',
				'profile_pic'       => 'uploads/users/1652413349_bb9b1d6f528c21c5c64d.png'
			]));

			$this->userModel->withGroup('admins')->save($this->user->fill([
				'active'            => 1,
				'force_pass_reset'  => 0,
				'is_phone_verified' => 1,
				'password'          => '1234',
				'firstname'         => 'demo',
				'lastname'          => 'admin',
				'is_online'         => 'offline',
				'username'          => 'demo-admin',
				'phone'             => '919876543210',
				'email'             => 'demo@admin.com',
			]));

			$this->userModel->withGroup('managers')->save($this->user->fill([
				'active'            => 1,
				'force_pass_reset'  => 0,
				'is_phone_verified' => 1,
				'password'          => '1234',
				'firstname'         => 'demo',
				'is_online'         => 'offline',
				'lastname'          => 'manager',
				'username'          => 'demo-manager',
				'phone'             => '919876543211',
				'email'             => 'demo@manager.com',
			]));

			$this->userModel->withGroup('fleets')->save($this->user->fill([
				'active'            => 1,
				'force_pass_reset'  => 0,
				'is_phone_verified' => 1,
				'password'          => '1234',
				'firstname'         => 'demo',
				'lastname'          => 'fleet',
				'is_online'         => 'offline',
				'username'          => 'demo-fleet',
				'phone'             => '919876543212',
				'email'             => 'demo@fleet.com',
			]));

			$this->userModel->withGroup('drivers')->save($this->user->fill([
				'active'            => 1,
				'force_pass_reset'  => 0,
				'is_phone_verified' => 1,
				'password'          => '1234',
				'firstname'         => 'demo',
				'lastname'          => 'driver',
				'is_online'         => 'offline',
				'username'          => 'demo-driver',
				'phone'             => '919876543213',
				'email'             => 'demo@driver.com',
			]));

			$this->userModel->withGroup('users')->save($this->user->fill([
				'active'            => 1,
				'force_pass_reset'  => 0,
				'is_phone_verified' => 1,
				'lastname'          => 'user',
				'password'          => '1234',
				'firstname'         => 'demo',
				'is_online'         => 'offline',
				'username'          => 'demo-user',
				'phone'             => '919876543214',
				'email'             => 'demo@user.com',
			]));
		}

		if ($this->db->tableExists('auth_permissions')) {
			$type        = ['read', 'add', 'add-action', 'update', 'update-action', 'show', 'mine', 'delete'];
			$permissions = ['creators', 'super-admins', 'admins', 'company-admins',  'managers', 'fleets', 'drivers', 'users', 'demo-admins', 'groups', 'permissions', 'accounts', 'bird_eyes', 'cards', 'categories', 'commissions', 'companies', 'companies_users',  'companies_groups', 'companies_policies', 'dispatch', 'documents', 'fares', 'messages', 'notifications', 'orders', 'promos', 'refers', 'reports', 'reviews', 'settings', 'tickets', 'tips', 'transactions', 'user_vehicles', 'vehicles', 'wallets', 'withdraws', 'zones'];

			for ($i = 0; $i < count($permissions); $i++) {
				if ($permissions[$i] === 'groups' || $permissions[$i] === 'permissions' || $permissions[$i] === 'dispatch' || $permissions[$i] === 'orders' || $permissions[$i] === 'tickets' || $permissions[$i] === 'vehicles') $type[] = 'assign';

				for ($j = 0; $j < count($type); $j++) {
					$id = $this->authorize->createPermission("{$permissions[$i]}.{$type[$j]}", "Allows a user to {$type[$j]} {$permissions[$i]}.");
					if (is_int($id)) {
						$this->authorize->addPermissionToGroup($id, 'creators');

						if ($permissions[$i] !== 'creators' && ($type[$j] === 'add' || $type[$j] === 'update' || $type[$j] === 'read' || $type[$j] === 'show'))
							$this->authorize->addPermissionToGroup($id, 'demo-admins');

						if ($permissions[$i] !== 'creators' && $permissions[$i] !== 'permissions' && (!($permissions[$i] === 'settings' && ($type[$j] === 'add' || $type[$j] === 'add-action' || $type[$j] === 'show' || $type[$j] === 'delete')) && !($permissions[$i] === 'groups' && $type[$j] === 'delete')))
							$this->authorize->addPermissionToGroup($id, 'super-admins');

						if (($permissions[$i] === 'orders' && $type[$j] === 'mine') || ($permissions[$i] === 'wallets' && $type[$j] === 'mine') || ($permissions[$i] === 'transactions' && $type[$j] === 'mine') || ($permissions[$i] === 'refers' && $type[$j] === 'mine') || ($permissions[$i] === 'messages' && $type[$j] === 'mine') || ($permissions[$i] === 'refers' && $type[$j] === 'mine') || ($permissions[$i] === 'withdraws' && $type[$j] === 'mine') || ($permissions[$i] === 'tickets' && $type[$j] === 'mine') || ($permissions[$i] === 'tips' && $type[$j] === 'mine') || ($permissions[$i] === 'user_vehicles' && $type[$j] === 'mine')) {
							$this->authorize->addPermissionToGroup($id, 'fleets');
							$this->authorize->addPermissionToGroup($id, 'drivers');
							$this->authorize->addPermissionToGroup($id, 'users');
						}

						if ($permissions[$i] === 'vehicles' && $type[$j] !== 'read') $this->authorize->addPermissionToGroup($id, 'fleets');

						if (($permissions[$i] === 'cards' && $type[$j] !== 'read') || ($permissions[$i] === 'accounts' && $type[$j] !== 'read')) {
							$this->authorize->addPermissionToGroup($id, 'fleets');
							$this->authorize->addPermissionToGroup($id, 'drivers');
							$this->authorize->addPermissionToGroup($id, 'users');
						}

						if (($permissions[$i] === 'documents' && $type[$j] !== 'read') || ($permissions[$i] === 'reviews' && $type[$j] !== 'read')) {
							$this->authorize->addPermissionToGroup($id, 'fleets');
							$this->authorize->addPermissionToGroup($id, 'drivers');
							$this->authorize->addPermissionToGroup($id, 'users');
						}
					}
				}
			}
		}
	}
}

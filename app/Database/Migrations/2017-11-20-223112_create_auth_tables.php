<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuthTables extends Migration
{
	public function up()
	{
		// Users
		$this->forge->addField([
			'id'                => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'uid'               => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
			'username'          => ['type' => 'varchar', 'constraint' => 30, 'null' => true],
			'firstname'         => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
			'lastname'          => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
			'email'             => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
			'phone'             => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
			'profile_pic'       => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
			'password_hash'     => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
			'speed'             => ['type' => 'varchar', 'constraint' => 255, 'null' => true, 'default' => 0],
			'heading'           => ['type' => 'varchar', 'constraint' => 255, 'null' => true, 'default' => 0],
			'lat'               => ['type' => 'varchar', 'constraint' => 255, 'null' => true, 'default' => 0],
			'long'              => ['type' => 'varchar', 'constraint' => 255, 'null' => true, 'default' => 0],
			'is_online'         => ['type' => 'ENUM', 'constraint' => ['online', 'offline'], 'null' => true, 'default' => 'offline'],
			'app_token'         => ['type' => 'TEXT', 'null' => true],
			'is_phone_verified' => ['type' => 'tinyint', 'constraint' => 1, 'null' => true, 'default' => 0],
			'reset_hash'        => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
			'reset_at'          => ['type' => 'datetime', 'null' => true],
			'reset_expires'     => ['type' => 'datetime', 'null' => true],
			'activate_hash'     => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
			'status'            => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
			'status_message'    => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
			'active'            => ['type' => 'tinyint', 'constraint' => 1, 'null' => 0, 'default' => 0],
			'force_pass_reset'  => ['type' => 'tinyint', 'constraint' => 1, 'null' => 0, 'default' => 0],
			'created_at'        => ['type' => 'datetime', 'null' => true],
			'updated_at'        => ['type' => 'datetime', 'null' => true],
			'deleted_at'        => ['type' => 'datetime', 'null' => true],
		]);

		$this->forge->addKey('id', true);
		$this->forge->addUniqueKey('username');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');

		$this->forge->createTable('users', true, ['ENGINE' => 'InnoDB']);

		// Auth Login Attempts
		$this->forge->addField([
			'id'         => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'ip_address' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
			'email'      => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
			'user_id'    => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true], // Only for successful logins
			'date'       => ['type' => 'datetime'],
			'success'    => ['type' => 'tinyint', 'constraint' => 1],
		]);
		$this->forge->addKey('id', true);
		$this->forge->addKey('email');
		$this->forge->addKey('user_id');
		// NOTE: Do NOT delete the user_id or email when the user is deleted for security audits
		$this->forge->createTable('auth_logins', true, ['ENGINE' => 'InnoDB']);

		/*
         * Auth Tokens
         * @see https://paragonie.com/blog/2015/04/secure-authentication-php-with-long-term-persistence
         */
		$this->forge->addField([
			'id'              => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'selector'        => ['type' => 'varchar', 'constraint' => 255],
			'hashedValidator' => ['type' => 'varchar', 'constraint' => 255],
			'user_id'         => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
			'expires'         => ['type' => 'datetime'],
		]);
		$this->forge->addKey('id', true);
		$this->forge->addKey('selector');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('auth_tokens', true, ['ENGINE' => 'InnoDB']);

		// Password Reset Table
		$this->forge->addField([
			'id'         => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'email'      => ['type' => 'varchar', 'constraint' => 255],
			'ip_address' => ['type' => 'varchar', 'constraint' => 255],
			'user_agent' => ['type' => 'varchar', 'constraint' => 255],
			'token'      => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
			'created_at' => ['type' => 'datetime', 'null' => false],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('auth_reset_attempts', true, ['ENGINE' => 'InnoDB']);

		// Activation Attempts Table
		$this->forge->addField([
			'id'         => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'ip_address' => ['type' => 'varchar', 'constraint' => 255],
			'user_agent' => ['type' => 'varchar', 'constraint' => 255],
			'token'      => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
			'created_at' => ['type' => 'datetime', 'null' => false],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('auth_activation_attempts', true, ['ENGINE' => 'InnoDB']);

		// Groups Table
		$fields = [
			'id'          => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'name'        => ['type' => 'varchar', 'constraint' => 255],
			'description' => ['type' => 'varchar', 'constraint' => 255],
		];

		$this->forge->addField($fields);
		$this->forge->addKey('id', true);
		$this->forge->createTable('auth_groups', true, ['ENGINE' => 'InnoDB']);

		// Permissions Table
		$fields = [
			'id'          => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'name'        => ['type' => 'varchar', 'constraint' => 255],
			'description' => ['type' => 'varchar', 'constraint' => 255],
		];

		$this->forge->addField($fields);
		$this->forge->addKey('id', true);
		$this->forge->createTable('auth_permissions', true, ['ENGINE' => 'InnoDB']);

		// Groups/Permissions Table
		$fields = [
			'group_id'      => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
			'permission_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
		];

		$this->forge->addField($fields);
		$this->forge->addKey(['group_id', 'permission_id']);
		$this->forge->addForeignKey('group_id', 'auth_groups', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('permission_id', 'auth_permissions', 'id', '', 'CASCADE');
		$this->forge->createTable('auth_groups_permissions', true, ['ENGINE' => 'InnoDB']);

		// Users/Groups Table
		$fields = [
			'group_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
			'user_id'  => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
		];

		$this->forge->addField($fields);
		$this->forge->addKey(['group_id', 'user_id']);
		$this->forge->addForeignKey('group_id', 'auth_groups', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('auth_groups_users', true, ['ENGINE' => 'InnoDB']);

		// Users/Permissions Table
		$fields = [
			'user_id'       => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
			'permission_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
		];

		$this->forge->addField($fields);
		$this->forge->addKey(['user_id', 'permission_id']);
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('permission_id', 'auth_permissions', 'id', '', 'CASCADE');
		$this->forge->createTable('auth_users_permissions', true, ['ENGINE' => 'InnoDB']);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		// drop constraints first to prevent errors
		if ($this->db->DBDriver !== 'SQLite3') // @phpstan-ignore-line
		{
			$this->forge->dropForeignKey('auth_tokens', 'auth_tokens_user_id_foreign');
			$this->forge->dropForeignKey('auth_groups_permissions', 'auth_groups_permissions_group_id_foreign');
			$this->forge->dropForeignKey('auth_groups_permissions', 'auth_groups_permissions_permission_id_foreign');
			$this->forge->dropForeignKey('auth_groups_users', 'auth_groups_users_group_id_foreign');
			$this->forge->dropForeignKey('auth_groups_users', 'auth_groups_users_user_id_foreign');
			$this->forge->dropForeignKey('auth_users_permissions', 'auth_users_permissions_user_id_foreign');
			$this->forge->dropForeignKey('auth_users_permissions', 'auth_users_permissions_permission_id_foreign');
		}

		$this->forge->dropTable('users', true);
		$this->forge->dropTable('auth_logins', true);
		$this->forge->dropTable('auth_tokens', true);
		$this->forge->dropTable('auth_reset_attempts', true);
		$this->forge->dropTable('auth_activation_attempts', true);
		$this->forge->dropTable('auth_groups', true);
		$this->forge->dropTable('auth_permissions', true);
		$this->forge->dropTable('auth_groups_permissions', true);
		$this->forge->dropTable('auth_groups_users', true);
		$this->forge->dropTable('auth_users_permissions', true);
	}
}
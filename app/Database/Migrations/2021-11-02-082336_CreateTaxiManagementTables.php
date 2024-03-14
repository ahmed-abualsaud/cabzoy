<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTaxiManagementTables extends Migration
{
	public function up()
	{
		// orders
		$this->forge->addField([
			'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'order_vehicle' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => 'vehicle category name or any'],
			'order_price'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'order_kms'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'order_otp'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'order_comment' => ['type' => 'TEXT', 'null' => true],
			'wait_time'     => ['type' => 'TIME', 'null' => true],
			'is_paid'       => ['type' => 'ENUM', 'constraint' => ['paid', 'not-paid'], 'default' => 'not-paid'],
			'order_status'  => ['type' => 'ENUM', 'constraint' => ['new', 'booked', 'dispatched',  'arrived', 'picked', 'ongoing', 'complete', 'cancel'], 'default' => 'new'],
			'order_type'    => ['type' => 'ENUM', 'constraint' => ['normal', 'outdoor', 'advanced'], 'default' => 'normal'],
			'payment_mode'  => ['type' => 'ENUM', 'constraint' => ['online', 'corporate', 'cod'], 'default' => 'online'],
			'booking_from'  => ['type' => 'ENUM', 'constraint' => ['web', 'app'], 'default' => 'app'],
			'booking_at'    => ['type' => 'DATETIME', 'null' => true],
			'created_by'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'created_at'    => ['type' => 'DATETIME', 'null' => true],
			'updated_at'    => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'    => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('created_by', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('orders', true, ['ENGINE' => 'InnoDB']);

		// order_locations
		$this->forge->addField([
			'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'order_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'order_location_type' => ['type' => 'ENUM', 'constraint' => ['pickup', 'stop', 'drop'], 'default' => 'pickup'],
			'order_location_text' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'order_location_lat'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'order_location_long' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'created_at'          => ['type' => 'DATETIME', 'null' => true],
			'updated_at'          => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'          => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('order_id', 'orders', 'id', '', 'CASCADE');
		$this->forge->createTable('order_locations', true, ['ENGINE' => 'InnoDB']);

		// order_drivers
		$this->forge->addField([
			'id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'driver_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'order_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'action'    => ['type' => 'ENUM', 'constraint' => ['accept', 'pending', 'rejected'], 'default' => 'pending'],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('driver_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('order_id', 'orders', 'id', '', 'CASCADE');
		$this->forge->createTable('order_drivers', true, ['ENGINE' => 'InnoDB']);

		// order_users
		$this->forge->addField([
			'id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'order_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true]
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('order_id', 'orders', 'id', '', 'CASCADE');
		$this->forge->createTable('order_users', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('order_users');
		$this->forge->dropTable('order_drivers');
		$this->forge->dropTable('order_locations');
		$this->forge->dropTable('orders');
	}
}

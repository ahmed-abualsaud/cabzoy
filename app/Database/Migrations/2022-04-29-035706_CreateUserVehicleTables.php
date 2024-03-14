<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserVehicleTables extends Migration
{
	public function up()
	{
		// user_vehicles
		$this->forge->addField([
			'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'vehicle_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('vehicle_id', 'vehicles', 'id', '', 'CASCADE');
		$this->forge->createTable('user_vehicles', true, ['ENGINE' => 'InnoDB']);

		// order_cancels
		$this->forge->addField([
			'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'order_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'comment'     => ['type' => 'LONGTEXT', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('order_id', 'orders', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('category_id', 'categories', 'id', '', 'CASCADE');
		$this->forge->createTable('order_cancels', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('order_cancels');
		$this->forge->dropTable('user_vehicles');
	}
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVehiclesCategoriesTables extends Migration
{
	public function up()
	{
		// Categories
		$this->forge->addField([
			'id'                   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'category_name'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'category_icon'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'category_image'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'category_description' => ['type' => 'TEXT', 'null' => true],
			'category_type'        => ['type' => 'ENUM', 'constraint' => ['vehicle', 'complaint', 'faq', 'ticket', 'cancellation', 'review'], 'default' => 'vehicle'],
			'category_status'      => ['type' => 'ENUM', 'constraint' => ['approved', 'pending', 'rejected'], 'default' => 'pending'],
			'created_by'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'created_at'           => ['type' => 'DATETIME', 'null' => true],
			'updated_at'           => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'           => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('created_by', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('categories', true, ['ENGINE' => 'InnoDB']);

		// Vehicles
		$this->forge->addField([
			'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'vehicle_number' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'vehicle_brand' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'vehicle_modal' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'vehicle_color' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'vehicle_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'vehicle_seats' => ['type' => 'INT', 'constraint' => 2, 'unsigned' => true, 'null' => true],
			'vehicle_status' => ['type' => 'ENUM', 'constraint' => ['approved', 'pending', 'rejected'], 'default' => 'pending'],
			'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'created_at' => ['type' => 'DATETIME', 'null' => true],
			'updated_at' => ['type' => 'DATETIME', 'null' => true],
			'deleted_at' => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('created_by', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('vehicles', true, ['ENGINE' => 'InnoDB']);

		// Categories Users Vehicles
		$this->forge->addField([
			'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'vehicle_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'started_at' => ['type' => 'DATETIME', 'null' => true],
			'ended_at' => ['type' => 'DATETIME', 'null' => true],
			'status' => ['type' => 'ENUM', 'constraint' => ['available', 'busy', 'not-available'], 'default' => 'not-available'],
			'created_at' => ['type' => 'DATETIME', 'null' => true],
			'updated_at' => ['type' => 'DATETIME', 'null' => true],
			'deleted_at' => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('vehicle_id', 'vehicles', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('category_id', 'categories', 'id', '', 'CASCADE');
		$this->forge->createTable('categories_users_vehicles', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('categories_users_vehicles');
		$this->forge->dropTable('vehicles');
		$this->forge->dropTable('categories');
	}
}

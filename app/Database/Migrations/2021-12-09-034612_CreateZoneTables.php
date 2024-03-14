<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateZoneTables extends Migration
{
	public function up()
	{
		// fares
		$this->forge->addField([
			'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'fare_name'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'fare'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'min_fare'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'fare_day'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'start_time'  => ['type' => 'TIME', 'null' => true],
			'end_time'    => ['type' => 'TIME', 'null' => true],
			'fare_from'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'fare_to'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'fare_type'   => ['type' => 'ENUM', 'constraint' => ['one-time', 'always'], 'default' => 'one-time'],
			'fare_status' => ['type' => 'ENUM', 'constraint' => ['active', 'disable'], 'default' => 'active'],
			'created_by'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'created_at'  => ['type' => 'DATETIME', 'null' => true],
			'updated_at'  => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('created_by', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('fares', true, ['ENGINE' => 'InnoDB']);


		// Zone
		$this->forge->addField([
			'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'company_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'zone_name'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'zone'       => ['type' => 'LONGTEXT', 'null' => true],
			'zone_type'  => ['type' => 'ENUM', 'constraint' => ['plot', 'boundary', 'off-limit'], 'default' => 'plot'],
			'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'created_at' => ['type' => 'DATETIME', 'null' => true],
			'updated_at' => ['type' => 'DATETIME', 'null' => true],
			'deleted_at' => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('company_id', 'companies', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('created_by', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('zones', true, ['ENGINE' => 'InnoDB']);


		// fare_relations
		$this->forge->addField([
			'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'vehicle_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'zone_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'fare_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('zone_id', 'zones', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('fare_id', 'fares', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('vehicle_id', 'vehicles', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('category_id', 'categories', 'id', '', 'CASCADE');
		$this->forge->createTable('fare_relations', true, ['ENGINE' => 'InnoDB']);


		// order_fares
		$this->forge->addField([
			'id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'order_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'fare_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('order_id', 'orders', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('fare_id', 'fares', 'id', '', 'CASCADE');
		$this->forge->createTable('order_fares', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('order_fares');
		$this->forge->dropTable('fare_relations');
		$this->forge->dropTable('zones');
		$this->forge->dropTable('fares');
	}
}

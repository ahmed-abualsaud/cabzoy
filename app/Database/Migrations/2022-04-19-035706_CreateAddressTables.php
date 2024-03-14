<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAddressTables extends Migration
{
	public function up()
	{
		// addresses
		$this->forge->addField([
			'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'company_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'address_title'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'address_location' => ['type' => 'TEXT', 'null' => true],
			'address_lat'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'address_long'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'address_status'   => ['type' => 'ENUM', 'constraint' => ['approved', 'pending', 'rejected'], 'default' => 'pending'],
			'created_at'       => ['type' => 'DATETIME', 'null' => true],
			'updated_at'       => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('company_id', 'companies', 'id', '', 'CASCADE');
		$this->forge->createTable('addresses', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('addresses');
	}
}

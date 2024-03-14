<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSosTables extends Migration
{
	public function up()
	{
		// emergency_contacts
		$this->forge->addField([
			'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'name'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'phone'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'email'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'created_at' => ['type' => 'DATETIME', 'null' => true],
			'updated_at' => ['type' => 'DATETIME', 'null' => true],
			'deleted_at' => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('emergency_contacts', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('emergency_contacts');
	}
}

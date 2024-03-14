<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWithDrawsTables extends Migration
{
	public function up()
	{
		// withdraws
		$this->forge->addField([
			'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'amount'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'comment'    => ['type' => 'TEXT', 'null' => true],
			'status'     => ['type' => 'ENUM', 'constraint' => ['approved', 'pending', 'rejected'], 'default' => 'pending'],
			'created_at' => ['type' => 'DATETIME', 'null' => true],
			'updated_at' => ['type' => 'DATETIME', 'null' => true],
			'deleted_at' => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('withdraws', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('withdraws');
	}
}

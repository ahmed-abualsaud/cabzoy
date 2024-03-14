<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMessageTables extends Migration
{
	public function up()
	{
		// message_groups
		$this->forge->addField([
			'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'order_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'created_at' => ['type' => 'DATETIME', 'null' => true],
			'updated_at' => ['type' => 'DATETIME', 'null' => true],
			'deleted_at' => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('order_id', 'orders', 'id', '', 'CASCADE');
		$this->forge->createTable('message_groups', true, ['ENGINE' => 'InnoDB']);

		// messages
		$this->forge->addField([
			'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'message_group_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'user_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'message'          => ['type' => 'TEXT', 'null' => true],
			'created_at'       => ['type' => 'DATETIME', 'null' => true],
			'updated_at'       => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('message_group_id', 'message_groups', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('messages', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('messages');
		$this->forge->dropTable('message_groups');
	}
}

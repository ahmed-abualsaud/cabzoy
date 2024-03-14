<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTipTables extends Migration
{
	public function up()
	{
		// tips
		$this->forge->addField([
			'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'tip_amount'  => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => '0'],
			'tip_comment' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'created_at'  => ['type' => 'DATETIME', 'null' => true],
			'updated_at'  => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('tips', true, ['ENGINE' => 'InnoDB']);

		// order_tips
		$this->forge->addField([
			'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'tip_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'order_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'amount'     => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => '0'],
			'created_at' => ['type' => 'DATETIME', 'null' => true],
			'updated_at' => ['type' => 'DATETIME', 'null' => true],
			'deleted_at' => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('order_id', 'orders', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('tip_id', 'tips', 'id', '', 'CASCADE');
		$this->forge->createTable('order_tips', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('order_tips');
		$this->forge->dropTable('tips');
	}
}

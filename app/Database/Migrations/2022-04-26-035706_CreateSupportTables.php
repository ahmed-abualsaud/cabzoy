<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSupportTables extends Migration
{
	public function up()
	{
		// tickets
		$this->forge->addField([
			'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'category_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'ticket_title'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'ticket_body'     => ['type' => 'TEXT', 'null' => true],
			'ticket_priority' => ['type' => 'ENUM', 'constraint' => ['low', 'medium', 'high'], 'default' => 'low'],
			'ticket_status'   => ['type' => 'ENUM', 'constraint' => ['new', 'in-progress', 'resolved', 'rejected', 'on-hold'], 'default' => 'new'],
			'created_at'      => ['type' => 'DATETIME', 'null' => true],
			'updated_at'      => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('category_id', 'categories', 'id', '', 'CASCADE');
		$this->forge->createTable('tickets', true, ['ENGINE' => 'InnoDB']);

		// ticket_comments
		$this->forge->addField([
			'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'ticket_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'user_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'ticket_comment' => ['type' => 'TEXT', 'null' => true]
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('ticket_id', 'tickets', 'id', '', 'CASCADE');
		$this->forge->createTable('ticket_comments', true, ['ENGINE' => 'InnoDB']);

		// ticket_attachments
		$this->forge->addField([
			'id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'ticket_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'user_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'file'      => ['type' => 'TEXT', 'null' => true]
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('ticket_id', 'tickets', 'id', '', 'CASCADE');
		$this->forge->createTable('ticket_attachments', true, ['ENGINE' => 'InnoDB']);

		// ticket_handlers
		$this->forge->addField([
			'id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'ticket_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'user_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true]
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('ticket_id', 'tickets', 'id', '', 'CASCADE');
		$this->forge->createTable('ticket_handlers', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('ticket_handlers');
		$this->forge->dropTable('ticket_attachments');
		$this->forge->dropTable('ticket_comments');
		$this->forge->dropTable('tickets');
	}
}

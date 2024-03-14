<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDriverTables extends Migration
{
	public function up()
	{
		// documents
		$this->forge->addField([
			'id'                   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'document_title'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'document_number'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'document_front_image' => ['type' => 'TEXT', 'null' => true],
			'document_back_image'  => ['type' => 'TEXT', 'null' => true],
			'document_comment'     => ['type' => 'TEXT', 'null' => true],
			'document_status'      => ['type' => 'ENUM', 'constraint' => ['approved', 'pending', 'rejected'], 'default' => 'pending'],
			'created_at'           => ['type' => 'DATETIME', 'null' => true],
			'updated_at'           => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'           => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('documents', true, ['ENGINE' => 'InnoDB']);

		// announcements
		$this->forge->addField([
			'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'announcement_title'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'announcement_body'   => ['type' => 'TEXT', 'null' => true],
			'announcement_status' => ['type' => 'ENUM', 'constraint' => ['active', 'inactive'], 'default' => 'inactive'],
			'announcement_for'    => ['type' => 'ENUM', 'constraint' => ['super-admins', 'admins', 'managers', 'fleets', 'drivers', 'users'], 'default' => 'users'],
			'created_at'          => ['type' => 'DATETIME', 'null' => true],
			'updated_at'          => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'          => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('announcements', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('documents');
		$this->forge->dropTable('announcements');
	}
}

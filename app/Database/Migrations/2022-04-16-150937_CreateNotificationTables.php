<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationTables extends Migration
{
	public function up()
	{
		// notifications
		$this->forge->addField([
			'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'notification_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'notification_body'  => ['type' => 'TEXT', 'null' => true],
			'notification_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'notification_type'  => ['type' => 'ENUM', 'constraint' => ['announcement', 'default', 'message', 'order', 'transaction'], 'default' => 'default'],
			'is_seen'            => ['type' => 'ENUM', 'constraint' => ['seen', 'unseen'], 'default' => 'unseen'],
			'created_at'         => ['type' => 'DATETIME', 'null' => true],
			'updated_at'         => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'         => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('notifications', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('notifications');
	}
}

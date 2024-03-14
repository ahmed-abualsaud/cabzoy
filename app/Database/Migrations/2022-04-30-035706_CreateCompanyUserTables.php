<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCompanyUserTables extends Migration
{
	public function up()
	{
		// companies_users_relations
		$this->forge->addField([
			'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'company_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('company_id', 'companies', 'id', '', 'CASCADE');
		$this->forge->createTable('companies_users_relations', true);
	}

	public function down()
	{
		$this->forge->dropTable('companies_users_relations');
	}
}

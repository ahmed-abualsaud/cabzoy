<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCompaniesManagementTables extends Migration
{
	public function up()
	{
		// cards
		$this->forge->addField([
			'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'card_number'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'card_holdername' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'card_month'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'card_year'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'card_cvv'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'card_status'     => ['type' => 'ENUM', 'constraint' => ['approved', 'pending', 'rejected'], 'default' => 'pending'],
			'card_type'       => ['type' => 'ENUM', 'constraint' => ['credit', 'debit'], 'default' => 'credit'],
			'is_default'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => '0'],
			'created_by'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'created_at'      => ['type' => 'DATETIME', 'null' => true],
			'updated_at'      => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('created_by', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('cards', true, ['ENGINE' => 'InnoDB']);

		// accounts
		$this->forge->addField([
			'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'account_number'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'account_holdername' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'bank_name'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'branch_number'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'branch_address'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'account_code'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'account_status'     => ['type' => 'ENUM', 'constraint' => ['approved', 'pending', 'rejected'], 'default' => 'pending'],
			'is_default'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => '0'],
			'created_by'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'created_at'         => ['type' => 'DATETIME', 'null' => true],
			'updated_at'         => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'         => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('created_by', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('accounts', true, ['ENGINE' => 'InnoDB']);

		// companies
		$this->forge->addField([
			'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'company_name'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'company_email'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'company_mobile'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'company_image'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'company_address'  => ['type' => 'TEXT', 'null' => true],
			'company_document' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'company_status'   => ['type' => 'ENUM', 'constraint' => ['approved', 'pending', 'rejected'], 'default' => 'pending'],
			'is_default'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => '0'],
			'created_by'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'created_at'       => ['type' => 'DATETIME', 'null' => true],
			'updated_at'       => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('created_by', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('companies', true, ['ENGINE' => 'InnoDB']);

		// payment_relations
		$this->forge->addField([
			'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'company_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'user_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'card_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'account_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'relation_type' => ['type' => 'ENUM', 'constraint' => ['card', 'account'], 'default' => 'card'],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('card_id', 'cards', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('account_id', 'accounts', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('company_id', 'companies', 'id', '', 'CASCADE');
		$this->forge->createTable('payment_relations', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('payment_relations');
		$this->forge->dropTable('cards');
		$this->forge->dropTable('accounts');
		$this->forge->dropTable('companies');
	}
}

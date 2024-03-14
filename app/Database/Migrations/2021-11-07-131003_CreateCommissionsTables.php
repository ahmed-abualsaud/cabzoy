<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommissionsTables extends Migration
{
	public function up()
	{
		// commissions
		$this->forge->addField([
			'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'category_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'vehicle_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'company_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'commission_name'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'commission'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'commission_type'   => ['type' => 'ENUM', 'constraint' => ['percentage', 'flat'], 'default' => 'flat'],
			'commission_status' => ['type' => 'ENUM', 'constraint' => ['approved', 'pending', 'rejected'], 'default' => 'pending'],
			'created_by'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'created_at'        => ['type' => 'DATETIME', 'null' => true],
			'updated_at'        => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'        => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('category_id', 'categories', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('vehicle_id', 'vehicles', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('company_id', 'companies', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('created_by', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('commissions', true, ['ENGINE' => 'InnoDB']);

		// commission_relations
		$this->forge->addField([
			'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'company_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'commission_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('company_id', 'companies', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('commission_id', 'commissions', 'id', '', 'CASCADE');
		$this->forge->createTable('commission_relations', true, ['ENGINE' => 'InnoDB']);

		// order_commissions
		$this->forge->addField([
			'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'order_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'commission_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'commission_amount' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => '0'],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('order_id', 'orders', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('commission_id', 'commissions', 'id', '', 'CASCADE');
		$this->forge->createTable('order_commissions', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('order_commissions');
		$this->forge->dropTable('commission_relations');
		$this->forge->dropTable('commissions');
	}
}

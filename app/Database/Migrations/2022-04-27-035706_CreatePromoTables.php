<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePromoTables extends Migration
{
	public function up()
	{
		// promos
		$this->forge->addField([
			'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'promo_code'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'promo_discount'      => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => '0'],
			'promo_min_amount'    => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => '0'],
			'promo_max_amount'    => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => '0'],
			'promo_count'         => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => '0'],
			'promo_discount_type' => ['type' => 'ENUM', 'constraint' => ['percentage', 'flat'], 'default' => 'flat'],
			'promo_status'        => ['type' => 'ENUM', 'constraint' => ['approved', 'pending', 'rejected'], 'default' => 'pending'],
			'created_at'          => ['type' => 'DATETIME', 'null' => true],
			'updated_at'          => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'          => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->createTable('promos', true, ['ENGINE' => 'InnoDB']);

		// order_promos
		$this->forge->addField([
			'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'promo_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'order_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'discount'   => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => '0'],
			'created_at' => ['type' => 'DATETIME', 'null' => true],
			'updated_at' => ['type' => 'DATETIME', 'null' => true],
			'deleted_at' => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('order_id', 'orders', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('promo_id', 'promos', 'id', '', 'CASCADE');
		$this->forge->createTable('order_promos', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('order_promos');
		$this->forge->dropTable('promos');
	}
}

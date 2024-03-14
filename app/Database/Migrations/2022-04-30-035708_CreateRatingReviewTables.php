<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRatingReviewTables extends Migration
{
	public function up()
	{
		// reviews
		$this->forge->addField([
			'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'order_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'review'     => ['type' => 'TEXT', 'null' => true],
			'rating'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'created_at' => ['type' => 'DATETIME', 'null' => true],
			'updated_at' => ['type' => 'DATETIME', 'null' => true],
			'deleted_at' => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('order_id', 'orders', 'id', '', 'CASCADE');
		$this->forge->createTable('reviews', true, ['ENGINE' => 'InnoDB']);

		// review_categories
		$this->forge->addField([
			'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'review_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'rating'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('review_id', 'reviews', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('category_id', 'categories', 'id', '', 'CASCADE');
		$this->forge->createTable('review_categories', true, ['ENGINE' => 'InnoDB']);

		// review_drivers
		$this->forge->addField([
			'id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'review_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'user_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'rating'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('review_id', 'reviews', 'id', '', 'CASCADE');
		$this->forge->createTable('review_drivers', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('review_drivers');
		$this->forge->dropTable('review_categories');
		$this->forge->dropTable('reviews');
	}
}

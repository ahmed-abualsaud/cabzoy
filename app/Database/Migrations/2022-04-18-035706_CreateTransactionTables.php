<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionTables extends Migration
{
	public function up()
	{
		// wallets
		$this->forge->addField([
			'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'company_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'amount'      => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => '0'],
			'action'      => ['type' => 'ENUM', 'constraint' => ['credit', 'debit'], 'default' => 'credit'],
			'status'      => ['type' => 'ENUM', 'constraint' => ['success', 'pending', 'failed'], 'default' => 'pending'],
			'wallet_type' => ['type' => 'ENUM', 'constraint' => ['order', 'offer', 'earn', 'reward', 'payout', 'charges', 'transaction', 'others'], 'default' => 'others'],
			'created_at'  => ['type' => 'DATETIME', 'null' => true],
			'updated_at'  => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('company_id', 'companies', 'id', '', 'CASCADE');
		$this->forge->createTable('wallets', true, ['ENGINE' => 'InnoDB']);

		// transactions
		$this->forge->addField([
			'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'company_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'txn'              => ['type' => 'TEXT', 'null' => true],
			'summary'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'amount'           => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => '0'],
			'action'           => ['type' => 'ENUM', 'constraint' => ['credit', 'debit'], 'default' => 'credit'],
			'status'           => ['type' => 'ENUM', 'constraint' => ['success', 'pending', 'failed'], 'default' => 'pending'],
			'transaction_type' => ['type' => 'ENUM', 'constraint' => ['card', 'cash', 'payment-gateway', 'virtual'], 'default' => 'payment-gateway'],
			'created_at'       => ['type' => 'DATETIME', 'null' => true],
			'updated_at'       => ['type' => 'DATETIME', 'null' => true],
			'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('company_id', 'companies', 'id', '', 'CASCADE');
		$this->forge->createTable('transactions', true, ['ENGINE' => 'InnoDB']);

		// wallet_transactions
		$this->forge->addField([
			'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'transaction_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'wallet_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('wallet_id', 'wallets', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('transaction_id', 'transactions', 'id', '', 'CASCADE');
		$this->forge->createTable('wallet_transactions', true, ['ENGINE' => 'InnoDB']);

		// wallet_receivers
		$this->forge->addField([
			'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'wallet_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'receiver_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addForeignKey('receiver_id', 'users', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('wallet_id', 'wallets', 'id', '', 'CASCADE');
		$this->forge->createTable('wallet_receivers', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('wallet_receivers');
		$this->forge->dropTable('wallet_transactions');
		$this->forge->dropTable('transactions');
		$this->forge->dropTable('wallets');
	}
}

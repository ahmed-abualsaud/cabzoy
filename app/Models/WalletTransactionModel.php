<?php

namespace App\Models;

use App\Entities\WalletTransaction;

class WalletTransactionModel extends BaseModel
{
	protected $useSoftDeletes  = false;
	protected $useTimestamps   = false;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $table           = 'wallet_transactions';
	protected $returnType      = WalletTransaction::class;
	protected $with            = ['transactions', 'wallets'];
	protected $allowedFields   = ['wallet_id', 'transaction_id'];
}

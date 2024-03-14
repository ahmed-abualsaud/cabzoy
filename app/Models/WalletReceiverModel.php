<?php

namespace App\Models;

use App\Entities\WalletReceiver;

class WalletReceiverModel extends BaseModel
{
	protected $useSoftDeletes  = false;
	protected $useTimestamps   = false;
	protected $table           = 'wallet_receivers';
	protected $with            = ['wallets', 'users'];
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $returnType      = WalletReceiver::class;
	protected $allowedFields   = ['wallet_id', 'receiver_id'];
}

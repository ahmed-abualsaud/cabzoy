<?php

namespace App\Models;

use App\Entities\MessageGroup;

class MessageGroupModel extends BaseModel
{
	protected $useSoftDeletes = true;
	protected $table          = 'message_groups';
	protected $returnType     = MessageGroup::class;
	protected $allowedFields  = ['order_id', 'user_id'];
}

<?php

namespace App\Models;

use App\Entities\Message;

class MessageModel extends BaseModel
{
	protected $useSoftDeletes = true;
	protected $table          = 'messages';
	protected $returnType     = Message::class;
	protected $allowedFields  = ['message_group_id', 'user_id', 'message'];
}

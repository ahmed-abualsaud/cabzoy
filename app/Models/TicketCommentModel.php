<?php

namespace App\Models;

use App\Entities\TicketComment;

class TicketCommentModel extends BaseModel
{
	protected $useSoftDeletes = false;
	protected $useTimestamps  = false;
	protected $table          = 'ticket_comments';
	protected $returnType     = TicketComment::class;
	protected $with           = ['users', 'tickets'];
	protected $allowedFields  = ['user_id', 'ticket_id', 'ticket_comment'];
}

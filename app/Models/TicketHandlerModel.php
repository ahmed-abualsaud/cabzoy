<?php

namespace App\Models;

use App\Entities\TicketHandler;

class TicketHandlerModel extends BaseModel
{
	protected $useSoftDeletes = false;
	protected $useTimestamps  = false;
	protected $table          = 'ticket_handlers';
	protected $returnType     = TicketHandler::class;
	protected $with           = ['users', 'tickets'];
	protected $allowedFields  = ['user_id', 'ticket_id'];
}

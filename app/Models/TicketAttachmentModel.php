<?php

namespace App\Models;

use App\Entities\TicketAttachment;

class TicketAttachmentModel extends BaseModel
{
	protected $useSoftDeletes = false;
	protected $useTimestamps  = false;
	protected $table          = 'ticket_attachments';
	protected $returnType     = TicketAttachment::class;
	protected $with           = ['users', 'tickets'];
	protected $allowedFields  = ['user_id', 'ticket_id', 'file'];
}

<?php

namespace App\Models;

use App\Entities\Ticket;

class TicketModel extends BaseModel
{
	protected $useSoftDeletes  = true;
	protected $table           = 'tickets';
	protected $returnType      = Ticket::class;
	protected $with            = ['users', 'categories', 'ticket_attachments', 'ticket_comments', 'ticket_handlers'];
	protected $allowedFields   = ['user_id', 'category_id', 'ticket_title', 'ticket_body', 'ticket_priority', 'ticket_status'];
	protected $validationRules = [
		'id'              => 'permit_empty|is_natural_no_zero',
		'ticket_priority' => 'in_list[low, medium, high]',
		'ticket_status'   => 'in_list[new, in-progress, resolved, rejected, on-hold]',
	];


	/** Status of Ticket
	 *
	 * @param string $status `new|in-progress|resolved|rejected|on-hold`
	 * @return self */
	public function statusIs(string $status = 'new')
	{
		$this->builder()->where('ticket_status', $status);
		return $this;
	}

	/** Priority of Ticket
	 *
	 * @param string $priority `low|medium|high`
	 * @return self */
	public function priorityIs(string $priority = 'new')
	{
		$this->builder()->where('ticket_priority', $priority);
		return $this;
	}
}

<?php

namespace App\Models;

use App\Entities\Notification;

class NotificationModel extends BaseModel
{
	protected $useSoftDeletes = true;
	protected $with           = 'users';
	protected $table          = 'notifications';
	protected $returnType     = Notification::class;
	protected $allowedFields  = ['user_id', 'notification_title', 'notification_body', 'notification_image', 'notification_type', 'is_seen'];

	protected $validationRules = [
		'id'                => 'permit_empty|is_natural_no_zero',
		'is_seen'           => 'in_list[seen, unseen]',
		'notification_type' => 'in_list[announcement, default, message, order, transaction]',
	];

	/**
	 * Mark as Read Notifications
	 *
	 * @param  integer $userId
	 * @param  string  $type choose between `announcement`, `default`, `message`, `order`, `transaction`
	 * @return boolean
	 */
	public function markAsRead(int $userId, string $type = 'default'): bool
	{
		$isRead = $this->where(['notification_type' => $type, 'is_seen' => 'unseen'])->update($userId, ['is_seen' => 'seen']);
		return $isRead;
	}
}

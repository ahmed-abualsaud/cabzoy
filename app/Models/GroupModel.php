<?php

namespace App\Models;

use Myth\Auth\Models\GroupModel as ModelsGroupModel;

class GroupModel extends ModelsGroupModel
{
	/** Returns an array of all users that are members of a group.
	 *
	 * @return array */
	final public function getUsersForGroup(int $groupId)
	{
		if (null === $found = cache("{$groupId}_users")) {
			$found = $this->builder()
				->select('auth_groups_users.*, users.*')
				->join('auth_groups_users', 'auth_groups_users.group_id = auth_groups.id', 'left')
				->join('users', 'auth_groups_users.user_id = users.id', 'left')
				->where('auth_groups.id', $groupId)
				->get()->getResult();
			log_message('debug', 'The GetUsersForGroup on {line} is {query}', ['query' => $this->db->getLastQuery()]);
			cache()->save("{$groupId}_users", $found, 300);
		}

		return $found;
	}
}

<?php

use App\Models\GroupModel;

if (!function_exists('perm')) {
	/**
	 * check Permission
	 *
	 * @param string $permission permission name
	 * @param string $type       comma separated string `add, add-action, read, update, update-action, delete` or `*`
	 * @param bool   $optional   when `$optional = false` return false when user don't have all describe permissions
	 *
	 * @return bool */
	function perm(string $permission, string $type, bool $optional = false): bool
	{
		helper('auth');
		$permissions = (array) explode(',', str_replace(' ', '', $type));

		if ($type === '*')
			$permissions = ['read', 'add', 'add-action', 'update', 'update-action', 'mine', 'delete'];

		for ($i = 0; $i < count($permissions); $i++) {
			$permissionType = $permissions[$i];

			if (!has_permission("{$permission}.{$permissionType}")) {
				if (!$optional) return false;
			}

			if (has_permission("{$permission}.{$permissionType}")) {
				if ($i !== (count($permissions) - 1) && !$optional) continue;

				return true;
			}
		}

		return false;
	}
}

if (!function_exists('user_groups')) {
	function user_groups($sort = false)
	{
		$groups = [];
		$groupModel = new GroupModel();

		foreach ($groupModel->findAll() as $group) {
			if (!perm($group->name, 'read, add, update')) $groupModel = $groupModel->where('name!=', $group->name);
		}

		$groups = $groupModel->findAll();
		if (isset($groups) && !empty($groups) && is_array($groups)) {
			if ($sort) sort($groups);

			return $groups;
		}

		return json_decode(json_encode([['id' => 1, 'name' => 'users', 'description' => 'normal users']]));
	}
}

if (!function_exists('role_from_url')) {

	function role_from_url()
	{
		helper('text');
		$url = uri_string(true);
		if ($urlArray = explode('/', $url)) {
			return plural($urlArray[1]);
		}

		return null;
	}
}

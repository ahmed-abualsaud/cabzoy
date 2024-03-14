<?php

namespace App\Collectors;

use App\Models\GroupModel;
use CodeIgniter\Debug\Toolbar\Collectors\BaseCollector;

/**
 * Debug Toolbar Collector for Auth
 */
class Auth extends BaseCollector
{
	/**
	 * Whether this collector has data that can
	 * be displayed in the Timeline.
	 *
	 * @var bool
	 */
	protected $hasTimeline = false;

	/**
	 * Whether this collector needs to display
	 * content in a tab or not.
	 *
	 * @var bool
	 */
	protected $hasTabContent = true;

	/**
	 * Whether this collector has data that
	 * should be shown in the Vars tab.
	 *
	 * @var bool
	 */
	protected $hasVarData = false;

	/**
	 * The 'title' of this Collector.
	 * Used to name things in the toolbar HTML.
	 *
	 * @var string
	 */
	protected $title = 'Auth';

	//--------------------------------------------------------------------

	/**
	 * Returns any information that should be shown next to the title.
	 */
	public function getTitleDetails(): string
	{
		return '<br> Current Login User with Roles & Powers <br><hr><br>';
	}

	/**
	 * Returns the data of this collector to be formatted in the toolbar
	 */
	public function display()
	{
		helper('text');
		$authenticate = service('authentication');

		if ($authenticate->isLoggedIn()) {
			$user         = $authenticate->user();
			$formattedUser = highlight_code(json_encode($user, JSON_PRETTY_PRINT));
			$groups       = model(GroupModel::class)->getGroupsForUser($user->id);

			if (is_array($groups) && !empty($groups)) {
				$groupsForUser       = singular(humanize(implode(', ', array_column($groups, 'name')), '-'));
				$permissionsForGroup = model(GroupModel::class)->getPermissionsForGroup($groups[0]['group_id']);
				$totalPermission     = count($permissionsForGroup);
				$formattedPermission  = highlight_code(json_encode($permissionsForGroup, JSON_PRETTY_PRINT));
			}

			$html = "<p><a href=\"#\" class=\"text-decoration-none\" data-bs-toggle=\"collapse\" data-bs-target=\"#userDropdownToolbar\" aria-expanded=\"false\" aria-controls=\"userDropdownToolbar\"><span class=\"fs-1 fw-bold text-decoration-underline\">{$user->name}</span><span class=\"fs-2\"> logged in ";

			if (isset($groupsForUser)) $html .= "as </span><span class=\"fs-1 fw-bold text-decoration-underline text-capitalize\">{$groupsForUser} </span></a><a href=\"#\" class=\"text-decoration-none\" data-bs-toggle=\"collapse\" data-bs-target=\"#permissionDropdownToolbar\" aria-expanded=\"false\" aria-controls=\"permissionDropdownToolbar\"><span class=\"fs-2\"> with </span><span class=\"fs-1 fw-bold text-decoration-underline\">{$totalPermission} permissions ";

			$html .= "</span></a></p><div class=\"collapse\" id=\"userDropdownToolbar\"><div class=\"card card-body\">{$formattedUser}</div></div>";

			if (isset($formattedPermission)) $html .= "<div class=\"collapse\" id=\"permissionDropdownToolbar\"><div class=\"card card-body\">{$formattedPermission}</div></div>";
		} else {
			$html = '<p style="text-transform:center">User Not logged in.</p>';
		}

		return $html;
	}

	/**
	 * Gets the "badge" value for the button.
	 *
	 * @return int|null ID of the current User, or null when not logged in
	 */
	public function getBadgeValue(): ?int
	{
		return service('authentication')->isLoggedIn() ? service('authentication')->id() : null;
	}

	/**
	 * Display the icon.
	 *
	 * Icon from https://icons8.com - 1em package
	 */
	public function icon(): string
	{
		return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAADLSURBVEhL5ZRLCsIwGAa7UkE9gd5HUfEoekxxJx7AhXoCca/fhESkJiQxBHwMDG3S/9EmJc0n0JMruZVXK/fMdWQRY7mXt4A7OZJvwZu74hRayIEc2nv3jGtXZrOWrnifiRY0OkhiWK5sWGeS52bkZymJ2ZhRJmwmySxLCL6CmIsZZUIixkiNezCRR+kSUyWH3Cgn6SuQIk2iuOBckvN+t8FMnq1TJloUN3jefN9mhvJeCAVWb8CyUDj0vxc3iPFHDaofFdUPu2+iae7nYJMCY/1bpAAAAABJRU5ErkJggg==';
	}
}

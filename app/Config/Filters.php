<?php

namespace Config;

use App\Filters\{LoginFilter, PermissionFilter, RoleFilter};
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\{CSRF, DebugToolbar, Honeypot, InvalidChars, SecureHeaders};

class Filters extends BaseConfig
{
	/**
	 * Configures aliases for Filter classes to
	 * make reading things nicer and simpler.
	 *
	 * @var array<string, string>
	 * @phpstan-var array<string, class-string>
	 */
	public array $aliases = [
		'csrf'          => CSRF::class,
		'honeypot'      => Honeypot::class,
		'role'          => RoleFilter::class,
		'login'         => LoginFilter::class,
		'toolbar'       => DebugToolbar::class,
		'invalidchars'  => InvalidChars::class,
		'secureheaders' => SecureHeaders::class,
		'permission'    => PermissionFilter::class,
	];

	/**
	 * List of filter aliases that are always
	 * applied before and after every request.
	 *
	 * @var array<string, array<string, array<string, string>>>|array<string, array<string>>
	 * @phpstan-var array<string, list<string>>|array<string, array<string, array<string, string>>>
	 */
	public array $globals = [
		'before' => [
			// 'honeypot',
			// 'csrf',
			// 'invalidchars',
		],
		'after' => [
			'toolbar' => ['except' => 'api/*'],
			// 'honeypot',
			// 'secureheaders',
		],
	];

	/**
	 * List of filter aliases that works on a
	 * particular HTTP method (GET, POST, etc.).
	 *
	 * Example:
	 * 'post' => ['foo', 'bar']
	 *
	 * If you use this, you should disable auto-routing because auto-routing
	 * permits any HTTP method to access a controller. Accessing the controller
	 * with a method you don't expect could bypass the filter.
	 */
	public array $methods = [];

	/**
	 * List of filter aliases that should run on any
	 * before or after URI patterns.
	 *
	 * Example:
	 * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
	 */
	public array $filters = [];
}

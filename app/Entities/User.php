<?php

namespace App\Entities;

use Myth\Auth\Entities\User as EntitiesUser;

helper('custom');
class User extends EntitiesUser
{
	use \Tatter\Relations\Traits\ModelTrait;

	protected $attributes = [
		'id'             => null,
		'username'       => null,
		'user_id'        => null,
		'guest_id'       => null,
		'name'           => null,
		'role'           => null,
		'firstname'      => null,
		'lastname'       => null,
		'email'          => null,
		'phone'          => null,
		'profile_pic'    => null,
		'lat'            => null,
		'long'           => null,
		'speed'          => null,
		'heading'        => null,
		'is_online'      => null,
		'status'         => null,
		'status_message' => null,
		'active'         => null,
		'created_at'     => null,
		'updated_at'     => null,
	];

	protected $datamap = ['name' => 'name', 'role' => 'role'];

	/**
	 * Define properties that are automatically converted to Time instances.
	 */
	protected $dates = ['reset_at', 'reset_expires', 'created_at', 'updated_at', 'deleted_at'];

	/** Array of field names and the type of value to cast them as
	 * when they are accessed.
	 *
	 * @var array */
	protected $casts = [
		'active'           => 'int',
		'speed'            => 'float',
		'heading'          => 'float',
		'lat'              => 'float',
		'long'             => 'float',
		'force_pass_reset' => 'boolean'
	];

	/** Per-user permissions cache
	 * @var array */
	protected $permissions = [];

	/** Per-user roles cache
	 * @var array */
	protected $roles = [];

	/** Returns a full name: "first last"
	 *
	 * @return string */
	public function getName()
	{
		return ucwords(trim(trim($this->attributes['firstname']) . ' ' . trim($this->attributes['lastname'])));
	}

	public function getProfilePic($formatted = true)
	{
		if ($formatted && !empty($this->attributes['profile_pic']) && !str_contains($this->attributes['profile_pic'], 'http://') && !str_contains($this->attributes['profile_pic'], 'https://')) return site_url($this->attributes['profile_pic']);

		return $this->attributes['profile_pic'];
	}

	public function setFirstname(?string $firstname = null)
	{
		$this->attributes['firstname'] = str_safe($firstname);
		return $this;
	}

	public function setPhone(?string $phone = null)
	{
		$this->attributes['phone'] = str_replace(['+', '-', '.'], '', $phone);
		return $this;
	}

	public function setLastname(?string $lastname = null)
	{
		$this->attributes['lastname'] = str_safe($lastname);
		return $this;
	}

	public function setUsername(?string $username = null)
	{
		$this->attributes['username'] = url_title($username, '-', true);

		return $this;
	}

	public function setEmail(?string $email = null)
	{
		$this->attributes['email'] = str_safe($email);
		return $this;
	}

	public function setGuestID(?int $guest_id = null)
	{
		$this->attributes['guest_id'] = $guest_id;
		return $this;
	}

	public function setProfilePic(?string $profilePic = null)
	{
		$this->attributes['profile_pic'] = !empty($profilePic) && strpos($profilePic, 'uploads/') === false && strpos($profilePic, 'http://') === false && strpos($profilePic, 'https://') === false ? "uploads/{$profilePic}" : $profilePic;

		return $this;
	}

	public function getRole()
	{
		$userRoles = [];
		$role      = $this->getRoles();

		if (empty($role)) return null;

		foreach ($role as $key => $value) {
			array_push($userRoles, ['id' => $key, 'name' => $value]);
		}

		return $userRoles;
	}

	/**
	 * Generates a secure random hash to use for account activation.
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function generateActivateHash()
	{
		$this->attributes['activate_hash'] = bin2hex(random_bytes(16));
		$this->attributes['active'] = 0;

		return $this;
	}

	/**
	 * Checks to see if a user is activated.
	 *
	 * @return bool
	 */
	public function isActivated(): bool
	{
		return isset($this->attributes['active']) && !!$this->attributes['active'] === true;
	}

	/**
	 * Activate user.
	 *
	 * @return $this
	 */
	public function activate()
	{
		$this->attributes['active'] = 1;
		$this->attributes['activate_hash'] = null;

		return $this;
	}

	/**
	 * Verified user.
	 *
	 * @return $this
	 */
	public function verified()
	{
		$this->attributes['active'] = 2;
		$this->attributes['activate_hash'] = null;

		return $this;
	}

	/**
	 * Checks to see if a user is verified.
	 *
	 * @return bool
	 */
	public function isVerified(): bool
	{
		return isset($this->attributes['active']) && (int)$this->attributes['active'] === 1;
	}
}

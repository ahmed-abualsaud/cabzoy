<?php

namespace App\Models;

use App\Entities\User;
use Myth\Auth\{Models\UserModel as ModelsUserModel};

class UserModel extends ModelsUserModel
{
	use \Tatter\Relations\Traits\ModelTrait;

	protected $primaryKey      = 'id';
	protected $table           = 'users';
	protected $returnType      = User::class;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $allowedFields   = [
		'email', 'username', 'password_hash', 'reset_hash', 'reset_at', 'reset_expires', 'activate_hash', 'status', 'status_message', 'active', 'force_pass_reset', 'permissions', 'deleted_at', 'firstname', 'lastname', 'phone', 'profile_pic', 'lat', 'long', 'speed', 'heading', 'is_online', 'app_token', 'user_id'
	];

	public function inGroup($group = null)
	{
		$groupId = '';

		if (!is_numeric($group) && is_string($group))
			$groupId = $this->db->table('auth_groups')->where('name', $group)->get(1)->getFirstRow()->id;

		elseif (is_array($group)) {
			$groupId = [];
			foreach ($group as $value) {
				if (is_string($value))
					array_push($groupId, $this->db->table('auth_groups')->where('name', $value)->get(1)->getFirstRow()->id);
			}
		} else $groupId = $group;

		$builder = $this->builder();
		$builder->select('users.*, auth_groups_users.*')
			->join('auth_groups_users', 'auth_groups_users.user_id = users.id', 'left')
			->join('auth_groups', 'auth_groups_users.group_id = auth_groups.id', 'left');

		if (is_array($groupId)) $builder->whereIn('auth_groups.id', $groupId);
		else $builder->where('auth_groups.id', $groupId);

		return $this;
	}

	/** User is online
	 *
	 * @param string $isOnline `online | offline`
	 * @return self */
	public function isOnline($isOnline = 'online')
	{
		$builder = $this->builder();
		if ($isOnline === 'online') $builder->where('is_online', $isOnline)->where('active', 1);
		else $builder->where('is_online', 'offline')->orWhere('is_online IS NULL')->where('active', 1);

		return $this;
	}

	/** Get Nearest available driver
	 *
	 * @param float $lat            Latitude
	 * @param float $long           Longitude
	 * @param array $driverIdsArray Array of the driver's id
	 *
	 * @return self */
	public function getNearestDriver(?float $lat = null, ?float $long = null, ?array $driverIdsArray = null, ?array $notDriverIdsArray = null)
	{
		$defaultDistance = config('Settings')->defaultDriverDistanceRadius ? config('Settings')->defaultDriverDistanceRadius : 25;
		$lengthUnit      = strtolower(config('Settings')->defaultLengthUnit);
		$defaultLength   = ($lengthUnit === 'km' || $lengthUnit === 'kms') ? 6.371 : 2.460011748;

		$this->builder()
			->select(['users.id', 'users.email', 'users.username', 'users.active', 'users.firstname', 'users.lastname', 'users.phone', 'users.profile_pic', 'users.lat', 'users.long', 'users.is_online', "({$defaultLength} * acos(cos(radians({$lat})) * cos(radians(`lat`)) * cos(radians(`long`) - radians({$long})) + sin(radians({$lat})) * sin(radians(`lat`)))) AS distance"])
			->where('users.deleted_at', null)->where('is_online', 'online')->where('active', 1)
			->join('categories_users_vehicles', 'users.id = categories_users_vehicles.user_id', 'left')
			->having('distance <', $defaultDistance)
			->orderBy('distance');

		if (null !== $driverIdsArray) $this->whereIn('users.id', $driverIdsArray);
		if (null !== $notDriverIdsArray) $this->whereNotIn('users.id', $notDriverIdsArray);

		return $this;
	}
}

<?php

use App\Models\ZoneModel;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

if (!function_exists('db')) {
	/** Get Database Instance
	 *
	 * @return BaseConnection */
	function db()
	{
		return Database::connect();
	}
}

function boundaryExists()
{
	$zone = new ZoneModel();
	$exist = $zone->where('zone_type', 'boundary')->first();
	return is($exist, 'object');
}

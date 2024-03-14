<?php

namespace App\Models;

use CodeIgniter\Model;
use Tatter\Relations\Traits\ModelTrait;

class BaseModel extends Model
{
	use ModelTrait;

	protected $primaryKey       = 'id';
	protected $useAutoIncrement = true;
	protected $protectFields    = true;
	protected $useTimestamps    = true;
	protected $DBGroup          = 'default';
	protected $createdField     = 'created_at';
	protected $updatedField     = 'updated_at';
	protected $deletedField     = 'deleted_at';

	public function countEveryHour()
	{
		$builder = $this->builder();
		// select hour(date) as Hour, count(*) as Count from `fab_auth_logins` where date between '2022-05-01 00:00:00' and '2022-05-07 00:00:00' group by hour(date)
		$hourCountArray = $builder->select('COUNT(*) AS total, HOUR(created_at) AS hour')
			->where('created_at BETWEEN NOW() - INTERVAL 1 DAY AND NOW()')
			->groupBy('hour')->orderBy('hour', 'ASC')->get()->getResultArray();

		$defaultArray = [
			['x' => '0:00', 'y' => 0,],
			['x' => '1:00', 'y' => 0,],
			['x' => '2:00', 'y' => 0,],
			['x' => '3:00', 'y' => 0,],
			['x' => '4:00', 'y' => 0,],
			['x' => '5:00', 'y' => 0,],
			['x' => '6:00', 'y' => 0,],
			['x' => '7:00', 'y' => 0,],
			['x' => '8:00', 'y' => 0,],
			['x' => '9:00', 'y' => 0,],
			['x' => '10:00', 'y' => 0,],
			['x' => '11:00', 'y' => 0,],
			['x' => '12:00', 'y' => 0,],
			['x' => '13:00', 'y' => 0,],
			['x' => '14:00', 'y' => 0,],
			['x' => '15:00', 'y' => 0,],
			['x' => '16:00', 'y' => 0,],
			['x' => '17:00', 'y' => 0,],
			['x' => '18:00', 'y' => 0,],
			['x' => '19:00', 'y' => 0,],
			['x' => '20:00', 'y' => 0,],
			['x' => '21:00', 'y' => 0,],
			['x' => '22:00', 'y' => 0,],
			['x' => '23:00', 'y' => 0],
		];

		$newObject = json_encode([]);

		if (!empty($hourCountArray)) {
			foreach ($defaultArray as $key => $value) {
				foreach ($hourCountArray as $hourCount) {
					if ($hourCount['hour'] . ':00' === $value['x']) $defaultArray[$key]['y'] = (int)$hourCount['total'];
					// s($hourCount['hour'] . ':00', $value['x'], $hourCount['hour'] . ':00' === $value['x']);
				}
			}

			$newObject = json_encode($defaultArray);
		}

		return $newObject;
	}
}

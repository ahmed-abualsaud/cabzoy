<?php

namespace App\Models;

use App\Entities\Zone;

class ZoneModel extends BaseModel
{
	protected $useSoftDeletes  = true;
	protected $table           = 'zones';
	protected $returnType      = Zone::class;
	protected $validationRules = ['id' => 'permit_empty|is_natural_no_zero'];
	protected $with            = ['users', 'companies'];
	protected $allowedFields   = ['company_id', 'zone_name', 'zone', 'zone_type', 'created_by'];

	/** Filter via Zone Type
	 *
	 * @param string $type One of Type `plot|boundary|off-limit`
	 * @return self */
	public function typeOf(?string $type = null)
	{
		$this->builder()->where('zone_type', $type);

		return $this;
	}
}

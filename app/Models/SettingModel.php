<?php

namespace App\Models;

use App\Entities\Setting;
use Tatter\Settings\Models\SettingModel as ModelsSettingModel;

class SettingModel extends ModelsSettingModel
{
	protected $primaryKey = 'id';
	protected $DBGroup    = 'default';
	protected $table      = 'settings';
	protected $returnType = Setting::class;
}

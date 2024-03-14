<?php

namespace App\Entities;

use Tatter\Settings\Entities\Setting as EntitiesSetting;

class Setting extends EntitiesSetting
{
	use \Tatter\Relations\Traits\ModelTrait;

	public function getContent()
	{
		if ($this->attributes['datatype'] === 'image' && null !== $this->attributes['content'])
			return site_url($this->attributes['content']);
		return $this->attributes['content'];
	}

	public function setName($name = null)
	{
		helper('custom');
		$this->attributes['name'] = preg_replace('/[^a-zA-Z0-9]/s', '', $name);

		return $this;
	}

	public function setContent($content = null)
	{
		helper('custom');
		$this->attributes['content'] = preg_replace('/[^a-zA-Z0-9 -_,.\/]/s', '', $content);

		return $this;
	}
}

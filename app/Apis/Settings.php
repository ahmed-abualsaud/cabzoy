<?php

namespace App\Apis;

use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Settings extends BaseResourceController
{

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
	}

	public function index()
	{
		$setting       = [];
		$settings      = new \App\Models\SettingModel();
		$settingObject = $settings->findAll();

		foreach ($settingObject as $value) {
			$setting[$value->name] = $value->content;
		}

		return $this->respond($setting)
			->setCache(['max-age' => 300, 's-maxage' => 900, 'etag' => 'settings-api'])
			->setStatusCode(200, 'Settings retrieved successfully.');
	}
}

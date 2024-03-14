<?php

namespace App\Apis;

use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Ping extends BaseResourceController
{
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
	}

	public function index()
	{
		return $this->respond(['ack' => time()]);
	}
}

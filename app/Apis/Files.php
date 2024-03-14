<?php

namespace App\Apis;

use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Files extends BaseResourceController
{
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
	}

	public function create()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$rules = [
			'file'   => 'uploaded[file]|max_size[file,2048]',
			'folder' => 'required|in_list[categories,documents,vehicles,users,settings]'
		];
		if (!$this->validate($rules)) return $this->failValidationErrors($this->validator->getErrors());

		$file   = $this->request->getFile('file');
		$folder = $this->request->getVar('folder');
		if (!$file->isValid()) return $this->fail($file->getErrorString());

		checkDir(UPLOADPATH . $folder);

		$uploadedFile = "$folder/" . $file->getRandomName();
		$file->move(UPLOADPATH, $uploadedFile);

		if (!$file->hasMoved()) return $this->fail($file->getErrorString());
		return $this->success(['file' => "uploads/$uploadedFile"], 'success', 'File uploaded successfully.');
	}
}

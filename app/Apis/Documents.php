<?php

namespace App\Apis;

use App\{Entities\Document, Models\DocumentModel};
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Documents extends BaseResourceController
{
	protected $modelName = DocumentModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');
		if (!config('Settings')->enableDocumentVerification)
			return $this->fail('Currently this service disabled by the administration.');

		$sort    = $this->request->getVar('sort') ?? 'desc';
		$perPage = $this->request->getVar('perPage') ?? null;

		$documents = $this->model->where('user_id', $this->authenticate->id())->orderBy('id', $sort)->paginate($perPage);

		return $this->success($documents, 'success', 'Documents fetched successfully.');
	}

	public function show($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');
		if (!config('Settings')->enableDocumentVerification)
			return $this->fail('Currently this service disabled by the administration.');

		$document = $this->model->find($id);
		if (!is($document, 'object')) return $this->fail('Document not found.');

		return $this->success($document, 'success', 'Documents fetched successfully.');
	}

	public function create()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');
		if (!config('Settings')->enableDocumentVerification)
			return $this->fail('Currently this service disabled by the administration.');

		$rules = [
			"document_front_image" => 'required',
			"document_title"       => 'required|alpha_space',
			"document_number"      => 'required|alpha_numeric',
		];
		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);

		$user_id              = $this->authenticate->id();
		$document_title       = $this->request->getVar('document_title');
		$document_number      = $this->request->getVar('document_number');
		$document_back_image  = $this->request->getVar('document_back_image');
		$document_front_image = $this->request->getVar('document_front_image');

		$document_id = $this->model->insert(new Document([
			'user_id'              => $user_id,
			'document_status'      => 'pending',
			'document_title'       => $document_title,
			'document_number'      => $document_number,
			'document_back_image'  => $document_back_image,
			'document_front_image' => $document_front_image,
		]));
		if (!$document_id) return $this->fail('Something went wrong while saving document.');

		return $this->success(null, 'created', 'Document saved successfully.');
	}

	public function update($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');
		if (!config('Settings')->enableDocumentVerification)
			return $this->fail('Currently this service disabled by the administration.');

		$document = $this->model->find($id);
		if (!is($document, 'object')) return $this->fail('Document not found.');


		$rules = [
			"document_front_image" => 'required',
			"document_title"       => 'required|alpha_space',
			"document_number"      => 'required|alpha_numeric',
		];
		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);

		$user_id              = $this->authenticate->id();
		$document_title       = $this->request->getVar('document_title');
		$document_number      = $this->request->getVar('document_number');
		$document_back_image  = $this->request->getVar('document_back_image');
		$document_front_image = $this->request->getVar('document_front_image');

		$document_id = $this->model->update($id, new Document([
			'user_id'              => $user_id,
			'document_status'      => 'pending',
			'document_title'       => $document_title,
			'document_number'      => $document_number,
			'document_back_image'  => $document_back_image,
			'document_front_image' => $document_front_image,
		]));
		if (!$document_id) return $this->fail('Something went wrong while updating document.');

		return $this->success(null, 'updated', 'Document updated successfully.');
	}

	public function delete($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');
		if (!config('Settings')->enableDocumentVerification)
			return $this->fail('Currently this service disabled by the administration.');

		$document = $this->model->find($id);
		if (!is($document, 'object')) return $this->fail('Document not found.');

		$document = $this->model->delete($id);
		if (!$document) return $this->fail('Something went wrong while deleting document.');

		return $this->success(null, 'deleted', 'Document deleted successfully.');
	}
}

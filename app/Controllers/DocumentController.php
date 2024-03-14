<?php

namespace App\Controllers;

use App\{Entities\Document, Models\DocumentModel};
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class DocumentController extends BaseController
{
	protected $documentModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		checkDir(UPLOADPATH . 'documents');
		$this->documentModel = new DocumentModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('documents', 'read')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to see documents',
		]);
		$documents = $this->documentModel->orderBy('id', 'desc')->findAll();

		return view('pages/document/list', ['documents' => $documents]);
	}

	public function add()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('documents', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create document',
		]);

		$users = $this->userModel->findAll();

		return view('pages/document/add', ['validation' => $this->validation, 'users' => $users]);
	}

	public function save(): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('documents', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create document',
		]);

		$rules = [
			"document_comment" => 'string',
			"document_number"  => 'alpha_numeric',
			"document_title"   => 'required|alpha_space',
			"user_id"          => 'required|is_natural_no_zero',
			"status"           => "required|in_list[approved, pending, rejected]",
			"back_image"       => "max_size[back_image,2048]|ext_in[back_image,png,jpg,jpeg]",
			"front_image"      => "uploaded[front_image]|max_size[front_image,2048]|ext_in[front_image,png,jpg,jpeg]",
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$front_image_path = null;
		$back_image_path  = null;
		$status           = $this->request->getPost('status');
		$user_id          = $this->request->getPost('user_id');
		$back_image       = $this->request->getFile('back_image');
		$front_image      = $this->request->getFile('front_image');
		$document_title   = $this->request->getPost('document_title');
		$document_number  = $this->request->getPost('document_number');
		$document_comment = $this->request->getPost('document_comment');

		if (!perm('documents', 'add-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to create document'
		]);

		if (!$front_image->isValid())
			return redirect()->back()->withInput()->with('errors', [$front_image->getErrorString()]);

		$front_image_path = 'documents/' . $front_image->getRandomName();
		$front_image->move(UPLOADPATH, $front_image_path);

		if (!$front_image->hasMoved()) return redirect()->back()->withInput()->with('errors', [$front_image->getErrorString()]);

		if ($back_image->isValid()) {

			$back_image_path = 'documents/' . $back_image->getRandomName();
			$back_image->move(UPLOADPATH, $back_image_path);

			if (!$back_image->hasMoved()) return redirect()->back()->withInput()->with('errors', [$back_image->getErrorString()]);
		}

		$document_id = $this->documentModel->insert(new Document([
			'document_status'      => $status,
			'user_id'              => $user_id,
			'document_title'       => $document_title,
			'document_back_image'  => $back_image_path,
			'document_front_image' => $front_image_path,
			'document_number'      => !empty($document_number) ? $document_number : null,
			'document_comment'     => !empty($document_comment) ? $document_comment : null,
		]));

		if (!$document_id) return redirect()->back()->withInput()->with(
			'errors',
			['Something went wrong in save document, please try sometime later.']
		);

		helper('notification');
		setNotification([
			'user_id'            => $user_id,
			'is_seen'            => 'unseen',
			'notification_type'  => 'announcement',
			'notification_title' => 'Admin assign a document',
			'notification_body'  => "Your document is updated.",
		]);

		return redirect()->to(route_to('documents'))->with('success', ['Document Added Successfully']);
	}

	public function edit(?int $id = null)
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('documents', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update document'
		]);

		$document = $this->documentModel->find($id);
		if (!is($document, 'object')) return redirect()->back()->with('errors', ['Document not found']);

		$users = $this->userModel->findAll();

		return view('pages/document/edit', [
			'document' => $document, 'users' => $users, 'validation' => $this->validation,
		]);
	}

	public function update(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('documents', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update document',
		]);

		$document = $this->documentModel->find($id);
		if (!is($document, 'object')) return redirect()->back()->with('errors', ['Document not found']);

		$rules = [
			"document_comment" => 'string',
			"document_number"  => 'alpha_numeric',
			"document_title"   => 'required|alpha_space',
			"user_id"          => 'required|is_natural_no_zero',
			"status"           => "required|in_list[approved, pending, rejected]",
			"back_image"       => "max_size[back_image,2048]|ext_in[back_image,png,jpg,jpeg]",
			"front_image"      => "uploaded[front_image]|max_size[front_image,2048]|ext_in[front_image,png,jpg,jpeg]",
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$front_image_path = null;
		$back_image_path  = null;
		$status           = $this->request->getPost('status');
		$user_id          = $this->request->getPost('user_id');
		$back_image       = $this->request->getFile('back_image');
		$front_image      = $this->request->getFile('front_image');
		$document_title   = $this->request->getPost('document_title');
		$document_number  = $this->request->getPost('document_number');
		$document_comment = $this->request->getPost('document_comment');

		if (!perm('documents', 'update-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to edit document'
		]);

		if (!$front_image->isValid())
			return redirect()->back()->withInput()->with('errors', [$front_image->getErrorString()]);

		$front_image_path = 'documents/' . $front_image->getRandomName();
		$front_image->move(UPLOADPATH, $front_image_path);

		if (!$front_image->hasMoved()) return redirect()->back()->withInput()->with('errors', [$front_image->getErrorString()]);

		if ($back_image->isValid()) {

			$back_image_path = 'documents/' . $back_image->getRandomName();
			$back_image->move(UPLOADPATH, $back_image_path);

			if (!$back_image->hasMoved()) return redirect()->back()->withInput()->with('errors', [$back_image->getErrorString()]);
		}

		$document_id = $this->documentModel->update($id, new Document([
			'document_status'      => $status,
			'user_id'              => $user_id,
			'document_title'       => $document_title,
			'document_back_image'  => $back_image_path,
			'document_front_image' => $front_image_path,
			'document_number'      => !empty($document_number) ? $document_number : null,
			'document_comment'     => !empty($document_comment) ? $document_comment : null,
		]));

		if (!$document_id) return redirect()->back()->withInput()->with(
			'errors',
			['Something went wrong in update document, please try sometime later.']
		);

		helper('notification');
		setNotification([
			'user_id'            => $user_id,
			'is_seen'            => 'unseen',
			'notification_type'  => 'announcement',
			'notification_title' => 'Admin reacted to your document',
			'notification_body'  => "Your document $document_title is $status .",
		]);

		return redirect()->to(route_to('documents'))->with('success', ['Document Updated Successfully']);
	}

	public function delete(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('documents', 'delete')) return redirect()->to(route_to('dashboard'))->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to delete document',
		]);

		$document = $this->documentModel->find($id);
		if (is($document, 'object')) {
			if ($this->documentModel->delete($id, true)) return redirect()->back()->with('success', ['Document deleted successfully']);

			return redirect()->back()->with('errors', ['Document not deleted']);
		}

		return redirect()->back()->with('errors', ['Document not found']);
	}
}

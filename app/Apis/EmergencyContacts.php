<?php

namespace App\Apis;

use App\{Entities\EmergencyContact, Models\EmergencyContactModel};
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class EmergencyContacts extends BaseResourceController
{
	protected $emergencyContactModel;
	protected $modelName = EmergencyContactModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->emergencyContactModel = new EmergencyContactModel();
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$sort    = $this->request->getVar('sort') ?? 'desc';
		$perPage = $this->request->getVar('perPage') ?? null;

		$emergency_contacts = $this->emergencyContactModel->where('user_id', $this->authenticate->id())->orderBy('id', $sort)->paginate($perPage);

		return $this->success($emergency_contacts, 'success', 'Emergency Contacts fetched successfully.');
	}

	public function show($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$emergency_contact = $this->model->find($id);
		if (!is($emergency_contact, 'object')) return $this->fail('Emergency Contact not found.');

		return $this->success($emergency_contact, 'success', 'Emergency Contacts fetched successfully.');
	}

	public function create()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$rules = [
			'phone' => 'required',
			'email' => 'required',
			'name'  => 'required|alpha_space',
		];
		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);

		$user_id = $this->authenticate->id();
		$name    = $this->request->getVar('name');
		$phone   = $this->request->getVar('phone');
		$email   = $this->request->getVar('email');

		$emergency_contact_id = $this->model->insert(new EmergencyContact([
			'name'    => $name,
			'phone'   => $phone,
			'email'   => $email,
			'user_id' => $user_id,
		]));
		if (!$emergency_contact_id) return $this->fail('Something went wrong while saving emergencyContact.');

		return $this->success(null, 'created', 'Emergency Contact saved successfully.');
	}

	public function update($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$emergency_contact = $this->model->find($id);
		if (!is($emergency_contact, 'object')) return $this->fail('Emergency Contact not found.');

		$rules = [
			'phone' => 'required',
			'email' => 'required',
			'name'  => 'required|alpha_space',
		];
		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);


		$user_id = $this->authenticate->id();
		$name    = $this->request->getVar('name');
		$phone   = $this->request->getVar('phone');
		$email   = $this->request->getVar('email');

		$emergency_contact_id = $this->model->update($id, new EmergencyContact([
			'user_id' => $user_id,
			'name'    => $name,
			'phone'   => $phone,
			'email'   => $email,
		]));
		if (!$emergency_contact_id) return $this->fail('Something went wrong while updating emergency Contact.');

		return $this->success(null, 'created', 'Emergency Contact updated successfully.');
	}

	public function delete($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$emergency_contact = $this->model->find($id);
		if (!is($emergency_contact, 'object')) return $this->fail('Emergency Contact not found.');

		$emergency_contact = $this->model->delete($id);
		if (!$emergency_contact) return $this->fail('Something went wrong while deleting account.');

		return $this->success(null, 'deleted', 'emergency contact deleted successfully.');
	}
}

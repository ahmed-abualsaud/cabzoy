<?php

namespace App\Apis;

use App\Entities\User;
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Families extends BaseResourceController
{
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$sort    = $this->request->getVar('sort') ?? 'desc';
		$perPage = $this->request->getVar('perPage') ?? null;

		$families = $this->userModel->where('user_id', $this->authenticate->id())->orderBy('id', $sort)->paginate($perPage);

		return $this->success($families, 'success', 'Families fetched successfully.');
	}

	public function show($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$family = $this->userModel->find($id);
		if (!is($family, 'object')) return $this->fail('Family not found.');

		return $this->success($family, 'success', 'Families fetched successfully.');
	}

	public function create()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$rules = [
			'password'    => 'required|min_length[3]',
			'firstname'   => 'required|alpha|min_length[3]',
			'lastname'    => 'required|alpha|min_length[3]',
			'group'       => 'required|in_list[users,drivers]',
			'phone'       => 'required|regex_match[' . VALID_PHONE . ']',
			'email'       => 'required|valid_email|is_unique[users.email,id]',
			'username'    => 'required|alpha_dash|min_length[3]|is_unique[users.username,id]',
		];
		$messages = ['username' => ['is_unique' => 'The username already taken, try another username.']];

		if (!$this->validate($rules, $messages)) return $this->failValidationErrors($this->validator->getErrors());

		$isAlreadyRegistered = $this->userModel->orWhere('email', $this->request->getVar('email'))
			->where('phone', $this->request->getVar('phone'))->withGroup($this->request->getVar('group'))->first();
		if (is_object($isAlreadyRegistered) && !empty($isAlreadyRegistered))
			return $this->failResourceExists('User already registered.');

		$user = new User([
			'force_pass_reset' => 0,
			'user_id'          => $this->authenticate->id(),
			'email'            => $this->request->getVar('email'),
			'phone'            => $this->request->getVar('phone'),
			'lastname'         => $this->request->getVar('lastname'),
			'password'         => $this->request->getVar('password'),
			'username'         => $this->request->getVar('username'),
			'firstname'        => $this->request->getVar('firstname'),
			'profile_pic'      => $this->request->getVar('profile_pic') ?? null,
		]);

		$this->config->requireActivation === null ? $user->activate() : $user->generateActivateHash();
		if (!$this->userModel->withGroup($this->request->getVar('group') ?? 'users')->save($user))
			return $this->fail($this->userModel->errors());

		if (!config('Settings')->enableAutoVerifyUser && $this->config->requireActivation !== null) {
			$activator = service('activator');
			$sent      = $activator->send($user);

			if (!$sent) return $this->fail([$activator->error() ?? lang('Auth.unknownError')]);

			return $this->success(null, 'created', lang('Auth.activationSuccess'));
		}

		return $this->success(null, 'created', 'User successfully added to family.');
	}

	public function delete($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$family = $this->userModel->where('user_id', $this->authenticate->id())->find($id);
		if (!is($family, 'object')) return $this->fail('Family member not found.');

		$family = $this->userModel->delete($id);
		if (!$family) return $this->fail('Something went wrong while deleting family.');

		return $this->success(null, 'deleted', 'Family deleted successfully.');
	}
}

<?php

namespace App\Apis;

use App\Entities\User as EntitiesUser;
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class User extends BaseResourceController
{
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
	}

	public function login()
	{
		if ($this->authenticate->check()) return $this->success($this->authenticate->user(), 'success', 'user already logged in.');

		$loginAttemptArray = [];
		$rules = [
			'login'    => 'required|in_list[phone,email,username]',
			'group'    => 'required|in_list[users,drivers]',
			'password' => 'required',
		];

		if (
			in_array($this->request->getVar('login'), $this->config->validFields)
			&& $this->request->getVar('login') === 'email'
			&& config('Settings')->enableEmailLogin
		) {
			$rules['email'] = 'required|valid_email';
			$loginAttemptArray['email'] = $this->request->getVar('email');
		} elseif (
			in_array($this->request->getVar('login'), $this->config->validFields)
			&& $this->request->getVar('login') === 'phone'
			&& config('Settings')->enablePhoneLogin
		) {
			$rules['phone'] = 'required|regex_match[' . VALID_PHONE . ']';
			$loginAttemptArray['phone'] = $this->request->getVar('phone');
		} elseif (
			in_array($this->request->getVar('login'), $this->config->validFields)
			&& 'username' === $this->request->getVar('login')
		) {
			$rules['username'] = 'required';
			$loginAttemptArray['username'] = $this->request->getVar('username');
		} else $rules['email'] = 'in_list[phone,email,username]';

		if (!$this->validate($rules))
			return $this->failValidationErrors($this->validator->getErrors());

		$loginAttemptArray['password'] = $this->request->getVar('password');

		$auth = $this->authenticate->attempt($loginAttemptArray, true, $this->request->getVar('group'));

		if ($auth) return $this->success($this->authenticate->user(), 'success', 'Logged in successfully');
		return $this->failUnauthorized($this->authenticate->error());
	}

	public function logout()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not logged in');

		$this->authenticate->logout();
		if (!$this->authenticate->check())
			return $this->success(null, 'no-content', 'User logout successfully.');
		return $this->failForbidden($this->authenticate->error() ?? 'Something went wrong while logging out.');
	}

	public function register()
	{
		if ($this->authenticate->check()) return $this->success($this->authenticate->user(), 'success', 'user already logged in.');

		$rules = [
			'password'  => 'required|min_length[3]',
			'firstname' => 'required|alpha|min_length[3]',
			'lastname'  => 'required|alpha|min_length[3]',
			'group'     => 'required|in_list[users,drivers]',
			'phone'     => 'required|regex_match[' . VALID_PHONE . ']',
			'email'     => 'required|valid_email|is_unique[users.email,id]',
			'username'  => 'required|alpha_dash|min_length[3]|is_unique[users.username,id]',
		];
		$messages = ['username' => ['is_unique' => 'The username already taken, try another username.']];

		if (!$this->validate($rules, $messages)) return $this->failValidationErrors($this->validator->getErrors());

		$isAlreadyRegistered = $this->userModel->orWhere('email', $this->request->getVar('email'))
			->where('phone', $this->request->getVar('phone'))->withGroup($this->request->getVar('group'))->first();
		if (is_object($isAlreadyRegistered) && !empty($isAlreadyRegistered))
			return $this->failResourceExists('User already registered.');

		$user = new EntitiesUser([
			'force_pass_reset' => 0,
			'email'            => $this->request->getVar('email'),
			'phone'            => $this->request->getVar('phone'),
			'lastname'         => $this->request->getVar('lastname'),
			'password'         => $this->request->getVar('password'),
			'username'         => $this->request->getVar('username'),
			'firstname'        => $this->request->getVar('firstname'),
			'profile_pic'      => $this->request->getVar('profile_pic') ?? null,
		]);

		config('Settings')->enableAutoVerifyUser && $this->config->requireActivation === null ? $user->activate() : $user->generateActivateHash();
		if (!$this->userModel->withGroup($this->request->getVar('group') ?? 'users')->save($user))
			return $this->fail($this->userModel->errors());

		if (!config('Settings')->enableAutoVerifyUser && $this->config->requireActivation !== null) {
			$activator = service('activator');
			$sent      = $activator->send($user);

			if (!$sent) return $this->fail([$activator->error() ?? lang('Auth.unknownError')]);

			return $this->success(null, 'created', lang('Auth.activationSuccess'));
		}

		return $this->success(null, 'created', 'User is register successfully');
	}

	public function forgotPassword()
	{
		$rules = ['email' => 'required|valid_email'];
		if (!$this->validate($rules)) return $this->failValidationErrors($this->validator->getErrors());;

		$user = $this->userModel->where('email', $this->request->getVar('email'))->first();
		if (null === $user) return $this->failNotFound(lang('Auth.forgotNoUser'));

		// Save the reset hash /
		$user->generateResetHash();
		$this->userModel->save($user);

		$resetter = service('resetter');
		$sent     = $resetter->send($user);

		if (!$sent) return $this->fail($resetter->error() ?? lang('Auth.unknownError'));

		return $this->success(null, 'no-content', lang('Auth.forgotEmailSent'));
	}

	public function me()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');
		return $this->success($this->authenticate->user(), 'success', 'User details fetched successfully.');
	}

	public function updateProfile()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not logged in');

		$user = $this->userModel->find($this->authenticate->id());
		if (!$user) return $this->failNotFound('User not found');

		$userData = [];
		if (!empty($this->request->getVar('lat'))) $userData['lat']                 = $this->request->getVar('lat');
		if (!empty($this->request->getVar('long'))) $userData['long']               = $this->request->getVar('long');
		if (!empty($this->request->getVar('speed'))) $userData['speed']             = $this->request->getVar('speed');
		if (!empty($this->request->getVar('heading'))) $userData['heading']         = $this->request->getVar('heading');
		if (!empty($this->request->getVar('lastname'))) $userData['lastname']       = $this->request->getVar('lastname');
		if (!empty($this->request->getVar('firstname'))) $userData['firstname']     = $this->request->getVar('firstname');
		if (!empty($this->request->getVar('is_online'))) $userData['is_online']     = $this->request->getVar('is_online');
		if (!empty($this->request->getVar('app_token'))) $userData['app_token']     = $this->request->getVar('app_token');
		if (!empty($this->request->getVar('profile_pic'))) $userData['profile_pic'] = $this->request->getVar('profile_pic');
		if (!empty($this->request->getVar('is_phone_verified')))
			$userData['is_phone_verified'] = $this->request->getVar('is_phone_verified');

		$userUpdate = $this->userModel->update($this->authenticate->id(), new EntitiesUser($userData));
		if ($userUpdate) return $this->success($this->userModel->find($this->authenticate->id()), 'updated', 'User updated successfully.');
		return $this->fail('Something went wrong while update users.');
	}
}

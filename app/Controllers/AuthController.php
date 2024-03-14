<?php

namespace App\Controllers;

use App\{Entities\User, Models\UserModel};

class AuthController extends BaseController
{
	/** @var \Myth\Auth\Config\Auth */
	protected $config;

	public function __construct()
	{
		$this->config = config('Auth');
	}

	//--------------------------------------------------------------------
	// Login/out
	//--------------------------------------------------------------------

	/**
	 * Displays the login form, or redirects
	 * the user to their destination/home if
	 * they are already logged in.
	 */
	public function login()
	{
		if ($this->authenticate->check()) return redirect()->to(route_to('dashboard'));

		return view('pages/auth/login', ['config' => $this->config, 'validation' => $this->validation]);
	}

	/**
	 * Attempts to verify the user's credentials
	 * through a POST request.
	 */
	public function attemptLogin()
	{
		$rules = ['login' => 'required', 'password' => 'required'];

		if ($this->config->validFields === ['email']) $rules['login'] .= '|valid_email';
		if (!$this->validate($rules))
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$login    = $this->request->getPost('login');
		$password = $this->request->getPost('password');
		$remember = (bool) $this->request->getPost('remember');

		// Determine credential type
		$type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

		// Try to log them in...
		if (!$this->authenticate->attempt([$type => $login, 'password' => $password], $remember))
			return redirect()->back()->withInput()->with('errors', ['auth' => $this->authenticate->error()]);

		// Is the user being forced to reset their password?
		if ($this->authenticate->user()->force_pass_reset === true)
			return redirect()->to(route_to('reset-password') . '?token=' . $this->authenticate->user()->reset_hash)->withCookies();

		return redirect()->to(route_to('dashboard'))->withCookies()->with('success', [lang('Auth.loginSuccess')]);
	}

	/**
	 * Log the user out.
	 */
	public function logout()
	{
		if ($this->authenticate->check()) $this->authenticate->logout();

		return redirect()->to(route_to('login'))->with('success', ['Logout successfully']);
	}

	//--------------------------------------------------------------------
	// Register
	//--------------------------------------------------------------------

	/**
	 * Displays the user registration page
	 */
	public function register()
	{
		// check if already logged in.
		if ($this->authenticate->check()) return redirect()->to(route_to('dashboard'));

		// Check if registration is allowed
		if (!$this->config->allowRegistration)
			return redirect()->back()->withInput()->with('errors', [lang('Auth.registerDisabled')]);

		return view('pages/auth/register', ['config' => $this->config, 'validation' => $this->validation]);
	}

	/**
	 * Attempt to register a new user.
	 */
	public function attemptRegister()
	{
		// Check if registration is allowed
		if (!$this->config->allowRegistration)
			return redirect()->back()->withInput()->with('errors', [lang('Auth.registerDisabled')]);

		$users = new UserModel();

		// Validate basics first since some password rules rely on these fields
		$rules = [
			'username' => 'required|alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username,id]',
			'email'    => 'required|valid_email|is_unique[users.email,id]',
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		// Validate passwords since they can only be validated properly here
		$rules = [
			'password'     => 'required|strong_password',
			'pass_confirm' => 'required|matches[password]',
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		// Save the user
		$allowedPostFields = array_merge(['password'], $this->config->validFields, $this->config->personalFields);
		$user              = new User($this->request->getPost($allowedPostFields));

		$this->config->requireActivation === null ? $user->activate() : $user->generateActivateHash();

		// Ensure default group gets assigned if set
		if (is($this->config->defaultUserGroup)) $users = $users->withGroup($this->config->defaultUserGroup);
		if (!$users->save($user)) return redirect()->back()->withInput()->with('errors', $users->errors());

		if ($this->config->requireActivation !== null) {
			$activator = service('activator');
			$sent      = $activator->send($user);

			if (!$sent) return redirect()->back()->withInput()->with('errors', [$activator->error() ?? lang('Auth.unknownError')]);

			return redirect()->route('login')->with('success', [lang('Auth.activationSuccess')]);
		}

		return redirect()->route('login')->with('success', [lang('Auth.registerSuccess')]);
	}

	//--------------------------------------------------------------------
	// Forgot Password
	//--------------------------------------------------------------------

	/**
	 * Displays the forgot password form.
	 */
	public function forgotPassword()
	{
		if ($this->authenticate->check()) return redirect()->to(route_to('dashboard'));
		if ($this->config->activeResetter === null) return redirect()->route('login')->with('errors', [lang('Auth.forgotDisabled')]);

		return view('pages/auth/forgot', ['config' => $this->config, 'validation' => $this->validation]);
	}

	/** Attempts to find a user account with that password
	 * and send password reset instructions to them. */
	public function attemptForgot()
	{
		if (logged_in()) return redirect()->back()->with('errors', [
			'You are already logged in with another account, Please logout and try again.'
		]);
		if ($this->config->activeResetter === null) return redirect()->route('login')->with('errors', [lang('Auth.forgotDisabled')]);

		$rules = ['email' => 'required|valid_email'];
		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$users = new UserModel();
		$user  = $users->where('email', $this->request->getPost('email'))->first();

		if (null === $user) return redirect()->back()->with('errors', [lang('Auth.forgotNoUser')]);

		// Save the reset hash /
		$user->generateResetHash();
		$users->save($user);

		$resetter = service('resetter');
		$sent     = $resetter->send($user);

		if (!$sent) return redirect()->back()->withInput()->with('errors', [$resetter->error() ?? lang('Auth.unknownError')]);

		return redirect()->route('reset-password')->with('success', [lang('Auth.forgotEmailSent')]);
	}

	/**
	 * Displays the Reset Password form
	 */
	public function resetPassword()
	{
		if ($this->authenticate->check()) return redirect()->to(route_to('dashboard'));
		if ($this->config->activeResetter === null) return redirect()->route('login')->with('errors', [lang('Auth.forgotDisabled')]);
		$token = $this->request->getGet('token');

		return view('pages/auth/reset', ['config' => $this->config, 'token' => $token, 'validation' => $this->validation]);
	}

	/** Verifies the code with the email and saves the new password,
	 * if they all pass validation.
	 *
	 * @return mixed */
	public function attemptReset()
	{
		if (logged_in()) return redirect()->back()->with('errors', [
			'You are already logged in with another account, Please logout and try again.'
		]);
		if ($this->config->activeResetter === null) return redirect()->route('login')->with('errors', [lang('Auth.forgotDisabled')]);

		$users = new UserModel();

		// First things first - log the reset attempt.
		$users->logResetAttempt(
			$this->request->getPost('email') ?? '',
			$this->request->getPost('token'),
			$this->request->getIPAddress(),
			(string) $this->request->getUserAgent()
		);

		$rules = [
			'token'        => 'required',
			'email'        => 'required|valid_email',
			'password'     => 'required|strong_password',
			'pass_confirm' => 'required|matches[password]',
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$user = $users->where('email', $this->request->getPost('email'))
			->where('reset_hash', $this->request->getPost('token'))
			->first();

		if (null === $user) return redirect()->back()->with('errors', [lang('Auth.forgotNoUser')]);

		// Reset token still valid?
		if (!empty($user->reset_expires) && time() > $user->reset_expires->getTimestamp())
			return redirect()->back()->withInput()->with('errors', [lang('Auth.resetTokenExpired')]);

		// Success! Save the new password, and cleanup the reset hash.
		$user->password         = $this->request->getPost('password');
		$user->reset_hash       = null;
		$user->reset_at         = date('Y-m-d H:i:s');
		$user->reset_expires    = null;
		$user->force_pass_reset = false;
		$users->save($user);

		return redirect()->route('login')->with('success', [lang('Auth.resetSuccess')]);
	}

	/** Activate account.
	 *
	 * @return mixed */
	public function activateAccount()
	{
		if (logged_in()) return redirect()->back()->with('errors', [
			'You are already logged in with another account, Please logout and try again.'
		]);
		$users = new UserModel();

		// First things first - log the activation attempt.
		$users->logActivationAttempt(
			$this->request->getGet('token'),
			$this->request->getIPAddress(),
			(string) $this->request->getUserAgent()
		);

		$throttler = service('throttler');

		if ($throttler->check(md5($this->request->getIPAddress()), 2, MINUTE) === false)
			return service('response')->setStatusCode(429)->setBody(lang('Auth.tooManyRequests', [$throttler->getTokentime()]));

		$user = $users->where('activate_hash', $this->request->getGet('token'))
			->where('active', 0)
			->first();

		if (null === $user) return redirect()->route('login')->with('errors', [lang('Auth.activationNoUser')]);

		if (config('Settings')->enableAutoVerifyUserAfterEmailVerify) $user->activate();
		else $user->verified();
		$users->save($user);

		return view('pages/auth/verification');
	}

	/** Resend activation account.
	 *
	 * @return mixed */
	public function resendActivateAccount()
	{
		if (logged_in()) return redirect()->back()->with('errors', [
			'You are already logged in with another account, Please logout and try again.'
		]);
		if ($this->config->requireActivation === null) return redirect()->route('login');

		$throttler = service('throttler');
		if ($throttler->check(md5($this->request->getIPAddress()), 2, MINUTE) === false)
			return service('response')->setStatusCode(429)->setBody(lang('Auth.tooManyRequests', [$throttler->getTokentime()]));

		$login = urldecode($this->request->getGet('login') ?? '');
		$type  = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
		$users = new UserModel();
		$user  = $users->where($type, $login)->where('active', 0)->first();

		if (null === $user) return redirect()->route('login')->with('errors', [lang('Auth.activationNoUser')]);

		$activator = service('activator');
		$sent      = $activator->send($user);

		if (!$sent) return redirect()->back()->withInput()->with('errors', [$activator->error() ?? lang('Auth.unknownError')]);

		return redirect()->route('login')->with('success', [lang('Auth.activationSuccess')]);
	}
}

<?php

namespace App\Controllers;

use App\Entities\User;
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class UserController extends BaseController
{
	protected $userRole;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		helper('text');
		checkDir(UPLOADPATH . 'users');
		$this->userRole  = role_from_url();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm($this->userRole, 'read')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to access ' . singular($this->userRole),
		]);

		$users = $this->userModel->inGroup($this->userRole)->orderBy('id', 'desc')->findAll();

		return view('pages/user/list', ['users' => $users, 'role' => $this->userRole]);
	}

	public function add()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm($this->userRole, 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create ' . singular($this->userRole),
		]);

		return view('pages/user/add', ['role' => $this->userRole, 'validation' => $this->validation]);
	}

	public function save(): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm($this->userRole, 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create ' . singular($this->userRole),
		]);

		$rules = [
			'phone'       => 'required|numeric',
			'active'      => 'required|in_list[0, 2, 1]',
			'firstname'   => 'required|alpha|min_length[3]',
			'lastname'    => 'required|alpha|min_length[3]',
			'password'    => 'required|string|min_length[3]',
			'email'       => 'required|valid_email|is_unique[users.email,id]',
			'username'    => 'required|alpha_dash|min_length[3]|is_unique[users.username,id]',
			'profile_pic' => 'uploaded[profile_pic]|max_size[profile_pic,2048]|ext_in[profile_pic,png,jpg,jpeg]',
		];

		$messages = ['username' => ['is_unique' => 'The username already taken, try another username.']];

		if (!$this->validate($rules, $messages))
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$email          = $this->request->getPost('email');
		$phone          = $this->request->getPost('phone');
		$status         = $this->request->getPost('status');
		$active         = $this->request->getPost('active');
		$lastname       = $this->request->getPost('lastname');
		$password       = $this->request->getPost('password');
		$username       = $this->request->getPost('username');
		$firstname      = $this->request->getPost('firstname');
		$file           = $this->request->getFile('profile_pic');
		$status_message = $this->request->getPost('status_message');

		if (!perm($this->userRole, 'add-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to create ' . singular($this->userRole)
		]);

		if (!$file->isValid())
			return redirect()->back()->withInput()->with('errors', [$file->getErrorString()]);

		$profile_pic = 'users/' . $file->getRandomName();
		$file->move(UPLOADPATH, $profile_pic);

		if (!$file->hasMoved())
			return redirect()->back()->withInput()->with('errors', [$file->getErrorString()]);

		$user = [
			'force_pass_reset' => '0',
			'speed'            => '0',
			'heading'          => '0',
			'email'            => $email,
			'phone'            => $phone,
			'active'           => $active,
			'status'           => $status,
			'lastname'         => $lastname,
			'password'         => $password,
			'username'         => $username,
			'is_online'        => 'offline',
			'firstname'        => $firstname,
			'profile_pic'      => $profile_pic,
			'status_message'   => $status_message,
		];

		if (!empty($status)) $updateData['status'] = '';

		$newUser = $this->userModel->withGroup($this->userRole)->save(new User($user));

		if (!$newUser) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong to assign the user to ' . $this->userRole,
		]);

		return redirect()->to(route_to($this->userRole))->with('success', [singular($this->userRole) . ' Added Successfully']);
	}

	public function edit(?int $id = null)
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm($this->userRole, 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update ' . singular($this->userRole),
		]);

		$user = $this->userModel->find($id);
		if (!is($user, 'object')) return redirect()->back()->with('errors', ['User not found']);

		return view('pages/user/edit', ['role' => $this->userRole, 'validation' => $this->validation, 'user' => $user]);
	}

	public function update(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm($this->userRole, 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update ' . singular($this->userRole),
		]);

		$user = $this->userModel->withGroup($this->userRole)->find($id);
		if (!is($user, 'object')) return redirect()->back()->with('errors', [singular($this->userRole) . ' not found']);

		$rules = [
			'phone'       => 'required|numeric',
			'active'      => 'required|in_list[0, 2, 1]',
			'firstname'   => 'required|alpha|min_length[3]',
			'lastname'    => 'required|alpha|min_length[3]',
			'email'       => "required|valid_email|is_unique[users.email,id,{$id}]",
			'username'    => "required|alpha_dash|min_length[3]|is_unique[users.username,id,{$id}]",
			'profile_pic' => 'uploaded[profile_pic]|max_size[profile_pic,2048]|ext_in[profile_pic,png,jpg,jpeg]',
		];

		$messages = ['username' => ['is_unique' => 'The username already taken, try another username.']];

		if (!$this->validate($rules, $messages))
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$email          = $this->request->getPost('email');
		$phone          = $this->request->getPost('phone');
		$status         = $this->request->getPost('status');
		$active         = $this->request->getPost('active');
		$lastname       = $this->request->getPost('lastname');
		$password       = $this->request->getPost('password');
		$username       = $this->request->getPost('username');
		$firstname      = $this->request->getPost('firstname');
		$file           = $this->request->getFile('profile_pic');
		$status_message = $this->request->getPost('status_message');

		if (!perm($this->userRole, 'update-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to edit ' . singular($this->userRole)
		]);

		if (!$file->isValid())
			return redirect()->back()->withInput()->with('errors', [$file->getErrorString()]);

		// Delete Old Files
		$imageFilePath = str_replace('uploads/', '', UPLOADPATH) . $user->getProfilePic(false);
		if (!empty($user->getProfilePic(false)) && file_exists($imageFilePath)) unlink($imageFilePath);

		$profile_pic = 'users/' . $file->getRandomName();
		$file->move(UPLOADPATH, $profile_pic);

		if (!$file->hasMoved())
			return redirect()->back()->withInput()->with('errors', [$file->getErrorString()]);

		$updateData = [
			'email'          => $email,
			'phone'          => $phone,
			'active'         => $active,
			'status'         => $status,
			'lastname'       => $lastname,
			'username'       => $username,
			'firstname'      => $firstname,
			'profile_pic'    => $profile_pic,
			'status_message' => $status_message,
		];

		if (empty($status)) $updateData['status'] = '';
		if (!empty($password)) $updateData['password'] = $password;

		$updatedUser = $this->userModel->update($id, new User($updateData));

		if (!$updatedUser) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong while updating the ' . singular($this->userRole),
		]);

		return redirect()->to(route_to($this->userRole))->with('success', [singular($this->userRole) . ' Updated Successfully']);
	}

	public function delete(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm($this->userRole, 'delete')) return redirect()->to(route_to('dashboard'))->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to delete ' . singular($this->userRole),
		]);

		$user = $this->userModel->find($id);
		if (is($user, 'object')) {
			if ($this->userModel->delete($id))
				return redirect()->back()->with('success', [singular($this->userRole) . ' deleted successfully']);

			return redirect()->back()->with('errors', [singular($this->userRole) . ' not deleted']);
		}

		return redirect()->back()->with('errors', [singular($this->userRole) . ' not found']);
	}
}

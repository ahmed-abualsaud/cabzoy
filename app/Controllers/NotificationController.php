<?php

namespace App\Controllers;

use App\{Entities\Notification, Models\NotificationModel};
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class NotificationController extends BaseController
{
	protected $notificationModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		checkDir(UPLOADPATH . 'notifications');
		$this->notificationModel = new NotificationModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('notifications', 'read')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to see notifications',
		]);

		$notifications = $this->notificationModel->orderBy('id', 'desc')->findAll();

		return view('pages/notification/list', ['notifications' => $notifications]);
	}


	public function add()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('notifications', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create notification',
		]);

		$users = $this->userModel->inGroup(['users', 'drivers'])->findAll();

		return view('pages/notification/add', ['validation' => $this->validation, 'users' => $users]);
	}

	public function save(): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('notifications', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create notification',
		]);

		$rules = [
			'user_id'            => 'required',
			'notification_title' => 'required|max_length[120]',
			'notification_body'  => 'required|max_length[500]',
			'image'              => 'max_size[image,2048]|ext_in[image,png,jpg,jpeg]',
			'notification_type'  => 'required|in_list[default, announcement, message, order, transaction]',
		];
		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$file               = $this->request->getFile('image');
		$userIds            = $this->request->getPost('user_id');
		$notification_body  = $this->request->getPost('notification_body');
		$notification_type  = $this->request->getPost('notification_type');
		$notification_title = $this->request->getPost('notification_title');

		$notificationData = [
			'is_seen'            => 'unseen',
			'notification_body'  => $notification_body,
			'notification_type'  => $notification_type,
			'notification_title' => $notification_title,
		];

		if (!perm('notifications', 'add-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to create notification'
		]);

		if ($file->isValid()) {
			$image = 'notifications/' . $file->getRandomName();
			$file->move(UPLOADPATH, $image);

			if (!$file->hasMoved()) return redirect()->back()->withInput()->with('errors', [$file->getErrorString()]);
			$notificationData['notification_image'] = $image;
		}

		foreach ($userIds as $user_id) {
			$notificationData['user_id'] = $user_id;

			$notification = $this->notificationModel->save(new Notification($notificationData));
			if (!$notification) return redirect()->back()->withInput()->with('errors', [
				"Something went wrong in sending notification to user id $user_id, please try sometime later.",
			]);
		}

		return redirect()->to(route_to('notifications'))->with('success', ['Notification Send Successfully']);
	}
}

<?php

namespace App\Apis;

use App\Libraries\PushNotification;
use App\Models\NotificationModel;
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Notifications extends BaseResourceController
{
	protected $modelName = NotificationModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
	}

	public function index()
	{
		if (!$this->authenticate->check())
			return $this->failUnauthorized('User not logged in');

		$sort = $this->request->getVar('sort') ?? 'desc';
		$perPage = $this->request->getVar('perPage') ?? null;

		$notification = $this->model->where('user_id', $this->authenticate->id())->orderBy('id', $sort)->paginate($perPage);
		return $this->success($notification, 'success', 'Notifications fetched successfully.');
	}


	public function create()
	{
		if (!$this->authenticate->check())
			return $this->failUnauthorized('User not login. Please login first.');

		if (!$this->validate(['user_id' => 'required']))
			return $this->failValidationErrors(
				$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
			);

		$body    = $this->request->getVar('body') ?? 'Incoming Audio Call';
		$data    = $this->request->getVar('data');
		$user_id = $this->request->getVar('user_id');

		$user = $this->userModel->find($user_id);
		if (empty($user) || empty($user->app_token))
			return $this->failNotFound('user token not found.');

		$title  = $this->request->getVar('title') ?? ucwords("$user->firstname $user->lastname");
		$client = new PushNotification("key=" . env('FCM_SERVER_KEY'));

		$rawResponse = $client->to($user->app_token)->toChannel('communication-channel')
			->withNotification($title, $body, $user->profile_pic)->withData($data)->send();
		$response = json_decode($rawResponse);

		if ($response->success !== 1) return $this->fail($response->results[0]->error);
		return $this->success($response->results[0], 'created', 'Notification send successfully.');
	}

	public function update($id = null)
	{
		if (!$this->authenticate->check())
			return $this->failUnauthorized('User not logged in');

		$notification = $this->model->find($id);

		if (!$notification)
			return $this->failNotFound('Notification not found');
		if ($notification->user_id !== $this->authenticate->id())
			return $this->failUnauthorized('Notification not found');

		$notificationUpdate = $this->model->update($id, ['is_seen' => 'seen']);
		if ($notificationUpdate)
			return $this->success($this->model->find($id), 'updated', 'Notification updated successfully.');
		return $this->fail('Something went wrong while update notifications.');
	}
}

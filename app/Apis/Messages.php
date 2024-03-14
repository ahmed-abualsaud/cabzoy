<?php

namespace App\Apis;

use App\Entities\{Message, MessageGroup};
use App\Models\{MessageGroupModel, MessageModel};
use CodeIgniter\HTTP\{RequestInterface, Response, ResponseInterface};
use Psr\Log\LoggerInterface;

class Messages extends BaseResourceController
{
	/** @var \App\Models\MessageGroupModel */
	protected $modelName = MessageGroupModel::class;

	/** @var \App\Models\MessageModel */
	protected $messageModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->messageModel = new MessageModel();
	}

	public function index(): Response
	{
		if (!$this->authenticate->check())
			return $this->failUnauthorized('User not login. Please login first.');

		$order_id = $this->request->getVar('order_id');
		$sort = $this->request->getVar('sort') ?? 'desc';

		if (empty($order_id))
			return $this->failNotFound();

		$messageGroupId = $this->model->where('order_id', $order_id)->first();

		if (empty($messageGroupId))
			return $this->failNotFound();

		$messages = $this->messageModel->where('message_group_id', $messageGroupId->id)->orderBy('id', $sort)->findAll();

		return $this->success($messages, 'success', 'Messages fetched successfully.');
	}

	public function create()
	{
		if (!$this->authenticate->check())
			return $this->failUnauthorized('User not login. Please login first.');

		if (!$this->validate(['order_id' => 'required', 'message'  => 'required|alpha_space']))
			return $this->failValidationErrors(
				$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
			);

		$user_id = $this->authenticate->id();
		$message = $this->request->getVar('message');
		$order_id = $this->request->getVar('order_id');

		$messageGroup = $this->model->where('order_id', $order_id)->first();

		if (empty($messageGroup)) {
			$message_group_id = $this->model->insert(new MessageGroup([
				'user_id'  => $user_id,
				'order_id' => $order_id,
			]));
			if (!$message_group_id)
				return $this->fail('Something went wrong while saving message.');
		} else
			$message_group_id = $messageGroup->id;

		$message_id = $this->messageModel->save(new Message([
			'user_id'          => $user_id,
			'message'          => $message,
			'message_group_id' => $message_group_id,
		]));
		if (!$message_id)
			return $this->fail('Something went wrong while linking message.');

		return $this->success(null, 'created', 'Message saved successfully.');
	}
}

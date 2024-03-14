<?php

namespace App\Apis;

use App\{Entities\Refer, Entities\ReferUser, Models\ReferModel, Models\ReferUserModel};
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Refers extends BaseResourceController
{
	protected $modelName = ReferModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$referUserModel = new ReferUserModel();
		$sort           = $this->request->getVar('sort') ?? 'desc';
		$perPage        = $this->request->getVar('perPage') ?? null;

		$referInfo = $this->model->where('user_id', $this->authenticate->id())->first();
		if (empty($referInfo)) return $this->failNotFound('Refer not found.');

		$referUsers = $referUserModel->where('refer_id', $referInfo->id)->orderBy('id', $sort)->paginate($perPage);
		return $this->success($referUsers, 'success', 'Refers fetched successfully.');
	}

	public function show($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$refer = $this->model->where('user_id', $this->authenticate->id())->first();
		if (empty($refer)) {
			helper('text');
			$refer = $this->model->save(new Refer([
				'user_id' => $this->authenticate->id(),
				'refer'   => \random_string() . time(),
			]));
			$refer = $this->model->where('user_id', $this->authenticate->id())->first();
		}

		return $this->success($refer, 'success', 'Refers fetched successfully.');
	}

	public function create()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$rules = ['referral_code' => 'required|string'];
		if (!$this->validate($rules)) return $this->failValidationErrors($this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.');

		$user_id       = $this->authenticate->id();
		$referral_code = $this->request->getVar('referral_code');

		$refer = $this->model->where('refer', $referral_code)->where('user_id!=', $user_id)->first();
		if (empty($refer)) return $this->failValidationErrors('Invalid referral code.');

		$referUserModel    = new ReferUserModel();
		$alreadyReferUsers = $referUserModel->where('user_id', $user_id)->first();
		if (!empty($alreadyReferUsers)) return $this->failValidationErrors('You already referred another user.');

		$referRelation  = $referUserModel->save(new ReferUser(['user_id' => $user_id, 'refer_id' => $refer->id]));
		if (!$referRelation) return $this->fail('Something went wrong while saving the referral code.');

		return $this->success(null, 'created', 'Referral code saved successfully.');
	}
}

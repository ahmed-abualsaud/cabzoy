<?php

namespace App\Apis;

use App\{Entities\Card, Entities\PaymentRelation, Models\CardModel, Models\PaymentRelationModel};
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Cards extends BaseResourceController
{
	protected $paymentRelationModel;
	protected $modelName = CardModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
		$this->paymentRelationModel = new PaymentRelationModel();
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$sort    = $this->request->getVar('sort') ?? 'desc';
		$perPage = $this->request->getVar('perPage') ?? null;

		$cards = $this->paymentRelationModel->where('user_id', $this->authenticate->id())->typeIs('card')->orderBy('id', $sort)->paginate($perPage);

		return $this->success($cards, 'success', 'Cards fetched successfully.');
	}

	public function show($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$card = $this->model->find($id);
		if (!is($card, 'object')) return $this->fail('Card not found.');

		return $this->success($card, 'success', 'Cards fetched successfully.');
	}

	public function create()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$rules = [
			'card_holdername' => 'required|alpha_space',
			'is_default'      => 'required|in_list[0,1]',
			'card_month'      => 'required|valid_date[m]',
			'card_year'       => 'required|valid_date[Y]',
			'card_type'       => 'required|in_list[credit,debit]',
			'card_cvv'        => 'required|is_natural|max_length[3]|min_length[3]',
			'card_number'     => 'required|is_natural|max_length[16]|min_length[16]|is_unique[cards.card_number,id]',
		];
		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);

		$created_by      = $this->authenticate->id();
		$card_cvv        = $this->request->getVar('card_cvv');
		$card_year       = $this->request->getVar('card_year');
		$card_type       = $this->request->getVar('card_type');
		$card_month      = $this->request->getVar('card_month');
		$is_default      = $this->request->getVar('is_default');
		$card_number     = $this->request->getVar('card_number');
		$card_holdername = $this->request->getVar('card_holdername');

		$card_id = $this->model->insert(new Card([
			'card_cvv'        => $card_cvv,
			'card_status'     => 'pending',
			'card_year'       => $card_year,
			'card_type'       => $card_type,
			'is_default'      => $is_default,
			'card_month'      => $card_month,
			'created_by'      => $created_by,
			'card_number'     => $card_number,
			'card_holdername' => $card_holdername,
		]));
		if (!$card_id) return $this->fail('Something went wrong while saving card.');

		$paymentRelation = $this->paymentRelationModel->save(new PaymentRelation([
			'user_id' => $created_by, 'relation_type' => 'card', 'card_id' => $card_id,
		]));
		if (!$paymentRelation) return $this->fail('Something went wrong while linking card.');

		return $this->success(null, 'created', 'Card saved successfully.');
	}

	public function update($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$card = $this->model->find($id);
		if (!is($card, 'object')) return $this->fail('Card not found.');

		$rules = [
			'card_holdername' => 'required|alpha_space',
			'is_default'      => 'required|in_list[0,1]',
			'card_month'      => 'required|valid_date[m]',
			'card_year'       => 'required|valid_date[Y]',
			'card_type'       => 'required|in_list[credit,debit]',
			'card_cvv'        => 'required|is_natural|max_length[3]|min_length[3]',
			'card_number'     => "required|is_natural|max_length[16]|min_length[16]|is_unique[cards.card_number,id,{$id}]",
		];
		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);

		$created_by      = $this->authenticate->id();
		$card_cvv        = $this->request->getVar('card_cvv');
		$card_year       = $this->request->getVar('card_year');
		$card_type       = $this->request->getVar('card_type');
		$card_month      = $this->request->getVar('card_month');
		$is_default      = $this->request->getVar('is_default');
		$card_number     = $this->request->getVar('card_number');
		$card_holdername = $this->request->getVar('card_holdername');

		$card_id = $this->model->update($id, new Card([
			'card_cvv'        => $card_cvv,
			'card_status'     => 'pending',
			'card_year'       => $card_year,
			'card_type'       => $card_type,
			'is_default'      => $is_default,
			'card_month'      => $card_month,
			'created_by'      => $created_by,
			'card_number'     => $card_number,
			'card_holdername' => $card_holdername,
		]));
		if (!$card_id) return $this->fail('Something went wrong while updating card.');

		return $this->success(null, 'created', 'Card updated successfully.');
	}

	public function delete($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$card = $this->model->find($id);
		if (!is($card, 'object')) return $this->fail('Card not found.');

		$card = $this->model->delete($id);
		if (!$card) return $this->fail('Something went wrong while deleting card.');

		return $this->success(null, 'deleted', 'Card deleted successfully.');
	}
}

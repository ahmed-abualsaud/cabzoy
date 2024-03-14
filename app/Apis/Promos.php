<?php

namespace App\Apis;

use App\Models\PromoModel;
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Promos extends BaseResourceController
{
	protected $modelName = PromoModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		if (!config('Settings')->enablePromoCode)
			return $this->fail('Promo Codes currently disabled by the administrations.');

		$sort    = $this->request->getVar('sort') ?? 'desc';
		$perPage = $this->request->getVar('perPage') ?? null;

		$promos  = $this->model->statusIs('approved')->orderBy('id', $sort)->paginate($perPage);

		return $this->success($promos, 'success', 'Promos calculated successfully.');
	}

	public function show($promo = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		if (!config('Settings')->enablePromoCode)
			return $this->fail('Promo Codes currently disabled by the administrations.');

		if (!is($promo)) return $this->failNotFound('Promo not found.');

		$promos = $this->model->where('promo_code', $promo)->statusIs('approved')->first();
		if (!is($promos, 'object')) return $this->failNotFound('Promo not found.');

		return $this->success($promos, 'success', 'Promos successfully.');
	}
}

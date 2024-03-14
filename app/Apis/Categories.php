<?php

namespace App\Apis;

use App\Models\{CategoryModel, VehicleRelationModel};
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Categories extends BaseResourceController
{
	protected $modelName = CategoryModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');
		$sort    = $this->request->getVar('sort') ?? 'desc';
		$perPage = $this->request->getVar('perPage') ?? null;
		$type    = $this->request->getVar('type') ?? 'vehicle';

		if (!in_array($type, ['vehicle', 'complaint', 'faq', 'ticket', 'cancellation', 'review'])) return $this->failForbidden('The type is invalid.');

		if ($type === 'vehicle' && !config('Settings')->enableShowCategoryWhenDriverNotOnline) {
			$vehicleIdArray = [];
			$relationModel  = new VehicleRelationModel();
			$vehicles       = $relationModel->notEnded()->findAll();

			foreach ($vehicles as $value) {
				$vehicleIdArray[] = $value->category_id;
			}

			$categories = $this->model->typeOf($type);
			if (!empty($vehicleIdArray)) $categories->whereIn('id', $vehicleIdArray);
			$categories = $categories->orderBy('id', $sort)->paginate($perPage);
		} else
			$categories = $this->model->typeOf($type)->orderBy('id', $sort)->paginate($perPage);

		return $this->success($categories, 'success', ucwords($type) . ' fetched successfully.');
	}
}

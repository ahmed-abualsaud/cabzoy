<?php

namespace App\Apis;

use App\Models\{VehicleRelationModel, ZoneModel};
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Locations extends BaseResourceController
{
	protected $vehicleRelationModel;
	protected $modelName = ZoneModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
		$this->vehicleRelationModel = new VehicleRelationModel();
	}

	public function index()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$sort     = $this->request->getVar('sort') ?? 'desc';
		$perPage  = $this->request->getVar('perPage') ?? null;

		$locations = $this->vehicleRelationModel->notEnded()->orderBy('id', $sort)->paginate($perPage);
		return $this->success($locations, 'success', 'Vehicle Locations fetched successfully.');
	}

	public function show($coords = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$coords = $coords ?? $this->request->getVar('coords');
		if (!$coords) return $this->failValidationErrors(['Coordinates not provided.']);

		$coords = explode(',', $coords);
		if (count($coords) !== 2) return $this->failValidationErrors(['Coordinates not valid.']);

		$latitude  = $coords[0];
		$longitude = $coords[1];

		if (config('Settings')->enableBoundary) {
			/** @var object Map Boundary */
			$zoneBoundary = $this->model->typeOf('boundary')->first();

			if (!is($zoneBoundary, 'object'))
				return $this->fail('The default map extent is not set yet.');

			if (!checkPointInsidePolygon($latitude, $longitude, $zoneBoundary->zone))
				return $this->fail('The coordinates is not inside a serviceable area.');
		}

		return $this->success(null, 'no-content', 'The coordinates are valid and in the serviceable area.');
	}

	public function getAllDriverLocation()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$db = db_connect();
		$drivers = $db->table('categories_users_vehicles as cuv')
			->select([
				'users.speed',
				'users.heading',
				'users.id as user_id',
				'users.lat as latitude',
				'users.long as longitude',
				'categories.category_name',
				'categories.category_icon',
				'vehicles.vehicle_brand',
				'vehicles.vehicle_modal',
				'vehicles.vehicle_number',
			])
			->join('users', 'users.id = cuv.user_id', 'left')
			->join('vehicles', 'vehicles.id = cuv.vehicle_id', 'left')
			->join('categories', 'categories.id = cuv.category_id', 'left')
			->join('auth_groups_users', 'auth_groups_users.user_id = users.id', 'left')
			->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id', 'left')
			->where([
				'users.lat!='           => null,
				'users.long!='          => null,
				'users.speed!='         => null,
				'cuv.ended_at'          => null,
				'users.heading!='       => null,
				'users.deleted_at'      => null,
				'vehicles.deleted_at'   => null,
				'categories.deleted_at' => null,
				'auth_groups.name'      => 'drivers',
				'cuv.status'            => 'available',
			])
			->orderBy('cuv.id', 'desc')
			->get()->getResultObject();

		return $this->success($drivers);
	}
}

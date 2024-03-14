<?php

namespace App\Controllers\Fares;

use App\{Controllers\BaseController, Entities\Fare};
use App\Models\{CategoryModel, FareModel, FareRelationModel, VehicleModel};

class CategoryController extends BaseController
{
	protected $fareModel;
	protected $vehicleModel;
	protected $categoryModel;
	protected $fareRelationModel;

	public function __construct()
	{
		$this->fareModel         = new FareModel();
		$this->vehicleModel      = new VehicleModel();
		$this->categoryModel     = new CategoryModel();
		$this->fareRelationModel = new FareRelationModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('fares', 'read')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to see fares',
		]);

		$fares = $this->fareRelationModel->type('category')->findAll();

		return view('pages/fare/listCategory', ['fares' => $fares]);
	}


	public function add()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('fares', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create fare',
		]);

		$categories   = $this->categoryModel->typeOf('vehicle');
		$fareRelation = $this->fareRelationModel->type('category')->findAll();

		if (is($fareRelation, 'array')) {
			$categoryIds = [];
			foreach ($fareRelation as $value) {
				$categoryIds[] = $value->category_id;
			}
			if (!empty($categoryIds))
				$categories = $categories->whereNotIn('id', $categoryIds);
		}

		return view('pages/fare/addCategory', ['validation' => $this->validation, 'categories' => $categories->findAll()]);
	}

	public function save()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('fares', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create fare',
		]);

		$rules = [
			'fare'        => 'required|numeric',
			'min_fare'    => 'required|numeric',
			'category_id' => 'required|is_natural_no_zero',
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$fare        = $this->request->getPost('fare');
		$min_fare    = $this->request->getPost('min_fare');
		$category_id = $this->request->getPost('category_id');

		$category = $this->categoryModel->typeOf('vehicle')->find($category_id);
		if (!is($category, 'object')) return redirect()->back()->withInput()->with('errors', ['Category not found.']);

		$isFareRelationExists = $this->fareRelationModel->type('category')->where('category_id', $category_id)->first();
		if (is($isFareRelationExists, 'object')) return redirect()->back()->withInput()->with('errors', ['Fare already exists.']);

		$newFareId = $this->fareModel->insert(new Fare([
			'fare'        => $fare,
			'fare_type'   => 'always',
			'fare_status' => 'active',
			'min_fare'    => $min_fare,
			'created_by'  => user_id(),
			'fare_name'   => $category->category_name,
		]), true);

		if (!$newFareId) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong when creating new fare, please try sometime later.',
		]);

		$newFareRelation = $this->fareRelationModel->save(['category_id' => $category_id, 'fare_id' => $newFareId,]);
		if (!$newFareRelation) {
			$this->fareModel->delete($newFareId, true);
			return redirect()->back()->withInput()->with('errors', [
				'Something went wrong when relate to the category, please try sometime later.',
			]);
		}

		return redirect()->to(route_to('category_fares'))->with('success', ['Vehicle Type Fare Added Successfully']);
	}


	public function edit(?int $id = null)
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('fares', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update fare',
		]);

		$fare = $this->fareRelationModel->type('category')->find($id);
		if (!is($fare, 'object')) return redirect()->back()->with('errors', ['Fare not found']);

		$categories   = $this->categoryModel->typeOf('vehicle');
		$fareRelation = $this->fareRelationModel->type('category')->findAll();

		if (is($fareRelation, 'array')) {
			$categoryIds = [];
			foreach ($fareRelation as $value) {
				if ($value->category_id != $fare->category_id) $categoryIds[] = $value->category_id;
			}
			if (!empty($categoryIds))
				$categories = $categories->whereNotIn('id', $categoryIds);
		}

		return view('pages/fare/editCategory', [
			'fare' => $fare, 'validation' => $this->validation, 'categories' => $categories->findAll()
		]);
	}

	public function update(?int $id = null)
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('fares', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update fare',
		]);

		$fareRelation = $this->fareRelationModel->type('category')->find($id);
		if (!is($fareRelation, 'object')) return redirect()->back()->with('errors', ['Fare not found']);

		$rules = [
			'fare'        => 'required|numeric',
			'min_fare'    => 'required|numeric',
			'category_id' => 'required|is_natural_no_zero',
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$fare        = $this->request->getPost('fare');
		$min_fare    = $this->request->getPost('min_fare');
		$category_id = $this->request->getPost('category_id');

		$category = $this->categoryModel->typeOf('vehicle')->find($category_id);
		if (!is($category, 'object')) return redirect()->back()->withInput()->with('errors', ['Category not found.']);

		$isFareRelationExists = $this->fareRelationModel->type('category')->where('category_id', $category_id)->where('id!=', $id)->first();
		if (is($isFareRelationExists, 'object')) return redirect()->back()->withInput()->with('errors', ['Fare already exists.']);

		$newFareId = $this->fareModel->update($fareRelation->fare_id, new Fare([
			'fare'      => $fare,
			'min_fare'  => $min_fare,
			'fare_name' => $category->category_name,
		]), true);

		if (!$newFareId) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong when creating new fare, please try sometime later.',
		]);

		$newFareRelation = $this->fareRelationModel->update($id, ['category_id' => $category_id]);
		if (!$newFareRelation) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong when relate to the category, please try sometime later.',
		]);

		return redirect()->to(route_to('category_fares'))->with('success', ['Fare Updated Successfully']);
	}

	public function delete(?int $id = null)
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('fares', 'delete')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to delete fare',
		]);

		$fare = $this->fareRelationModel->type('category')->find($id);
		if (is($fare, 'object')) {
			if ($this->fareRelationModel->type('category')->delete($id) && $this->fareModel->delete($fare->fare_id, true))
				return redirect()->back()->with('success', ['Fare deleted successfully']);

			return redirect()->back()->with('errors', ['Fare not deleted']);
		}

		return redirect()->back()->with('errors', ['Fare not found']);
	}
}

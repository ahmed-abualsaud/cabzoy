<?php

namespace App\Controllers;

use App\{Entities\Category, Models\CategoryModel};
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class CategoryController extends BaseController
{
	protected $categoryModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		checkDir(UPLOADPATH . 'categories');
		$this->categoryModel = new CategoryModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('categories', 'read')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to see categories',
		]);

		$categories = $this->categoryModel->with('users')->orderBy('id', 'desc')->findAll();

		return view('pages/category/list', ['categories' => $categories]);
	}

	public function add()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('categories', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create category',
		]);

		return view('pages/category/add', ['validation' => $this->validation]);
	}

	public function save(): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('categories', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create category',
		]);

		$rules = [
			'name'   => 'required|min_length[3]',
			'status' => 'required|in_list[approved, pending, rejected]',
			'icon'   => 'max_size[icon,2048]|ext_in[icon,png,jpg,jpeg]',
			'image'  => 'max_size[image,2048]|ext_in[image,png,jpg,jpeg]',
			'type'   => 'required|in_list[vehicle, complaint, faq, ticket, cancellation, review]',
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$type        = $this->request->getPost('type');
		$name        = $this->request->getPost('name');
		$iconFile    = $this->request->getFile('icon');
		$file        = $this->request->getFile('image');
		$status      = $this->request->getPost('status');
		$description = $this->request->getPost('description');

		$categoryData = [
			'category_type'        => $type,
			'category_name'        => $name,
			'category_status'      => $status,
			'created_by'           => user_id(),
			'category_description' => $description,
		];

		if (!perm('categories', 'add-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to create category'
		]);

		if ($file->isValid()) {
			$image = 'categories/' . $file->getRandomName();
			$file->move(UPLOADPATH, $image);

			if (!$file->hasMoved()) return redirect()->back()->withInput()->with('errors', [$file->getErrorString()]);
			$categoryData['category_image'] = $image;
		}

		if ($iconFile->isValid()) {
			$icon = 'categories/' . $iconFile->getRandomName();
			$iconFile->move(UPLOADPATH, $icon);

			if (!$iconFile->hasMoved()) return redirect()->back()->withInput()->with('errors', [$iconFile->getErrorString()]);
			$categoryData['category_icon'] = $icon;
		}

		$newCategory = $this->categoryModel->save(new Category($categoryData));
		if (!$newCategory) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong, please try sometime later.',
		]);

		return redirect()->to(route_to('categories'))->with('success', [ucwords($name ?? 'Category') . ' Added Successfully']);
	}

	public function edit(?int $id = null)
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('categories', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update category',
		]);

		$category = $this->categoryModel->find($id);
		if (!is($category, 'object')) return redirect()->back()->with('errors', ['Category not found']);

		return view('pages/category/edit', ['role' => 'categories', 'validation' => $this->validation, 'category' => $category]);
	}

	public function update(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('categories', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update category',
		]);

		$category = $this->categoryModel->find($id);
		if (!is($category, 'object')) return redirect()->back()->with('errors', ['Category not found']);

		$rules = [
			'name'   => 'required|min_length[3]',
			'status' => 'required|in_list[approved, pending, rejected]',
			'icon'   => 'max_size[icon,2048]|ext_in[icon,png,jpg,jpeg]',
			'image'  => 'max_size[image,2048]|ext_in[image,png,jpg,jpeg]',
			'type'   => 'required|in_list[vehicle, complaint, faq, ticket, cancellation, review]',
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$type        = $this->request->getPost('type');
		$name        = $this->request->getPost('name');
		$iconFile    = $this->request->getFile('icon');
		$file        = $this->request->getFile('image');
		$status      = $this->request->getPost('status');
		$description = $this->request->getPost('description');

		$categoryData = [
			'category_type'        => $type,
			'category_name'        => $name,
			'category_status'      => $status,
			'category_description' => $description,
		];

		if (!perm('categories', 'update-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to update category'
		]);

		if ($file->isValid()) {

			$image = 'categories/' . $file->getRandomName();
			$file->move(UPLOADPATH, $image);

			if (!$file->hasMoved()) return redirect()->back()->withInput()->with('errors', [$file->getErrorString()]);

			if (file_exists(PUBLICPATH . $category->getCategoryImage(false)) && is_file(PUBLICPATH . $category->getCategoryImage(false)) && $file->hasMoved()) unlink(PUBLICPATH . $category->getCategoryImage(false));
			$categoryData['category_image'] = $image;
		}

		if ($iconFile->isValid()) {

			$icon = 'categories/' . $iconFile->getRandomName();
			$iconFile->move(UPLOADPATH, $icon);

			if (!$iconFile->hasMoved()) return redirect()->back()->withInput()->with('errors', [$iconFile->getErrorString()]);

			if (file_exists(PUBLICPATH . $category->getCategoryIcon(false)) && is_file(PUBLICPATH . $category->getCategoryIcon(false)) && $iconFile->hasMoved()) unlink(PUBLICPATH . $category->getCategoryIcon(false));
			$categoryData['category_icon'] = $icon;
		}

		$updatedUser = $this->categoryModel->update($id, new Category($categoryData));

		if (!$updatedUser) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong while updating the category',
		]);

		return redirect()->to(route_to('categories'))->with('success', ["{$name} Updated Successfully"]);
	}

	public function delete(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('categories', 'delete')) return redirect()->to(route_to('dashboard'))->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to delete category',
		]);

		$category = $this->categoryModel->find($id);
		if (is($category, 'object')) {
			if ($this->categoryModel->delete($id))
				return redirect()->back()->with('success', ['Category deleted successfully']);

			return redirect()->back()->with('errors', ['Category not deleted']);
		}

		return redirect()->back()->with('errors', ['Category not found']);
	}
}

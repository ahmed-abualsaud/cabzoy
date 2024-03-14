<?php

namespace App\Apis;

use App\Entities\{Review, ReviewCategory, ReviewDriver};
use App\Models\{CategoryModel, ReviewCategoryModel, ReviewDriverModel, ReviewModel};
use CodeIgniter\HTTP\{RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class Reviews extends BaseResourceController
{
	protected $categoryModel;
	protected $reviewDriverModel;
	protected $reviewCategoryModel;
	protected $modelName = ReviewModel::class;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
		$this->categoryModel       = new CategoryModel();
		$this->reviewDriverModel   = new ReviewDriverModel();
		$this->reviewCategoryModel = new ReviewCategoryModel();
	}

	public function index()
	{
	}

	public function show($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$review = $this->reviewDriverModel->where('user_id', $id)->findAll();
		if (!is($review, 'array')) return $this->fail('Review not found.');

		return $this->success($review, 'success', 'Reviews fetched successfully.');
	}

	public function create()
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');

		$rules = [
			'review'             => 'required',
			'order_id'           => 'required',
			'rate.*.category_id' => 'required_without[rate.*.user_id]',
			'rate.*.user_id'     => 'required_without[rate.*.category_id]',
			'rate.*.rating'      => 'required|less_than_equal_to[5]|numeric',
		];
		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);

		$countRating = 0;
		$created_by  = $this->authenticate->id();
		$rate        = $this->request->getVar('rate');
		$review      = $this->request->getVar('review');
		$order_id    = $this->request->getVar('order_id');

		$review_id = $this->model->insert(new Review([
			'rating' => 0, 'review' => $review, 'order_id' => $order_id, 'user_id' => $created_by,
		]));
		if (!$review_id) return $this->fail('Something went wrong while saving review.');

		foreach ($rate as $count) {
			if (isset($count->rating) && (isset($count->user_id) || isset($count->category_id))) {

				if (!empty($count->user_id)) {
					$user_id = $this->reviewDriverModel->save(new ReviewDriver([
						'review_id' => $review_id, 'user_id' => $count->user_id, 'rating' => $count->rating
					]));
					if (!$user_id) return $this->fail('Something went wrong while saving driver review.');
				}

				if (!empty($count->category_id)) {
					$category = $this->categoryModel->typeOf('review')->find($count->category_id);
					if (!$category) return $this->fail('Invalid review category, Please select valid review category.');

					$category_id = $this->reviewCategoryModel->save(new ReviewCategory([
						'review_id' => $review_id, 'category_id' => $count->category_id, 'rating' => $count->rating
					]));
					if (!$category_id) return $this->fail('Something went wrong while saving review category.');
				}
				$max = ($max ?? 0) + $count->rating;
				$countRating++;
			}
		}

		$rating =  round(($max ?? 0) / $countRating, 2);

		$updated = $this->model->update($review_id, ['rating' => $rating]);
		if (!$updated) return $this->fail('Something went wrong while updating review.');

		return $this->success(null, 'created', 'Review saved successfully.');
	}

	public function update($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');
		if (!config('Settings')->enableReviewVerification)
			return $this->fail('Currently this service disabled by the administration.');

		$review = $this->model->find($id);
		if (!is($review, 'object')) return $this->fail('Review not found.');


		$rules = [
			'review_front_image' => 'required',
			'review_title'       => 'required|alpha_space',
			'review_number'      => 'required|alpha_numeric',
		];
		if (!$this->validate($rules)) return $this->failValidationErrors(
			$this->validator->getErrors() ? $this->validator->getErrors() : 'Invalid form field value.'
		);

		$user_id              = $this->authenticate->id();
		$review_title       = $this->request->getVar('review_title');
		$review_number      = $this->request->getVar('review_number');
		$review_back_image  = $this->request->getVar('review_back_image');
		$review_front_image = $this->request->getVar('review_front_image');

		$review_id = $this->model->update($id, new Review([
			'user_id'              => $user_id,
			'review_status'      => 'pending',
			'review_title'       => $review_title,
			'review_number'      => $review_number,
			'review_back_image'  => $review_back_image,
			'review_front_image' => $review_front_image,
		]));
		if (!$review_id) return $this->fail('Something went wrong while updating review.');

		return $this->success(null, 'updated', 'Review updated successfully.');
	}

	public function delete($id = null)
	{
		if (!$this->authenticate->check()) return $this->failUnauthorized('User not login. Please login first.');
		if (!config('Settings')->enableReviewVerification)
			return $this->fail('Currently this service disabled by the administration.');

		$review = $this->model->find($id);
		if (!is($review, 'object')) return $this->fail('Review not found.');

		$review = $this->model->delete($id);
		if (!$review) return $this->fail('Something went wrong while deleting review.');

		return $this->success(null, 'deleted', 'Review deleted successfully.');
	}
}

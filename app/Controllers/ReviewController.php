<?php

namespace App\Controllers;

use App\Models\ReviewModel;
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class ReviewController extends BaseController
{
	protected $reviewModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->reviewModel = new ReviewModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('reviews', 'read')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to see reviews',
		]);
		$reviews = $this->reviewModel->with('review_drivers')->orderBy('id', 'desc')->findAll();

		return view('pages/review/list', ['reviews' => $reviews]);
	}

	public function delete(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('reviews', 'delete')) return redirect()->to(route_to('dashboard'))->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to delete review',
		]);

		$review = $this->reviewModel->find($id);
		if (is($review, 'object')) {
			if ($this->reviewModel->delete($id))
				return redirect()->back()->with('success', ['Review deleted successfully']);

			return redirect()->back()->with('errors', ['Review not deleted']);
		}

		return redirect()->back()->with('errors', ['Review not found']);
	}
}

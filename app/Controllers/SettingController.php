<?php

namespace App\Controllers;

use App\{Entities\Setting, Models\SettingModel};
use CodeIgniter\HTTP\{RedirectResponse, RequestInterface, ResponseInterface};
use Psr\Log\LoggerInterface;

class SettingController extends BaseController
{
	protected $settingModel;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		checkDir(UPLOADPATH . 'settings');
		$this->settingModel = new SettingModel();
	}

	public function index()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('settings', 'read')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to see settings',
		]);

		$settings = $this->settingModel->findAll();

		return view('pages/setting/list', ['settings' => $settings]);
	}

	public function add()
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('settings', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create setting',
		]);

		return view('pages/setting/add', ['validation' => $this->validation]);
	}

	public function save(): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('settings', 'add')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to create setting',
		]);

		$rules = [
			'summary'  => 'required|min_length[3]',
			'name'     => 'required|alpha|min_length[3]',
			'datatype' => 'required|in_list[string, int, uri, image, bool, color]',
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$name     = $this->request->getPost('name');
		$file     = $this->request->getFile('image');
		$summary  = $this->request->getPost('summary');
		$content  = $this->request->getPost('content');
		$datatype = $this->request->getPost('datatype');

		if (!perm('settings', 'add-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to create setting'
		]);

		if ($file !== null && $file->isValid()) {
			$content = 'settings/' . $file->getRandomName();
			$file->move(UPLOADPATH, $content);

			if (!$file->hasMoved()) return redirect()->back()->withInput()->with('errors', [$file->getErrorString()]);
			$content = "uploads/{$content}";
		}

		$updatedUser = $this->settingModel->save(new Setting([
			'protected' => 1,
			'name'      => $name,
			'summary'   => $summary,
			'content'   => $content,
			'datatype'  => $datatype,
		]));

		if (!$updatedUser) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong while creating the setting',
		]);

		return redirect()->to(route_to('settings'))->with('success', ["{$name} Created Successfully"]);
	}

	public function edit(?int $id = null)
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('settings', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update setting',
		]);

		$setting = $this->settingModel->find($id);
		if (!is($setting, 'object')) return redirect()->back()->with('errors', ['Setting not found']);

		return view('pages/setting/edit', ['validation' => $this->validation, 'setting' => $setting]);
	}

	public function update(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('settings', 'update')) return redirect()->to(route_to('dashboard'))->with('errors', [
			'You don\'t have permission to update setting',
		]);

		$setting = $this->settingModel->find($id);
		if (!is($setting, 'object')) return redirect()->back()->with('errors', ['Setting not found']);

		$rules = [
			'summary'  => 'required|min_length[3]',
			'name'     => 'required|alpha|min_length[3]',
			'datatype' => 'required|in_list[string, int, uri, image, bool, color]',
		];

		if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

		$name     = $this->request->getPost('name');
		$file     = $this->request->getFile('image');
		$summary  = $this->request->getPost('summary');
		$content  = $this->request->getPost('content');
		$datatype = $this->request->getPost('datatype');

		if (!perm('settings', 'update-action')) return redirect()->back()->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to edit setting'
		]);

		if ($file !== null && $file->isValid()) {
			$content = 'settings/' . $file->getRandomName();
			$file->move(UPLOADPATH, $content);

			if (!$file->hasMoved()) return redirect()->back()->withInput()->with('errors', [$file->getErrorString()]);
			if (file_exists(PUBLICPATH . $setting->content) && is_file(PUBLICPATH . $setting->content) && $file->hasMoved()) unlink(PUBLICPATH . $setting->content);
			$content = "uploads/{$content}";
		}

		$settingArray                                            = [];
		if (perm('settings', 'add')) $settingArray['name']       = $name;
		if (perm('settings', 'add')) $settingArray['summary']    = $summary;
		if (perm('settings', 'update')) $settingArray['content'] = $content;
		if (perm('settings', 'add')) $settingArray['datatype']   = $datatype;

		$updatedUser = $this->settingModel->update($id, new Setting($settingArray));

		if (!$updatedUser) return redirect()->back()->withInput()->with('errors', [
			'Something went wrong while updating the setting',
		]);

		return redirect()->to(route_to('settings'))->with('success', ["{$name} Updated Successfully"]);
	}

	public function delete(?int $id = null): RedirectResponse
	{
		if (!logged_in()) return redirect()->to(route_to('login'));
		if (!perm('settings', 'delete')) return redirect()->to(route_to('dashboard'))->with('errors', [
			...$this->demoMessage,
			'You don\'t have permission to delete setting',
		]);

		$setting = $this->settingModel->find($id);
		if (is($setting, 'object')) {
			if ($this->settingModel->delete($id))
				return redirect()->back()->with('success', ['Setting deleted successfully']);

			return redirect()->back()->with('errors', ['Setting not deleted']);
		}

		return redirect()->back()->with('errors', ['Setting not found']);
	}
}

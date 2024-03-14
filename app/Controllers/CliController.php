<?php

namespace App\Controllers;

class CliController extends BaseController
{
	private function enableMessage(string $title, bool $isEnable = true, string $type = 'enable'): string
	{
		if ($isEnable) return "{$title} {$type}d";
		return "Please {$type} {$title}.";
	}

	public function check()
	{
		helper('filesystem');

		$check   = [];
		$isValid = false;

		$file = ROOTPATH . '.env';
		$url  = 'https://' . rtrim($this->request->getServer('SERVER_NAME') . $this->request->getServer('REQUEST_URI') ?? '', "/");

		$content = "
app.indexPage = ''
app.uriProtocol = 'PATH_INFO'
app.baseURL = $url
		";

		if (!empty($url) && !write_file($file, $content)) return redirect()->with('errors', [
			'Something went wrong in updating the environment file.',
		]);

		$check['php']['value']   = version_compare(phpversion(), '7.4', '>=');
		$check['php']['message'] = $check['php']['value'] ? 'v' . phpversion() : 'Please change PHP Version to 7.4 or newer.';

		$check['mysqli_extension']['value']   = extension_loaded('mysqli');
		$check['mysqli_extension']['message'] = $this->enableMessage('MySqli Extension', $check['mysqli_extension']['value']);

		$check['mbstring_extension']['value']   = extension_loaded('mbstring');
		$check['mbstring_extension']['message'] = $this->enableMessage('MbString Extension', $check['mbstring_extension']['value']);

		$check['intl_extension']['value']   = extension_loaded('intl');
		$check['intl_extension']['message'] = $this->enableMessage('Intl Extension', $check['intl_extension']['value']);

		$check['curl_extension']['value']   = extension_loaded('curl');
		$check['curl_extension']['message'] = $this->enableMessage('Curl Extension', $check['curl_extension']['value']);

		$check['upload_directory']['value']   = is_writeable(WRITEPATH);
		$check['upload_directory']['message'] = $this->enableMessage('UPLOAD Directory', $check['upload_directory']['value'], 'writeable');

		foreach ($check as $value) {
			if (!$value['value']) {
				$isValid = false;
				break;
			}
			$isValid = true;
		}

		return view('pages/cli/check', ['check' => $check, 'is_valid' => $isValid]);
	}

	public function requestData()
	{
		return view('pages/cli/request', ['validation' => $this->validation]);
	}

	public function init()
	{
		helper('filesystem');

		$rules = [
			"db_name"        => "required|alpha_dash",
			"db_username"    => "required|alpha_dash",
			"site_name"      => "required|alpha_space",
			"smtp_username"  => "required|valid_email",
			"db_password"    => "required|min_length[3]",
			"smtp_password"  => "required|min_length[3]",
			"fcm_server_key" => "required|min_length[3]",
			// "cc_license_key" => "required|min_length[3]",
			"smtp_host"      => "required|valid_url_strict[https]",
		];

		if (!$this->validate($rules)) return redirect()->to('/')->withInput()->with('errors', $this->validator->getErrors());

		$db_name        = trim($this->request->getPost('db_name') ?? '');
		$site_name      = trim($this->request->getPost('site_name') ?? '');
		$smtp_host      = trim($this->request->getPost('smtp_host') ?? '');
		$db_username    = trim($this->request->getPost('db_username') ?? '');
		$db_password    = trim($this->request->getPost('db_password') ?? '');
		$smtp_username  = trim($this->request->getPost('smtp_username') ?? '');
		$smtp_password  = trim($this->request->getPost('smtp_password') ?? '');
		$fcm_server_key = trim($this->request->getPost('fcm_server_key') ?? '');
		// $cc_license_key = trim($this->request->getPost('cc_license_key') ?? '');
		$url            = 'https://' . rtrim($this->request->getServer('SERVER_NAME') . $this->request->getServer('REQUEST_URI') ?? '', "/");
		$url = str_replace('/install', '', $url);

		// if ($cc_license_key !== '48bUd15b-Dx7z-2447-ab62-7hdcd10abe2d') return redirect()->to('/')->withInput()->with('errors', ['cc_license_key' => 'Invalid license key.']);

		$content = "
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------

CI_ENVIRONMENT = production

#--------------------------------------------------------------------
# ENVIRONMENT TYPE
#--------------------------------------------------------------------

ENV_TYPE = release

#--------------------------------------------------------------------
# PROJ TYPE
# mini | zone | dispatch | pro
#--------------------------------------------------------------------

PROJ_TYPE = dispatch

#--------------------------------------------------------------------
# Tokens
#--------------------------------------------------------------------

JWT_SECRET_KEY = theFabithub$123
FCM_SERVER_KEY = $fcm_server_key

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------

app.indexPage = ''
app.name = '$site_name'
app.uriProtocol = PATH_INFO
app.appTimezone = Asia/Kolkata
app.forceGlobalSecureRequests = true
app.baseURL = $url

#--------------------------------------------------------------------
# AUTH
#--------------------------------------------------------------------

auth.allowRemembering = true
auth.allowRegistration = false
auth.defaultUserGroup = users
auth.minimumPasswordLength = 4

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------

database.default.DBPrefix = fab_
database.default.username = $db_username
database.default.password = '$db_password'
database.default.hostname = 127.0.0.1
database.default.database = $db_name

#--------------------------------------------------------------------
# EMAIL
#--------------------------------------------------------------------

email.SMTPPort = 465
email.fromEmail = $smtp_username
email.fromName = 'Hello from $site_name'
email.SMTPHost = $smtp_host
email.SMTPUser = $smtp_username
email.SMTPPass = '$smtp_password'

#--------------------------------------------------------------------
# ENCRYPTION
#--------------------------------------------------------------------

encryption.key = hex2bin:0324443c83293fb5658de440aa1fe9f2bac15202ad740e780a3f6d279cf4a606
";

		if (is_string($content) && !empty($content)) {
			if (!write_file(ROOTPATH . '.env', $content)) return redirect()->withInput()->with('errors', [
				'Something went wrong in updating the environment file.',
			]);
		}
		return view('pages/cli/success');
	}

	public function reset()
	{
		$output = explode('.', command('reset:all'));
		return view('pages/cli/reset', ['title' => 'Initialize Project', 'output' => $output]);
	}
}

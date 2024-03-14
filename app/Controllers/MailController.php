<?php

namespace App\Controllers;

use Config\{Email, Services};

class MailController extends BaseController
{
	public function index()
	{
		$appName  = getDefaultConfig('siteName', env('app.name', 'Fab IT Hub'));
		$userMail = $this->request->getGet('mail') ?? '';
		$config   = new Email();
		$mail     = Services::email();
		$sent     = $mail->setFrom($config->fromEmail, $config->fromName)
			->setTo($userMail)
			->setSubject('Test Email')
			->setMessage("This is a testing mail from {$appName}.")
			->setMailType('html')
			->send();
		$mail->printDebugger(['headers', 'body', 'subject']);

		return var_dump($sent);
	}
}

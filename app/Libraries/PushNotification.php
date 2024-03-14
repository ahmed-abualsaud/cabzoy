<?php

namespace App\Libraries;

use Config\Services;
use stdClass;

class PushNotification
{
	/** @var \CodeIgniter\HTTP\CURLRequest */
	protected $client;
	protected $body = [];
	protected $url  = 'https://fcm.googleapis.com/fcm/send';

	public function __construct($auth = null)
	{
		$this->client = Services::curlrequest(['headers' => ['Authorization' => $auth]]);
		$this->body   = ["priority" => "high", "time_to_live" => 45];
	}

	public function to($token = null)
	{
		if (!empty($token)) $this->body['to'] = $token;

		return $this;
	}

	public function toChannel($channel = null)
	{
		if (!empty($channel)) {
			$this->body['notification']['channel_id']         = $channel;
			$this->body['notification']['android_channel_id'] = $channel;
		}

		return $this;
	}

	public function withData($data = [])
	{
		if (!empty($data)) $this->body['data'] = $data;

		return $this;
	}

	public function withNotification($title = null, $body = null, $image = null)
	{
		if (!empty($body)) $this->body['notification']['body']   = $body;
		if (!empty($title)) $this->body['notification']['title'] = $title;
		if (!empty($image)) $this->body['notification']['icon'] = $image;
		$this->body['notification']['color'] = '#ffffff';

		return $this;
	}

	public function send()
	{
		$output = $this->client->post($this->url, ['json' => $this->body]);

		$response = json_decode($output->getJSON());
		return $response;
	}
}

<?php

use App\{Entities\Notification, Models\NotificationModel};

function setNotification($notification)
{
	$notificationModel = new NotificationModel();
	return $notificationModel->save(new Notification($notification));
}

function sendNotification($device_id, $message)
{
	$api_key = env('FCM_SERVER_KEY');
	$url     = 'https://fcm.googleapis.com/fcm/send';

	if (empty($api_key)) return;
	$fields  = ['registration_ids' => [$device_id], 'data' => $message];
	$headers = ['Content-Type:application/json', 'Authorization:key=' . $api_key];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	$result = curl_exec($ch);
	if ($result === FALSE) return die('FCM Send Error: ' . curl_error($ch));

	curl_close($ch);
	return $result;
}

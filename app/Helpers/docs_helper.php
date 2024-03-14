<?php

use App\Models\DocumentModel;

if (!function_exists('getDocumentsArray')) {
	function getDocumentsArray($type = 'user')
	{
		$documentTypeFromSetting = getDefaultConfig('driverDocumentType');
		if ($type === 'user') $documentTypeFromSetting = getDefaultConfig('userDocumentType');

		return array_map('trim', explode(',', strtolower($documentTypeFromSetting)));
	}
}


if (!function_exists('isDocumentVerified')) {
	function isDocumentVerified($type = 'user', $user_id = null)
	{
		helper(['auth', 'custom']);

		$documents = [];
		$documentModel = new DocumentModel();
		$requiredDocuments = getDocumentsArray($type);

		$documents = $documentModel->where('user_id', $user_id ?? user_id())->whereIn('document_title', $requiredDocuments)->where('document_status', 'approved')->findAll();

		if (is($documents, 'array') && count($requiredDocuments) === count($documents)) return true;
		return false;
	}
}

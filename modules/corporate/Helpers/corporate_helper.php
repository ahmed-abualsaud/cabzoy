<?php

use Modules\Corporate\Models\CompanyModel;

if (!function_exists('valid_company')) {
	function valid_company($type = 'bool')
	{
		$companyModel = new CompanyModel();
		$companyBy    = $companyModel->where('created_by', user_id())->first();


		switch ($type) {
			case 'bool':
				return empty($companyBy) ? true : !empty($companyUser);
				break;

			default:
				return empty($companyBy) ? true : !empty($companyUser);
				break;
		}
	}
}

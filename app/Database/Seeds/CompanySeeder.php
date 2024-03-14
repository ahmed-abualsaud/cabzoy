<?php

namespace App\Database\Seeds;

use App\{Entities\Company, Models\CompanyModel};
use CodeIgniter\Database\Seeder;

class CompanySeeder extends Seeder
{

	public function run()
	{
		$company = new CompanyModel();

		$data = [
			[
				'created_by'       => 1,
				'is_default'       => '1',
				'company_status'   => 'approved',
				'company_name'     => 'main company',
				'company_mobile'   => '1910123456789',
				'company_email'    => 'main@company.com',
				'company_address'  => '601 S College Rd, Wilmington, NC',
				'company_image'    => 'uploads/companies/profile/1641101951_e4c733175ba3537006bb.png',
				'company_document' => 'uploads/companies/documents/1641101378_61669c6dbbbc931d21cc.pdf',
			],
		];

		foreach ($data as $row) {
			$company->save(new Company($row));
		}
	}
}

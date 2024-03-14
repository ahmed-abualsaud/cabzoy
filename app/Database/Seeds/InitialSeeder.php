<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
	public function run()
	{
		$this->call('UsersSeeder');
		$this->call('DefaultSettingSeeder');
		$this->call('CompanySeeder');

		foreach (directory_map(MODULESPATH, 0) as $folder => $files) {
			if (!empty($folder)) {
				$folder = str_replace('/', '\\', ucfirst($folder));
				if (!empty($files['Database/']['Seeds/']) && is_array($files['Database/']['Seeds/'])) {
					foreach ($files['Database/']['Seeds/'] as $value) {
						$file = str_replace('.php', '', $value);
						if (!empty($value) && is_string($value)) {
							$filePath = "Modules\\{$folder}Database\Seeds\\{$file}";
							if (file_exists(ROOTPATH . str_replace('\\', '/', $filePath) . '.php')) $this->call($filePath);
						}
					}
				}
			}
		}
	}
}

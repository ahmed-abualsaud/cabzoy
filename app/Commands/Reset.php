<?php

namespace App\Commands;

use CodeIgniter\Cache\CacheFactory;
use CodeIgniter\CLI\{BaseCommand, CLI};
use CodeIgniter\Config\Factories;
use Config\Database;
use Throwable;

class Reset extends BaseCommand
{
	/**
	 * The Command's Group
	 *
	 * @var string
	 */
	protected $group = 'Reset';

	/**
	 * The Command's Name
	 *
	 * @var string
	 */
	protected $name = 'reset:all';

	/**
	 * The Command's Description
	 *
	 * @var string
	 */
	protected $description = 'Reset the Project';

	/**
	 * The Command's Usage
	 *
	 * @var string
	 */
	protected $usage = 'reset:all';

	/**
	 * The Command's Arguments
	 *
	 * @var array
	 */
	protected $arguments = [
		'caches'    => 'Clearing the caches',
		'dbs'       => 'Resetting the tables',
		'debuggers' => 'Deleting the debuggers',
		'logs'      => 'Deleting the log files',
		'migrates'  => 'Migrating the database',
		'seeders'   => 'Seeding the database',
	];

	/**
	 * The Command's Options
	 *
	 * @var array
	 */
	protected $options = [];

	/** Actually execute a command. */
	public function run(array $params)
	{
		if (empty($params) || in_array('logs', $params)) {
			CLI::newLine();
			CLI::write(':: Deleting the log files start ::', 'white');
			$this->resetLog();
		}

		if (empty($params) || in_array('debuggers', $params)) {
			CLI::newLine();
			CLI::write(':: Deleting the debuggers files start ::', 'white');
			$this->resetDebugger();
		}

		if (empty($params) || in_array('caches', $params)) {
			CLI::newLine();
			CLI::write(':: Deleting the caches files start ::', 'white');
			$this->resetCache();
			CLI::newLine();
		}

		if (empty($params) || in_array('dbs', $params)) {
			CLI::newLine();
			CLI::write(':: Deleting & Creating the database files start ::', 'white');
			$this->resetDB();
			CLI::newLine();
		}

		if (empty($params) || in_array('migrates', $params)) {
			CLI::newLine();
			CLI::write(':: Migrating the database files start ::', 'white');
			$this->migrateDB();
			CLI::newLine();
		}

		if (empty($params) || in_array('seeders', $params)) {
			$this->seederDB();
			CLI::newLine();
		}
	}

	private function resetDebugger()
	{
		helper('filesystem');

		if (!delete_files(WRITEPATH . 'debugbar')) {
			// @codeCoverageIgnoreStart
			CLI::error('Error deleting the debugbar JSON files.');
			CLI::newLine();

			return;
			// @codeCoverageIgnoreEnd
		}

		CLI::write('Debugbar cleared.', 'green');
		CLI::newLine();
	}

	private function resetLog()
	{
		helper('filesystem');

		if (!delete_files(WRITEPATH . 'logs', false, true)) {
			// @codeCoverageIgnoreStart
			CLI::error('Error in deleting the logs files.', 'light_gray', 'red');
			CLI::newLine();

			return;
			// @codeCoverageIgnoreEnd
		}

		CLI::write('Logs cleared.', 'green');
		CLI::newLine();
	}

	private function resetCache()
	{
		$config  = config('Cache');
		$handler = $config->handler;

		if (!array_key_exists($handler, $config->validHandlers)) {
			CLI::error($handler . ' is not a valid cache handler.');

			return;
		}

		$config->handler = $handler;
		$cache = CacheFactory::getHandler($config);

		if (!$cache->clean()) {
			// @codeCoverageIgnoreStart
			CLI::error('Error while clearing the cache.');

			return;
			// @codeCoverageIgnoreEnd
		}

		CLI::write(CLI::color('Cache cleared.', 'green'));
	}

	private function resetDB()
	{
		$db = Database::connect();
		try {
			$data   = $db->listTables();
			$prefix = $db->getPrefix();
			$tables = array_map(static function ($table) use ($prefix) {
				Database::forge()->dropTable(str_replace($prefix, '', $table), true, true);
				return $table;
			}, $data);
			$db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		} catch (Throwable $e) {
			$this->showError($e);
		} finally {
			Factories::reset('config');
			CLI::write(CLI::color(implode(', ', $tables ?? []) . " remove successfully.", 'green'));
		}
	}

	private function migrateDB()
	{
		CLI::write(command('migrate --all'));
		Factories::reset('config');
		$this->schemaDB();
	}

	private function seederDB()
	{
		CLI::write(command('db:seed InitialSeeder'));
	}

	private function schemaDB()
	{
		CLI::newLine();
		CLI::write(':: Making Schemas the database files start ::', 'white');
		CLI::write(command('schemas'));
	}
}

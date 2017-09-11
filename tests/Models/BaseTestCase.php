<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Models\DatabaseManager as DatabaseManager;
use Sw0rdfish\Models\BaseModel as BaseModel;

/**
*
*/
abstract class BaseTestCase extends TestCase
{
	protected $app;
	protected $db;

    public function setUp()
    {
    	// load up an instance of the actual slim app
    	$this->loadApp();

    	// load up an instance of the database
    	$this->loadDb();

		// cleanup any tables defined on the test class
		$this->cleanupData();
	}

	public function tearDown() 
	{
    	// load up an instance of the actual slim app
    	$this->loadApp();

    	// load up an instance of the database
    	$this->loadDb();

		// cleanup any tables defined on the test class
		$this->cleanupData();
	}

	private function loadDb()
	{
		$this->db = DatabaseManager::getDbConnection();
	}

	private function loadApp()
	{
		$this->app = new Application("test", [
					'settings' => [
					  'displayErrorDetails' => true
					]
				]);
	}

	private function cleanupData()
	{
		if (defined(sprintf("%s::CLEANUP_TABLES", static::class))) {
			foreach (static::CLEANUP_TABLES as $table) {
				$query = "SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE $table; SET FOREIGN_KEY_CHECKS = 1;";
				$statement = $this->db->prepare($query);
				$statement->execute();
			}
		}
	}
}
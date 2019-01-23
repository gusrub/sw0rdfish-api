<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Models\DatabaseManager as DatabaseManager;
use Sw0rdfish\Models\BaseModel as BaseModel;

/**
 * Represents a base test case that all other test cases extend from. This class
 * basically has functionality that all the other tests needs like initial
 * setup, teardowns, etc.
 */
abstract class BaseModelTestCase extends TestCase
{
    /**
     * The Slim application instance.
     */
    protected $app;

    /**
     * A \PDO connection object instance to be used for database operations.
     */
    protected $db;

    /**
     * Set ups each test case before actually running it
     */
    public function setUp()
    {
        // load up an instance of the actual slim app
        $this->loadApp();

        // load up an instance of the database
        $this->loadDb();

        // cleanup any tables defined on the test class
        $this->cleanupData();
    }

    /**
     * Executes actions after each test case is run.
     */
    public function tearDown()
    {
        // load up an instance of the actual slim app
        $this->loadApp();

        // load up an instance of the database
        $this->loadDb();

        // cleanup any tables defined on the test class
        $this->cleanupData();
    }

    /**
     * Instantiates the database and gets a connection.
     */
    private function loadDb()
    {
        $this->db = DatabaseManager::getDbConnection();
    }

    /**
     * Instantiates a new Slim application
     */
    private function loadApp()
    {
        $this->app = new Application([
                    'settings' => [
                      'displayErrorDetails' => true
                    ]
                ]);
    }

    /**
     * Truncates all defined tables in the model.
     */
    private function cleanupData()
    {
        if (defined(sprintf("%s::CLEANUP_TABLES", static::class))) {
            foreach (static::CLEANUP_TABLES as $table) {
                $query = DatabaseManager::getTruncateQuery($table);
                if (is_array($query)) {
                    foreach ($query as $singleQuery) {
                        $statement = $this->db->prepare($singleQuery);
                        $statement->execute();
                    }
                } else {
                    $statement = $this->db->prepare($query);
                    $statement->execute();
                }
            }
        }
    }
}

<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Models\DatabaseManager as DatabaseManager;
use Sw0rdfish\Models\BaseModel as BaseModel;
use Sw0rdfish\Controllers;

/**
 * Represents a base test case that all other test cases extend from. This class
 * basically has functionality that all the other tests needs like initial
 * setup, teardowns, etc.
 */
abstract class BaseControllerTestCase extends TestCase
{

    /**
     * Defines the controllers namespace path.
     */
    const CONTROLLERS_NAMESPACE = "Sw0rdfish\Controllers";

    /**
     * The Slim application instance.
     */
    protected $app;

    /**
     * A \PDO connection object instance to be used for database operations.
     */
    protected $db;

    /**
     * The container of the app
     */
    protected $container;

    /**
     * The environment of the app
     */
    protected $environment;

    /**
     * The controller to be tested
     */
    protected $controller;

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

    protected function mockGET($uri, $headers = [])
    {
        return $this->mockHTTPRequest('GET', $uri, $headers);
    }

    protected function mockPOST($uri, $headers = [], $params = [])
    {
        return $this->mockHTTPRequest('POST', $uri, $headers, $params);
    }

    protected function mockPUT($uri, $headers = [], $params = [])
    {
        return $this->mockHTTPRequest('PUT', $uri, $headers, $params);
    }

    protected function mockPATCH($uri, $headers = [], $params = [])
    {
        return $this->mockHTTPRequest('PATCH', $uri, $headers, $params);
    }

    protected function mockDELETE($uri, $headers = [])
    {
        return $this->mockHTTPRequest('DELETE', $uri, $headers);
    }

    private function mockHTTPRequest($method, $uri, $headers = [], $params = [])
    {
        $this->environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => $uri
        ]);
        $this->container['environment'] = $this->environment;
        $request = \Slim\Http\Request::createFromEnvironment($this->environment);

        foreach ($headers as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        if (!empty($params)) {
            $request = $request->withParsedBody($params);
        }

        $response = $this->app->process($request, new Response());

        return $response;
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
        $this->container = $this->app->getContainer();
        $paths = explode("\\", static::class);
        $klass = $paths[count($paths)-1];
        $klass = str_replace("Test", "", $klass);
        $klass = self::CONTROLLERS_NAMESPACE."\\$klass";
        $this->controller = new $klass($this->container);
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

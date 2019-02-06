<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Routes as Routes;
use Tests\Controllers\BaseControllerTestCase;
use Sw0rdfish\Controllers\UsersController;
use Tests\Factories\UserFactory as UserFactory;

/**
* Contains tests for the Sw0rdfish\Controllers\UsersController endpoint.
*/
class UsersControllerTest extends BaseControllerTestCase
{

    /** Defines an array of tables that should be cleaned before each test */
    const CLEANUP_TABLES = ['users', 'user_tokens', 'secrets'];

    /**
     * Basic index test
     *
     * @return void
     * @test
     */
    function indexAction()
    {
        UserFactory::createList(3);

        $headers = [
            'Content-Type' => 'application/json'
        ];
        $response = $this->mockGET('/users', $headers);
        $result = (string)$response->getBody();

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertNotEmpty($result);
        $this->assertCount(3, json_decode($result));
    }

    /**
     * Basic create test
     *
     * @return void
     * @test
     */
    function createAction()
    {
        $user = UserFactory::build();
        $params = $user->asArray();
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $response = $this->mockPOST('/users', $headers, $params);
        $result = (string)$response->getBody();
        $result = json_decode($result, true);

        $this->assertEquals($response->getStatusCode(), 201);
        $this->assertNotEmpty($result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('firstName', $result);
        $this->assertArrayHasKey('lastName', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('role', $result);
        $this->assertArrayHasKey('createdDate', $result);
        $this->assertArrayHasKey('updatedDate', $result);
        #TODO: password should not be returned
        #$this->assertArrayNotHasKey('password', $result);

        $this->assertNotEmpty($result['id']);
        $this->assertNotEmpty($result['firstName']);
        $this->assertNotEmpty($result['lastName']);
        $this->assertNotEmpty($result['email']);
        $this->assertNotEmpty($result['role']);
        $this->assertNotEmpty($result['createdDate']);
        $this->assertEmpty($result['updatedDate']);
    }

    /**
     * Basic update test
     *
     * @return void
     * @test
     */
    function updateAction()
    {
        $user = UserFactory::create([
            'firstName' => 'John',
            'lastName' => 'Wayne',
            'email' => 'john@example.com',
            'role' => 'user'
        ]);
        $updateParams = [
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => 'jane@example.com',
            'role' => 'admin'
        ];
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $userId = $user->id;
        $response = $this->mockPUT("/users/$userId", $headers, $updateParams);

        $result = (string)$response->getBody();
        $result = json_decode($result, true);

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertNotEmpty($result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('firstName', $result);
        $this->assertArrayHasKey('lastName', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('role', $result);
        $this->assertArrayHasKey('createdDate', $result);
        $this->assertArrayHasKey('updatedDate', $result);

        $this->assertNotEmpty($result['id']);
        $this->assertNotEmpty($result['firstName']);
        $this->assertNotEmpty($result['lastName']);
        $this->assertNotEmpty($result['email']);
        $this->assertNotEmpty($result['role']);
        $this->assertNotEmpty($result['createdDate']);
        $this->assertNotEmpty($result['updatedDate']);

        $this->assertEquals($userId, $result['id']);
        $this->assertEquals($updateParams['firstName'], $result['firstName']);
        $this->assertEquals($updateParams['lastName'], $result['lastName']);
        $this->assertEquals($updateParams['email'], $result['email']);
        $this->assertEquals($updateParams['role'], $result['role']);
    }

    /**
     * Basic show test
     *
     * @return void
     * @test
     */
    function showAction()
    {
        $user = UserFactory::create([
            'firstName' => 'John',
            'lastName' => 'Wayne',
            'email' => 'john@example.com',
            'role' => 'user'
        ]);
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $userId = $user->id;
        $response = $this->mockGET("/users/$userId", $headers);

        $result = (string)$response->getBody();
        $result = json_decode($result, true);

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertNotEmpty($result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('firstName', $result);
        $this->assertArrayHasKey('lastName', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('role', $result);
        $this->assertArrayHasKey('createdDate', $result);
        $this->assertArrayHasKey('updatedDate', $result);

        $this->assertNotEmpty($result['id']);
        $this->assertNotEmpty($result['firstName']);
        $this->assertNotEmpty($result['lastName']);
        $this->assertNotEmpty($result['email']);
        $this->assertNotEmpty($result['role']);
        $this->assertNotEmpty($result['createdDate']);

        $this->assertEquals($userId, $result['id']);
        $this->assertEquals($user->firstName, $result['firstName']);
        $this->assertEquals($user->lastName, $result['lastName']);
        $this->assertEquals($user->email, $result['email']);
        $this->assertEquals($user->role, $result['role']);
        $this->assertEquals($user->createdDate, $result['createdDate']);
    }

    /**
     * Basic delete test
     *
     * @return void
     * @test
     */
    function destroyAction()
    {
        $user = UserFactory::create([
            'firstName' => 'John',
            'lastName' => 'Wayne',
            'email' => 'john@example.com',
            'role' => 'user'
        ]);

        $userId = $user->id;
        $response = $this->mockDELETE("/users/$userId");
        $result = (string)$response->getBody();

        $this->assertEquals($response->getStatusCode(), 203);
        $this->assertEmpty($result);
    }

}

<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Routes as Routes;
use Tests\Controllers\BaseControllerTestCase;
use Sw0rdfish\Controllers\SecretsController;
use Tests\Factories\UserFactory as UserFactory;
use Tests\Factories\SecretFactory as SecretFactory;

/**
* Contains tests for the Sw0rdfish\Controllers\SecretsController endpoint.
*/
class SecretsControllerTest extends BaseControllerTestCase
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
        $user = UserFactory::create();
        SecretFactory::createList(
            3,
            [
                'userId' => $user->id
            ],
            true
        );

        $headers = [
            'Content-Type' => 'application/json'
        ];
        $response = $this->mockGET("/users/$user->id/secrets", $headers);
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
        $user = UserFactory::create();
        $params = SecretFactory::build()->asArray();
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $response = $this->mockPOST("/users/$user->id/secrets", $headers, $params);
        $result = (string)$response->getBody();
        $result = json_decode($result, true);

        $this->assertEquals($response->getStatusCode(), 201);
        $this->assertNotEmpty($result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('userId', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('notes', $result);
        $this->assertArrayHasKey('category', $result);
        $this->assertArrayHasKey('username', $result);
        $this->assertArrayHasKey('password', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('website', $result);
        $this->assertArrayHasKey('createdDate', $result);
        $this->assertArrayHasKey('updatedDate', $result);

        $this->assertNotEmpty($result['id']);
        $this->assertNotEmpty($result['userId']);
        $this->assertNotEmpty($result['name']);
        $this->assertNotEmpty($result['description']);
        $this->assertNotEmpty($result['notes']);
        $this->assertNotEmpty($result['category']);
        $this->assertNotEmpty($result['username']);
        $this->assertNotEmpty($result['password']);
        $this->assertNotEmpty($result['email']);
        $this->assertNotEmpty($result['website']);
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
        $user = UserFactory::create();
        $userId = $user->id;
        $secret = SecretFactory::create([
            'userId' => $user->id,
            'category' => 'generic_secret'
        ]);
        $secretId = $secret->id;
        $updateParams = [
            'name' => 'My test secret',
            'description' => 'Test secret description',
            'notes' => 'Test secret notes',
            'category' => 'website_credential_secret',
            'username' => 'john_updated',
            'password' => 'password_updated',
            'email' => 'john-updated@example.com',
            'website' => 'https://www.example-updated.com'
        ];
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $response = $this->mockPUT("/users/$userId/secrets/$secretId", $headers, $updateParams);

        $result = (string)$response->getBody();
        $result = json_decode($result, true);

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertNotEmpty($result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('userId', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('notes', $result);
        $this->assertArrayHasKey('category', $result);
        $this->assertArrayHasKey('username', $result);
        $this->assertArrayHasKey('password', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('website', $result);
        $this->assertArrayHasKey('createdDate', $result);
        $this->assertArrayHasKey('updatedDate', $result);

        $this->assertNotEmpty($result['id']);
        $this->assertNotEmpty($result['userId']);
        $this->assertNotEmpty($result['name']);
        $this->assertNotEmpty($result['description']);
        $this->assertNotEmpty($result['notes']);
        $this->assertNotEmpty($result['category']);
        $this->assertNotEmpty($result['username']);
        $this->assertNotEmpty($result['password']);
        $this->assertNotEmpty($result['email']);
        $this->assertNotEmpty($result['website']);
        $this->assertNotEmpty($result['createdDate']);
        $this->assertNotEmpty($result['updatedDate']);

        $this->assertEquals($secret->userId, $result['id']);
        $this->assertEquals($updateParams['name'], $result['name']);
        $this->assertEquals($updateParams['description'], $result['description']);
        $this->assertEquals($updateParams['notes'], $result['notes']);
        $this->assertEquals($updateParams['category'], $result['category']);
        $this->assertEquals($updateParams['username'], $result['username']);
        $this->assertEquals($updateParams['password'], $result['password']);
        $this->assertEquals($updateParams['email'], $result['email']);
        $this->assertEquals($updateParams['website'], $result['website']);
    }

    /**
     * Basic show test
     *
     * @return void
     * @test
     */
    function showAction()
    {
        $user = UserFactory::create();
        $userId = $user->id;
        $secret = SecretFactory::create([
            'name' => 'My test secret',
            'description' => 'Test secret description',
            'notes' => 'Test secret notes',
            'category' => 'website_credential_secret',
            'username' => 'john_updated',
            'password' => 'password_updated',
            'email' => 'john-updated@example.com',
            'website' => 'https://www.example-updated.com',
            'userId'=> $userId
        ]);
        $secretId = $secret->id;
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $response = $this->mockGET("/users/$userId/secrets/$secretId", $headers);

        $result = (string)$response->getBody();
        $result = json_decode($result, true);

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertNotEmpty($result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('notes', $result);
        $this->assertArrayHasKey('category', $result);
        $this->assertArrayHasKey('username', $result);
        $this->assertArrayHasKey('password', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('website', $result);
        $this->assertArrayHasKey('userId', $result);
        $this->assertArrayHasKey('createdDate', $result);
        $this->assertArrayHasKey('updatedDate', $result);

        $this->assertNotEmpty($result['id']);
        $this->assertNotEmpty($result['name']);
        $this->assertNotEmpty($result['description']);
        $this->assertNotEmpty($result['notes']);
        $this->assertNotEmpty($result['category']);
        $this->assertNotEmpty($result['username']);
        $this->assertNotEmpty($result['password']);
        $this->assertNotEmpty($result['email']);
        $this->assertNotEmpty($result['website']);
        $this->assertNotEmpty($result['userId']);
        $this->assertNotEmpty($result['createdDate']);

        $this->assertEquals($secretId, $result['id']);
        $this->assertEquals($userId, $result['userId']);
        $this->assertEquals($secret->name, $result['name']);
        $this->assertEquals($secret->description, $result['description']);
        $this->assertEquals($secret->notes, $result['notes']);
        $this->assertEquals($secret->category, $result['category']);
        $this->assertEquals($secret->username, $result['username']);
        $this->assertEquals($secret->password, $result['password']);
        $this->assertEquals($secret->email, $result['email']);
        $this->assertEquals($secret->website, $result['website']);
        $this->assertEquals($secret->createdDate, $result['createdDate']);
    }

    /**
     * Basic delete test
     *
     * @return void
     * @test
     */
    function destroyAction()
    {
        $user = UserFactory::create();
        $userId = $user->id;
        $secret = SecretFactory::create([
            'userId' => $userId
        ]);
        $secretId = $secret->id;

        $response = $this->mockDELETE("/users/$userId/secrets/$secretId");
        $result = (string)$response->getBody();

        $this->assertEquals($response->getStatusCode(), 203);
        $this->assertEmpty($result);
    }

}

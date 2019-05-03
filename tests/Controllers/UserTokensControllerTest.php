<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Routes as Routes;
use Tests\Controllers\BaseControllerTestCase;
use Sw0rdfish\Controllers\UserTokensController;
use Tests\Factories\UserFactory as UserFactory;
use Tests\Factories\UserTokenFactory as UserTokenFactory;

/**
 * Contains tests for the Sw0rdfish\Controllers\USerTokensController endpoint.
 */
class UserTokensControllerTest extends BaseControllerTestCase
{

    /**
     * Defines an array of tables that should be cleaned before each test
     */
    const CLEANUP_TABLES = ['users', 'user_tokens'];

    /**
     * Basic create test
     *
     * @return void
     * @test
     */
    function createAction()
    {
        $user = UserFactory::create();
        $params = [
            'email' => $user->email,
            'password' => $user->password,
            'type' => 'session'
        ];
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $response = $this->mockPOST('/tokens', $headers, $params);
        $result = (string)$response->getBody();
        $result = json_decode($result, true);

        var_dump($result);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertNotEmpty($result);

        // $this->assertArrayHasKey('id', $result);
        // $this->assertArrayHasKey('userId', $result);
        // $this->assertArrayHasKey('name', $result);
        // $this->assertArrayHasKey('description', $result);
        // $this->assertArrayHasKey('notes', $result);
        // $this->assertArrayHasKey('category', $result);
        // $this->assertArrayHasKey('username', $result);
        // $this->assertArrayHasKey('password', $result);
        // $this->assertArrayHasKey('email', $result);
        // $this->assertArrayHasKey('website', $result);
        // $this->assertArrayHasKey('createdDate', $result);
        // $this->assertArrayHasKey('updatedDate', $result);

        // $this->assertNotEmpty($result['id']);
        // $this->assertNotEmpty($result['userId']);
        // $this->assertNotEmpty($result['name']);
        // $this->assertNotEmpty($result['description']);
        // $this->assertNotEmpty($result['notes']);
        // $this->assertNotEmpty($result['category']);
        // $this->assertNotEmpty($result['username']);
        // $this->assertNotEmpty($result['password']);
        // $this->assertNotEmpty($result['email']);
        // $this->assertNotEmpty($result['website']);
        // $this->assertNotEmpty($result['createdDate']);
        // $this->assertEmpty($result['updatedDate']);
    }

}

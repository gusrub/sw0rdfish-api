<?php

namespace Test\Models;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Models\DatabaseManager as DatabaseManager;
use Sw0rdfish\Models\UserToken as UserToken;
use Tests\Models\BaseTestCase;
use Tests\Factories\UserFactory as UserFactory;
use Tests\Factories\UserTokenFactory as UserTokenFactory;

/**
* Contains tests for the Sw0rdfish\Models\UserToken model.
*/
class UserTokenTest extends BaseTestCase
{

    /**
     * Defines an array of tables that should be cleaned before each test
     */
    const CLEANUP_TABLES = ['users', 'user_tokens'];

    /**
     * Test that the model defines a table constant
     *
     * @return void
     * @test
     */
    function definesTableConstant()
    {
        $this->assertTrue(
            defined('Sw0rdfish\Models\UserToken::TABLE_NAME'),
            'UserToken has a TABLE_NAME constant defined'
        );
    }

    /**
     * Test that userId is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfUserId()
    {
        $token = UserTokenFactory::build(['userId'=>null]);
        $this->assertFalse($token->valid());
        $errors = $token->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('userId', $errors);
        $this->assertArrayHasKey('presence', $errors['userId']);
    }

    /**
     * Test that userId is numeric
     *
     * @return void
     * @test
     */
    function validatesNumericalityOfUserId()
    {
        $token = UserTokenFactory::build(['userId'=>'invalid']);
        $this->assertFalse($token->valid());
        $errors = $token->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('userId', $errors);
        $this->assertArrayHasKey('numeric', $errors['userId']);
    }

    /**
     * Test that userId is positive integer
     *
     * @return void
     * @test
     */
    function validatesPositivityOfUserId()
    {
        $token = UserTokenFactory::build(['userId'=>-15]);
        $this->assertFalse($token->valid());
        $errors = $token->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('userId', $errors);
        $this->assertArrayHasKey('numeric', $errors['userId']);
    }

    /**
     * Test that type is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfType()
    {
        $token = UserTokenFactory::build(['type'=>null]);
        $this->assertFalse($token->valid());
        $errors = $token->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('type', $errors);
        $this->assertArrayHasKey('presence', $errors['type']);
    }

    /**
     * Test that type is a valid value
     *
     * @return void
     * @test
     */
    function validatesInclusionOfType()
    {
        $token = UserTokenFactory::build(['type'=>'invalid']);
        $this->assertFalse($token->valid());
        $errors = $token->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('type', $errors);
        $this->assertArrayHasKey('inclusion', $errors['type']);
    }

    /**
     * Test that expiration is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfExpiration()
    {
        $token = UserTokenFactory::build(['expiration'=>null]);
        $this->assertFalse($token->valid());
        $errors = $token->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('expiration', $errors);
        $this->assertArrayHasKey('presence', $errors['expiration']);
    }

    /**
     * Test that a new user token is created
     *
     * @return void
     * @test
     */
    function createNew()
    {
        $user = UserFactory::create();
        $token = UserTokenFactory::build(['userId'=>$user->id]);
        $this->assertTrue($token->valid());
        $token->save();
        $this->assertNotEmpty($token->id);
    }

    /**
     * Test that an existing user token is retrieved
     *
     * @return void
     * @test
     */
    function get()
    {
        $user = UserFactory::create();
        $token = UserTokenFactory::create(['userId'=>$user->id]);
        $token = UserToken::get($token->id);
        $this->assertNotEmpty($token);
    }

    /**
     * Test that a user token is successfully deleted
     *
     * @return void
     * @test
     */
    function deleteExisting()
    {
        $user = UserFactory::create();
        $token = UserTokenFactory::create(['userId'=>$user->id]);
        $this->assertTrue($token->delete());
        $this->assertEmpty(UserToken::get($token->id));
    }

    /**
     * Test that all user tokens are returned when using no filters
     *
     * @return void
     * @test
     */
    function listWithoutFilters()
    {
        $user = UserFactory::create();
        $tokens = UserTokenFactory::createList(
            4,
            ['userId'=>$user->id],
            true
        );
        $tokens = UserToken::all();
        $this->assertNotEmpty($tokens);
        $this->assertEquals(4, count($tokens));
    }

    /**
     * Test that all user tokens that match where criteria are returned
     *
     * @return void
     * @test
     */
    function listWithWhereFilter()
    {
        $userId = UserFactory::create()->id;
        $tokens = UserTokenFactory::createList(
            4,
            ['userId'=>$userId],
            true
        );

        $tokens = UserToken::all([
            'where' => [
                'userId' => $userId
            ]
        ]);

        $this->assertNotEmpty($tokens);
        $this->assertEquals(4, count($tokens));
    }

    /**
     * Test that all user tokens that match like criteria are returned
     *
     * @return void
     * @test
     */
    function listWithLikeFilter()
    {
        $userId = UserFactory::create()->id;
        $tokens = UserTokenFactory::createList(
            2,
            [
                'type' => 'session',
                'userId'=> $userId
            ],
            true
        );
        $tokens = UserTokenFactory::createList(
            1,
            [
                'type' => 'email_confirmation',
                'userId'=> $userId
            ],
            true
        );

        $tokens = UserToken::all([
            'like' => [
                'type' => 'email'
            ]
        ]);

        $this->assertNotEmpty($tokens);
        $this->assertEquals(1, count($tokens));
    }

    /**
     * Test that only paginated records are returned
     *
     * @return void
     * @test
     */
    function listWithPagination()
    {
        putenv("MAX_RECORDS_PER_PAGE=5");
        $userId = UserFactory::create()->id;
        UserTokenFactory::createList(7, ['userId'=>$userId], true);

        $tokens = UserToken::all([
            'page' => 1
        ]);
        $this->assertNotEmpty($tokens);
        $this->assertEquals(5, count($tokens));

        $tokens = UserToken::all([
            'page' => 2
        ]);
        $this->assertNotEmpty($tokens);
        $this->assertEquals(2, count($tokens));
    }

    /**
     * Test that records are properly sorted
     *
     * @return void
     * @test
     */
    function listWithSorting()
    {
        $userId = UserFactory::create()->id;
        $tokens = UserTokenFactory::createList(4, ['userId'=>$userId], true);

        $tokens = UserToken::all([
            'orderBy' => 'id',
            'sort' => 'desc'
        ]);

        $this->assertNotEmpty($tokens);
        $this->assertEquals(4, $tokens[0]->id);
    }

}

<?php

namespace Test\Models;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Models\DatabaseManager as DatabaseManager;
use Sw0rdfish\Models\EmailSecret as EmailSecret;
use Tests\Models\BaseTestCase;
use Tests\Factories\UserFactory as UserFactory;
use Tests\Factories\EmailSecretFactory as EmailSecretFactory;

/**
* Contains tests for the Sw0rdfish\Models\EmailSecret model.
*/
class EmailSecretTest extends BaseTestCase
{
    /**
     * Defines an array of tables that should be cleaned before each test
     */
    const CLEANUP_TABLES = ['users', 'secrets', 'email_secrets'];

    /**
     * Test that the model defines a table constant
     *
     * @return void
     * @test
     */
    function definesTableConstant()
    {
        $this->assertTrue(
            defined('Sw0rdfish\Models\EmailSecret::TABLE_NAME'),
            'EmailSecret has a TABLE_NAME constant defined'
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
        $secret = EmailSecretFactory::build(['userId'=>null]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
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
        $secret = EmailSecretFactory::build(['userId'=>'invalid']);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
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
        $secret = EmailSecretFactory::build(['userId'=>-15]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('userId', $errors);
        $this->assertArrayHasKey('numeric', $errors['userId']);
    }

    /**
     * Test that name is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfName()
    {
        $secret = EmailSecretFactory::build(['name'=>null]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('presence', $errors['name']);
    }

    /**
     * Test that name is unique
     *
     * @return void
     * @test
     */
    function validatesUniquenessOfName()
    {
        $user = UserFactory::create();
        EmailSecretFactory::create([
            'name' => 'Test Email Secret',
            'userId' => $user->id
        ]);
        $secret = EmailSecretFactory::build([
            'name' => 'Test Email Secret',
            'userId' => $user->id
        ]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('uniqueness', $errors['name']);
    }

    /**
     * Test that category is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfCategory()
    {
        $secret = EmailSecretFactory::build([
            'category' => null
        ]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('category', $errors);
        $this->assertArrayHasKey('presence', $errors['category']);
    }

    /**
     * Test that category is present
     *
     * @return void
     * @test
     */
    function validatesInclusionOfCategory()
    {
        $secret = EmailSecretFactory::build([
            'category' => 'invalid'
        ]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('category', $errors);
        $this->assertArrayHasKey('inclusion', $errors['category']);
    }

    /**
     * Test that email is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfEmail()
    {
        $secret = EmailSecretFactory::build(['email'=>null]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('presence', $errors['email']);
    }

    /**
     * Test that password is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfPassword()
    {
        $secret = EmailSecretFactory::build(['password'=>null]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('password', $errors);
        $this->assertArrayHasKey('presence', $errors['password']);
    }

    /**
     * Test that a new email secret is created
     *
     * @return void
     * @test
     */
    function createNew()
    {
        $user = UserFactory::create();
        $secret = EmailSecretFactory::build(['userId'=>$user->id]);
        $this->assertTrue($secret->valid());
        $secret->save();
        $this->assertNotEmpty($secret->id);
    }

    /**
     * Test that an existing email secret is retrieved
     *
     * @return void
     * @test
     */
    function get()
    {
        $user = UserFactory::create();
        $secret = EmailSecretFactory::create(['userId'=>$user->id]);
        $secret = EmailSecret::get($secret->id);
        $this->assertNotEmpty($secret);
    }

    /**
     * Test that an email secret is successfully deleted
     *
     * @return void
     * @test
     */
    function deleteExisting()
    {
        $user = UserFactory::create();
        $secret = EmailSecretFactory::create(['userId'=>$user->id]);
        $this->assertTrue($secret->delete());
        $this->assertEmpty(EmailSecret::get($secret->id));
    }

    /**
     * Test that all email secrets are returned when using no filters
     *
     * @return void
     * @test
     */
    function listWithoutFilters()
    {
        $user = UserFactory::create();
        $secrets = EmailSecretFactory::createList(
            4,
            ['userId'=>$user->id],
            true
        );
        $secrets = EmailSecret::all();
        $this->assertNotEmpty($secrets);
        $this->assertEquals(4, count($secrets));
    }

    /**
     * Test that all email secrets that match where criteria are returned
     *
     * @return void
     * @test
     */
    function listWithWhereFilter()
    {
        $userId = UserFactory::create()->id;
        $secrets = EmailSecretFactory::createList(
            4,
            ['userId'=>$userId],
            true
        );

        $secrets = EmailSecret::all([
            'where' => [
                'userId' => $userId
            ]
        ]);

        $this->assertNotEmpty($secrets);
        $this->assertEquals(4, count($secrets));
    }

    /**
     * Test that all email secrets that match like criteria are returned
     *
     * @return void
     * @test
     */
    function listWithLikeFilter()
    {
        $userId = UserFactory::create()->id;
        $secrets = EmailSecretFactory::createList(
            2,
            [
                'description' => 'desc1',
                'userId'=> $userId
            ],
            true
        );
        $secrets = EmailSecretFactory::createList(
            2,
            [
                'description' => 'desc2',
                'userId'=> $userId
            ],
            true
        );

        $secrets = EmailSecret::all([
            'like' => [
                'description' => 'desc1'
            ]
        ]);

        $this->assertNotEmpty($secrets);
        $this->assertEquals(2, count($secrets));
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
        EmailSecretFactory::createList(7, ['userId'=>$userId], true);

        $secrets = EmailSecret::all([
            'page' => 1
        ]);
        $this->assertNotEmpty($secrets);
        $this->assertEquals(5, count($secrets));

        $secrets = EmailSecret::all([
            'page' => 2
        ]);
        $this->assertNotEmpty($secrets);
        $this->assertEquals(2, count($secrets));
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
        $secrets = EmailSecretFactory::createList(4, ['userId'=>$userId], true);

        $secrets = EmailSecret::all([
            'orderBy' => 'id',
            'sort' => 'desc'
        ]);

        $this->assertNotEmpty($secrets);
        $this->assertEquals(4, $secrets[0]->id);
    }
}

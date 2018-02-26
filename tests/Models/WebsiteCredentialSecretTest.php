<?php

namespace Test\Models;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Models\DatabaseManager as DatabaseManager;
use Sw0rdfish\Models\WebsiteCredentialSecret as WebsiteCredentialSecret;
use Tests\Models\BaseTestCase;
use Tests\Factories\UserFactory as UserFactory;
use Tests\Factories\WebsiteCredentialSecretFactory as WebsiteCredentialSecretFactory;

/**
* Contains tests for the Sw0rdfish\Models\WebsiteCredentialSecret model.
*/
class WebsiteCredentialSecretTest extends BaseTestCase
{
    /**
     * Defines an array of tables that should be cleaned before each test
     */
    const CLEANUP_TABLES = ['users', 'secrets', 'website_credential_secrets'];

    /**
     * Test that the model defines a table constant
     *
     * @return void
     * @test
     */
    function definesTableConstant()
    {
        $this->assertTrue(
            defined('Sw0rdfish\Models\WebsiteCredentialSecret::TABLE_NAME'),
            'WebsiteCredentialSecret has a TABLE_NAME constant defined'
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
        $secret = WebsiteCredentialSecretFactory::build(['userId'=>null]);
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
        $secret = WebsiteCredentialSecretFactory::build(['userId'=>'invalid']);
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
        $secret = WebsiteCredentialSecretFactory::build(['userId'=>-15]);
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
        $secret = WebsiteCredentialSecretFactory::build(['name'=>null]);
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
        WebsiteCredentialSecretFactory::create([
            'name' => 'Test Website Credential Secret',
            'userId' => $user->id
        ]);
        $secret = WebsiteCredentialSecretFactory::build([
            'name' => 'Test Website Credential Secret',
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
        $secret = WebsiteCredentialSecretFactory::build([
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
        $secret = WebsiteCredentialSecretFactory::build([
            'category' => 'invalid'
        ]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('category', $errors);
        $this->assertArrayHasKey('inclusion', $errors['category']);
    }

    /**
     * Test that username is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfUSername()
    {
        $secret = WebsiteCredentialSecretFactory::build(['username'=>null]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('username', $errors);
        $this->assertArrayHasKey('presence', $errors['username']);
    }

    /**
     * Test that password is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfPassword()
    {
        $secret = WebsiteCredentialSecretFactory::build(['password'=>null]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('password', $errors);
        $this->assertArrayHasKey('presence', $errors['password']);
    }

    /**
     * Test that url is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfUrl()
    {
        $secret = WebsiteCredentialSecretFactory::build(['url'=>null]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('url', $errors);
        $this->assertArrayHasKey('presence', $errors['url']);
    }

    /**
     * Test that a new website credential secret is created
     *
     * @return void
     * @test
     */
    function createNew()
    {
        $user = UserFactory::create();
        $secret = WebsiteCredentialSecretFactory::build(['userId'=>$user->id]);
        $this->assertTrue($secret->valid());
        $secret->save();
        $this->assertNotEmpty($secret->id);
    }

    /**
     * Test that an existing website credential secret is retrieved
     *
     * @return void
     * @test
     */
    function get()
    {
        $user = UserFactory::create();
        $secret = WebsiteCredentialSecretFactory::create(['userId'=>$user->id]);
        $secret = WebsiteCredentialSecret::get($secret->id);
        $this->assertNotEmpty($secret);
    }

    /**
     * Test that a website credential secret is successfully deleted
     *
     * @return void
     * @test
     */
    function deleteExisting()
    {
        $user = UserFactory::create();
        $secret = WebsiteCredentialSecretFactory::create(['userId'=>$user->id]);
        $this->assertTrue($secret->delete());
        $this->assertEmpty(WebsiteCredentialSecret::get($secret->id));
    }

    /**
     * Test that all website credential secrets are returned when using no filters
     *
     * @return void
     * @test
     */
    function listWithoutFilters()
    {
        $user = UserFactory::create();
        $secrets = WebsiteCredentialSecretFactory::createList(
            4,
            ['userId'=>$user->id],
            true
        );
        $secrets = WebsiteCredentialSecret::all();
        $this->assertNotEmpty($secrets);
        $this->assertEquals(4, count($secrets));
    }

    /**
     * Test that all website credential secrets that match where criteria are returned
     *
     * @return void
     * @test
     */
    function listWithWhereFilter()
    {
        $userId = UserFactory::create()->id;
        $secrets = WebsiteCredentialSecretFactory::createList(
            4,
            ['userId'=>$userId],
            true
        );

        $secrets = WebsiteCredentialSecret::all([
            'where' => [
                'userId' => $userId
            ]
        ]);

        $this->assertNotEmpty($secrets);
        $this->assertEquals(4, count($secrets));
    }

    /**
     * Test that all website credential secrets that match like criteria are returned
     *
     * @return void
     * @test
     */
    function listWithLikeFilter()
    {
        $userId = UserFactory::create()->id;
        $secrets = WebsiteCredentialSecretFactory::createList(
            2,
            [
                'description' => 'desc1',
                'userId'=> $userId
            ],
            true
        );
        $secrets = WebsiteCredentialSecretFactory::createList(
            2,
            [
                'description' => 'desc2',
                'userId'=> $userId
            ],
            true
        );

        $secrets = WebsiteCredentialSecret::all([
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
        WebsiteCredentialSecretFactory::createList(7, ['userId'=>$userId], true);

        $secrets = WebsiteCredentialSecret::all([
            'page' => 1
        ]);
        $this->assertNotEmpty($secrets);
        $this->assertEquals(5, count($secrets));

        $secrets = WebsiteCredentialSecret::all([
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
        $secrets = WebsiteCredentialSecretFactory::createList(4, ['userId'=>$userId], true);

        $secrets = WebsiteCredentialSecret::all([
            'orderBy' => 'id',
            'sort' => 'desc'
        ]);

        $this->assertNotEmpty($secrets);
        $this->assertEquals(4, $secrets[0]->id);
    }
}
